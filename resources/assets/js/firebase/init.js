var cdn_url = $('meta[id="cdnUrl"]').attr('content');
/**
 * local
 */
var api_key = "AIzaSyBh7xBvP3QymQX8zIgWL-4pIBbnK0myp8c";
var messaging = "1080088935753";
var env = "dev";
/**
 * vr2
 */
if (cdn_url.match("vr2")) {
    api_key = "AIzaSyBjMuY_766e2POouqIMsbAGqRwPZO_LqHk";
    messaging = "193334722570";
    env = "vr2";
}
/**
 * uat
 */
else if (cdn_url.match("uat")) {
    api_key = "AIzaSyAYP6TpJ8kr2N-4Vo2cVJh2jujtr4iB1rA";
    messaging = "135149225271";
    env = "uat";
} 
/**
 * Live
 */
else if (cdn_url.match("https") && !cdn_url.match("uat")) {
    var api_key = "AIzaSyBRTVNmrpykLVB27ZaA94B2yLr9fP9b_iU";
    var messaging = "15773246541";
    var env = "live";
}
/**
 * Initialize Firebase
 */
var config = {
    apiKey: api_key,
    authDomain: "goetu-" + env + ".firebaseapp.com",
    databaseURL: "https://goetu-" + env + ".firebaseio.com",
    projectId: "goetu-" + env,
    storageBucket: "goetu-" + env + ".appspot.com",
    messagingSenderId: messaging,
};
firebase.initializeApp(config);