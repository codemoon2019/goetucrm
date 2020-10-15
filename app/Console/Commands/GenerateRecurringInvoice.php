<?php

namespace App\Console\Commands;

use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\InvoiceFrequency;
use App\Models\InvoiceHeader;
use App\Models\InvoicePayment;
use App\Models\PaymentFrequency;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateRecurringInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:generateRecurringInvoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate recurring invoice';

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
        Log::info('Started Generate Recurring Invoice');            
        $paymentFrequencies = PaymentFrequency::where([
            ['status', 'A'],
            ['name', '<>', 'One-Time']
        ])->get();

        foreach ($paymentFrequencies as $paymentFrequency) {
            Log::info($paymentFrequency->name);

            $systemSetting = SystemSetting::where('set_code', 
                'due_date_add_days')->first();

            $due = $paymentFrequency->days + $systemSetting->set_value;
            $hasRecord = false;

            /** Raw */
            /* $cmd = "SELECT inf.order_id, inf.due_date, inf.bill_date,
				        inf.bill_date + INTERVAL {$paymentFrequency->days} DAY AS new_bill_date,
				        inf.due_date + INTERVAL {$due} DAY AS new_due_date,
				        inf.partner_id, SUM(inf.amount) AS total_amount 
				    FROM invoice_frequencies inf
				    WHERE inf.frequency = '{$paymentFrequency->name}' 
                        AND inf.status = 'Active'
                        AND inf.start_date <= DATE(NOW())
                        AND inf.end_date > DATE(NOW())
                        AND inf.bill_date <=  NOW() - INTERVAL {$paymentFrequency->days} DAY 
                    GROUP BY inf.order_id, inf.bill_date, inf.start_date, 
                        inf.end_date, inf.due_date, inf.partner_id";
            
            $invoiceFrequencies = DB::select(DB::raw($cmd)); */

            /** Eloquent */
            $invoiceFrequencies = InvoiceFrequency::select('order_id', 'due_date', 'bill_date', 
                'partner_id', DB::raw('SUM(amount) as total_amount'))
                ->where([
                    ['frequency', $paymentFrequency->name],
                    ['status', 'Active'],
                    ['start_date', '<=', Carbon::now()],
                    ['end_date', '>', Carbon::now()],
                    ['bill_date', '<=', Carbon::now()->subDays($paymentFrequency->days)]
                ])->groupBy('order_id', 'bill_date', 'start_date', 'end_date', 
                'due_date', 'partner_id')
                ->get();

            foreach ($invoiceFrequencies as $invoiceFrequency) {
                DB::beginTransaction();
                
                $newBillDate = Carbon::parse($invoiceFrequency->bill_date)->addDays($paymentFrequency->days);
                $newDueDate = Carbon::parse($invoiceFrequency->due_date)->addDays($due);

                try {
                    $product = ProductOrder::find($invoiceFrequency->order_id);
                    $product = Product::find($product->product_id);

                    $invoiceHeader = InvoiceHeader::create([
                        'order_id' => $invoiceFrequency->order_id,
                        'partner_id' => $invoiceFrequency->partner_id,
                        'invoice_date' => $newBillDate,
                        'due_date' => $newDueDate, 
                        'total_due' => $invoiceFrequency->total_amount,
                        'create_by' => 'Admin',
                        'status' => 'U',
                        'reference' => '',
                        'remarks' => 'System Generated Invoice',
                        'reference' => $product->name,
                    ]);

                    $invoicePayment = InvoicePayment::whereHas('invoiceHeader', 
                        function($query) use ($invoiceFrequency){
                            $query->where('order_id', $invoiceFrequency->order_id);
                        })->first();

                    InvoicePayment::create([
                        'invoice_id' => $invoiceHeader->id,
                        'payment_type_id' => is_null($invoicePayment) ?
                            2 : $invoicePayment->payment_type_id
                    ]);

                    $frequencies = InvoiceFrequency::with(['product' => function($query) {
                        $query->whereRaw('id = parent_id');
                    }, 'product.category:name'])->where([
                        ['frequency', $paymentFrequency->name],
                        ['order_id', $invoiceFrequency->order_id],
                        ['bill_date', $invoiceFrequency->bill_date],
                        ['due_date', $invoiceFrequency->due_date],
                        ['partner_id', $invoiceFrequency->partner_id]
                    ])->get();

                    Log::info($frequencies);

                    $lineCtr = 0;
                    foreach ($frequencies as $frequency) {
                        $lineCtr++;
                        $product = Product::find($frequency->product_id);
                        InvoiceDetail::create([
                            'invoice_id' => $invoiceHeader->id,
                            'line_number' => $lineCtr,
                            'product_id' => $frequency->product_id,
                            // 'description' => "{$frequency->parent_name}-{$frequency->product_name}-{$frequency->cat_name}",
                            'description' => $product->name,
                            'amount' => $frequency->amount,
                            'quantity' => 1,
                            'invoice_frequency_id' => $frequency->id,
                            'order_id' => $invoiceFrequency->order_id
                        ]);

                        $frequency->bill_date = $newBillDate;
                        $frequency->last_bill_date = $invoiceFrequency->bill_date;
                        $frequency->due_date = $newDueDate;
                        $frequency->save();
                    }

                    $hasRecord = true;
                    DB::commit();
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                    DB::rollBack();
                }
            }

            if (!$hasRecord) {
                $message = "No recurring invoice for {$paymentFrequency->name} payments";    
            }
        }

        Log::info('End Generate Recurring Invoice');            
    }
}
