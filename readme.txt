=== WebSuite Push Notifier ===
Contributors: publisherstoolbox, jacofabersa, kaylalampsa, drmzindec
Tags: publishers, websuite, push notifications, mobile monetization, user engagement
Requires at least: 5.0
Tested up to: 6.3.1
Requires PHP: 7.4
Stable tag: 1.1.7
Version: 1.1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send push notifications with custom messaging when a post is published.

== Description ==

Drive engagement by keeping your digital audience informed of your latest content/promotions/campaigns with our FREE WebSuite Push Notifier plug-in. Send push notifications with custom messaging when a post is published. This plugin is simple to install and easy to use - making this the perfect solution for media groups, bloggers and most other users who rely on WP architecture.

== Installation ==

= Simple installation for WordPress v5.0 and later =

1. Go to the Plugins, Add new.
2. Search WordPress for WebSuite Push Notifier then press Install now.
3. Activate plugin.
4. Enable WebSuite Push Notifier and Set plugin settings.
5. Enjoy.

= Comprehensive setup =

A more comprehensive setup process and guide to configuration is as follows.

1. Locate your WordPress folder on the file system.
2. Extract the contents of `websuite-push-notifier.zip` into `wp-content/plugins`
3. In `wp-content/plugins` you should now see a directory named `websuite-push-notifier`
4. Login to the WordPress admin panel at `http://yoursite.com/wp-admin`
5. Go to the Plugins menu.
6. Click Activate for the plugin.
7. Go to the WebSuite Push Notifier settings page.
8. You are all done!

== Frequently Asked Questions ==

== Screenshots ==

1. The settings page where your AWS details need to be entered.
2. The plugin page where you can enter a custom message or select a post for sending.
3. The logs page where successful and failed messages will be recorded.
4. The Send button on the posts list where you can send a post as a push notification.
5. The customised message sidebar widget on a post where you can send the post with a custom title and message.

== Changelog ==

= 1.1.7 =

* Add additional capabilities to the FCM push functions.

= 1.1.6 =

* Add additional capabilities to the FCM push functions.

= 1.1.5 =

* Fix FCM Send and topic editing on php 7.4

= 1.1.3 =

* Added Firebase Cloud Messaging (FCM) support.
* Tested with WordPress 6.2.2
* Tested with PHP 8.1

= 1.1.2 =

* Fix standalone messages being escaped multiple times.

= 1.0.10 =

* Fixed messageIcon sending through as NULL instead of an empty string.

= 1.0.9 =

* Fixed issue with scripts conflicting with other plugins.
* Checked compatibility with WordPress 5.9.1.

= 1.0.8 =

* Allow for WordPress proxy.
* Remove messageId from payload if the notification is a non-article.

= 1.0.7 =
* Added notice on the Send Notification page.
* Fixed parse issue when post_content was not plain text.
* Changed default order of logs.

= 1.0.6 =
* Fixed malformed object for GCM.

= 1.0.5 =
* Changed dirname() calls to ABSPATH for wp-admin.
* Moved parse_str into relevant switch case.

= 1.0.4 =
* Fixed multi-platform object having issues for iOS.
* Fixed multi-site compatibility for the logs.

= 1.0.3 =
* Fixed multi-platform object to send in json.
* Combined the ARN and Topic ARN into one field.
* Added buttons to clear the logs (either by days or by number).
* Added CRON to clear old logs automatically according to users choices.
* Respect the specified 'max_logs' at any given time.

= 1.0.2 =
* Updated multi-platform object to support iOS.
* Fixed upgrade require.
* Fixed failed sent message.
* Send button margin fix.

= 1.0.1 =
* Sanitise, escape and validate data.
* Escape echoed variables.
* Remove unnecessary popup.

= 1.0.0 =
* Include AWS SDK.
* Add the settings page.
* Add the logs page.
* Add ability to send from the plugin page.
* Add ability to send from posts page.
* Add ability to send customised message from the single post page.

== Upgrade Notice ==
* Updated multi-platform object to support iOS.

== About Us ==

Publisher's Toolbox is a reputable, reliable and professional global innovator of digital products for the publishing, advertising, brand agency and sports industries.
Our mission is to provide affordable content and community creation tools for small and large teams, allowing them to compete with some of the world’s most prestigious media groups and brand agencies.

Our unique suite of digital products have helped traditional publishing, brand and broadcasting agencies establish sustainable content ecosystems.

== Features ==
* Send notifications via the plugin page, post list or from the post itself.
* Compatible with all popular browsers (Chrome, Firefox, Safari).
* Customise notification title and messaging.

== Support ==
Please message us on the Forum for support queries or to provide your feedback.
