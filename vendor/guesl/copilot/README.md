<p align="center">
  <img src="https://github.com/adam-p/markdown-here/raw/master/src/common/images/icon48.png">
</p>

<p align="center">
<a href="https://packagist.org/packages/guesl/copilot"><img src="https://poser.pugx.org/guesl/copilot/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/guesl/copilot"><img src="https://poser.pugx.org/guesl/copilot/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/guesl/copilot"><img src="https://poser.pugx.org/guesl/copilot/license.svg" alt="License"></a>
</p>

## 1. Install and config CoPilot.
Card Connect CoPilot basic feature.

##### 1.1 Install the library
    composer require guesl/copilot
    
##### 1.2 Update app.php file under Laravel config folder.
    'providers' => [
       ...,

        /*
         * CardConnect CoPilot service provider.
         */
        Guesl\CardConnect\CoPilot\CoPilotServiceProvider::class,
    ],

    'aliases' => [
            ...,
            
            "CoPilot" => Guesl\CardConnect\CoPilot\Facades\CoPilot::class,
    ],

##### 1.3 Publish config file to application config folder.
    php artisan vendor:publish --tag=cardconnect
    
    
##### 1.4 Add env variables.
    
    COPILOT_USERNAME=ccapi@cardconnect.com<copilot username>
    COPILOT_PASSWORD=meESV@yWgGNa9<copilot password>
    COPILOT_CLIENT_SECRET=87b246b0-02c5-43e5-a8c6-7f0bef598448<copilot secret>
    COPILOT_SALES_CODE=CC99<copilot sales code>
    COPILOT_VERSION=1.0<copilot version>

## 2. How to Use
 [Official CoPilot APIs Documentations](https://developer.cardconnect.com/copilotapi)

### 2.1 Models
    Like Merchant, OwnerSiteUser, Bank, Fee.
    
    You can find all the names of the models and the columns in the documentations.
 
 [Official CoPilot APIs Schemas](https://developer.cardconnect.com/copilotapi#schemas) 
### 2.2
    /**
     * Created by Jianfeng Li.
     * User: Jianfeng Li
     * Date: 2018/05/04
     */
    interface CoPilotService
    {
        /**
         * Create access token.
         *
         * @return mixed
         */
        public function createAccessToken();
    
        /**
         * Refresh access token.
         *
         * @param $refreshToken
         * @return mixed
         */
        public function refreshAccessToken($refreshToken);
    
        /**
         * Create Merchant.
         *
         * @param int $templateId
         * @param Merchant $merchant
         * @param OwnerSiteUser|null $ownerUser
         * @return int
         */
        public function createMerchant(int $templateId, Merchant $merchant, OwnerSiteUser $ownerUser = null);
    
        /**
         * Update merchant.
         *
         * @param int $id
         * @param Merchant $merchant
         * @param OwnerSiteUser|null $ownerUser
         * @return mixed
         */
        public function updateMerchant(int $id, Merchant $merchant, OwnerSiteUser $ownerUser = null);
    
        /**
         * Retrieve merchant.
         *
         * @param int $id
         * @return Merchant
         */
        public function retrieveMerchant(int $id);
    
        /**
         * Retrieve merchant status.
         *
         * @param int $id
         * @return mixed
         */
        public function retrieveMerchantStatus(int $id);
    
        /**
         * Request Merchant Signature.
         *
         * If the request signature was successful, the signature url will be returned.
         *
         * @param int $id
         * @return string
         */
        public function signMerchant(int $id);
    
        /**
         * Request merchant signature status.
         *
         * @param int $id
         * @return SignatureStatus
         */
        public function retrieveMerchantSignatureStatus($id);
    
        /**
         * Put the merchant back in the In Progress state.
         *
         * @param $id
         */
        public function retractMerchantSignature($id);
    
        /**
         * Create a merchant attachment.
         *
         * return attachment id.
         *
         * @param $id
         * @param Attachment $attachment
         * @return int
         */
        public function createMerchantAttachment($id, Attachment $attachment);
    
        /**
         * Create owner site user for "CardPoint".
         *
         * @param int $merchantId
         * @param OwnerSiteUser $ownerUser
         * @return mixed
         */
        public function createOwnerSiteUser(int $merchantId, OwnerSiteUser $ownerUser);
    
        /**
         * Update owner site user for "CardPoint".
         *
         * @param int $merchantId
         * @param OwnerSiteUser|null $ownerUser
         * @return mixed
         */
        public function updateOwnerSiteUser(int $merchantId, OwnerSiteUser $ownerUser = null);
    
        /**
         * Retrieve owner site user of the merchant.
         *
         * @param int $merchantId
         * @return OwnerSiteUser
         */
        public function retrieveOwnerSiteUser(int $merchantId);
    
        /**
         * List the equipment available for a sales code.
         *
         * @param string|null $salesCode
         * @param string $equipmentSupplierCd
         * @param string $equipmentTypeCd
         * @param int $pageNumber
         * @param int $pageSize
         * @return EquipmentPageObject
         */
        public function listEquipmentCatalog(string $salesCode = null, string $equipmentSupplierCd, string $equipmentTypeCd, int $pageNumber, int $pageSize);
    }
    
    Usage: 
    Ex: CoPilot::createAccessToken();
        CoPilot::createAccessToken($refreshToken);
        
        // Take a look at the "MerchantTest" for more detail.
        CoPilot::createMerchant($templateId, $merchant, OwnerSiteUser $ownerUser = null);
        
        public function fakeOwnerSiteUser()
        {
            $faker = Factory::create();
            $ownerSiteUser = (new OwnerSiteUser())->setEmail($faker->email)->setFirstName($faker->firstName)->setLastName($faker->lastName);
            return $ownerSiteUser;
        }
        
        // Generate fake merchant and owner site user.
        // All models like Merchant, OwnerSiteUser support chain setter. 
        
        public function fakeMerchant()
        {
            $faker = Factory::create();
            $merchant = new Merchant();
            $merchant->setAkaBusinessName($faker->name);
            $merchant->setCustPrimaryAcctFlg(false);
            $merchant->setDbaName($faker->name);
            $merchant->setLegalBusinessName($faker->name);
    
            $demographic = new Demographic();
            $demographic->setBusinessIncorporatedStateCd("CO");
            $demographic->setMerchantTimeZone("MT");
            $demographic->setBusinessPhone("444-444-4444");
            $demographic->setBusinessFax("444-444-4444");
    
            $businessAddress = new Address();
            $businessAddress->setAddress1($faker->streetAddress);
            $businessAddress->setAddress2($faker->buildingNumber);
            $businessAddress->setCity($faker->city);
            $businessAddress->setCountryCd("US");
            $businessAddress->setStateCd("IL");
            $businessAddress->setZip("12345");
    
            $demographic->setBusinessAddress($businessAddress);
    
            $mailAddress = new Address();
            $mailAddress->setAddress1($faker->streetAddress);
            $mailAddress->setAddress2($faker->buildingNumber);
            $mailAddress->setCity($faker->city);
            $mailAddress->setCountryCd("US");
            $mailAddress->setStateCd("IL");
            $mailAddress->setZip("12345");
    
            $demographic->setMailingAddress($mailAddress);
    
            $merchant->setDemographic($demographic);
    
            $merchant->setBusinessIdTypeCd("ARTICLE");
            $merchant->setBusinessStartDate("04/26/2002");
    
            $bankDetail = new BankDetail();
            $depositBank = new Bank();
            $depositBank->setBankAcctNum("12345678");
            $depositBank->setBankRoutingNum("445566778");
            $depositBank->setBankAcctTypeCd("SAVINGS");
            $depositBank->setBankName("Dep Bank");
    
            $withdrawalBank = new Bank();
            $withdrawalBank->setBankAcctNum("87654321");
            $withdrawalBank->setBankRoutingNum("887766554");
            $withdrawalBank->setBankAcctTypeCd("BIZ");
            $withdrawalBank->setBankName("With Bank");
    
            $bankDetail->setDepositBank($depositBank);
            $bankDetail->setWithdrawalBank($withdrawalBank);
    
            $merchant->setBankDetail($bankDetail);
    
            $ownership = new Ownership();
            $owner = new Owner();
            $owner->setOwnerAddress(
                (new Address())
                    ->setAddress1("123 Fake St")
                    ->setAddress2("Apt 121")
                    ->setCity("Boulder")
                    ->setCountryCd("US")
                    ->setStateCd("CO")
                    ->setZip("19335")
            );
            $owner->setOwnerEmail("mowner@test.com");
            $owner->setOwnerName("Mike Lucas Owner");
            $owner->setOwnerDob("09/22/1997");
            $owner->setOwnerPhone("555-555-5555");
            $owner->setOwnerTitle("OWNER");
    
            $ownership->setOwner($owner);
            $ownership->setOwnershipTypeCd("INDIVSOLE");
            $ownership->setDriversLicenseNumber("2342343243");
            $ownership->setDriversLicenseStateCd("CO");
            $ownership->setOwnerOwnershipPct("100");
    
            $merchant->setOwnership($ownership);
    
            $merchantContactInfo = new MerchantContactInfo();
            $merchantContactInfo->setContactName("John Doe");
            $merchantContactInfo->setContactEmail("jdoe@contactemail.com");
            $merchantContactInfo->setContactPhone("333-333-3333");
    
            $merchant->setMerchantContactInfo($merchantContactInfo);
    
            $processing = new Processing();
            $platformDetails = new PlatformDetails();
            $platformDetails->setAmexProgramAssetCd("ESA");
            $platformDetails->setAmexProgramMid("2341251235434");
            $platformDetails->setDiscoverMid("2134123423555");
            $platformDetails->setDiscoverProgramCd("MAP");
            $platformDetails->setAcquiringFlg(true);
            $platformDetails->setTaxId("485968574");
            $platformDetails->setTid("968596859");
            $platformDetails->setBusnsId("234444234234");
            $platformDetails->setBusnsIdPlaceOfIssue("CO");
            $platformDetails->setCurrencyCode("USD");
            $platformDetails->setMccId("3073");
            $platformDetails->setBusinessDescription("Business Description");
    
            $businessDetail = new BusinessDetails();
            $businessDetail->setCustomerBillPriorToShipFlg(false);
            $businessDetail->setDepositReqForFulfillFlg(true);
            $businessDetail->setWhenCustomerChargedCd("INADVANCE");
            $businessDetail->setRefundPolicyCd("EXCHONLY");
            $businessDetail->setServiceProvidedInCd("30LESS");
    
            $volumeDetails = new VolumeDetails();
            $volumeDetails->setAverageMonthlyVolume(9999.00);
            $volumeDetails->setHighTicketAmount(99.99);
            $volumeDetails->setAverageTicketAmount(9.99);
    
            $deliveryPercentages = new DeliveryDetails();
            $deliveryPercentages->setDlvry0To7DaysPct(97);
            $deliveryPercentages->setDlvry15To30DaysPct(1);
            $deliveryPercentages->setDlvry8To14DaysPct(1);
            $deliveryPercentages->setDlvryOver30DaysPct(1);
    
            $modeOfTransaction = new TransactionDetails();
            $modeOfTransaction->setECommercePct(0);
            $modeOfTransaction->setKeyedPct(20);
            $modeOfTransaction->setMailOrderPct(30);
            $modeOfTransaction->setSwipedPct(50);
    
            $processing->setPlatformDetails($platformDetails);
            $processing->setBusinessDetails($businessDetail);
            $processing->setVolumeDetails($volumeDetails);
            $processing->setDeliveryPercentages($deliveryPercentages);
            $processing->setModeOfTransaction($modeOfTransaction);
    
            $merchant->setProcessing($processing);
    
            $merchant->setWebLeadFlg(true);
            $pricing = new Pricing();
            $pricing->setFlatPricing(
                (new FlatPricing())->setAmexEsaQualDiscountPct(22.00)
                    ->setAmexOptBlueQualDiscountPct(11.00)
                    ->setDiscoverQualCreditDiscountPct(1.00)
                    ->setMastercardQualCreditDiscountPct(5.00)
                    ->setVisaQualCreditDiscountPct(4.00)
            );
    
            $merchant->setPricing($pricing);
    
            $fee = new Fee();
            $fee->setAchBatchFee(1.11);
            $fee->setAddressVerifFee(1.11);
            $fee->setAnnualMembershipFee(1.11);
            $fee->setAppFee(1.11);
            $fee->setAuthFee(1.11);
            $fee->setChargebackFee(1.11);
            $fee->setDataBreachFee(1.11);
            $fee->setDdaRejectFee(1.11);
            $fee->setEarlyCancelFee(1.11);
            $fee->setMinProcessFee(1.11);
            $fee->setPciAnnualFee(1.11);
            $fee->setPciNonComplianceFee(1.11);
            $fee->setRegProdMonthlyFee(1.11);
            $fee->setRetrievalFee(1.11);
            $fee->setStatementFee(1.11);
            $fee->setTransactionFee(1.11);
            $fee->setVoiceAuthFee(0.10);
            $fee->setWirelessActivationFee(0.10);
            $fee->setWirelessFee(50);
            $fee->setDuesAndAssessmentsFlg(true);
            $fee->setPassthruInterchgCostsFlg(true);
    
            $merchant->setFee($fee);
            return $merchant;
        }