/**
 * Created by Jianfeng Li on 11/21/2017.
 */

import i18n from 'i18next';
import XHR from 'i18next-xhr-backend';
import * as Config from "./config";

i18n
    .use(XHR)
    .init({
        lng: Config.APP_LOCALE,
        fallbackLng: "en",
        ns: ['common'],
        defaultNS: 'common',
        //resources:
        backend: {
            loadPath: `${Config.APP_CDN_URL}/lang/{{lng}}/{{ns}}.json`,
            crossDomain: true,
            // adds parameters to resource URL. 'example.com' -> 'example.com?v=1.3.5'
            queryStringParams: {v: Config.APP_VERSION}
        },
    });

export default i18n;
