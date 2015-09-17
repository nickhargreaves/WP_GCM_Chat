<?php
require(realpath(dirname(__FILE__)).'/../../../wp-blog-header.php');
?>

<table cellspacing="0">
                <?php
                /*
                 * show latest messages
                 */
                $user_id = $_POST['user_id'];
                
                $current_user = wp_get_current_user();

                //get all current user messages
                $messages = get_user_messages($current_user->ID);

                //reverse order to show latest at the bottom
                $messages = array_reverse($messages);

                $displayed_messages = 0;
                foreach($messages as $message){

                    /*
                     * Loop through messages
                     * Check if current user is author or recipient
                     * Check if requested user is author or recipient
                     */

                    if($displayed_messages < 25) {
                        $message_author = get_post_meta($message->ID, 'author', true);
                        $message_recipient = get_post_meta($message->ID, 'recipient', true);

                        if(($message_recipient == $user_id)||($message_author == $user_id )){
                            //show message
                            $author = get_userdata($message->post_author);

                            $display_gravatar = get_gravatar_url($author->user_email);
                            $display_name = $author->nickname;

                            print '<div class="chat chat-'.$message->ID.' rounded">
                            <span class="gravatar"><img src="'.$display_gravatar.'" width="23" height="23" onload="this.style.visibility=\'visible\'" /></span>
                            <span class="author">'.$display_name.':</span><span class="text">' . $message->post_title .'</span>
                            <span class="time">'.$message->post_date.'</span></div>';

                            $displayed_messages++;
                        }

                    }else{
                        //we don't want to go through all messages
                        break;
                    }

                }


                foreach($displayed_messages as $message){

                }
                ?>
</table>