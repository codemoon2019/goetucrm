<?php

namespace App\Console\Commands;

use App\Models\PartnerPaymentInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadReturnFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:downloadReturnFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download return file';

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
        Log::info('Started Download Return File Cron');            

        $sftpInformations = PartnerPaymentInfo::select(
                'sftp_address', 'sftp_user', 'sftp_password', 'pay_to')
            ->distinct()->where([
                ['payment_type_id', 1],
                ['sftp_address', '<>', null],
                ['sftp_user', '<>', null],
                ['sftp_password', '<>', null]])
            ->get();

        foreach ($sftpInformations as $sftpInformation) {
            $ftp = Storage::createFtpDriver([
                'host'     => $sftpInformation->sftp_address, // To be Changed
                'username' => $sftpInformation->sftp_user,
                'password' => $sftpInformation->sftp_password,
                'timeout'  => '180',
            ]);

            $files = $ftp->files('/returns');

            foreach ($files as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($extension=="csv") {
                    $fileContents = $ftp->get($file);
                    Storage::disk('backup')->put("raw/" . basename($file), $fileContents);
                    Log::info(
                        "Downloaded from {$sftpInformation->sftp_address}/" .
                        "{$file} to app/public/backups/" . basename($file)
                    );  

                    Storage::disk('download')->put(basename($file), $fileContents);
                    Log::info(
                        "Downloaded from {$sftpInformation->sftp_address}/" .
                        "{$file} to app/public/downloads/" . basename($file)
                    );  

                    $ftp->delete($file);
                    Log::info(
                        "Deleted file {$sftpInformation->sftp_address}/" .
                        "{$file}"
                    );  

                    $hasFile = true;
                }
            }

        }

        if (!$hasFile) {
            Log::info('No files in SFTP directory.');        
        }
        
        Log::info('End Download Return File Cron');
    }
}
