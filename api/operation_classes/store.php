<?php
    class Store{
        private $conn;
        
        public function __construct($db){
            $this->conn = $db;
        }
        
        public function dismiss(){
            $this->conn->close();
        }
        
        public function checkFor($farmer_id){
            $query = "SELECT * FROM `store_info` 
                      WHERE `farmer_id` = '$farmer_id'";
            $result = mysqli_query($this->conn, $query);
            return ($result->num_rows != 0)
                ? json_encode($result->fetch_assoc())
                : null;
        }
        
        public function info($farmer_id){
            $query = "SELECT * FROM `store_info` 
                      WHERE `farmer_id` = '$farmer_id'";
            $result = mysqli_query($this->conn, $query);
        
            return ($result->num_rows != 0)
                ? json_encode($result->fetch_assoc())
                : null;
        }
        
        public function register($farmer_id, $name, $desc, $province, 
                                 $municipality, $barangay, $open_time,
                                 $close_time, $open_day, $close_day){
            
            $query = "INSERT INTO `store_info` 
                (`farmer_id`, `name`, `description`, `barangay`, `municipality`, 
                 `province`, `open_time`, `close_time`,`open_day`,`close_day`) 
                VALUES 
                ('$farmer_id', '$name', '$desc', '$barangay', '$municipality',
                 '$province' ,'$open_time','$close_time', '$open_day','$close_day')";
                 
            return mysqli_query($connect, $query);
        }
        
    }
        
?>