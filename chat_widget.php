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
            $gravatar_link = get_gravatar_url($current_user->user_email);
        ?>
        <div id="chatTopBar" class="rounded">
            <span><img src="<?php echo $gravatar_link;?>" width="23" height="23" />
				<span class="name"><?php echo $current_user->user_nicename;?></span><a href="" class="logoutButton rounded">0</a></span>
        </div>
        <div id="chatLineHolder"></div>

        <div id="chatUsers" class="rounded">
            <?php
                $blog_users = get_users( 'blog_id=1&orderby=nicename' );
                foreach($blog_users as $user){
                    if($user->user_nicename != $current_user->user_nicename)
                        print '<div class="user_thumb" title="'.$user->user_nicename.'" gravatar="'.get_gravatar_url($user->user_email).'"><img src="'.get_gravatar_url($user->user_email).'" width="30" height="30" onload="this.style.visibility=\'visible\'" /></div>';
                }
            ?>
            <p class="count"><a href="<?php print get_admin_url();?>/users.php"><?php print count($blog_users)-1; ?> Total Users</a></p>
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
