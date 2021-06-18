<?php
    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/auth.php';
    
    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }
    $db = new Database();
    $auth = new Auth($db->connection());
    
    try{
        $password = post('password');
        $mobile = post('mobile');
        $res = $auth->login($mobile, $password);
        echo $res
            ? $res
            : 'failed';
    }catch(Exception $e){
        echo $e;
        http_response_code(418);
    }
    
    $auth->dismiss();
?>