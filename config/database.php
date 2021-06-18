<?php
    
    class Database {
        private $host = "localhost";
        private $db_name = "istack_main";//"u488180748_istack_main";
        private $username = "root";//"u488180748_istack_admin";
        private $password = "";//"@17L2#V(0~>[QAHu";
        private $conn;
        
        public function __construct(){
            $this->conn = mysqli_connect($this->host, 
                                         $this->username, 
                                         $this->password, 
                                         $this->db_name);
            if($this->conn->connect_error){ 
                echo 'error connecting';
                die("Connection Failed: " . $this->conn->connect_error);
            }
        }
        
        public function connection(){
            return $this->conn;
        }
    }
?>