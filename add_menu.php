<?php
add_action( 'admin_menu', 'wp_gcm_chat_config_menu' );

function wp_gcm_chat_config_menu() {
add_options_page( 'WP GCM Chat Options', 'WP GCM Chat', 'manage_options', 'wpgcmchat', 'wp_gcm_chat_config_options' );
}

/** Step 3. */
function wp_gcm_chat_config_options() {
if ( !current_user_can( 'manage_options' ) )  {
wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

    include_once("config_options.php");

}