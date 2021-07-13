<?php
    class Market {
        private $conn;
        
        public function __construct($db){
            $this->conn = $db;
        }
        
        public function dismiss(){
            $this->conn->close();
        }
        
        public function getProducts($category, $province, $municipality,
                                    $barangay, $offset){
            $query = "SELECT 
                    `products`.`id`,
                    `products`.`farmer_id`,
                    `products`.`name`,
                    `products`.`price`,
                    `product_images`.`url`
                FROM `products` 
                LEFT JOIN `product_images`
                ON `product_images`.`product_id` = `products`.`id`
                LEFT JOIN `store_info`
                ON `products`.`farmer_id` = `store_info`.`farmer_id` ";
                
            if($category != '')
                $query = $query . " WHERE `products`.`category` = '$category'";
                
            if($province != '' && $category != '')
                $query = $query . " AND  `user`.`province` = '$province'";
            else if($province != '' && $category == '')
                $query = $query . " WHERE `store_info`.`province` = '$province'";
            
            if($municipality != '')
                $query = $query . " AND `store_info`.`municipality` = '$municipality'";
            if($barangay != '')
                $query = $query . " AND `store_info`.`barangay` = '$barangay'";
            
            $filter = "AND `posted` = '1';"; //LIMIT '$offset', 5;";   
            
            $result = mysqli_query($this->conn, $query . $filter);
            
            if($result)
                return json_encode($result->fetch_all(MYSQLI_ASSOC));
            else
                return null;
        }
    }
?>