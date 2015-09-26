<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 02/09/15
 * Time: 20:49
 */

/*
 * Create Message Content Type
 */
function register_message() {
    $labels = array(
        'name'               => _x( 'Messages', 'post type general name' ),
        'singular_name'      => _x( 'Message', 'post type singular name' ),
        'add_new'            => _x( 'Compose New', 'message' ),
        'add_new_item'       => __( 'Compose New Message' ),
        'new_item'           => __( 'New Message' ),
        'all_items'          => __( 'All Messages' ),
        'view_item'          => __( 'View Messages' ),
        'search_items'       => __( 'Search Messages' ),
        'not_found'          => __( 'No messages found' ),
        'not_found_in_trash' => __( 'No messages found in the Trash' ),
        'parent_item_colon'  => '',
        'menu_name'          => 'Messages'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Defines message structure',
        'public'        => true,
        'menu_position' => 6,
        'supports'      => array( 'title', 'custom-fields'),
        'has_archive'   => false,
    );
    register_post_type( 'message', $args );
}
add_action( 'init', 'register_message' );


//Recipient meta box
add_action( 'add_meta_boxes', 'recipient_box' );
function recipient_box() {
    add_meta_box(
        'recipient_box',
        __( 'Choose Message Recipient', 'myplugin_textdomain' ),
        'recipient_box_content',
        'message',
        'side',
        'high'
    );
}

function recipient_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'recipient_box_content_nonce' );
    $recipient = get_post_meta( get_the_ID(), 'recipient', true);
    if(empty($recipient)){
        $recipient = array();
    }

    //list of users in drop down
    $blogusers = get_users( 'blog_id=1&orderby=nicename' );
    print '<select name="recipient">';
    foreach ( $blogusers as $user ) {

        print '<option value="' . $user->ID . '">' . esc_html($user->user_login) . '</option>';

    }
    print '</select>';
    ?>
    <?php
}

add_action( 'save_post', 'recipient_box_save' );

function recipient_box_save( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( !wp_verify_nonce( $_POST['recipient_box_content_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }
    $recipient= $_POST['recipient'];
    update_post_meta( $post_id, 'recipient', $recipient );

    $notified = get_post_meta( $post_id, 'notified' );

    if(empty($notified)){
        update_post_meta( $post_id, 'notified', "1" );

        $pushMessage = get_the_title($post_id);

        $reg_ids = users_gcm_ids($recipient);
        $message = array("chat" => $pushMessage, "user"=>"admin");
        send_push_notification($reg_ids, $message);
    }
}