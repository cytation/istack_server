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

	$buyer = 0;
	$farmer = 1;
	$courrier = 2;

	try{
		$db = new Database();
		$order = new Order($db->connection());

		$user_type = post('user_type');
		$id = post('id');

		$res = $order->getHistory($user_type, $id);

		echo $res
			? $res
			: 'empty';

	}catch(Exception $e){
		echo $e;
		http_response_code(418);
	}
	
	$order->dismiss();

?>