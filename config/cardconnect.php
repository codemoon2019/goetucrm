<?php

return [
    "copilot" => [
        "testMode" => config("app.env") == "production",
        "username" => env("COPILOT_USERNAME"),
        "password" => env("COPILOT_PASSWORD"),
        "clientSecret" => env("COPILOT_CLIENT_SECRET"),
        "salesCode" => env("COPILOT_SALES_CODE"),
        "version" => env("COPILOT_VERSION", "1.0"),
    ]
];
