<?php
    include '../../config/database.php';
    include '../../config/apiauth.php';
    include '../../config/request.php';
    include '../../operation_classes/auth.php';
    
    $api = new Api();
    if($api->notAuthorized()){
        http_response_code(401);
        return;
    }
    
    $db = new Database();
    $auth = new Auth($db->connection());
    
    try{
        $mobile = post("mobile");
        $password = password_hash(post("password"), PASSWORD_DEFAULT);
        $first_name = post("first_name");
        $last_name = post("last_name");
        $bio = post("bio");
        $barangay = post("barangay");
        $municipality = post("municipality");
        $province = post("province");
        $gender = post("gender");
        $user_type = post("user_type");
        $date_joined = post("date_joined");
        $birthday = post("birthday");
        $zipcode = post("zipcode");
        
        $res = $auth->register($mobile, $password, $first_name, $last_name, 
                                 $bio, $barangay, $municipality, $province,
                                 $gender, $user_type, $date_joined, $birthday,
                                 $zipcode);
        echo $res
            ? $res
            : 'failed';
    }catch(Exception $e){
        echo $e;
        http_response_code(418);
    }
    
    $auth->dismiss();
?>