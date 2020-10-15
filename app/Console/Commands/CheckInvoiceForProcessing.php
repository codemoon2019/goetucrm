<?php

namespace App\Console\Commands;

use App\Models\InvoiceDetail;
use App\Models\InvoiceHeader;
use App\Models\Partner;
use App\Models\ProductInventory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckInvoiceForProcessing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:checkInvoiceForProcessing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check invoice for processing';

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
        Log::info('Start check invoice for processing');
        DB::beginTransaction();
        try {
            $invoiceHeaders = InvoiceHeader::where([
                ['is_exported', 1],
                ['status', '<>', 'P'],
                ['export_date', '<', Carbon::now()]
            ])->get();
    
            foreach ($invoiceHeaders as $invoiceHeader) {
                $invoiceDetails = InvoiceDetail::with('product')
                    ->where('invoice_id', $invoiceHeader->id)->get();
                
                foreach ($invoiceDetails as $invoiceDetail) {

                    $productType = $invoiceDetail->product->product_type ?? '';

                    if ($productType == 'INVENTORY') {
                        $companyId = Partner::get_upline_company($invoiceHeader->partner_id);
                        $productInventory = ProductInventory::where([
                            'partner_id' => $companyId,
                            'product_id' => $invoiceDetail->product_id
                        ])->first();

                        if (is_null($productInventory)) {
                            $quantity = $invoiceDetail->quantity * -1;
                            $data = array(
                                'partner_id' => $companyId,
                                'product_id' => $invoiceDetail->product_id,
                                'quantity' => $quantity,
                                'create_by' => $invoiceHeader->update_by,
                            );
                            
                            ProductInventory::create($data);
                        } else {
                            $quantity = $productInventory->quantity - $invoiceDetail->quantity;
                            $id = $productInventory->id;
                            
                            $productInventory->partner_id = $companyId;
                            $productInventory->product_id = $invoiceDetail->product_id;
                            $productInventory->quantity = $quantity;
                            $productInventory->update_by = $invoiceHeader->update_by;
                            $productInventory->save();
                        }
                    }
                }
    
                $invoiceHeader->status = 'H';
                $invoiceHeader->save();
            }

            DB::commit();
            Log::info('Successfully checked invoice for processing');
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error("Error: {$ex->getMessage()}");
        }
        Log::info('End Check Invoice For Processing');
    }
}
