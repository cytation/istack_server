<?php 
    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/store.php';

    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }

    $db = new Database();
    $store = new Store($db->connection());
    
    try{
        $farmer_id = post('farmer_id');
        $res = $store->info($farmer_id);
        echo $res ? $res : 'empty';
    }catch(Exception $e){
       echo $e;
       http_response_code(418);
    }
    
    $store->dismiss();
?>