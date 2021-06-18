<?php
    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/store.php';

    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }

    $db = new Database();
    $store = new Store($db->connection());
    
    try{
        $farmer_id = post('farmer_id');
        $name = post('name');
        $desc = post('desc');
        $province = post('province');
        $municipality = post('municipality');
        $barangay = post('barangay');
        $open_time = post('open_time');
        $close_time = post('close_time');
        $open_day = post('open_day');
        $close_day = post('close_day');
        
        $query = "INSERT INTO `store_info` 
                  (`farmer_id`, `name`, `description`, `barangay`, `municipality`, 
                   `province`, `open_time`,`close_time`,`open_day`,`close_day`) 
                  VALUES 
                  ('$farmer_id', '$name', '$desc', '$barangay', '$municipality',
                   '$province' ,'$open_time','$close_time','$open_day','$close_day')";
        
        $result = $store->register($farmer_id, $name, $desc, $province,  
                                   $municipality, $barangay, $open_time, 
                                   $close_time, $open_day, $close_day);
        echo $result 
            ? 'success'
            : 'failed';
    }catch(Exception $e){
        echo $e;
        http_response_code(418);
    }
    $store->dismiss();
?>