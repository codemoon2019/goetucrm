<?php

use Illuminate\Database\Seeder;
use App\Models\InvoiceRejectCode;

class InvoiceRejectCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        InvoiceRejectCode::create([
            'code' => 'R01',
            'name' => 'Insufficient Funds',
            'remarks' => 'When the merchant has enough in the bank account to cover the outstanding debt, notifyAccounting and ask to re-debit the account or have the merchant pay over the phone with a credit card at any time.',
            'description' => 'Available balance is not sufficient to cover the dollar amount of the debit entry',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R02',
            'name' => 'Account Closed',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved. ',
            'description' => 'Previously active bank account is now closed',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R03',
            'name' => 'No Account/Unable to Locate Account',
            'remarks' => 'Verify with the merchant the deposit bank account information and fix any mistakes. If the bank information is correct, have the bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'The account number does not correspond to the individual identified in the entry or a valid account.',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R04',
            'name' => 'Invalid Account Number',
            'remarks' => 'Verify with the merchant deposit bank account information and fix any mistakes. If the bank information is correct, have the bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'The account number fails the check digit validation or may contain an incorrect number of digits.',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R05',
            'name' => 'Unauthorized Debit Entry',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'A business debit entry was transmitted to a members consumer account, and the member had not authorized',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R06',
            'name' => "Returned at Our Bank's Request",
            'remarks' => 'No action needed',
            'description' => '',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R07',
            'name' => 'Authorization Revoked by Customer',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'You previously authorized an entry but revoked authorization',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R08',
            'name' => 'Payment Stopped',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'You requested a stop payment',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R09',
            'name' => 'Uncollected Funds',
            'remarks' => 'When the merchant has enough in the bank account to cover the outstanding debt, inform Accountingand ask to re-debit the account.',
            'description' => 'Available balance is sufficient, but collected balance is not sufficient to cover the entry',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R10',
            'name' => 'Customer Advises Not Authorized',
            'remarks' => 'Have the merchant bank write or call us to confirm that this bank reject issue is resolved.',
            'description' => 'You advised your bank that we are not authorized to debit your account.',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R11',
            'name' => 'Check Safekeeping Entry Return',
            'remarks' => "You don't need to do anything.",
            'description' => '',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R12',
            'name' => 'Branch Sold To Another DFI',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'Entry destined for an account at a branch that has been sold to another financial institution.',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R13',
            'name' => 'Bank Not Qualified to Participate',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => "Your bank can't participate in ACH or the routing number is incorrect",
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R14',
            'name' => 'Account Holder Deceased',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'Account holder has died (used in the event of a Representative Payee, Guardian, or trustee.)',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R15',
            'name' => 'Beneficiary Deceased',
            'remarks' => "If principal is deceased, send us a Death Certificate. Depending on the bank's policy, a letter or complete account change may also be required.",
            'description' => 'Beneficiary had died.',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R16',
            'name' => 'Account Frozen',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'Funds unavailable due to legal action or your bank.',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R17',
            'name' => 'File Record Edit Criteria',
            'remarks' => "You don't need to do anything.",
            'description' => 'Your bank rejected some portions of this item (identified in return addenda)',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R20',
            'name' => 'Non-Transaction Account',
            'remarks' => 'The merchant deposit bank account must be transactional. In other words, we must be able to deposit and withdraw funds. (Not a savings account).    Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'Policies or regulations prohibit or limit ACH activity on your account',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R21',
            'name' => 'Invalid Company Identification',
            'remarks' => 'Not Applicable',
            'description' => '',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R22',
            'name' => 'Invalid Individual ID Number',
            'remarks' => 'Not Applicable',
            'description' => '',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R23',
            'name' => 'Entry refused by your bank',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'Due to one of the following:  Minimum or exact amount not remitted, Account subject to litigation',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R24',
            'name' => 'Duplicate Entry',
            'remarks' => "You don't need to do anything. Have the merchant bank write us to confirm that this bank reject issue is resolved.",
            'description' => 'Request appears to be a duplicate entry',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R29',
            'name' => 'Corporate Customer Advises Not Authorized',
            'remarks' => 'Have the merchant bank write us to confirm that this bank reject issue is resolved.',
            'description' => 'You told your bank that entry was not authorized',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R31',
            'name' => 'Permissible Return Entry',
            'remarks' => "You don't need to do anything.",
            'description' => 'We agree to accept a return entry beyond normal return deadline',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R33',
            'name' => 'Return of XCK Entry',
            'remarks' => 'Not Applicable',
            'description' => 'RDFI, at its discretion, returns an XCK entry (code only used for XCK returns), XCK entries may be',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R37',
            'name' => 'Source Doc Presented for Payment',
            'remarks' => 'Not Applicable',
            'description' => 'The source document to which an ACH entry relates has been presented for payment.',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R61',
            'name' => 'Misrouted return',
            'remarks' => 'Not Applicable',
            'description' => '',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R62',
            'name' => 'Incorrect trace number',
            'remarks' => 'Not Applicable',
            'description' => '',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R63',
            'name' => 'Incorrect dollar amount',
            'remarks' => 'Not Applicable',
            'description' => '',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R64',
            'name' => 'Incorrect individual identification',
            'remarks' => 'Not Applicable',
            'description' => '',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceRejectCode::create([
            'code' => 'R65',
            'name' => 'Incorrect transaction code',
            'remarks' => 'Not Applicable',
            'description' => '',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);


    }
}
