<?php

    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/deliveryInfo.php';


    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }
    
    $db = new Database();
    $delInfo = new DeliveryInfo($db->connection());

    try{
        $res = $delInfo->get(post('buyer_id'));
        echo $res ? $res : 'failed';
    }catch(Exception $e){
        echo $e;
        http_response_code(418);
    }

    $delInfo->dismiss();
?>