<?php

	include '../../config/database.php';
	include '../../config/apiauth.php';
	include '../../config/request.php';
	include '../../operation_classes/order.php';
    include '../../operation_classes/product.php';

	$api = new Api();
	if($api->notAuthorized()){
	    http_response_code(401);
	    return;
	}

	try{
		$db = new Database();
		$order = new Order($db->connection());
    	$product = new Product($db->connection());

		$ao = $order->countFarmerItems(post('farmer_id'));
		$ap = $product->countFarmerItems(post('farmer_id'));

		$o = $ao ? $ao["active_orders"] : "0";
		$p = $ap ? $ap["active_products"] : "0";

		$itemCount = array("active_products" => $p,
				   "active_orders" => $o);
		echo json_encode($itemCount);
	}catch(Exception $e){
		echo $e;
		http_response_code(418);
	}
	
	$db->connection()->close();
?>