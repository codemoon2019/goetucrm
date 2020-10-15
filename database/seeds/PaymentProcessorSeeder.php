<?php

use App\Models\PaymentProcessor;
use Illuminate\Database\Seeder;

class PaymentProcessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $system = new PaymentProcessor;
        $system->name = 'Merchant Company';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = '1st National Processing';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'American Credit Card Processing, Corp';
        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'ARGUS MERCHANT SERVICES';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'BA Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Bank of America Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'BANKCARD USA';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();  

        $system = new PaymentProcessor;
        $system->name = 'Capital Bankcard';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'CARDCONNECT';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();        

        $system = new PaymentProcessor;
        $system->name = 'Cardpayment Solutions ';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();   

        $system = new PaymentProcessor;
        $system->name = 'Cardservice International';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();  

        $system = new PaymentProcessor;
        $system->name = 'Chase Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();  

        $system = new PaymentProcessor;
        $system->name = 'Cynergydata';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();  

        $system = new PaymentProcessor;
        $system->name = 'Discover Network & First Data';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();  

        $system = new PaymentProcessor;
        $system->name = 'Electronic Merchant Systems';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();  

        $system = new PaymentProcessor;
        $system->name = 'EPAY WORLD';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();  

        $system = new PaymentProcessor;
        $system->name = 'EVO Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();  

        $system = new PaymentProcessor;
        $system->name = 'Express MPS';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();  

        $system = new PaymentProcessor;
        $system->name = 'Fifth Third Bank';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save(); 

        $system = new PaymentProcessor;
        $system->name = 'First American Payment System';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'First Banks Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'First Horizon Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'FirstData Card Service';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Heartland Payment Systems';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Huntington Bank';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Merchant Lynx Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Merchant Processing Center';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Merchant Services Inc';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Merchant Warehouse';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Mercury Payment Systems';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Money Tree Service Inc';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'MSI Merchant Services Inc';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Nation Payment System';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'National Bankcard Systems';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'National Processing  Company LLC';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Nationwide Payment Solution';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'NOVA INFO SYSTEMS';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'NPC Louisville';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Orion Payment Systems';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'RBSLynk';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Retriever Payment Systems Auth';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Sovereign Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Sun Trust Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Termnet Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'The Next Evolution Merchant Services';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Total Merchant Concept Inc';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Total Merchant Services ';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Transaction Solutions, LLC';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Transtech Merchant Group';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'UNION BANK';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'United Bank Card';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'US BANKCARD SERIVCE';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Well Fargo Bank';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

        $system = new PaymentProcessor;
        $system->name = 'Woodforest National Bank';

        $system->status = 'A';
        $system->create_by = 'Seeder';
        $system->save();

    }
}
