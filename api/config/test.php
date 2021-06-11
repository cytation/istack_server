<?php
    include 'database.php';
    include '../operation_classes/address.php';
    
    $db = new Database();
    $conn = $db->connection();
    $address = new Address($conn);
    echo $address->getProvinces();
?>