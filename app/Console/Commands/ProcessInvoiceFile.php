<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\PartnerCompany;
use App\Models\Product;
use App\Models\PaymentFrequency;
use App\Models\InvoiceFrequency;
use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoicePayment;
use App\Models\PartnerPaymentInfo;

class ProcessInvoiceFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:processInvoiceFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Invoice file from gotosos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $hasFile = false;
        $proceed = true;
       	$file = Storage::disk('gotosos')->get('gotosos.csv');
       	$file = explode("\r\n", $file);
       	foreach ($file as $f){
       		$f = str_replace('"', '', $f);
       		$data = explode(",", $f);
       		if($data[0] != ""){
	       		if($data[0] != "dba"){
	       		
	       			$merchant = PartnerCompany::where('company_name',$data[0])->first();
	       			$product = Product::where('name',$data[6])->where('parent_id','<>',-1)->first();

					if(!isset($merchant)){
						Log::info('Cannot find Merchant - '.$data[0]);
						$proceed = false;    
					}else{
						if($merchant->partner->partner_type_id != 3){
							Log::info('Cannot find Merchant - '.$data[0]);
							$proceed = false;  						
						}
					}

					if(!isset($product)){
						Log::info('Cannot find Product - '.$data[6]);
						$proceed = false;    
					}
	       		}
	       	}
       	}
       	if($proceed){
	       	foreach ($file as $f){
	       		$f = str_replace('"', '', $f);
	       		$data = explode(",", $f);
	       		if($data[0] != ""){
		       		if($data[0] != "dba"){
		       			$merchant = PartnerCompany::where('company_name',$data[0])->first();
		       			$product = Product::where('name',$data[6])->where('parent_id','<>',-1)->first();
		       			$start_date = strtotime($data[10]);
						$start_date = date('Y-m-d',$start_date);
						if($data[11] == '0000-00-00 00:00:00'){
							$end_date = $start_date;
						}else{
			       			$end_date = strtotime($data[11]);
							$end_date = date('Y-m-d',$end_date);						
						}

	                    $invoiceFrequency = new InvoiceFrequency;
	                    $invoiceFrequency->order_id = -1;
	                    $invoiceFrequency->partner_id = $merchant->partner_id;
	                    $invoiceFrequency->product_id = $product->id;
	                    $invoiceFrequency->frequency = $data[8];
	                    $invoiceFrequency->register_date = date('Y-m-d'); 
	                    $invoiceFrequency->bill_date = $start_date;
	                    $invoiceFrequency->start_date = $start_date;
	                    $invoiceFrequency->end_date =  $end_date;
	                    $invoiceFrequency->due_date =  date('y:m:d', strtotime( $start_date. "+10 days"));
	                    $invoiceFrequency->amount =  $data[9];
	                    $invoiceFrequency->status =  $data[13];
	                    $invoiceFrequency->save();

		                $invoice = new InvoiceHeader;
		                $invoice->order_id =  -1;
		                $invoice->partner_id = $merchant->partner_id;
		                $invoice->invoice_date =  $start_date; 
		                $invoice->due_date =  date('y:m:d', strtotime( $start_date. "+10 days"));
		                $invoice->total_due =  $data[9];
		                $invoice->reference =  $product->mainproduct->name;
		                $invoice->create_by =  'System';
		                $invoice->status =  'P';
		                $invoice->remarks =  'Imported from GOTOSOS';
		                $invoice->save();

	                    $invoiceDetail = new InvoiceDetail;
	                    $invoiceDetail->invoice_id = $invoice->id;
	                    $invoiceDetail->order_id = -1; 
	                    $invoiceDetail->line_number = 1;
	                    $invoiceDetail->product_id = $product->id;
	                    $invoiceDetail->description = $product->name;
	                    $invoiceDetail->amount =  $data[9];
	                    $invoiceDetail->quantity =  1;
	                    $invoiceDetail->invoice_frequency_id =  $invoiceFrequency->id;
	                    $invoiceDetail->save();

		                $paymentInfo = PartnerPaymentInfo::where('partner_id',$merchant->partner_id)->where('is_default_payment',1)->first();
		                $paymentId = isset($paymentInfo) ? $paymentInfo->payment_type_id : 2;

		                $invoicePayment = new InvoicePayment;
		                $invoicePayment->invoice_id = $invoice->id;
		                $invoicePayment->payment_type_id =  $paymentId;
		                $invoicePayment->save();
		       		}
		       	}
	       	}       		
       	}else{
       		dd('Error/Missing Data encountered. Please check log file.');
       	}


        Log::info('End Gotosos file processing');
    }
}
