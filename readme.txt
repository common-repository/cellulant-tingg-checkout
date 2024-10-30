=== Cellulant Tingg Checkout ===
Contributors: Cellulant
Tags: tingg,checkout,payments,cellulant,woocommerce,payment gateway
Requires at least: 4.6
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.2.1
WC tested up to: 7.6.1
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A Woocommerce payment gateway plugin that allows merchants to integrate Tingg express checkout on their Woocommerce checkout page,
offering their customers a pan-african variety of payment options.

== Installation ==
**Note: You Should Have the Woocommerce plugin installed and activated.**
1. Click on 'manage' on woocommerce payment settings to open plugin settings.
2. Select the tingg ``checkout version`` you would like to integrate, i.e version 2 or version 3 (recommended).
3. Enable ``test mode`` to use test keys from the developer sandbox.
4. Fill in the test credentials in their respective fields, i.e. ``Test Service Code (version 2)`` or ``Business Code (version 3)``,
 ``Test IV Key`` , ``Test Secret Key`` , ``Test Access Key`` , ``Test Client ID`` and ``Test Client Secret``.
5. The ``API Key`` is a required field if you select checkout version 3 in step 2 above.
6. **Go Live:** disable test mode and add your production keys on the live keys section to start collecting payments online.
**Please refer to our official documentation for more details**
- [Version 2](https://cellulant.gitbook.io/checkout/)
- [Version 3](https://cellulant.gitbook.io/tingg-checkout-v3-api/)

== Screenshots ==
1. Edit Payment Configurations
2. Select the Tingg Checkout Version you would like to integrate.
3. Enable testmode to populate test fields with test credentials from sandbox and click on *save changes*.

== Frequently Asked Questions ==
1. Does Tingg checkout collect outside Africa?
Currently we are only enable payments in African markets.

2. Where can I find the API documentation?
You can refer to the user guides for [version 2](https://cellulant.gitbook.io/checkout/checkout-api/wordpress-plugin) and
[version 3](https://cellulant.gitbook.io/tingg-checkout-v3-api/plugins/wordpress)

== Changelog ==
= Version 1.1.0 =
- Added: Client ID, Client Secret and API Key fields in the configs. All are required fields for checkout version 3.0 while the
API Key is not required for checkout version 2.8.
- Changed: The payment acknowledgement process via the wordpress webhook call.

= Version 1.2.0 =
- Added: Enable logs option in the configs
- Added: Clear cart after order is placed.

= Version 1.2.1 =
- This version fixes a configuration related bug for UG merchants.  Upgrade immediately if seeking to collect in Uganda..
- Changed: Checkout logic to pick billing country if it's supported on tingg checkout platform.

== Use of Tingg as a Third Party Service ==
- The plugin is designed to enable merchants to receive payments through Tingg Checkout which is an online payment gateway
provided by Cellulant.
- Upon checkout, the customer is redirected to the Tingg checkout platform where they can make their payments using Card,
Bank Account, Bank Transfer, USSD,  or Mobile Money.
- Tingg checkout has 2 versions being supported: checkout version 2 and checkout version 3 which is the latest version.
- Both versions are supported in the plugin as shown in step 2) of the installation process.
- The plugin supports integration in both the test and live environments. The test environment allows merchants to test their integration
by simulating payments while the live environment allows the merchants to collect live payments from customers on their platform.
- The payment links for the test and live environments for version 2 and version 3 are as shown below.
**Checkout 2.8:**
    - Test: https://developer.tingg.africa/checkout/v2/express/
    - Live: https://online.tingg.africa/v2/express/
View our [developer docs](https://cellulant.gitbook.io/checkout/) for checkout version 2.
**Checkout 3.0**
    - Test: https://online.uat.tingg.africa/testing/express/checkout
    - Live: https://checkout.tingg.africa/express/checkout
View our [developer docs](https://cellulant.gitbook.io/tingg-checkout-v3-api/) for checkout version 3.

== Terms of Service ==
- [Cellulant Privacy Policy](https://www.cellulant.io/privacy-policy/) and [Terms and Conditions](https://www.cellulant.io/legal/)