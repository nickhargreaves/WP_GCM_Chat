<?php
/*
Controller name: GCM
Controller description:Additional functionaility to the JSON_API controller for the GCM Chat app integration
*/
class JSON_API_GCM_Controller {


    public function edit_user_device(){
        $username = $_POST['username'];
        $user = get_user_by( "login", $username );
        $user_id = $user->ID;
        $device_id = $_POST['regId'];

        update_user_meta($user_id, 'gcm_id', $device_id);

        return array("result"=>"OK", "message"=>"Device registered successfully!");

    }


    public function send_message(){

        $recipient = 1;
        if(isset($_POST['recipient']))
            $recipient = $_POST['recipient'];

        if(isset($_POST['author'])){
            $author = $_POST['author'];
        }else{
            //if sent username instead
            if(isset($_POST['username'])){

                $author_data = get_user_by('login', $_POST['username']);
                $author = $author_data->ID;

            }else{
                return array("result"=>"NOK", "message"=>"Missing parameters");
            }
        }

        send_message($author, $_POST['message_string'], $recipient);

        return array("result"=>"OK", "message"=>"Message sent!");
    }

}