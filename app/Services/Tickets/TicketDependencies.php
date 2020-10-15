<?php

namespace App\Services\Tickets;

use App\Models\Access;
use App\Models\Partner;
use App\Models\Product;
use App\Models\TicketHeader;
use App\Models\TicketPriority;
use App\Models\TicketReason;
use App\Models\TicketStatus;
use App\Models\TicketType as TicketIssueType;
use App\Models\User;
use App\Services\Tickets\Departments\TicketDepartmentAccessorFactory;
use App\Services\Tickets\Merchants\TicketMerchantAccessorFactory;
use App\Services\Tickets\Partners\TicketPartnerAccessorFactory;
use App\Services\Tickets\Users\TicketUserAccessorFactory;

class TicketDependencies
{
    protected $user;
    protected $userClassification;
    protected $companyId;

    protected $ticketHeader;

    public $departments;
    public $departmentsGroups;

    public $merchants;
    public $merchantsGroups;

    public $partners;
    public $partnersGroups;

    public $products;
    public $productsGroups;

    public $ticketIssueTypes;
    public $ticketPriorities;
    public $ticketReasons;
    public $ticketStatuses;

    public $users;
    public $usersGroups;


    public function __construct(
        User $user,
        int $userClassification, 
        ?TicketHeader $ticketHeader=null)
    {

        $this->user = $user;
        $this->userClassification = $userClassification;
        $this->companyId = $user->company_id;

        $this->ticketHeader = $ticketHeader;

        $this->initializeDependencies();
    }

    public function initializeDependencies()
    {
        $this->setProducts();
        $this->setTicketIssueTypes();
        $this->setTicketReasons();
        
        switch($this->userClassification) {
            case TicketUserClassification::SUPER_ADMIN:
            case TicketUserClassification::COMPANY:
            case TicketUserClassification::INTERNAL_USER_DEPARTMENT_HEAD:
            case TicketUserClassification::INTERNAL_USER:
                $this->setTicketPriorities();
                $this->setTicketStatuses();
                $this->setUsers();
                break;

            case TicketUserClassification::PARTNER:
                $this->setUsers();
                break;
                
            case TicketUserClassification::MERCHANT:
                $this->setUsers();
                break;
        }
    }

    private function setProducts()
    {
        $columns = [
            'id', 
            'display_picture', 
            'name',
            'code',
            'company_id',
        ];

        $this->products = Product::select($columns)
            ->with('partnerCompany:id,partner_id,company_name')
            ->isActive()
            ->whereHas('partnerCompany', function($query) {
                $query->isActive();
            })
            ->whereCompany($this->companyId)
            ->orderBy('name')
            ->get();

        if (isset($this->ticketHeader)) {
            if (!$this->products->contains($this->ticketHeader->product))
                $this->products->push($this->ticketHeader->product);
        }

        $this->productsGroups = $this->products
            ->sortBy('partnerCompany.company_name', SORT_NATURAL|SORT_FLAG_CASE)
            ->groupBy('company_id');
    }

    private function setTicketIssueTypes()
    {
        $columns = [
            'id',
            'description',
            'company_id',
            'product_id',
        ];

        $this->ticketIssueTypes = TicketIssueType::isActive()
            ->whereCompany($this->companyId)
            ->get();

        if (isset($this->ticketHeader)) {
            $ticketIssueType = $this->ticketHeader->ticketIssueType;
            
            if (!$this->ticketIssueTypes->contains($ticketIssueType))
                $this->ticketIssueTypes->push($ticketIssueType);
        }
    }

    private function setTicketPriorities()
    {
        $columns = [
            'id',
            'code',
            'description',
        ];
        
        $this->ticketPriorities = TicketPriority::isActive()->get();
    }

    private function setTicketReasons()
    {
        $columns = [
            'id', 
            'description', 
            'ticket_type_id',
            'ticket_priority_code'
        ];

        $this->ticketReasons = TicketReason::select($columns)
            ->isActive()
            ->with('ticketPriority:id,code')
            ->whereCompany($this->companyId)
            ->orderBy('description')
            ->get();

        if (isset($this->ticketHeader)) {
            $ticketReason = $this->ticketHeader->ticketReason;
    
            if (!$this->ticketReasons->contains($ticketReason))
                $this->ticketReasons->push($ticketReason);
        }
    }

    private function setTicketStatuses()
    {
        $columns = [
            'id',
            'code',
            'description',
        ];

        $this->ticketStatuses = TicketStatus::select($columns)
            ->isActive()
            ->isAction()
            ->get();
    }

    private function setUsers()
    {
        $this->users = (new TicketUserAccessorFactory())
            ->make($this->user, $this->userClassification)
            ->getUsers();

        if (isset($this->ticketHeader)) {
            $ticketCCs = $this->ticketHeader->ccs;
            $ticketCCsDiff = $ticketCCs->diff($this->users);

            if (!$ticketCCsDiff->isEmpty())
                $this->users = $this->users->merge($ticketCCsDiff);
        }

        $this->usersGroups = $this->users
            ->sortBy('partnerCompany.company_name', SORT_NATURAL|SORT_FLAG_CASE)
            ->groupBy('company_id')
            ->map(function($users) {
                return $users->sortBy('full_name');
            });
    }
}