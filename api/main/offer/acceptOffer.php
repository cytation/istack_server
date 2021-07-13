<?php

    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/offer.php';


    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }
    
    $db = new Database();
    $offer = new Offer($db->connection());

    try{
        echo $offer->acceptOffer(post('order_id'), 
                                 post('quantity'),
                                 post('final_price'),
                                 post('product_id'))
            ? 'success'
            : 'failed';
    }catch(Exception $e){
        http_response_code(418);
        echo($e);
    }

    $offer->dismiss();
?>