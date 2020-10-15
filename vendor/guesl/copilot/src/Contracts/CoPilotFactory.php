<?php
/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/05/04
 */
namespace Guesl\CardConnect\CoPilot\Contracts;

interface CoPilotFactory
{
    /**
     * Init the exact payment gateway driver.
     *
     * @param $driver
     * @return mixed
     */
    public function driver($driver);
}
