<ul class="tabs-rectangular">
    @php
        $involvedStatuses = [
            App\Models\MerchantStatus::BOARDING_ID,
            App\Models\MerchantStatus::FOR_APPROVAL_ID,
            App\Models\MerchantStatus::DECLINED_ID,
        ];

        $involvedStatuses2 = [
            App\Models\MerchantStatus::BOARDED_ID,
            App\Models\MerchantStatus::LIVE_ID,
            App\Models\MerchantStatus::CANCELLED_ID,
        ];

        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";

        if (strpos($merchantaccess, 'create order') === false){
            $canOrder = false;
            if (strpos($merchantaccess, 'order list') === false){
                $canOrder = false;
            }else{
                $canOrder = true;
            }

            if (in_array($merchant->merchant_status_id, $involvedStatuses)) {
                $canOrder = true;
            }
        }else{
            $canOrder = true;
            
            if (in_array($merchant->merchant_status_id, $involvedStatuses)) {
                $canOrder = true;
            }
    	}


        if (strpos($merchantaccess, 'view invoice') === false){
            $canBill = false;
        }else{
			$canBill = true;
        }
    @endphp

    @if (in_array($merchant->merchant_status_id, $involvedStatuses2))
        <li class="{{strpos(Request::url(),'dashboard')!== false ? "active" : "" }}"><a href="{{ url('merchants/details/'.$id.'/dashboard') }}">Dashboard</a></li>
    @endif

    
    <li class="{{strpos(Request::url(),'profile')!== false ? "active" : "" }}"><a href="{{ url('merchants/details/'.$id.'/profile') }}">Profile</a></li>
    
        @if ($canOrder)
            <li class="{{strpos(Request::url(),'products')!== false ? "active" : "" }}"><a href="{{ url('merchants/details/'.$id.'/products') }}">Products</a></li>
        @endif
        
    @if (in_array($merchant->merchant_status_id, $involvedStatuses2))
        <li style="display:none" class="{{strpos(Request::url(),'rmaServicing')!== false ? "active" : "" }}"><a href="{{ url('merchants/details/'.$id.'/rmaServicing') }}">RMA / Servicing</a></li>
        
        @if ($canBill)
            <li class="{{strpos(Request::url(),'billing')!== false ? "active" : "" }}"><a href="{{ url('merchants/details/'.$id.'/billing') }}">Billing</a></li>
        @endif

        @if ($merchant->copilot_merchant_id != 0)
            <li class="{{strpos(Request::url(),'cardconnect')!== false ? "active" : "" }}" style="background-color: orange"><a href="{{ url('merchants/details/'.$id.'/cardconnect') }}">CardConnect</a></li>
        @endif

        <li class="{{strpos(Request::url(),'brlist')!== false ? "active" : "" }}"><a href="{{ url('merchants/details/'.$id.'/brlist') }}">Branches</a></li>
    @endif


</ul>