<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 02/09/15
 * Time: 20:51
 */

/*
 * Send feedback after comment posted
 */

function send_feedback_after_comment($comment_id){

    $comment = get_comment($comment_id);

    $author_uname = $comment->comment_author;
    $raw_message = $comment->comment_content;

    //get author of original post
    $post_id = $comment->comment_post_ID;
    $post = get_post($post_id);
    $author_id = $post->post_author;

    //get comment author gravatar
    $author = get_user_by('login', $author_uname);
    $author_email = $author->user_email;
    $gravatar = get_gravatar_url($author_email);

    $message = array("feedback" => $raw_message, "author"=>$author_uname, "icon_url"=>$gravatar);
    send_push_notification(users_gcm_ids($author_id), $message);
}
add_action('comment_post', 'send_feedback_after_comment', 10, 3);
