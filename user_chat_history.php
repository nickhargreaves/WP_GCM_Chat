<?php
require(realpath(dirname(__FILE__)).'/../../../wp-blog-header.php');
?>

<table cellspacing="0">
                <?php
                /*
                 * show latest messages
                 */
                    //Get where current user is recipient
                $user = $_POST['username'];
                
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
                        'meta_value'       => $current_user->user_nicename,
                        'post_type'        => 'message',
                        'post_mime_type'   => '',
                        'post_parent'      => '',
                        'author'	   => $user,
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
                        'meta_key'         => 'recipient',
                        'meta_value'       => $user,
                        'post_type'        => 'message',
                        'post_mime_type'   => '',
                        'post_parent'      => '',
                        'author'	   => $current_user->user_nicename,
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

                    foreach($messages as $message){

                        //check if id and user not already added
                        if(!in_array($message->ID, $displayed_messages)) {
                            $author = get_userdata($message->post_author);

                            $display_gravatar = get_gravatar_url($author->user_email);
                            $display_name = $author->nickname;


                                print '<div class="chat chat-'.$message->ID.' rounded">
                                    <span class="gravatar"><img src="'.$display_gravatar.'" width="23" height="23" onload="this.style.visibility=\'visible\'" /></span>
                                    <span class="author">'.$display_name.':</span><span class="text">' . $message->post_title .'</span>
                                    <span class="time">'.$message->post_date.'</span></div>';

                                //add this to array of displayed messages
                                $displayed_messages[] = $message->ID;

                        }
                    }
                ?>
</table>