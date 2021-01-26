=== BigMarker action for Elementor Pro Forms ===
Contributors: slapic
Tags: bigmarker, elementor, forms, webinars, subscription form
Requires at least: 5.5
Tested up to: 5.6
Requires PHP: 7.3
Stable tag: 1.1.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Requires [Elementor Pro](https://elementor.com) 3.0 or greater

Simple plugin that integrates Elementor Pro form widget with BigMarker via API.


== Description ==


Simple solution for users of BigMarker and Elementor. This plugin will allow you to add channel subscribers and register webinar attendees via Elementor's Pro form widget.


== Installation ==

= Minimum Requirements =

* WordPress 5.5 or greater
* PHP version 7.3 or greater
* MySQL version 5.0 or greater
* [Elementor Pro](https://elementor.com) 3.0 or greater

= We recommend your host supports: =

* PHP version 7.4 or greater
* MySQL version 5.6 or greater
* WordPress Memory limit of 64 MB or greater (128 MB or higher is preferred)


= Installation =

1. Install using the WordPress built-in Plugin installer, or Extract the zip file and drop the contents in the `wp-content/plugins/` directory of your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Pages > Add New
4. Press the 'Edit with Elementor' button.
5. Now you can drag and drop the form widget of Elementor Pro from the left panel onto the content area, and find the BigMarker action in the "Actions after submit" dropdown.
6. Fill your BigMarker API Key and channel name details and all your subscribers will be subscribed to that channel.
7. Add the required fields as form fields: email, firstname, lastname and webinar (use the exact name as field ID on the advanced tab). The webinar should be a hidden field containing the BigMarker webinar ID (you can find it on the webinar manage page's dashboard)
8. Add optional fields: utm_bmcr_source


== Frequently Asked Questions ==

**Why do I need this plugin?**

Because there's no native way to send subscribers/registrants from Elementor Pro form widget to BigMarker, so if you want to avoid modifying your Functions.php to achieve this, you can just install BigMarker support for Elementor Forms with a couple of clicks.

**Why is Elementor Pro required?**

Because this integration works with the Form Widget, which is an Elementor Pro unique feature not available in the free plugin.

**Can I still use other integrations if I install BigMarker support for Elementor Forms?**

Yes, all the other form widget integrations will be available. You can even use more than one at the same time per form.

**Do I need to know how to code to use BigMarker support for Elementor Forms?**

No, you don't and that's the main reason that I created this plugin, so you can integrate both BigMarker and Elementor without knowing how to code.


== Screenshots ==


== Changelog ==

= 1.1.2 - 2021-01-25 =
* Reorganised files into the includes/ dir
* Only init the class if the relevant Elementor Pro class exists to prevent fatal errors
* Moved action init to the main class as a public function

= 1.1.1 - 2021-01-22 =
* Changed name to comply Wordpress restrictions

= 1.1.0 - 2021-01-22 =
* Tweak: only subscribe to the channel if the channel name is specified
* Tweak: only add to the webinar if the webinar field exists and not empty
* New: Handle API responses
* New: Add the returned bmid info as a custom field (bmid) to the user
* New: Add the returned webinar url (conference link) as a custom field to the user (bigmarker_conference_url)
* Tweak: only add the user_id to the json (conference registration) if the user is logged in
* Tweak: only add the utm_bmcr_source info to the json (conference registration) if it's not empty

= 1.0.0 - 2021-01-21 =
* Initial Release
