<?php

namespace App\Http\Controllers\Merchants;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Partner;
use App\Models\PartnerType;
use App\Models\PartnerCompany;
use App\Models\PartnerContact;
use App\Models\PartnerBillingAddress;
use App\Models\PartnerShippingAddress;
use App\Models\PartnerDbaAddress;
use App\Models\PartnerAttachment;
use App\Models\PartnerPaymentInfo;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use App\Models\Ownership;
use Yajra\Datatables\Datatables;
use Cache;
use DB;
use Carbon\Carbon;
use DateTime;

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

use App\CardPointe\CardConnectRestClient;
use App\Models\CardConnectOrder;

class CardConnectController extends Controller
{
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
            return 'Merchant data does not exist';
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

    public function coPilotMerchantRetrieve($id,$action)
    {
        try{
            if(!($action == 'detail' || $action == 'status')){
                return 'Invalid action';
            }

            CoPilot::createAccessToken();
            $merchantData = Partner::where('id',$id)->where('partner_type_id',3)->first();
            if(!isset($merchantData))
            {   
                return 'Merchant does not exist';
            }  
            if($action == 'detail'){
                return CoPilot::retrieveMerchant($merchantData->copilot_merchant_id);
            }
            if($action == 'status'){
                return CoPilot::retrieveMerchantStatus($merchantData->copilot_merchant_id);
            }
        } catch (\Exception $e) {
            return  $e->getMessage();
        }        
    }


    public function coPilotEquipmentRetrieve($supplierCode,$typeCode,$pageNumber,$pageSize)
    {
        try{
            CoPilot::createAccessToken();
            // return CoPilot::listEquipmentCatalog(env('COPILOT_SALES_CODE'),'CARDCONNECT','TERMINAL',1,10);
            return CoPilot::listEquipmentCatalog(env('COPILOT_SALES_CODE'),$supplierCode,$typeCode,$pageNumber,$pageSize);
        } catch (\Exception $e) {
            return  $e->getMessage();
        }        
    }

    public function cardconnect($id)
    {
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        $formOrderUrl = "/merchants/copilot_create_order/".$id;
        $formCardPointeUrl = "/merchants/copilot_update_owner/".$id;
        $formBillingUrl = "/merchants/copilot_create_billing/".$id;
        $formOrderUpdateUrl = "/merchants/copilot_update_order/".$id;
        $formOrderCancelUrl = "/merchants/copilot_cancel_order/".$id;
        CoPilot::createAccessToken();
        try{
            $copilot = CoPilot::retrieveOwnerSiteUser($merchant->copilot_merchant_id);
            $merchantStatus = CoPilot::retrieveMerchantStatus($merchant->copilot_merchant_id);
            $signStat = CoPilot::retrieveMerchantSignatureStatus($merchant->copilot_merchant_id);
        } catch (\Exception $e) {
            return redirect('/merchants')->with('failed', $e->getMessage());
        }   
        $stateUS = State::where('country','US')->orderBy('abbr')->get();
        $merchantStatus = $merchantStatus->getBoardingProcessStatusCd() == "INPROG" ? "In Progress" : $merchantStatus->getBoardingProcessStatusCd();
        $signUrl = $signStat->getSignatureUrl();
        $signatureStatus = $signStat->getSignatureStatusCd() == "NOT_SENT" ? "Not Sent" : $signStat->getSignatureStatusCd();
        $signatureUrl = "/merchants/copilot_request_signature/".$id;

        $user = env('CARDPOINTE_USERNAME');
        $password = env('CARDPOINTE_PASSWORD');
        $url = env('CARDPOINTE_URL');
        $merchid = env('CARDPOINTE_MID');
        $client = new CardConnectRestClient($url, $user, $password);
        $ccProfiles = Array();
        if(isset($merchant->cardconnect_profile_id)){
            $acctid = "";
            $ccProfiles = $client->profileGet($merchant->cardconnect_profile_id, $acctid, $merchid); 
            if(isset($ccProfiles['0']['respstat'])){
                $ccProfiles = Array();
                $merchant->cardconnect_profile_id = null;
                $merchant->save();
            }
        }

        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        try{
            $orderList = CoPilot::listOrders($merchant->copilot_merchant_id,1,1000);
            foreach($orderList as &$order)
            {
                $ccOrder = CardConnectOrder::where('orderId',$order['orderId'])->first();
                $order['equipment'] = isset($ccOrder->equipment_name) ? $ccOrder->equipment_name : "";
            }
        } catch (\Exception $e) {
            return redirect('/merchants')->with('failed', $e->getMessage());
        }    

        return view("merchants.details.cardconnect",compact('id','merchant','formOrderUrl','copilot','merchantStatus','formCardPointeUrl','signatureStatus','signatureUrl','stateUS','ccProfiles','formBillingUrl','signUrl','orderList','formOrderUpdateUrl','formOrderCancelUrl'));
    }
    
    public function coPilotRequestSignature($id)
    {
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        try{
            CoPilot::createAccessToken();
            CoPilot::signMerchant($merchant->copilot_merchant_id);
            return redirect('/merchants/details/'.$id.'/cardconnect')->with('success', 'Signature Requested. Please click the provided link');
        } catch (\Exception $e) {
            return redirect('/merchants/details/'.$id.'/cardconnect')->with('failed', $e->getMessage());
        }      
    }

    public function coPilotEquipmentList($supplierCode,$typeCode)
    {
        try{
            CoPilot::createAccessToken();
            $equipments = CoPilot::listEquipmentCatalog(env('COPILOT_SALES_CODE'),$supplierCode,$typeCode,1,1000);
            $option = ""; 
            $equipments = json_decode($equipments);
            foreach ($equipments->rows as $equip) {
                $option .= '<option value="' . $equip->equipmentId .  '" data-name="'. $equip->equipmentName .'" data-desc="'. $equip->description .'" data-make="'. $equip->make .'" data-model="'. $equip->model .'" data-price="'. number_format((float)$equip->defaultPrice, 2, '.', '') .'" >' . $equip->equipmentName .'</option> ';
            }
            if($option == ""){
                 $option .= '<option value="-1"  data-name="NO AVAILABLE EQUIPMENT" data-desc="NO AVAILABLE EQUIPMENT" data-make="NA" data-model="NA" data-price="0.00" >NO AVAILABLE EQUIPMENT</option> ';
            }
            return array(
                'success' => true,
                'data' => $option,            
            ); 


        } catch (\Exception $e) {
            return array(
                'success' => false,     
                'message' => $e->getMessage(),
            ); 

        }        
    }

    public function coPilotCreateOrder($id, Request $request)
    {
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        
        $order = new Order;
        $order->setMerchantId($merchant->copilot_merchant_id);
        $order->setEquipmentId($request->equipmentCode);
        $order->setOrderNotes(isset($request->equipmentNotes) ? $request->equipmentNotes : "");
        $order->setQuantity($request->equipmentQty);
        if($request->billFrequency == "ONETIME"){
            $order->setUnitPrice($request->equipmentUnitPrice);
        }
        if($request->billFrequency == "MONTHLY"){
            $order->setMonthlyPrice($request->equipmentUnitPrice);
        }        
        
        $order->setBillToCd($request->billTo);
        $order->setBillingFrequencyCd($request->billFrequency);
        if($request->billTo == "PARTNERCC"){
            $order->setProfileId($request->profileID);
            $order->setAcctId($request->acctID);            
        }

        $shippingDetail = new OrderShippingDetail;
        $shippingDetail->setShippingAddress((new Address())
                ->setAddress1($request->shippingAddress)
                ->setAddress2('')
                ->setCity($request->city)
                ->setCountryCd('')
                ->setStateCd('')
                ->setZip($request->zip));

        $shippingDetail->setShipToAttn($request->shipTo);
        $shippingDetail->setShipToAttnEmail($request->emailTo);
        $shippingDetail->setShippingMethodCd($request->shippingMethod);
        if($request->shippingMethod == "EXPEDITED"){
            $shippingDetail->setShippingBillToCd($request->shippingBillTo);
        }

        // $shippingDetail->setShippingCarrierCd('');
        // $shippingDetail->setTrackingNumber('');
        // $shippingDetail->setShippingCost(0);
        $shippingDetail->setMerchantContactPhone($request->contactNo);
        // $shippingDetail->setMerchantContactPhoneExt('');
        // $shippingDetail->setPoNumber('');

        $order->setShippingDetails($shippingDetail);
        $order->setOrderStatusCd('NEW');
        // $order->setPlacedDatetime('');
        // $order->setShippedDatetime('');
        // $order->setCanceledDatetime('');
        // $order->setFulfillingDatetime('');
        // dd($order); 
        try{
            CoPilot::createAccessToken();
            $ccOrder = CoPilot::createOrder($order);
            $order = New CardConnectOrder;
            $order->orderId = $ccOrder['orderId'];
            $order->equipmentId = $request->equipmentCode;
            $order->equipment_name = $request->equipmentName;
            $order->save();
            return redirect('/merchants/details/'.$id.'/cardconnect#ohistory')->with('success','CardConnect Order Created');
        } catch (\Exception $e) {
            return redirect('/merchants/details/'.$id.'/cardconnect')->with('failed',$e->getMessage());  
        }      

    }

    public function coPilotUpdateOrder($id,Request $request)
    {
        $order = new Order;
        $order->setOrderNotes(isset($request->editEquipmentNotes) ? $request->editEquipmentNotes : "");
        $order->setQuantity($request->editEquipmentQty);
        if($request->editBillFrequency == "ONETIME"){
            $order->setUnitPrice($request->editEquipmentUnitPrice);
        }
        if($request->editBillFrequency == "MONTHLY"){
            $order->setMonthlyPrice($request->editEquipmentUnitPrice);
        }        
        
        $order->setBillToCd($request->editBillTo);
        $order->setBillingFrequencyCd($request->editBillFrequency);
        if($request->billTo == "PARTNERCC"){
            $order->setProfileId($request->editProfileID);
            $order->setAcctId($request->editAcctID);            
        }

        $shippingDetail = new OrderShippingDetail;
        $shippingDetail->setShippingAddress((new Address())
                ->setAddress1($request->editShippingAddress)
                ->setAddress2('')
                ->setCity($request->editCity)
                ->setCountryCd('')
                ->setStateCd('')
                ->setZip($request->editZip));

        $shippingDetail->setShipToAttn($request->editShipTo);
        $shippingDetail->setShipToAttnEmail($request->editEmailTo);
        $shippingDetail->setShippingMethodCd($request->editShippingMethod);
        if($request->editShippingMethod == "EXPEDITED"){
            $shippingDetail->setShippingBillToCd($request->editShippingBillTo);
        }

        $shippingDetail->setMerchantContactPhone($request->editContactNo);
        $order->setShippingDetails($shippingDetail);
        $order->setOrderStatusCd('NEW');

        try{
            CoPilot::createAccessToken();
            CoPilot::updateOrder($request->txtOrderId,$order);
            return redirect('/merchants/details/'.$id.'/cardconnect#ohistory')->with('success','CardConnect Order Updated');
        } catch (\Exception $e) {
            return redirect('/merchants/details/'.$id.'/cardconnect')->with('failed',$e->getMessage());  
        }      

    }

    public function coPilotOrderList($id)
    {
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        try{
            CoPilot::createAccessToken();
            return CoPilot::listOrders($merchant->copilot_merchant_id,1,10);
        } catch (\Exception $e) {
            return  $e->getMessage();
        }      
    }

    public function coPilotGetOrder($id)
    {
        try{
            CoPilot::createAccessToken();
            return CoPilot::retrieveOrder($id);
        } catch (\Exception $e) {
            return  $e->getMessage();
        }      
    }

    public function coPilotCancelOrder($id,$orderId)
    {
        try{
            CoPilot::createAccessToken();
            CoPilot::cancelOrder($orderId);
            return redirect('/merchants/details/'.$id.'/cardconnect#ohistory')->with('success','CardConnect Order Cancelled');
        } catch (\Exception $e) {
            return redirect('/merchants/details/'.$id.'/cardconnect')->with('failed',$e->getMessage());  
        }         
    }

    public function coPilotUpdateOwner($id, Request $request)
    {
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        try{
            $owner = new OwnerSiteUser;
            $owner->setFirstName($request->cpFirstName);
            $owner->setLastName($request->cpLastName);
            $owner->setEmail($request->cpEmail);

            CoPilot::createAccessToken();
            CoPilot::updateOwnerSiteUser($merchant->copilot_merchant_id,$owner);

            return redirect('/merchants/details/'.$id.'/cardconnect#cpointe')->with('success','Owner Info Updated');
        } catch (\Exception $e) {
            return redirect('/merchants/details/'.$id.'/cardconnect#cpointe')->with('failed',$e->getMessage());
        }      
    }

    public function cardPointSaveProfile($id, Request $request)
    {
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        $user = env('CARDPOINTE_USERNAME');
        $password = env('CARDPOINTE_PASSWORD');
        $url = env('CARDPOINTE_URL');
        $merchid = env('CARDPOINTE_MID');
        $client = new CardConnectRestClient($url, $user, $password);

        if(isset($merchant->cardconnect_profile_id)){
            $params = array(
                'profile' => $merchant->cardconnect_profile_id,
                'merchid' => $merchid,
                'defaultacct' => $request->txtDefault,
                'account' => $request->txtAccount,
                'expiry' => $request->txtExpiry,
                'name' => $request->txtName,
                'address' => $request->txtAddress,
                'city' => $request->txtCity,
                'region' => $request->txtState,
                'country' => "US",
                'postal' => $request->txtPostal,
            );
        }else{
            $params = array(
                'merchid' => $merchid,
                'defaultacct' => $request->txtDefault,
                'account' => $request->txtAccount,
                'expiry' => $request->txtExpiry,
                'name' => $request->txtName,
                'address' => $request->txtAddress,
                'city' => $request->txtCity,
                'region' => $request->txtState,
                'country' => "US",
                'postal' => $request->txtPostal,
            );
        }
        if($request->ccID != -1){
            $params = array(
                'profile' => $merchant->cardconnect_profile_id.'/'.$request->ccID,
                'merchid' => $merchid,
                'defaultacct' => $request->txtDefault,
                'expiry' => $request->txtExpiry,
                'name' => $request->txtName,
                'address' => $request->txtAddress,
                'city' => $request->txtCity,
                'region' => $request->txtState,
                'country' => "US",
                'postal' => $request->txtPostal,
                'profileupdate' => "Y"
            );
        }

        $response = $client->profileCreate($params);
        if($response['respstat'] == "A")
        {
            $merchant->cardconnect_profile_id = $response['profileid'];
            $merchant->save();
            return redirect('/merchants/details/'.$id.'/cardconnect#cconnect')->with('success',$response['resptext']);
        }else{
            return redirect('/merchants/details/'.$id.'/cardconnect#cconnect')->with('failed',$response['resptext']);
        }
        
    }

    public function cardPointGetProfile($id, $acctID)
    {
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        if(isset($merchant->cardconnect_profile_id)){
            $user = env('CARDPOINTE_USERNAME');
            $password = env('CARDPOINTE_PASSWORD');
            $url = env('CARDPOINTE_URL');
            $merchid = env('CARDPOINTE_MID');
            $client = new CardConnectRestClient($url, $user, $password);
            $response = $client->profileGet($merchant->cardconnect_profile_id, $acctID, $merchid);
            if($response[0]['defaultacct'] == ""){
                return array(
                    'success' => false,     
                    'message' => 'Account does not exist',
                ); 
            }
            return array(
                'success' => true,     
                'data' => $response[0],
            ); 

        }else{
            return array(
                'success' => false,     
                'message' => 'No merchant profile id found',
            ); 
        }
    }   

    public function cardPointDeleteProfile($id, $acctID)
    {
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        if(isset($merchant->cardconnect_profile_id)){
            $user = env('CARDPOINTE_USERNAME');
            $password = env('CARDPOINTE_PASSWORD');
            $url = env('CARDPOINTE_URL');
            $merchid = env('CARDPOINTE_MID');
            $client = new CardConnectRestClient($url, $user, $password);
            $response = $client->profileDelete($merchant->cardconnect_profile_id, $acctID, $merchid);
            if($response['respstat'] == 'A'){
                return array(
                    'success' => true,     
                    'message' => $response['resptext'],
                ); 
            }else{
                return array(
                    'success' => false,     
                    'message' => $response['resptext'],
                );                
            }

        }else{
            return array(
                'success' => false,     
                'message' => 'No merchant profile id found',
            ); 
        }
    }  

    public function coPilotCreateBillPlan($id, Request $request)
    {
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        
        $billPlan = new BillingPlan;
        $billPlan->setMerchId('496160873888');
        $billPlan->setProfileId($merchant->cardconnect_profile_id);
        $billPlan->setAcctId($request->billProfile);
        $billPlan->setAmount($request->billAmount);
        $billPlan->setTimeSpan($request->billTimeSpan);
        $billPlan->setEvery($request->billEvery);
        $billPlan->setUntilCondition($request->billUntil);
        if($request->billUntil == "N"){
            $billPlan->setUntilNumPayments($request->billUntilNumPayments);
        }
        if($request->billUntil == "D"){
            $billPlan->setUntilDate($request->billUntilDate);
        }
        $billPlan->setCurrencySymbol("$");
        $billPlan->setStartDate($request->billStartDate);
        $billPlan->setBillingPlanName($request->billName);
        if($request->billReceipt == "Y"){
            $email = 1;
        }else{
            $email = 0;
        }
        $billPlan->setOptions(Array("name" => "email_receipt", "value" => $email));
        // $billPlan->setPlanSatus($request->);
        // $billPlan->setBillingPlanSchedules($request->);

        try{
            CoPilot::createAccessToken();
            return CoPilot::createBillingPlan($billPlan);
        } catch (\Exception $e) {
            return  $e->getMessage();
        }      

    }


    public function test(){
        $merchant = Partner::where('id',2)->where('partner_type_id',3)->first();
        return CoPilot::createAccessToken();
        return CoPilot::listBillingPlans($merchant->copilot_merchant_id);
        // $user = "testing";
        // $password = "testing123";
        // $url = 'https://fts.cardconnect.com:6443/cardconnect/rest';
        $user = env('CARDPOINTE_USERNAME');
        $password = env('CARDPOINTE_PASSWORD');
        $url = env('CARDPOINTE_URL');
        $merchid = env('CARDPOINTE_MID');
        $client = new CardConnectRestClient($url, $user, $password);

        $profileid = "12748029339151154089";

        $request = array(
            'profile' => $profileid.'/1',
            'merchid' => $merchid,
            'defaultacct' => "Y",
            'account' => "5454545454545454",
            'expiry' => "0914",
            'name' => "Test Userx",
            'address' => "123 TEEST St",
            'city' => "TestCity",
            'region' => "TestState",
            'country' => "US",
            'postal' => "11111",
            'profileupdate' => "Y"
        );

        //9441149619831111

        // $response = $client->profileCreate($request);
        //  dd($response);
        
        $acctid = "";
        $response = $client->profileGet($profileid, $acctid, $merchid);
        dd($response);

        // $response = $client->profileDelete($profileid, $acctid, $merchid);
        // dd($response);
    }


}

