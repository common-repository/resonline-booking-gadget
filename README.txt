=== ResOnline Booking Gadget ===
Contributors: psdtofinal
Donate link: https://codecanyon.net/user/phoenixonline
Tags: resonline, accommodation, booking widget, booking gadget
Requires at least: 4.6
Tested up to: 5.2.1
Stable tag: trunk
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Displays a ResOnline Booking Gadget for any ResOnline property, using a simple short code.

== Description ==

The [ResOnline Booking Gadget](https://www.resonline.com/features/online-booking-integration) allows registered Hotels, Motels, Caravan Parks and other Accommodation Businesses to embed a relatively user-friendly booking widget directly on their website.

This plugin simplifies the process of embedding the *ResOnline Booking Gadget* by adding a simple shortcode to WordPress, allowing you to add the ResOnline Booking Gadget to any page, post or CPT on your site using:

`[resonline id="123456"]`

**ResOnline Booking Gadget Lite**

* Embed a Booking Gadget on **any** Page, Post or CPT on your site
* Set your default currency
* Set a default search layout (horizontal or vertical)
* Switch image previews / thumbnails on and off
* Override default settings at a shortcode-level

**ResOnline Booking Gadget Pro**

* **All of the free versions' options, plus...**
* Set the default number of days / columns displayed
* Change the default *Room Name* label

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/resonline-booking-gadget` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Navigate to *WordPress Dashboard* => *Settings* => *ResOnline* to cusotmise the settings

== Frequently Asked Questions ==

= Can I style the Booking Gadget =

Yes, but it's a bit fiddly. You will need to add custom CSS to your child theme.

Styling options will be coming in a later version of this plugin.

= Can I customise "Pro" features on the free version =

Yes, you can. This is done at the shortcode level, eg `[resonline id="123456" columns="12"]`.

== Screenshots ==

1. Default *Horizontal* layout of the *ResOnline Booking Gadget*
2. Default *Vertical* layout of the *ResOnline Booking Gadget*
3. The *ResOnline Booking Gadget* after custom styles have been added manually using a child-theme. Note that styling "options" are not presently available (although there are plans to add the feature soon)
4. *Standard* and *Pro* options
5. Shortcode options (available from *WordPress Dashboard* => *Settings* => *ResOnline* once the plugin has been installed and activated)

== Changelog ==

= 1.0.3 =
* Temporarily locked Pro features ON while an issue with the software licencing platform is being sorted

= 1.0.2 =
* Updated method used to connect to the verification server

= 1.0.1 =
* Added input sanitisation
* Added output escaping

= 1.0.0 =
* Initial build

== Upgrade Notice ==

= 1.0.2 =
Updated Pro verification service, made ResOnline scripts more WordPress'y

= 1.0.1 =
Hardened Admin input fields

= 1.0.0 =
Adds the core ResOnline Booking Gadget functionality

== Shortcode Usage ==

For ease of use and flexibility, this plugin's shortcodes can be added with practically no options; however most functional options can be used and / or overridden at a shortcode level (including the Columns and Room Label options)

**Basic Usage**

At a minimum, the Hotel ID must be supplied in order to "pick up" the correct Booking Gadget. So, if you have the Hotel ID 12345, the corresponding short code would be:

`[resonline id="12345"]`

**Additional Parameters / Overrides**

The following parameters can be added to the Standard or Pro version of the short code to override the default features:

`currency`

Any three-letter ISO currency code available to the vendor (eg AUD, USD, IDR, etc)

`layout`

Layout override. Either horiz for the Wide / Horizontal Layout or vert for the Tall / Vertical Layout

`show-images`

Set to true to display preview images / thumbnails, or false to hide image previews

`columns`

The default number of columns to display, when showing a Booking Gadget in Wide / Horizontal Layout mode

`label`

The label to show (eg, Rooms, Apartments, Cabins, etc)

**Example Shortcode Overrides**

Different default currency:

`[resonline id="12345" currency="GBP"]`

Force a horizontal layout:

`[resonline id="12345" layout="horiz"]`

Hide thumbnails:

`[resonline id="12345" show-images="false"]`

Reduce columns for a narrow layout:

`[resonline id="12345" columns="5"]`

Display a custom room label:

`[resonline id="12345" label="Yurts"]`

A combination of options:

`[resonline id="12345" currency="USD" layout="vert" label="Houseboats"]`