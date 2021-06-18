<?php
    class Address {
        private $conn;
        
        public function __construct($db){
            $this->conn = $db;
        }

        public function dismiss(){
            $this->conn->close();
        }
        
        public function getBarangays($municipality_id){
            $query = "SELECT * FROM `barangays` 
                      WHERE `municipality_id` = '$municipality_id' 
                      ORDER BY `name`";
            $result = mysqli_query($this->conn, $query);
            return $result
                ? json_encode($result->fetch_all(MYSQLI_ASSOC))
                : null;
        }
    
        public function getMunicipalities($province_id){
            $query = "SELECT * FROM `municipalities` 
                      WHERE `province_id` = '$province_id' 
                      ORDER BY `name`";
            $result = mysqli_query($this->conn, $query);
            
            return $result
                ? json_encode($result->fetch_all(MYSQLI_ASSOC))
                : null;
        }
        
        public function getProvinces(){
            $query = "SELECT * FROM `provinces` 
                      ORDER BY `name`";
            $result = mysqli_query($this->conn, $query);
            return $result
                ? json_encode($result->fetch_all(MYSQLI_ASSOC))
                : null;
        }
    }
?>