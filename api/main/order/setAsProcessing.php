<?php
	include '../../config/database.php';
	include '../../config/apiauth.php';
	include '../../config/request.php';
	include '../../operation_classes/order.php';
	

	$api = new Api();
	if($api->notAuthorized()){
	    http_response_code(401);
	    return;
	}

	$db = new Database();
	$order = new Order($db->connection());

	try{
		echo $order->setAsProcessing(post('order_id'))
			? 'success'
			: 'failed';
	}catch(Exception $e){
		echo $e;
		http_response_code(418);
	}

	$order->dismiss();
?>