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
        <?php
            $current_user = wp_get_current_user();
            $gravatar_link = get_gravatar_url($current_user->user_email)
        ?>
        <div id="chatTopBar" class="rounded">
            <span><img src="<?php echo $gravatar_link;?>" width="23" height="23" />
				<span class="name"><?php echo $current_user->user_nicename?></span><a href="" class="logoutButton rounded">0</a></span>
        </div>
        <div id="chatLineHolder"></div>

        <div id="chatUsers" class="rounded">
            <?php
            $blogusers = get_users( 'blog_id=1&orderby=nicename' );

            ?>
            <p class="count"><?php print count($blogusers); ?> Total Users</p>
        </div>
        <div id="chatBottomBar" class="rounded">
            <div class="tip"></div>

            <form id="loginForm" method="post" action="" style="display:none;">
                <input id="name" name="name" class="rounded" maxlength="16" />
                <input id="email" name="email" class="rounded" />
                <input type="submit" class="blueButton" value="Login" />
            </form>

            <form id="submitForm" method="post" action="">
                <input id="chatText" name="chatText" class="rounded" maxlength="255" />
                <input type="submit" class="blueButton" value="Send" />
            </form>

        </div>

    </div>
    <?php

}
