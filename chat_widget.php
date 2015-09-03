<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 02/09/15
 * Time: 21:13
 */

function wp_gcm_chat_add_dashboard_widgets() {

    wp_add_dashboard_widget(
        'wp_gcm_chat_dashboard_widget',         // Widget slug.
        'Messaging',         // Title.
        'wp_gcm_chat_dashboard_widget_function' // Display function.
    );
}
add_action( 'wp_dashboard_setup', 'wp_gcm_chat_add_dashboard_widgets' );


function wp_gcm_chat_dashboard_widget_function() {

    ?>
    <div id="chatContainer">

        <div id="chatTopBar" class="rounded"></div>
        <div id="chatLineHolder"></div>

        <div id="chatUsers" class="rounded"></div>
        <div id="chatBottomBar" class="rounded">
            <div class="tip"></div>

            <form id="loginForm" method="post" action="" style="display:none;">
                <input id="name" name="name" class="rounded" maxlength="16" />
                <input id="email" name="email" class="rounded" />
                <input type="submit" class="blueButton" value="Login" />
            </form>

            <form id="submitForm" method="post" action="">
                <input id="chatText" name="chatText" class="rounded" maxlength="255" />
                <input type="submit" class="blueButton" value="Submit" />
            </form>

        </div>

    </div>
    <?php

}
