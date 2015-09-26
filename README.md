# WP_GCM_Chat
A GCM based messaging & notifications plugin for WordPress for the interaction between users. This can is meant to enable chat between GCM enabled devices and dashboard.

# Installation

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
