/**
 * Created by Jianfeng Li on 2017/3/4.
 */
export const PAGE_SIZE = 20;

export const LOCALE_ZH_CN = "zh_cn";
export const LOCALE_DEFAULT = "en";

// Role
export const ROLE_SUPER_ADMIN = 99;
export const ROLE_ADMIN = 4;
export const ROLE_MERCHANT_ADMIN = 3;
export const ROLE_MERCHANT_EMPLOYEE = 2;
export const ROLE_COMMON = 1;

// Permission
export const READABLE = 1;
export const WRITABLE = 2;
export const EXECUTABLE = 4;

//common
export const STATUS_ACTIVATED = 1;
export const STATUS_DEACTIVATED = 0;
export const STATUS_POSITIVE = 1;
export const STATUS_NEGATIVE = 0;

export const SUCCESSFUL = "successful";
export const FAILURE = "failure";

// Stripe Object type
export const STRIPE_OBJECT_BANK_ACCOUNT = "bank_account";
export const STRIPE_OBJECT_CARD = "card";
