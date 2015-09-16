<?php
require(realpath(dirname(__FILE__)).'/../../../wp-blog-header.php');

$post_id = wp_insert_post(
    array(
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_author' => $_POST['author'],
        'post_title' => $_POST['message'],
        'post_status' => 'draft',
        'post_type' => 'message'
    )
);

update_post_meta( $post_id, 'recipient', $_POST['recipient']);

echo "blab";
?>