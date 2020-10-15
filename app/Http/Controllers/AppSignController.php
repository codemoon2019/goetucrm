<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductOrder;
use App\Models\ProductOrderDetail;
use App\Models\Partner;
use App\Models\PartnerProduct;
use PDF;
use DB;
use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoicePayment;
use App\Models\InvoiceFrequency;
use App\Models\PartnerPaymentInfo;

use CoPilot;
use Guesl\CardConnect\CoPilot\Models\Merchant;
use Guesl\CardConnect\CoPilot\Models\Demographic;
use Guesl\CardConnect\CoPilot\Models\Address;
use Guesl\CardConnect\CoPilot\Models\BankDetail;
use Guesl\CardConnect\CoPilot\Models\Bank;
use Guesl\CardConnect\CoPilot\Models\Ownership as Own;
use Guesl\CardConnect\CoPilot\Models\Owner;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;
use Guesl\CardConnect\CoPilot\Models\Order;
use Guesl\CardConnect\CoPilot\Models\OrderShippingDetail;
use Guesl\CardConnect\CoPilot\Models\Pricing;
use Guesl\CardConnect\CoPilot\Models\FlatPricing;
use Guesl\CardConnect\CoPilot\Models\Fee;
use Guesl\CardConnect\CoPilot\Models\BillingPlan;

use App\Models\User;
use App\Models\Product;

class AppSignController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function appsign($key)
    {
        $order = ProductOrder::where('sign_code',$key)->whereIn('status',Array('Pending','PDF Sent'))->first();
        if(!isset($order)){
            return redirect('/');
        }
        $pdfUrl = '/appsign/'.$key.'/preview';
        return view("merchants.sign.appsign_email",compact('pdfUrl'));
    }   

    public function appsigned($key,Request $request)
    {
        DB::transaction(function() use ($key,$request){
            $order = ProductOrder::where('sign_code',$key)->whereIn('status',Array('Pending','PDF Sent'))->first();
            $order->signature = $request->txtImage;
            $order->status = 'Application Signed';
            $order->date_signed = date('Y-m-d H:i:s');
            $order->save();

            $invoice = new InvoiceHeader;
            $invoice->order_id =  $order->id;
            $invoice->partner_id =  $order->partner_id;
            $invoice->invoice_date =  date('Y-m-d'); 
            $invoice->due_date =  date('y:m:d', strtotime("+10 days"));
            $invoice->total_due =  $order->amount;
            $invoice->reference =  $order->product->name;
            $invoice->create_by =  'System';
            $invoice->status =  'U';
            $invoice->remarks =  'Created from Order #'.$order->id;
            $invoice->save();

            $paymentInfo = PartnerPaymentInfo::where('partner_id',$order->partner_id)->where('is_default_payment',1)->first();
            $paymentId = isset($paymentInfo) ? $paymentInfo->payment_type_id : 2;

            $invoicePayment = new InvoicePayment;
            $invoicePayment->invoice_id = $invoice->id;
            $invoicePayment->payment_type_id =  $paymentId;
            $invoicePayment->save();
            $lineCtr = 0;
            $hasCC = false;
            foreach($order->details as $detail){
                $lineCtr++;

                $invoiceFrequency = new InvoiceFrequency;
                $invoiceFrequency->order_id = $order->id;
                $invoiceFrequency->partner_id = $order->partner_id;
                $invoiceFrequency->product_id = $detail->product_id;
                $invoiceFrequency->frequency = $detail->frequency;
                $invoiceFrequency->register_date = date('Y-m-d'); 
                $invoiceFrequency->bill_date = $detail->start_date;
                $invoiceFrequency->start_date = $detail->start_date;
                $invoiceFrequency->end_date =  $detail->end_date;
                $invoiceFrequency->due_date =  date('y:m:d', strtotime( $detail->start_date. "+10 days"));
                $invoiceFrequency->amount =  $detail->amount;
                $invoiceFrequency->status =  'Active';
                $invoiceFrequency->save();

                $invoiceDetail = new InvoiceDetail;
                $invoiceDetail->invoice_id = $invoice->id;
                $invoiceDetail->order_id = $order->id; 
                $invoiceDetail->line_number = $lineCtr;
                $invoiceDetail->product_id = $detail->product_id;
                $invoiceDetail->description = $order->product->name;
                $invoiceDetail->amount =  $detail->amount;
                $invoiceDetail->quantity =  $detail->quantity;
                $invoiceDetail->invoice_frequency_id =  $invoiceFrequency->id;
                //get actual cost
                $partner = Partner::find($order->partner_id);
                $productCost = PartnerProduct::where('partner_id',$partner->parent_id)->where('product_id',$detail->product_id)->first();
                $invoiceDetail->cost = $productCost->buy_rate;

                $invoiceDetail->save();

                $pCheck = Product::find($detail->product_id);
                if ($pCheck->name == "CardPointe"){
                    $hasCC = true;
                }
            }

            if($hasCC){
                $msg = $this->coPilotMerchantSave($order->partner_id,'create');
                if( $msg != 'success'){
                    DB::rollback();
                    return redirect('/')->with('failed',$msg );
                }
            }

        });
        return redirect('/');
    }  

    public function coPilotMerchantSave($id,$action){
        CoPilot::createAccessToken();
        $merchant = new Merchant;
        if(!($action == 'create' || $action == 'update')){
            return 'Invalid action';
        }
        if($action == 'create')
        {
            $merchantData = Partner::where('id',$id)->where('partner_type_id',3)->where('copilot_merchant_id',0)->first();      
        }else{
            $merchantData = Partner::where('id',$id)->where('partner_type_id',3)->first();
        }
        if(!isset($merchantData))
        {   
            return 'success';
        }
        
        $merchant->setakaBusinessName($merchantData->partner_company->company_name);
        $merchant->setCustPrimaryAcctFlg(false);
        $merchant->setDbaName($merchantData->partner_company->dba);
        $merchant->setLegalBusinessName($merchantData->partner_company->company_name);
        $merchant->settaxFilingMethod($merchantData->partner_company->ownership == 'INDIVSOLE' ? 'SSN' : 'EIN');
        $merchant->settaxFilingName($merchantData->tax_filing_name);
        // $merchant->settaxFilingName('TESTTT');

        $demographic = new Demographic();
        $demographic->setMerchantTimeZone('ET');
        $demographic->setwebsiteAddress($merchantData->merchant_url);

        $businessAddress = new Address();
        $businessAddress->setAddress1($merchantData->partner_company->address1);
        $businessAddress->setCity($merchantData->partner_company->city);
        $businessAddress->setZip($merchantData->partner_company->zip);

        $demographic->setBusinessAddress($businessAddress);

        $mailingAddress = new Address();
        $mailingAddress->setAddress1($merchantData->partner_billing->address);
        $mailingAddress->setCity($merchantData->partner_billing->city);
        $mailingAddress->setZip($merchantData->partner_billing->zip);

        $demographic->setMailingAddress($mailingAddress);

        $merchant->setDemographic($demographic);

        $bankDetail = new BankDetail();
        $depositBank = new Bank();
        $depositBank->setBankAcctNum($merchantData->bank_account_no);
        $depositBank->setBankRoutingNum($merchantData->bank_routing_no);
        $depositBank->setBankAcctTypeCd($merchantData->bank_account_type_code);
        $depositBank->setBankName($merchantData->bank_name);

        $withdrawalBank = new Bank();
        $withdrawalBank->setBankAcctNum($merchantData->withdraw_bank_account_no);
        $withdrawalBank->setBankRoutingNum($merchantData->withdraw_bank_routing_no);
        $withdrawalBank->setBankAcctTypeCd($merchantData->withdraw_bank_account_type_code);
        $withdrawalBank->setBankName($merchantData->withdraw_bank_name);

        $bankDetail->setDepositBank($depositBank);
        $bankDetail->setWithdrawalBank($withdrawalBank);
        $merchant->setBankDetail($bankDetail);


        $ownership = new Own();
        $owner = new Owner();
        $owner->setOwnerAddress(
            (new Address())
                ->setAddress1($merchantData->partner_contact()->address1)
                ->setCity($merchantData->partner_contact()->city)
                ->setZip($merchantData->partner_contact()->zip)
        );
        $owner->setOwnerEmail($merchantData->partner_company->email);
        $owner->setOwnerName($merchantData->partner_contact()->first_name . ' ' . $merchantData->partner_contact()->last_name);
        $owner->setOwnerPhone(ltrim($merchantData->partner_contact()->other_number,'-'));
        $owner->setOwnerMobilePhone(ltrim($merchantData->partner_contact()->mobile_number,'-'));
        $owner->setOwnerSSN($merchantData->partner_contact()->ssn);
        $owner->setOwnerTitle("OWNER");

        $ownership->setOwner($owner);
        $ownership->setOwnershipTypeCd($merchantData->partner_company->ownership);
        $ownership->setDriversLicenseNumber($merchantData->partner_contact()->issued_id);
        $ownership->setDriversLicenseStateCd($merchantData->partner_company->state);

        $merchant->setOwnership($ownership);
        $merchant->setSalesCode(env('COPILOT_SALES_CODE'));
        $merchant->setWebLeadFlg(false);

        $pricing = new Pricing;
        $flatPricing = new FlatPricing;
        $flatPricing->setAmexEsaQualDiscountPct(0);
        $flatPricing->setAmexOptBlueQualDiscountPct(0);
        $flatPricing->setDiscoverQualCreditDiscountPct(0);
        $flatPricing->setMastercardQualCreditDiscountPct(0);
        $flatPricing->setVisaQualCreditDiscountPct(0);
        $object = json_decode(json_encode($flatPricing->toArray()), FALSE);
        $pricing->setFlatPricing($object);
        
        $merchant->setPricing($pricing);

        $fee = new Fee;
        $fee->setAchBatchFee(0);
        $fee->setAddressVerifFee(0);
        $fee->setAnnualMembershipFee(0);
        $fee->setAppFee(0);
        $fee->setAuthFee(0);
        $fee->setChargebackFee(0);
        $fee->setDataBreachFee(0);
        $fee->setDdaRejectFee(0);
        $fee->setEarlyCancelFee(0);
        $fee->setMinProcessFee(0);
        $fee->setMonthlyEquipmentRentalFee(0);
        $fee->setPciAnnualFee(0);
        $fee->setPciNonComplianceFee(0);
        $fee->setRegProdMonthlyFee(0);
        $fee->setRegProdMonthlyFee(0);
        $fee->setRetrievalFee(0);
        $fee->setStatementFee(0);
        $fee->setTransactionFee(0);
        $fee->setVoiceAuthFee(0);
        $fee->setWirelessActivationFee(0);
        $fee->setWirelessFee(0);
        $fee->setDuesAndAssessmentsFlg(true);
        $fee->setPassthruInterchgCostsFlg(true);

        $merchant->setFee($fee);

        $templateID =  1050;

        try{
            if($action == 'create')
            {
                $copilot =  CoPilot::createMerchant($templateID, $merchant, null);
                $merchantData->copilot_merchant_id = $copilot['merchantId'];
                $merchantData->save();
            }else{
                $copilot =  CoPilot::updateMerchant($merchantData->copilot_merchant_id, $merchant, null);
            }
            return 'success';
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }


    public function orderPreview($key){
        $order = ProductOrder::where('sign_code',$key)->whereIn('status',Array('Pending','PDF Sent'))->first();
        if(!isset($order)){
            return redirect('/');
        }
        $html = $this->createPDFHtml($order->id);
        return PDF::loadHTML($html)->setPaper('a4', 'portrait')->setWarnings(false)->save(public_path().'/pdf/order_preview_'.$order->id.'.pdf')->stream('order_preview_'.$order->id.'.pdf');
    }

    private function createPDFHtml($id){
        $order = ProductOrder::find($id);
        $merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',Array(3,9))->first();
        $user = User::where('username',$order->create_by)->first();
        $detailHtml= "";
        foreach($order->details as $detail){
            $detailHtml .= '<tr>
                                <td>'.$detail->product->name.'</td>
                                <td>'.$detail->frequency.'</td>
                                <td style="text-align: right;">'.$detail->quantity.'</td>
                                <td style="text-align: right;">$ '.$detail->amount.'</td>
                            </tr>';
        }
        
        $sig = "";
        $sigDate="";
        $sendDate=isset($order->date_sent) ? $order->date_sent->format('m/d/Y') : "";

        if($order->status == "Application Signed"){
            $data = explode(',', $order->signature);
            $sig = '<img class="imported" src="' . $order->signature . '" height="50" width="200"></img>';
            $sigDate = $order->date_signed->format('m/d/Y');
        }

        $html = '<!Doctype>
                    <html>
                        <head>
                            <meta charset="utf-8" />
                            <meta name="viewport" content="width=device-width, initial-scale=1" />
                            
                            <title>
                                GoETU Order Preview
                            </title>
                            <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
                            <style type="text/css">
                                .float-right{ float: right; }
                                .text-right{ text-align: right; }
                                .text-center{ text-align: center; }
                                table {
                                    width: 100%;
                                }
                                table td{
                                    border: 1px solid #000;
                                }
                                table .no-bordered{
                                    border: 0px;
                                }
                                .row-title{
                                    background: #000;
                                    color: #fff;
                                    font-weight: bold;
                                    text-align: center;
                                    text-transform: uppercase;
                                }
                                .sub{
                                    color: #000;
                                    background: #c7c7c7;
                                }
                                .no-border td{
                                    border-color: #fff;
                                }
                            </style>
                            </head>
                            <body>
                                <table class="table">
                                    <tr class="no-border">
                                        <td><img src="images/goetu.jpg" alt="Go3Sutdio"  height="50" width="200"/></td>
                                        <td colspan="3">
                                            <p class="text-right">
                                                <strong>Merchant Application Setup Form & Agreement</strong><br/>
                                                50 Broad St., Suite 1701, New York, NY 10004<br/>
                                                T:(888)377-381 &nbsp;&nbsp; F: (888)406-0777 &nbsp;&nbsp; E:support@go3solutions.com
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                <table class="table">
                                    <tr class="row-title"><td colspan="4">Product Name</td></tr>
                                    <tr class="text-center"><td colspan="4"><h4><strong>'.$order->product->name.'</strong></h4></td></tr>
                                    <tr><td colspan="4">&nbsp;</td></tr>

                                    <tr class="row-title">
                                        <td>Fees</td>
                                        <td>Frequency</td>
                                        <td>Quantity</td>
                                        <td>Amount</td>
                                    </tr>'.$detailHtml.'

                                    <tr>
                                        <td colspan="3">
                                            <span class="float-right">
                                                <strong>TOTAL: </strong>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">$ '.number_format($order->amount,2,".",",").'</td>
                                    </tr>

                                    <tr class="row-title"><td colspan="4">Payment Information</td></tr>
                                    <tr><td colspan="4"><strong>Preferred Payment.:&nbsp;&nbsp;&nbsp;&nbsp;'.$order->preferred_payment.'</strong></td></tr>
                                    <tr><td colspan="4"><strong>Account No.:&nbsp;&nbsp;&nbsp;&nbsp;'.$merchant->bank_account_no.'</strong></td></tr>
                                    <tr><td colspan="4"><strong>Routing No.:&nbsp;&nbsp;&nbsp;&nbsp;'.$merchant->bank_routing_no.'</strong></td></tr>
                                    <tr><td colspan="4">***Please provide a copy of a voided check with this application***</td></tr>

                                    <tr class="row-title"><td colspan="4">Agent Information</td></tr>
                                    <tr>
                                        <td colspan="2"><strong>Agent Name:'.$user->first_name.' '.$user->last_name.'</strong></td>
                                        <td colspan="2"><strong>Contact No:'.$user->country_code.$user->mobile_number.'</strong></td>
                                    </tr>

                                </table>
                                <br><br>
                                <table class="table">
                                    <tr class="row-title"><td colspan="4">Confirmation</td></tr>
                                    <tr >
                                        <td colspan="4" height="100" valign="top">
                                            <strong>Signature:</strong><br>'.$sig.'<br>
                                            <strong>Printed Name:&nbsp;&nbsp;&nbsp; '.$merchant->partner_contact()->first_name.' '. $merchant->partner_contact()->middle_name .' '.$merchant->partner_contact()->last_name.'</strong><br>
                                            <strong>Date Sent: &nbsp;&nbsp;&nbsp;'.$sendDate.'</strong><br>
                                            <strong>Date Signed: &nbsp;&nbsp;&nbsp;'.$sigDate.'</strong><br>
                                        </td>
                                    </tr>
                                </table>
                            </body>
                        </html>';

        return $html;
    }

}
