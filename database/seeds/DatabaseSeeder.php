<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CompaniesTableSeeder::class);
        $this->call(CalendarRemindersTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(DocumentsTableSeeder::class);
        $this->call(InventoryStatusesTableSeeder::class);
        $this->call(InvoiceRejectCodesTableSeeder::class);
        $this->call(InvoiceStatusesTableSeeder::class);
        $this->call(MarkUpTypesTableSeeder::class);
        $this->call(OwnershipsTableSeeder::class);
        $this->call(BankAccountTypesTableSeeder::class);
        $this->call(PartnerStatusesTableSeeder::class);
        $this->call(PaymentFrequenciesTableSeeder::class);
        $this->call(PaymentTypeFieldsTableSeeder::class);
        $this->call(PaymentTypesTableSeeder::class);
        $this->call(ProductPriceRulesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(SystemSettingsTableSeeder::class);
        $this->call(TicketEmailSupportsTableSeeder::class);
        $this->call(TimeZonesTableSeeder::class);
        $this->call(TrainingModulesTableSeeder::class);
        $this->call(TrainingsTableSeeder::class);

        /**
         * Partners
         */
        $this->call(PartnerTypesTableSeeder::class); //partner types
        $this->call(MerchantStatusesTableSeeder::class); 
        //$this->call(PartnersTableSeeder::class); //partners
        //$this->call(PartnerCompaniesTableSeeder::class); //partner companies

        /**
         * Products
         */
        $this->call(ProductTypesTableSeeder::class); //product types
        $this->call(ProductCategoriesTableSeeder::class); //product categories
        $this->call(ProductsTableSeeder::class); // products
        $this->call(ProductPaymentTypesTableSeeder::class); // product payment types

        /**
         * Resources
         */
        $this->call(ResourceGroupsTableSeeder::class); // resource groups
        $this->call(ResourceGroupAccessesTableSeeder::class); // resource group accesses
        $this->call(ResourcesTableSeeder::class); // resources

        /**
         * Users
         */
        $this->call(UserStatusesTableSeeder::class); //user statuses
        $this->call(UserTypesTableSeeder::class); //user types
        $this->call(UsersTableSeeder::class); //users
        $this->call(UserTemplatesTableSeeder::class); //users templates
        $this->call(UserTypeProductAccessesTableSeeder::class); //user type product accesses

        /**
         * Tickets
         */
        $this->call(TicketFilterCreationsTableSeeder::class); //ticket filter creations
        $this->call(TicketFiltersTableSeeder::class); //ticket filters
        $this->call(TicketPrioritiesTableSeeder::class); //ticket priorities
        $this->call(TicketStatusesTableSeeder::class); //ticket statuses
        $this->call(TicketTypesTableSeeder::class); //ticket types
        $this->call(TicketReasonsTableSeeder::class); //ticket types
        $this->call(FaqSeeder::class); //ticket types
        //$this->call(TicketDetailsTableSeeder::class); //ticket details
        //$this->call(TicketHeadersTableSeeder::class); //ticker headers

        $this->call(ReportsACLSeeder::class); //Reports access
        $this->call(AddOrderStartEndDateAccessSeeder::class); //Reports access
        $this->call(AddDivisionAccessSeeder::class); //Add Division Access
        $this->call(AddReportExportAccessSeeder::class); //Reports Access
        $this->call(LanguageTableSeeder::class); //Language Seeder
        $this->call(AddVerifySSNtoResoucesSeeder::class); //VerifySSN Access
        $this->call(AddMerchantConfirmationtoResoucesSeeder::class); //Add Merchant Confirmation Access
        $this->call(BannerAccessSeeder::class);
        $this->call(AnalyticsAccessSeeder::class);
        $this->call(SupplierLeadAccessSeeder::class);
    }
}
