<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadConfFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:downloadConfFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download configuration file';

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

        $cmd  = "SELECT DISTINCT ach.sftp_address, ach.sftp_user, ach.sftp_password, " .
                    "ach.pay_to, filename " .
                "FROM partner_payment_infos ppi " .
                "INNER JOIN invoice_payments p " .
                    "ON p.payment_type_id = ppi.payment_type_id " .
                    "AND p.payment_type_id = 1 " .
                "INNER JOIN invoice_headers h " .
                    "ON h.id = p.invoice_id " .
                "INNER JOIN partners pt on h.partner_id = pt.id " .
                "INNER JOIN ach_configurations ach on pt.company_id = ach.partner_id " .
                "WHERE filename IS NOT NULL " . 
                    "AND ach.sftp_address IS NOT NULL " .
                    "AND ach.sftp_user IS NOT NULL " .
                    "AND ach.sftp_password IS NOT NULL ";

        /* $sftpInformations = PartnerPaymentInfo::select(
            'sftp_address', 'sftp_user', 'sftp_password', 'pay_to', 'filename')
            ->distinct()
            ->join()
            ->join('invoice_header', 'invoice_header.id', '=', 'invoice_payment.id')
            ->where([
                ['payment_type_id', 1],
                ['sftp_address', '<>', null],
                ['sftp_user', '<>', null],
                ['sftp_password', '<>', null]])
            ->get(); */
        
        $sftpInformations = DB::select(DB::raw($cmd));

        Log::info($sftpInformations);

        foreach ($sftpInformations as $sftpInformation) {
            $ftp = Storage::createSftpDriver([
                'host'     => $sftpInformation->sftp_address, // To be Changed
                'username' => $sftpInformation->sftp_user,
                'password' => $sftpInformation->sftp_password,
                'timeout'  => '180',
            ]);

            if ($ftp->exists("/conffile/{$sftpInformation->filename}")) {
                $fileContents = $ftp->get("/conffile/{$sftpInformation->filename}");

                Storage::disk('download')->put("conffile/{$sftpInformation->filename}.csv", $fileContents);
                Log::info(
                    "Downloaded from {$sftpInformation->sftp_address}/" .
                    "{$sftpInformation->filename} to app/public/downloads/conffile/{$sftpInformation->filename}.csv"
                );  

                $hasFile = true;
            }
        }

        if (!$hasFile) {
            Log::info('No files in SFTP directory.');        
        }
        
        Log::info('End Download Conf File Cron');
    }
}
