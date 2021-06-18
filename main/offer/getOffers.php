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
    	$res = $offer->getOffers(post('order_id'));
    	echo $res 
    		? $res
    		: 'failed';
    }catch(Exception $e){
    	echo $e;
  		http_response_code(418);
    }


    $offer->dismiss();
?>