<?php
    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/user.php';
    
    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }
    
    $db = new Database();
    $user = new User($db->connection());


    try{
        $res = $user->getInfo(post('id'));
        echo $res ? $res : 'empty';
    }catch(Exception $e){
        echo $e;
        http_response_code(418);
    }

    $user->dismiss();

?>