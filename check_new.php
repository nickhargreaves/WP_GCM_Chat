<?php
require(realpath(dirname(__FILE__)).'/../../../wp-blog-header.php');

/**
 * Created by PhpStorm.
 *
 * Check for new messages for author from recipient
 *
 * User: nick
 * Date: 17/09/15
 * Time: 14:49
 */

$author = $_GET['author'];

$recipient = $_GET['recipient'];

$last_message_from_recipient = $_GET['last_message'];

//get recipient details
$user = get_user_by('id', $recipient);
$display_gravatar = get_gravatar_url($user->user_email);
$display_name = $user->user_login;

//get recipients messages
$messages_from_recipient = get_user_messages($recipient, $recipient);

//array to store result
$lines = array();
$new_last_message_id = 0;
//loop through messages
if(count($messages_from_recipient)>0){
    $first = $messages_from_recipient[0];

    if($last_message_from_recipient < $first->ID){
        //only loop if first returned has greater id

        foreach($messages_from_recipient as $message){
            if($message->ID > $last_message_from_recipient){

                $lines[] = '<div class="chat chat-'.$message->ID.' rounded">
                            <span class="gravatar"><img src="'.$display_gravatar.'" width="23" height="23" onload="this.style.visibility=\'visible\'" /></span>
                            <span class="author">'.$display_name.':</span><span class="text">' . $message->post_title .'</span>
                            <span class="time">'.$message->post_date.'</span></div>';

                $new_last_message_id = $message->ID;
            }
        }

    }
}

$result = array("messages" => $lines, "new_last_message_id" => $new_last_message_id);
print json_encode($result);
