<?php
// Add a custom controller
add_filter('json_api_controllers', 'add_gcm_controller');

function add_gcm_controller($controllers) {
    // Corresponds to the class JSON_API_CR_Controller
    $controllers[] = 'GCM';
    return $controllers;
}

//set path for custom controller
function set_gcm_controller_path() {
    return  plugin_dir_path(  dirname( __FILE__ )  ) . "json-api-extend/json-api-gcm-controller.php";
}
add_filter('json_api_gcm_controller_path', 'set_gcm_controller_path');


?>