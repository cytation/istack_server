<?php
    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/market.php';
    
    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }
    
    $db = new Database();
    $market = new Market($db->connection());
    
    try{ 
        $offset = post('offset');
        $category = post('category');
        $province = post('province');
        $municipality = post('municipality');
        $barangay = post('barangay');
        $res = $market->getProducts($category, $province, $municipality,
                             $barangay, $offset);
        echo $res ? $res : 'empty';                
    }catch(Exception $e){
        echo $e;
        http_response_code(418);
    }
    $market->dismiss();
?>