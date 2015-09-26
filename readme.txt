=== WP Flat Admin ===
Contributors: nickhargreaves
Tags: chat, gcm chat, android chat, notifications, send notifications, new post notification
Requires at least: 3.0.1
Tested up to: 4.3.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A GCM based messaging & notifications plugin for WordPress for the interaction between users. This can is meant to enable chat between GCM enabled devices and a dashboard widget.

== Description ==
A GCM based messaging & notifications plugin for WordPress for the interaction between users. This can is meant to enable chat between GCM enabled devices and a dashboard widget.

It is completely Open Source and still in early stages of development so feel free to contribute to the code on [Github](https://github.com/nickhargreaves/WP_GCM_Chat/ "WP GCM Chat on Github")

You can also contribute by reporting issues and making feature requests [here](http://github.com/nickhargreaves/WP_GCM_Chat/issues, "Report issues")

== Installation ==

1. Download the plugin to your wp-content/plugins folder

2. Activate the plugin on your Wordpress admin dashboard

3. Activate Android Cloud Messaging on Google Console

    Find out how to do that [here](http://stackoverflow.com/questions/19866623/getting-an-api-key-to-use-with-google-cloud-messaging)

4. Add your Google API Key

    Go to your admin menu > WP GCM Chat then paste your API Key

5. Install the JSON-API plugin if you don't already have it.

6. Activate the GCM controller

    Go to your admin menu > JSON-API then activate WP GCM

You are now ready to start using your Wordpress dashboard for chat between users or with a GCM enabled app.

Note: The app must have the project ID configured to the project ID you got from step 3 above.

## Integration

Applying notifications in your other plugins is super easy. Simply use it as follows:


```
$reg_ids = array("user_id1", "user_id2");
$message = array("key" => "value");

send_push_notification($reg_ids, $message);
```

== Screenshots ==

1. Main wdiget

2. Single Chat

3. App

== Changelog ==

= 1.0 =
* Version 1
