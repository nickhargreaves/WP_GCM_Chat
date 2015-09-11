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
            <span><img id="user_thumb" src="<?php echo $gravatar_link;?>" width="23" height="23" />
				<span class="name"><?php echo $current_user->user_nicename;?></span><img id="inbox-button" src="<?php echo plugins_url( 'assets/img/inbox.png', __FILE__ );?>"></span>
        </div>
        <div id="chatRecipient"></div>
        <div id="chatLineHolder"></div>
        <div id="inbox">
            <table cellspacing="0">
                <?php
                /*
                 * show latest messages
                 */
                    //Get where current user is recipient

                    $args = array(
                        'posts_per_page'   => 500000,
                        'offset'           => 0,
                        'category'         => '',
                        'category_name'    => '',
                        'orderby'          => 'date',
                        'order'            => 'DESC',
                        'include'          => '',
                        'exclude'          => '',
                        'meta_key'         => 'recipient',
                        'meta_value'       => $current_user->user_nicename,
                        'post_type'        => 'message',
                        'post_mime_type'   => '',
                        'post_parent'      => '',
                        'author'	   => '',
                        'post_status'      => 'draft',
                        'suppress_filters' => true
                    );
                    $messages_recipient = get_posts($args);

                    //Get where current user is author
                    $args = array(
                        'posts_per_page'   => 500000,
                        'offset'           => 0,
                        'category'         => '',
                        'category_name'    => '',
                        'orderby'          => 'date',
                        'order'            => 'DESC',
                        'include'          => '',
                        'exclude'          => '',
                        'post_type'        => 'message',
                        'post_mime_type'   => '',
                        'post_parent'      => '',
                        'author'	   => $current_user->user_nicename,
                        'post_status'      => 'draft',
                        'suppress_filters' => true
                    );
                    $messages_author = get_posts($args);

                    //now combine both
                    $messages = $messages_recipient + $messages_author;
                    //sort by latest
                    $arr = array();
                    foreach ($messages as $key => $row)
                    {
                        $arr[$key] = $row->ID;
                    }
                    array_multisort($arr, SORT_DESC, $messages);

                    //add to array by id, user and ignore if already added
                    $displayed_messages = array();
                    $displayed_users = array();

                    foreach($messages as $message){

                        //check if id and user not already added
                        if(!in_array($message->ID, $displayed_messages)) {
                            $author = get_userdata($message->post_author);

                            if($current_user->nickname != $author->nickname){
                                $display_gravatar = get_gravatar_url($author->user_email);
                                $display_name = $author->nickname;
                            }else{
                                //current user is author, so get recipient instead
                                $recipient_nickname = get_post_meta($message->ID, 'recipient', true);
                                $recipient = get_user_by('login', $recipient_nickname);
                                $display_gravatar = get_gravatar_url($recipient->user_email);
                                $display_name = $recipient->nickname;
                            }
                            if(!in_array($display_name, $displayed_users)){
                                print '<tr>
                                <th scope="row"><img src="' . $display_gravatar . '"></th>
                                <td>' . $display_name . '</td>
                                <td>' . $message->post_title . '</td>
                                </tr>';
                                //add this to array of displayed messages
                                $displayed_messages[] = $message->ID;
                                //add user to array of displayed
                                $displayed_users[] = $display_name;
                            }
                        }
                    }
                ?>
                </table>
            </div>

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

            <div id="chatForm" author_id="<?php echo $current_user->ID;?>" author="<?php echo $current_user->user_nicename;?>" gravatar="<?php echo $gravatar_link;?>">
                <input id="chatText" name="chatText" class="rounded" maxlength="255" />
                <input id="submitChat" type="submit" class="blueButton" value="Send" />
            </div>

        </div>

    </div>
    <?php

}
