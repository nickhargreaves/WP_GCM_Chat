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

//get recipients messages
$messages_from_recipient = get_user_messages($recipient, $recipient);

//loop through messages

if(count($messages_from_recipient)>0){
    $first = $messages_from_recipient[0];

    if($last_message_from_recipient < $first->ID){
        //only loop if first returned has greater id

        foreach($messages_from_recipient as $message){
            if($message->ID > $last_message_from_recipient){
                print $message->post_title;
            }
        }

    }
}
