<?php

use Illuminate\Database\Seeder;
use App\Models\TicketEmailSupport;

class TicketEmailSupportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TicketEmailSupport::create([
            'company' => 'Go3 Solutions',
            'server' => 'mail.goetuit.com',
            'email_address' => 'go3cs@goetu.com',
            'password' => 'M@go3csE2',
            'port' => '587',
            'status'=> 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        TicketEmailSupport::create([
            'company' => 'Ez2Eat',
            'server' => 'mail.goetuit.com',
            'email_address' => 'ez2eatcs@goetu.com',
            'password' => 'M@ez2eatE3',
            'port' => '587',
            'status'=> 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        TicketEmailSupport::create([
            'company' => 'Go2POS',
            'server' => 'mail.goetuit.com',
            'email_address' => 'go2cs@goetu.com',
            'password' => 'M@go2csE4',
            'port' => '587',
            'status'=> 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);


    }
}
