<?php
require(realpath(dirname(__FILE__)).'/../../../wp-blog-header.php');
?>
<table cellspacing="0">
                <?php
                /*
                 * show latest messages
                 */
                    //Get where current user is recipient
                $current_user = wp_get_current_user();

                //get all current user messages
                $messages = get_user_messages($current_user->ID);

                    //add to users array and ignore if already added
                    $displayed_users = array();



                    foreach($messages as $message){

                        $message_author = get_post_meta($message->ID, 'author', true);
                        $message_recipient = get_post_meta($message->ID, 'recipient', true);

                        $author = get_userdata($message_author);//message_author must equal message->post_author
                        $recipient = get_userdata($message_recipient);
                            if($current_user->ID != $message_author){
                                //current user is the recipient, show author avatar
                                $display_gravatar = get_gravatar_url($author->user_email);
                                $display_name = $author->user_login;
                                $display_id = $author->ID;
                            }else{
                                //current user is author, so get recipient instead
                                $display_gravatar = get_gravatar_url($recipient->user_email);
                                $display_name = $recipient->user_login;
                                $display_id = $recipient->ID;
                            }

                        //check if user is already added
                        if(!in_array($display_name, $displayed_users)){

                                print '<tr class="user_row" user_id="'.$display_id.'" title="'.$display_name.'"  gravatar="'.$display_gravatar.'">
                                <th scope="row"><img class="user_row" src="' . $display_gravatar . '"></th>
                                <td><span class="inbox_name">' . $display_name . '</span></br>
                                <span class="inbox_text">' . $message->post_title . '</span>
                                </tr>';

                                //add user to array of displayed
                                $displayed_users[] = $display_name;
                            }
                    }
                ?>
</table>