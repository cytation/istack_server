<?php
    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/address.php';

    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }
    
    $db = new Database();
    $address = new Address($db->connection());
    
    try{
        $res = $address->getProvinces();
        echo $res
            ? $res
            : 'failed';
    }catch(Exception $e){
        echo $e;
        http_response_code(418);
    }
    
    $address->dismiss();
?>