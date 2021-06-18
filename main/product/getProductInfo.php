<?php
    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/product.php';
    
    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }
    $db = new Database();
    $product = new Product($db->connection());

    try{
        $res = $product->getInfo(post('product_id'));
        echo $res ? $res : 'failed';
    }catch(Exception $e){
        echo $e;
        http_response_code(418);
    }
    $product->dismiss();
?>