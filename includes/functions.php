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

function send_message($user_id, $message, $recipient){

    $post_id = wp_insert_post(
        array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_author' => $user_id,
            'post_title' => $message,
            'post_status' => 'draft',
            'post_type' => 'message'
        )
    );

    update_post_meta( $post_id, 'recipient', $recipient);
    update_post_meta( $post_id, 'author', $user_id);


    //send gcm notification
    $user = get_user_by('id', $user_id);

    $gcm = array("chat" => $message, "user"=>$user->user_login);

    $reg_id = users_gcm_ids($recipient);

    send_push_notification($reg_id, $gcm);;
}



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

/**
 * Get specified user's messages
 * @param the user id
 * @return user's messages
 */

function get_user_messages($user_id, $author = null){

    //Get messages where current user is recipient
    $args = array(
        'numberposts'      => -1,
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
        'post_status'      => 'draft',
        'suppress_filters' => true
    );

    $messages = get_posts($args);

    $displayed_messages = array();


    foreach($messages as $message){

        /*
         * Loop through messages
         * Check if current user is author or recipient
         */

        $message_author = get_post_meta($message->ID, 'author', true);
        $message_recipient = get_post_meta($message->ID, 'recipient', true);

        if(($user_id == $message_recipient)||($message_author == $user_id)) {
            if($author == null){
                //add to messages
                $displayed_messages[] = $message;
            }else{
                if($message_author == $author){
                    //add to messages
                    $displayed_messages[] = $message;
                }
            }
        }

    }

    return $displayed_messages;
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

        var checkNewMessagesTimeout;

        function load_user_chat(gravatar, username, user_id){
            jQuery('#chatRecipient').html("<img src='" +gravatar+"'>" + username);
            jQuery('#chatRecipient').attr("title", username);
            jQuery('#chatRecipient').attr("user_id", user_id);

            //show user chat history
            jQuery.post("<?php print plugins_url( 'user_chat_history.php', __FILE__ );?>",
                {
                    username: username,
                    user_id: user_id
                })
                .done(function( data ) {

                    jQuery("#chatLineHolderContainer").html('<div id="chatLineHolder"></div>');

                    jQuery("#chatLineHolder").html(data);

                    chat.data.lastID = 0;

                    chat.data.noActivity = 0;

                    chat.data.jspAPI = jQuery('#chatLineHolder').jScrollPane({
                        verticalDragMinHeight: 12,
                        verticalDragMaxHeight: 12
                    }).data('jsp');

                    chat.data.jspAPI.scrollToBottom(true);

                    // schedule the first invocation to keep checking for new messages
                    checkNewMessagesTimeout = setTimeout(checkNewMessages, 5000);

                });

            //show chat box
            jQuery("#inbox").hide();
            jQuery("#chatBottomBar").show();
            jQuery("#chatLineHolder").show();
            jQuery("#chatRecipient").show();
            //update notification
        }
        jQuery.post("<?php print plugins_url( 'load_messages.php', __FILE__ );?>")
            .done(function( data ) {
                jQuery("#inbox").html(data);
            });

        jQuery(document).ready(function($) {
            jQuery("#chatBottomBar").hide();
            jQuery("#chatLineHolder").hide();
            jQuery("#chatRecipient").hide();
            jQuery("#inbox").show();

        });
        jQuery(document).on("click", ".user_row", function(e){

            var gravatar = jQuery(this).attr('gravatar');
            var username = jQuery(this).attr('title');
            var user_id = jQuery(this).attr('user_id');

            load_user_chat(gravatar, username, user_id);
        });
        jQuery(".inbox_button").click(function(){

            jQuery("#chatBottomBar").hide();
            jQuery("#chatLineHolder").hide();
            jQuery("#chatRecipient").hide();
            jQuery("#inbox").show();

            //reload messages
            jQuery.post("<?php print plugins_url( 'load_messages.php', __FILE__ );?>")
                .done(function( data ) {
                    jQuery("#inbox").html(data);
                });
        });
        jQuery("#inbox-button").click(function(){

            //Stop refreshing chat when user toggles to inbox
            clearTimeout(checkNewMessagesTimeout);

            jQuery("#chatBottomBar").hide();
            jQuery("#chatLineHolder").hide();
            jQuery("#chatRecipient").hide();
            jQuery("#inbox").show();

            //reload messages
            jQuery.post("<?php print plugins_url( 'load_messages.php', __FILE__ );?>")
                .done(function( data ) {
                    jQuery("#inbox").html(data);
                });
        });
        jQuery(".user_thumb").click(function(){
            var gravatar = jQuery(this).attr('gravatar');
            var username = jQuery(this).attr('title');
            var user_id = jQuery(this).attr('user_id');
            jQuery( "#users_dialog" ).dialog("close");
            load_user_chat(gravatar, username, user_id);

        });

        //show users dialog
        jQuery("#users-button").click(function(){
            jQuery( "#users_dialog" ).dialog();
        });

        var chat = {

            // data holds variables for use in the class:

            data: {
                lastID: 0,
                noActivity: 0,
                jspAPi: null
            }
        };

        $('#chatText').keypress(function (e) {
            if (e.which == 13) {
                jQuery('#submitChat').click();
                return false;
            }
        });
        //periodic function to load new chats for recipient from user
        /**
         * periodic function to load new chats for recipient from specific user
         * @param: author, user_id
         */

        function checkNewMessages() {
            var recipient = jQuery('#chatRecipient').attr("user_id");
            var author = jQuery("#chatForm").attr("author_id");
            var last_message = jQuery("#chatForm").attr("last_message");

            jQuery.ajax({
                    url: "<?php print plugins_url( 'check_new.php', __FILE__ );?>" + "?author=" + author + "&recipient=" + recipient + "&last_message=" + last_message,
                    dataType: "json",
                success: function(data) {

                    var new_messages = data['messages'];
                    var new_last_message_id = data['new_last_message_id'];

                    if(new_messages.length > 0){
                        for(var i=0; i<new_messages.length; i++){
                            //loop through result and append html
                            chat.data.jspAPI.getContentPane().append(new_messages);
                            //and scroll to bottom
                            chat.data.jspAPI.reinitialise();
                            chat.data.jspAPI.scrollToBottom(true);
                        }

                        //update last message
                        jQuery('#chatForm').attr('last_message', new_last_message_id);
                    }

            },
            complete: function() {
                // schedule the next request *only* when the current one is complete:
                checkNewMessagesTimeout = setTimeout(checkNewMessages, 5000);
            }
        });
        }

        //on submit chat
        jQuery('#submitChat').click(function () {


            var text = jQuery('#chatText').val();

            if (text.length == 0) {
                return false;
            }

            jQuery('#chatText').val("");

            // Assigning a temporary ID to the chat:
            var tempID = 't' + Math.round(Math.random() * 1000000),
                params = {
                    id: tempID,
                    text: text.replace(/</g, '&lt;').replace(/>/g, '&gt;'),
                    author: jQuery("#chatForm").attr("author"),
                    author_id: jQuery("#chatForm").attr("author_id"),
                    gravatar: jQuery("#chatForm").attr("gravatar"),
                    recipient: jQuery("#chatRecipient").attr("user_id")
                };

            // add the chat
            // to the screen immediately, without waiting for
            // the AJAX request to complete:
            // All times are displayed in the user's timezone

            var d = new Date();
            if (params.time) {

                // PHP returns the time in UTC (GMT). We use it to feed the date
                // object and later output it in the user's timezone. JavaScript
                // internally converts it for us.

                d.setUTCHours(params.time.hours, params.time.minutes);
            }

            params.time = (d.getHours() < 10 ? '0' : '' ) + d.getHours() + ':' +
                (d.getMinutes() < 10 ? '0' : '') + d.getMinutes();

            //var markup = chat.render('chatLine',params),
            var markup = '<div class="chat chat-' + params.id + ' rounded">' + '<span class="gravatar"><img src="' + params.gravatar + '" width="23" height="23" onload="this.style.visibility=\'visible\'" /></span><span class="author">' + params.author + ':</span><span class="text">' + params.text + '</span><span class="time">' + params.time + '</span></div>';

            var exists = jQuery('#chatLineHolder .chat-' + params.id);

            if (exists.length) {
                exists.remove();
            }


            if (!chat.data.lastID) {
                // If this is the first chat, remove the
                // paragraph saying there aren't any:

                jQuery('#chatLineHolder p').remove();
            }

            // If this isn't a temporary chat:
            if (params.id.toString().charAt(0) != 't') {
                 var previous = jQuery('#chatLineHolder .chat-' + (+params.id - 1));

                if (previous.length) {
                    previous.after(markup);
                 }else{
                     chat.data.jspAPI.getContentPane().append(markup);
                 }
             }else{
                chat.data.jspAPI.getContentPane().append(markup);
            }

            // As we added new content, we need to
            // reinitialise the jScrollPane plugin:

            chat.data.jspAPI.reinitialise();
            chat.data.jspAPI.scrollToBottom(true);

            //send chat to user
            jQuery.post("<?php print plugins_url( 'create_message.php', __FILE__ );?>",
                {
                    author: params.author_id,
                    username: params.author,
                    message: params.text,
                    recipient: params.recipient
                });

        });


    </script> <?php
}