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
	    $res = $offer->sendFirstOffer(post('product_id'), 
	    					   post('final_price'), 
	    					   post('quantity'),
	    					   post('date_posted'),  
	    					   post('payment_method'),
	    					   post('buyer_id'), 
	    					   post('farmer_id'), 
	    					   post('offer_message'));
	    echo $res
	    	? $res
	    	: 'failed';
    }catch(Exception $e){
    	http_response_code(418);
    	echo $e;
    }

    $offer -> dismiss();
?>