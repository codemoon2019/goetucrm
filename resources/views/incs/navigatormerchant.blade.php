@php
    $activatedRestaurant = $user->activatedRestaurant();
    if(isset($activatedRestaurant)){
     $merchant = $activatedRestaurant->users()->wherehas('roles', function ($query) {
            $query->where('name', App\Models\Role::MERCHANT_ADMIN);
        })->first();
    }
@endphp
<li class="header text-uppercase">{{ __("common.title") }}</li>

<!-- Restaurant Management Module -->
<li class="treeview {{ \App\Contracts\Constant::MODULE_MERCHANT_RESTAURANT == $module ? "active" : "" }}">
    <a href="#">
        <i class="fa fa-th-large" aria-hidden="true"></i>
        <span>{{ __("model.restaurant.manage") }}</span>
        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
    </a>

    <ul class="treeview-menu">
        @if(isset($activatedRestaurant))
            <li class="{{ \App\Contracts\Constant::MERCHANT_RESTAURANT == $menu ? "active" : "" }}">
                <a href="{{ url("/restaurants/" . $activatedRestaurant->id . "/edit") }}">
                    <i class="fa fa-circle-o" aria-hidden="true"></i>
                    <span>{{ __("model.restaurant.nav") }}</span>
                </a>
            </li>
            <li class="{{ \App\Contracts\Constant::MERCHANT_RESTAURANT_ANNOUNCEMENT == $menu ? "active" : "" }}">
                <a href="{{ url("/announcements") }}">
                    <i class="fa fa-circle-o" aria-hidden="true"></i>
                    <span>{{ __("model.announcement.nav") }}</span>
                </a>
            </li>
            <li class="{{ \App\Contracts\Constant::MERCHANT_RESTAURANT_DELIVERY_ROUTE == $menu ? "active" : "" }}">
                <a href="{{ url("/deliveryroutes/" . $activatedRestaurant->id . "/edit/") }}">
                    <i class="fa fa-circle-o" aria-hidden="true"></i>
                    <span>{{ __("model.restaurantDeliveryRoute.nav") }}</span>
                </a>
            </li>
        @else
            {{--<li class="{{ \App\Contracts\Constant::MERCHANT_RESTAURANT == $menu ? "active" : "" }}">--}}
            {{--<a href="{{ url("/restaurants/create") }}">--}}
            {{--<i class="fa fa-plus-circle" aria-hidden="true"></i>--}}
            {{--<span>{{ __("model.restaurant.create") }}</span>--}}
            {{--</a>--}}
            {{--</li>--}}
        @endif
    </ul>
</li>
<!-- End Restaurant Management Module -->
@if(isset($activatedRestaurant))
    <!-- Coupon Management -->
    <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_COUPON == $module ? "active" : "" }}">
        <a href="{{ url("/coupons") }}">
            <i class="fa fa-tags" aria-hidden="true"></i>
            <span>{{ __("model.coupon.manage") }}</span>
        </a>
    </li>
    <!-- End Coupon Management -->

    <!-- Order Management -->
    <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_ORDER == $module ? "active" : "" }}">
        <a href="{{ url("/orders") }}">
            <i class="fa fa-opera" aria-hidden="true"></i>
            <span>{{ __("model.order.manage") }}</span>
        </a>
    </li>
    <!-- End Order Management -->

    <!-- Menu Management Module -->
    <li class="treeview {{ \App\Contracts\Constant::MODULE_MERCHANT_MENU == $module ? "active" : "" }}">
        <a href="#">
            <i class="fa fa-folder" aria-hidden="true"></i>
            <span>{{ __("model.menu.nav") }}</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
            {{--<li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_MENU_TITLE == $menu ? "active" : "" }}">--}}
            {{--<a href="{{ url("/menutitles") }}">--}}
            {{--<i class="fa fa-circle-o" aria-hidden="true"></i>--}}
            {{--<span>{{ __("model.menuTitle.manage") }}</span>--}}
            {{--</a>--}}
            {{--</li>--}}

            <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_MENU_CATEGORY == $menu ? "active" : "" }}">
                <a href="{{ url("/menucategories") }}">
                    <i class="fa fa-circle-o" aria-hidden="true"></i>
                    <span>{{ __("model.menuCategory.manage") }}</span>
                </a>
            </li>

            <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_MENU_ITEM == $menu ? "active" : "" }}">
                <a href="{{ url("/menuitems") }}">
                    <i class="fa fa-circle-o" aria-hidden="true"></i>
                    <span>{{ __("model.menuItem.manage") }}</span>
                </a>
            </li>

            <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_ADDON == $menu ? "active" : "" }}">
                <a href="{{ url("/addons") }}">
                    <i class="fa fa-circle-o" aria-hidden="true"></i>
                    <span>{{ __("model.menuOptionLabel.manage") }}</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- End Menu Management Module -->
    <!-- Payment Management -->
    @if(isset($merchant) && !empty($merchant->stripe_account_id))
        <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_PAYMENT == $module ? "active" : "" }}">
            <a href="{{ url("/payments") }}">
                <i class="fa fa-credit-card" aria-hidden="true"></i>
                <span>{{ __("model.paymentMethod.manage") }}</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">

                <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_PERSONAL_INFO == $menu ? "active" : "" }}">
                    <a href="{{ url("/usercenter/accounts") }}">
                        <i class="fa fa-circle-o" aria-hidden="true"></i>
                        <span>{{ __("model.paymentMethod.personalInfo") }}</span>
                    </a>
                </li>

                <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_PAYMENT_METHOD == $menu ? "active" : "" }}">
                    <a href="{{ url("/usercenter/paymentmethods") }}">
                        <i class="fa fa-circle-o" aria-hidden="true"></i>
                        <span>{{ __("model.paymentMethod.paymentMethod") }}</span>
                    </a>
                </li>

                <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_PAYMENT_TRANSACTION == $menu ? "active" : "" }}">
                    <a href="{{ url("/usercenter/transactions") }}">
                        <i class="fa fa-circle-o" aria-hidden="true"></i>
                        <span>{{ __("model.paymentMethod.transaction") }}</span>
                    </a>
                </li>

            </ul>
        </li>
        <!-- End Payment Management -->

    @else
        <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_PAYMENT == $module ? "active" : "" }}">
            <a href="{{ url("/payments") }}">
                <i class="fa fa-credit-card" aria-hidden="true"></i>
                <span>{{ __("model.paymentMethod.manage") }}</span>
            </a>
        </li>
    @endif
    <!-- User Management -->
    <li class="{{ \App\Contracts\Constant::MODULE_MERCHANT_USER == $module ? "active" : "" }}">
        <a href="{{ url("/users") }}">
            <i class="fa fa-users" aria-hidden="true"></i>
            <span>{{ __("users") }}</span>
        </a>
    </li>


    <!-- End User Management -->
@endif
