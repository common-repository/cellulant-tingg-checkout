<?php

/*
 * Add values that you would want centralised throughout the plugin
*/

class TinggWordPressConstants
{
    const BRAND_NAME = 'tingg';
    const TINGG_ICON = 'https://developer.tingg.africa/logos/tingg-checkout-logo.png';
    const PAYMENT_GATEWAY = self::BRAND_NAME . '_checkout';
    const PAYMENT_GATEWAY_DESCRIPTION = (self::BRAND_NAME) . ' allows you to make and collect payment in 33+ countries in Africa from a single integration';

    const CHECKOUT_REDIRECT_LINKS = [
        "3.0" => [
            "test" =>"https://online.uat.tingg.africa/testing/express/checkout",
            "live" =>"https://checkout.tingg.africa/express/checkout"
        ],
        "2.8" => [
            "test" => "https://developer.tingg.africa/checkout/v2/express/",
            "live" => "https://online.tingg.africa/v2/express/"
        ]
    ];

    const GENERATE_OAUTH_TOKEN =  [
        "3.0" => [
            "test" => "https://api-dev.tingg.africa/v1/oauth/token/request",
            "live" => "https://api.tingg.africa/v1/oauth/token/request",
        ],
        "2.8" => [
            "test" => "https://developer.tingg.africa/checkout/v2/custom/oauth/token",
            "live" => "https://online.tingg.africa/v2/custom/oauth/token"
        ]
    ];

    const ACKNOWLEDGE_PAYMENTS = [
        "3.0" => [
            "test" => "https://api-dev.tingg.africa/v3/checkout-api/acknowledgement/request ",
            "live" => "https://api.tingg.africa/v3/checkout-api/acknowledgement/request"
        ],
        "2.8" => [
            "test" => "https://developer.tingg.africa/checkout/v2/custom/requests/acknowledge",
            "live" => "https://online.tingg.africa/v2/custom/requests/acknowledge",
        ]
    ];
    //supported countries
    const COUNTRIES = [
        "kenya" => [
            "currency_code" => "KES",
            "country_code" => "KE",
            "iso3_country_code" => 'KEN'
        ],
        "tanzania" => [
            "currency_code" => "TZS",
            "country_code" => "TZ",
            "iso3_country_code" => 'TZA'
        ],
        "uganda" => [
            "currency_code" => "UGX",
            "country_code" => "UG",
            "iso3_country_code" => 'UGA'
        ],
        "ghana" => [
            "currency_code" => "GHS",
            "country_code" => "GH",
            "iso3_country_code" => 'GHA'
        ],
        "zambia" => [
            "currency_code" => "ZMW",
            "country_code" => "ZM",
            "iso3_country_code" => 'ZMB'
        ],
        "zimbabwe" => [
            "currency_code" => "USD",
            "country_code" => "ZW",
            "iso3_country_code" => 'ZWE'
        ],
        "mozambique" => [
            "currency_code" => "MZN",
            "country_code" => "MZ",
            "iso3_country_code" => 'MOZ'
        ],
        "nigeria" => [
            "currency_code" => "NGN",
            "country_code" => "NG",
            "iso3_country_code" => 'NGA'
        ],
        "south-africa" => [
            "currency_code" => "ZAR",
            "country_code" => "ZA",
            "iso3_country_code" => 'ZAF'
        ],
        "senegal" => [
            "currency_code" => "XOF",
            "country_code" => "SN",
            "iso3_country_code" => 'SEN'
        ],
        "egypt" => [
            "currency_code" => "EGP",
            "country_code" => "EG",
            "iso3_country_code" => 'EGY'
        ],
        "botswana" => [
            "currency_code" => "BWP",
            "country_code" => "BW",
            "iso3_country_code" => 'BWA'
        ],
        "ivory coast" => [
            "currency_code" => "XOF",
            "country_code" => "CI",
            "iso3_country_code" => 'CIV'
        ],
        "rwanda" => [
            "currency_code" => "RWF",
            "country_code" => "RW",
            "iso3_country_code" => 'RWA'
        ],
        "malawi" => [
            "currency_code" => "MWK",
            "country_code" => "MW",
            "iso3_country_code" => 'MWI'
        ],
    ];
}