<?php

namespace App\Services\Tickets\Requesters;

use App\Models\Product;
use App\Models\User;
use App\Services\Users\UserClassification;
use Exception;

class TicketRequesterAccessor
{
    public $merchants;
    public $merchantsGroups;
    
    public $partners;
    public $partnersGroups;

    public function __construct(Product $product, User $user)
    {
        $userClassification = new UserClassification($user);

        if ($userClassification->isAdmin ||
            $userClassification->isCompany ||
            $userClassification->isInternal ||
            $userClassification->isInternalDepartmentHead) {

            $trAccessor = new DefaultTicketRequesterAccessor($product);
        } else if ($userClassification->isPartner) {

            $trAccessor = new PartnerTicketRequesterAccessor($product, $user);
        } else {
            $this->merchants = null;
            $this->merchantsGroups = null;
            $this->partners = null;
            $this->partnersGroups = null;
            
            return;
        }

        $this->merchants = $trAccessor->getMerchants();
        $this->merchantsGroups = $this->merchants->groupBy('company_id');
        $this->partners = $trAccessor->getPartners();
        $this->partnersGroups = $this->partners->groupBy('company_id');
    }
}