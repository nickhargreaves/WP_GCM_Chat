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
            <span>
                <img id="user_thumb" src="<?php echo $gravatar_link;?>" width="23" height="23" />
				<span class="name">
                    <?php echo $current_user->user_nicename;?>
                </span>

                <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
                <script src="//code.jquery.com/jquery-1.10.2.js"></script>
                <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

                <img id="users-button" src="<?php echo plugins_url( 'assets/img/users.png', __FILE__ );?>">
                <div id="users_dialog" style="display: none;">

                    <div id="chatUsers" class="rounded">
                        <?php
                            $blog_users = get_users( 'blog_id=1&orderby=nicename' );

                            foreach($blog_users as $user){
                                if($user->user_nicename != $current_user->user_nicename)
                                    print '<div class="user_thumb" user_id="'.$user->ID.'" title="'.$user->user_nicename.'" gravatar="'.get_gravatar_url($user->user_email).'"><img src="'.get_gravatar_url($user->user_email).'" width="30" height="30" onload="this.style.visibility=\'visible\'" /></div>';
                            }
                        ?>
                        <p class="count"><a href="<?php print get_admin_url();?>/users.php"><?php print count($blog_users)-1; ?> Total Users</a></p>
                    </div>

                </div>

                <img id="inbox-button" src="<?php echo plugins_url( 'assets/img/inbox.png', __FILE__ );?>">
            </span>
        </div>

                    <div id="chatRecipient"></div>

                    <div id="chatLineHolderContainer">
                        <div id="chatLineHolder"></div>
                    </div>

                    <div id="inbox">

                    </div>


        <div id="chatBottomBar" class="rounded">
            <div class="tip"></div>

            <div id="chatForm" author_id="<?php echo $current_user->ID;?>" author="<?php echo $current_user->user_nicename;?>" gravatar="<?php echo $gravatar_link;?>">
                <input id="chatText" name="chatText" class="rounded" maxlength="255" />
                <input id="submitChat" type="submit" class="blueButton" value="Send" />
            </div>

        </div>

    </div>
    <?php

}
