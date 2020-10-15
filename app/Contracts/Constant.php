<?php
/**
 * Created by PhpStorm.
 * User: eunamagpantay
 * Date: 4/6/18
 * Time: 3:19 PM
 */

namespace App\Contracts;


interface Constant
{
    /**
     * Page list constant
     */
    const PAGE_SIZE = 20;
    const SMALL_PAGE_SIZE = 8;

    /**
     * Default language
     */
    const LOCALE_DEFAULT = "en";

    const MODULE_ADMIN_COMPANY = "MODULE_ADMIN_COMPANY";

    const DEFAULT_CREATE_BY = 'Seeder';
    const DEFAULT_UPDATE_BY = 'Seeder';
    const DEFAULT_CREATE_BY_SYSTEM = 'SYSTEM';

    const DEFAULT_STATUS_ACTIVE = 'A';
    const DEFAULT_STATUS_DELETED = 'D';
    const DEFAULT_STATUS_INACTIVE = 'I';
}
