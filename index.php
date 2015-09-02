<?php

/**
 * Plugin Name: WP GCM Chat
 * Plugin URI: http://github.com/nickhargreaves/wp_gcm_chat
 * Description: A Wordpress GCM based chat plugin with a dashboard chat widget.
 * Version: 1.0.0
 * Author: Nick Hargreaves
 * Author URI: http://nickhargreaves.com
 * License: GPL2
 */

require_once('functions.php');
require_once('message_post_type.php');
require_once('feedback_notification.php');
require_once('add_menu.php');
require_once('chat_widget.php');

/*
 * Load assets
 */
function wp_gcm_chat_assets_style() {
    print '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
    wp_enqueue_style('wp-gcm-chat-assets-main', plugin_dir_url( __FILE__ ) . 'assets/css/chat.css');
    wp_enqueue_style('wp-gcm-chat-assets-scroll', plugin_dir_url( __FILE__ ) . 'assets/js/jScrollPane/jScrollPane.css');
    wp_enqueue_script( 'wp-gcm-chat-assets-mousewheeljs', plugin_dir_url( __FILE__ ) . 'assets/js/jScrollPane/jquery.mousewheel.js', array(), '1.0.0', true );
    wp_enqueue_script( 'wp-gcm-chat-assets-scrolljs', plugin_dir_url( __FILE__ ) . 'assets/js/jScrollPane/jScrollPane.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'wp-gcm-chat-assets-mainjs', plugin_dir_url( __FILE__ ) . 'assets/js/script.js', array(), '1.0.0', true );
}
add_action('admin_enqueue_scripts', 'wp_gcm_chat_assets_style');