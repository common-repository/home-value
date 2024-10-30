=== Home Value ===
Contributors: (8blocks)
Donate link: http://wordpress.org
Tags: real estate, home value, home price evaluations, avm
Requires at least: 4.6
Stable tag: trunk
Tested up to: 6.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Home Value provides automated home valuations for properties throughout the United States.

== Description ==

Home Value provides your website visitors the ability to get accurate home price valuations of their applicable property(s). The plugin also features address autocomplete searches powered by Google for easy and accurate address information. Upon selection of the chosen address the user is prompted with a screen to enter their basic information via a web form in exchange for a free property value available on the next screen after a successful form submission. You get leads, they get free valuations.

<strong>Introducing in 2.13: Zapier Integration</strong>
You can now integrate this plugin with Zapier webhooks to post this data into virtually any CRM out there. Check out the configuration instructions below to set it up!

<strong>Interested in a demo? Click Below: <br /> <a href="https://agentrules.com" target="_blank">Home Value Demo</a></strong>

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Network administrators can use the Network Settings > 8b Home Value screen to configure the plugin. Single installations have the settings available under the "8b Home Value" menu item.
4. From the main settings screen, check the "Generate or retrieve key" checkbox & then click save settings. This will generate your home value license key as well as a working Google Places API Key applicable to your website only.
5. Navigate to the E-Mail settings tab and enter in your desired email address to receive email notifications upon users completing your home evaluation form.
6. Finally place the <strong>[8b_home_value]</strong> shortcode on a page where you'd like the home value form to display and you're ready to start getting leads!

== Frequently Asked Questions ==

= How many home valuations do I get for free? =

You get 10 free valuations per month absolutely free. That number resets automatically every month.

= What if I want more valuations than provided for free? =

1. We offer premium upgrades that allow you to purchase additional values in bulk at rates much cheaper than the industry average starting at just $35.
2. From your 8b Home Value settings panel simply look for the Get More Valuations button and click it to view the current options available.
3. Upon checking out you will download and install the premium plugin and refresh your valuation status to view your additional valuations available.
4. Renewal and enterprise discounts are also available at <a href="https://homevalueplugin.com" target="_blank">Homevalueplugin.com</a>.

= Do you have values for every property in the US? =

Unfortunately we do not. However we did build this circumstance into the plugin so if a user enters an invalid address or address we do not current have the data for to provide an instant value, they will still go through the lead process at which point you can edit the information provided to them and optimally can then provide the user with a manual valuation of the property. 

= The values don't appear accurate, can I adjust them? =

Yes. As of the 2.20 release you now have the ability to increase/decrease the valuations based on %.

= Does this plugin work outside of the United States? =

No, it currently only works in the United States.

= How do I use Zapier to integrate Home Value with my CRM? =

1. Create a free account on Zapier.com if you don t already have one.
2. Create a Zap
3. Choose Webhooks by Zapier as the trigger app.
4. Choose Catch Hook as the trigger.
5. Leave Pick off a Child Key blank, click Continue.
6. Copy the provided Webhook URL into your Home Value Settings screen in the Webhooks box and check the Test Webhooks checkbox and Save.
7. Now return to Zapier and click the OK, I did this button to confirm the Webhook test.

You have now successfully configured Zapier Webhooks with our Home Value Plugin and can proceed to setting up your action step which will allow you to connect to your CRM of choice and map the data in there in real-time.

== Screenshots ==

1. Address Autocomplete
2. Property Found & Lead Capture
3. Home Valuation Delivered!
4. Home Value Settings Screen

== Upgrade Notice ==

= 1.0 20170117 =
First Version! Enjoy Kiddos

== Changelog ==

= 3.1.5 20230815 =

* Update available shortcodes for emails

= 3.1.4 20230808 =

* Single install settings update

= 3.1.1 20230807 =

* Messaging update

= 3.1 20230807 =

* Google Places API update requiring users to generate their own API key

= 3.0.8 20230802 =

* ajax fix

= 3.0.7 20230731 =

* Clean code in class-home-values-shortcodes.php

= 3.0.5 20230728 =

* Webhook issue resolved

= 3.0.4 20230628 =

* Shortcode conflict fixed

= 3.0.3 20230627 =

* Array and session fixes along with Wordpress MU updates

= 3.0.1 20230626 =

* New API configuration, EDD required updates, PHP 8.2 compatibility, license key updates, new auto-refill options.

= 2.33 20230330 =

* JS field and ajax fix

= 2.32 20221212 =

* API Valuation Status Fix

= 2.31 20220608 =

* PHP 8 Fix

= 2.28 20200929 =

* Update Maps API

= 2.26 20200929 =

* Fix returned valuations of $0

= 2.25 20200526 =

* Significant expansion of coverage of our home valuation api

= 2.23 20190913 =

* Fixed csv export issue

= 2.22 20190802 =

* Fixed email issue to work with smtp

= 2.21 20190711 =

* Fixed submit button issue

= 2.20 20190613 =

* Can now adjust property valuations based on % via General Settings
* Now sending property address with the webhook
* Fixed map so it displays default Google map if streetview is not found
* Sends email to both site admin and user who requested valuation upon completion
* Displays recent sales on final valuation screen if property was found

= 2.14 20170911 =

* Updated pricing algorithm to reflect better accuracy in the current market.

= 2.13 20170911 =

* New: Add webhooks option. The new lead data is sent to any specified webhook URLs.

= 2.12 20170911 =

* Fix: Local places API key is always used now.

= 2.11 20170907 =

* Google Places API key is now automatically included.

= 2.10 20170902 =

* Update common codebase with Sold Alerts plugin.

= 2.9 20170831 =

* New: Allow shortcodes in the e-mail sender e-mail and name.
* Fix: Show values properly. Read the file from disk, as it should.

= 2.8 20170822 =

* Rework admin menu in order to allow for more texts to be translated and replaced.

= 2.7 20170421 =

* Fix: Load PHPmailer class directly, instead of using wp_mail and, therefore, asking nicely. Fixes not being able to receive e-mails for some users.

= 2.6 20170405 =

* Fix: Better handling of status updates in the admin.
* Code: Rename api info text files.

= 2.5 20170315 =

* Fix: Export function correctly generates csv file after API update.

= 2.4 20170313 =

* Fix: Refresh valuation status upon entering a new API key.

= 2.3 20170310 =

* New: Add system info tab for debugging purposes.
* Fix: Use wp_remote_get function call to communicate with API server instead of file_get_contents, which is disabled on some web hosts.
* Code: Lots of separation of generic functions. See the vendor directory.

= 2.2 20170307 =

* New: Two new views for customizing the Lead post content, including support for shortcodes: lead_content_address and lead_content_no_address.
* New: If no Google API key is present when creating a Home Value API key, one will be retrieved automatically.
* E-mail shortcodes now have 8b_home_value prefix.

= 2.1 20170222 =

* Fix: Save Google API key on single installs.
* Fix: Test buttons now work on single installs.

= 2.0 20170216 =

* Moved to own Home Valuation API server.

= 1.0 20170117 =

* First Version of Home Value Plugin
