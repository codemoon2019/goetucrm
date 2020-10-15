/**
 * Created by Jianfeng Li on 2017/8/1.
 */

let ctx = document.querySelector("#ctx").getAttribute("content");
let token = document.querySelector("#token").getAttribute("content");
let version = document.querySelector("#appVersion").getAttribute("content");
let locale = document.querySelector("#appLocale").getAttribute("content");
let name = document.querySelector("#appName").getAttribute("content");
let cdnUrl = document.querySelector("#cdnUrl").getAttribute("content");
let storageUrl = document.querySelector("#storageUrl").getAttribute("content");
let googleKey = document.querySelector("#googleKey").getAttribute("content");

export const
    APP_STORAGE_URL = storageUrl,
    APP_CDN_URL = cdnUrl,
    APP_NAME = name,
    APP_LOCALE = locale,
    APP_VERSION = version,
    TOKEN = token,
    GOOGLE_KEY = googleKey,
    APP_URL = ctx;