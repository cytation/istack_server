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
    
    $farmer_id = post('farmer_id');       //string
    $name = post('name');                 //string
    $description = post('description');   //string
    $category = post('category');         //int
    $price = post('price');               //double
    $unit = post('unit');                 //string
    $in_stock = post('in_stock');         //double
    $date_posted = post('date_posted');   //datetime
    $posted  = post('posted');            //int
    $deleted = post('deleted');           //int
    
    try{
        $id = $product->add($farmer_id, $name, $description, $category, 
                                   $price, $unit, $in_stock, $date_posted, 
                                   $posted, $deleted);
                                   echo $id;
        echo $id != 0 ? $id : 'failed';
    }catch(Exception $e){
        echo $e;
        http_response_code(418);
    }
    
    $product->dismiss();
?>