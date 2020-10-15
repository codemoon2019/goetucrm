<?php

namespace Guesl\CardConnect\CoPilot\Contracts;

use App\Models\Order;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/05/09
 */
interface BillingPlanService
{
    public function create(Order $order);

    public function cancel();

    public function fetch();

    public function retrieve();
}
