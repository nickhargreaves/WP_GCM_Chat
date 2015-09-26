<?php
require(realpath(dirname(__FILE__)) . '/../../../../wp-blog-header.php');

send_message($_POST['author'], $message, $_POST['recipient']);

?>