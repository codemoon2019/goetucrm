<?php

namespace Guesl\CardConnect\CoPilot\Contracts;

use App\Models\Order;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/05/09
 */
interface OrderService
{
    public function create(Order $order);

    public function update();

    public function retrieve();

    public function cancel();
}
