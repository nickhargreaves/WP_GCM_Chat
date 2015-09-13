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
                        'meta_value'       => $current_user->user_nickname,
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
                        'author'	   => $current_user->user_nickname,
                        'post_status'      => 'draft',
                        'suppress_filters' => true
                    );
                    $messages_author = get_posts($args);

                    //now combine both
                    $messages = array_merge($messages_recipient, $messages_author);
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
                                print '<tr class="user_row" title="'.$display_name.'"  gravatar="'.$display_gravatar.'"'.$display_name.'">
                                <th scope="row"><img class="user_row" src="' . $display_gravatar . '"></th>
                                <td><span class="inbox_name">' . $display_name . '</span></br>
                                <span class="inbox_text">' . $message->post_title . '</span>
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