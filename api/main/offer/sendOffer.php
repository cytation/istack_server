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
        echo $offer -> sendOffer(post('product_id'), post('final_price'),
                        post('quantity'), post('date_posted'), post('order_id'),
                        post('sender_id'), post('receiver_id'), post('offer_message'))
            ? 'success'
            : 'failed';
    }catch(Exception $e){
        echo $e;
    }

    $offer->dismiss();
?>