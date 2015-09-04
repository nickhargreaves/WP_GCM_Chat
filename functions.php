<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 02/09/15
 * Time: 20:52
 */
/*
 * Get all users GCM ID
 */

function users_gcm_ids($user_id=null){
    $ids = array();

    if($user_id == null) {
        $blog_users = get_users(array());
        foreach ($blog_users as $user) {
            $user_gcm_id = get_user_meta($user->ID, 'gcm_id', true);
            if (!empty($user_gcm_id)) {
                $ids[] = $user_gcm_id;
            }
        }
    }else{
        $user_gcm_id = get_user_meta($user_id, 'gcm_id', true);
        if (!empty($user_gcm_id)) {
            $ids[] = $user_gcm_id;
        }
    }

    return $ids;
}

/*
 * Send GCM Message
 * TODO: create new post 'message'
 */
function send_push_notification($registration_ids, $message) {


    // Set POST variables
    $url = 'https://android.googleapis.com/gcm/send';

    $fields = array(
        'registration_ids' => $registration_ids,
        'data' => $message,
    );

    define("GOOGLE_API_KEY", get_option( 'wp_gcm_chat_google_api_key' ));

    $headers = array(
        'Authorization: key=' . GOOGLE_API_KEY,
        'Content-Type: application/json'
    );
    //print_r($headers);
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }

    // Close connection
    curl_close($ch);
}
/*
 * Get user gravatars for notifications
 */


function get_gravatar_url( $email ) {
    $hash = md5( strtolower( trim ( $email ) ) );
    return 'http://gravatar.com/avatar/' . $hash;
}

/*
 * Select user to begin chatting
 */

add_action( 'admin_footer', 'chat_action_javascript' ); // Write our JS below here

function chat_action_javascript() { ?>
    <script type="text/javascript" >

        jQuery(document).ready(function($) {
            jQuery("#chatBottomBar").hide();
            jQuery("#chatLineHolder").hide();
            jQuery("#chatRecipient").hide();
            jQuery("#inbox").show();

        });
        jQuery("#inbox-button").click(function(){
            jQuery("#chatBottomBar").hide();
            jQuery("#chatLineHolder").hide();
            jQuery("#chatRecipient").hide();
            jQuery("#inbox").show();
        });
        jQuery(".user_thumb").click(function(){
            //show user details
            var gravatar = jQuery(this).attr('gravatar');
            var username = jQuery(this).attr('title');

            jQuery('#chatRecipient').html("<img src='" +gravatar+"'>" + username);

            //show user chat history
            //show chat box
            jQuery("#inbox").hide();
            jQuery("#chatBottomBar").show();
            jQuery("#chatLineHolder").show();
            jQuery("#chatRecipient").show();
            //hide notification
        });
        jQuery('#submitChat').click(function() {

            var text = jQuery('#chatText').val();

            if (text.length == 0) {
                return false;
            }

            jQuery('#chatText').val("");

        // Assigning a temporary ID to the chat:
        var tempID = 't'+Math.round(Math.random()*1000000),
            params = {
                id			: tempID,
                text		: text.replace(/</g,'&lt;').replace(/>/g,'&gt;'),
                author      : jQuery("#chatForm").attr("author"),
                author_id      : jQuery("#chatForm").attr("author_id"),
                gravatar    : jQuery("#chatForm").attr("gravatar"),
                recipient   : jQuery(".user_thumb").attr("title")
            };

        // add the chat
        // to the screen immediately, without waiting for
        // the AJAX request to complete:
        // All times are displayed in the user's timezone

            var d = new Date();
            if(params.time) {

                // PHP returns the time in UTC (GMT). We use it to feed the date
                // object and later output it in the user's timezone. JavaScript
                // internally converts it for us.

                d.setUTCHours(params.time.hours,params.time.minutes);
            }

            params.time = (d.getHours() < 10 ? '0' : '' ) + d.getHours()+':'+
                (d.getMinutes() < 10 ? '0':'') + d.getMinutes();

            //var markup = chat.render('chatLine',params),
            var markup = '<div class="chat chat-' + params.id + ' rounded">' + '<span class="gravatar"><img src="'+params.gravatar+'" width="23" height="23" onload="this.style.visibility=\'visible\'" /></span><span class="author">' + params.author + ':</span><span class="text">' + params.text + '</span><span class="time">' + params.time + '</span></div>';

            var exists = jQuery('#chatLineHolder .chat-'+params.id);

            if(exists.length){
                exists.remove();
            }
            var chat = {

                // data holds variables for use in the class:

                data: {
                    lastID: 0,
                    noActivity: 0
                }
            }
            chat.data.jspAPI = jQuery('#chatLineHolder').jScrollPane({
                verticalDragMinHeight: 12,
                verticalDragMaxHeight: 12
            }).data('jsp');

            if(!chat.data.lastID){
                // If this is the first chat, remove the
                // paragraph saying there aren't any:

                jQuery('#chatLineHolder p').remove();
            }

            // If this isn't a temporary chat:
            if(params.id.toString().charAt(0) != 't'){
                var previous = jQuery('#chatLineHolder .chat-'+(+params.id - 1));
                if(previous.length){
                    previous.after(markup);
                }
                else chat.data.jspAPI.getContentPane().append(markup);
            }
            else chat.data.jspAPI.getContentPane().append(markup);

            // As we added new content, we need to
            // reinitialise the jScrollPane plugin:

            chat.data.jspAPI.reinitialise();
            chat.data.jspAPI.scrollToBottom(true);

            //send chat to user
            jQuery.post("<?php print plugins_url( 'create_message.php', __FILE__ );?>",
                {
                    author: params.author_id,
                    message: params.text,
                    recipient: params.recipient
                });

        });


    </script> <?php
}