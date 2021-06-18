<?php
    class Auth {
        private $conn;
        
        public function __construct($db){
            $this->conn = $db;
        }
        
        public function dismiss(){
            $this->conn->close();
        }
        
        public function login($mobile, $password){
            try{
                $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE `mobile` = ?");
                $stmt->bind_param('s', $mobile);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if(mysqli_num_rows($result) != 0) {
                    $data = $result->fetch_assoc();
                    $isMatch = password_verify($password, $data['password']);
                    if($isMatch) 
                        return json_encode($data);
                }
                return null;
            }catch(Exception $e){
                echo $e;
            }
        }
        
        public function register($mobile, $password, $first_name, $last_name, 
                                 $bio, $barangay, $municipality, $province,
                                 $gender, $user_type, $date_joined, $birthday,
                                 $zipcode){
            try{
                $stmt = $this->conn->prepare("INSERT INTO `users` 
                                    (`mobile`, `password`, `first_name`, `last_name`, 
                                     `bio`,`barangay`, `municipality`, `province`, 
                                     `gender`, `user_type`,  `date_joined`, `birthday`,
                                     `zipcode`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param('ssssssssiisss', $mobile, $password, $first_name,
                                    $last_name, $bio, $barangay, $municipality,
                                    $province, $gender, $user_type, $date_joined,
                                    $birthday, $zipcode);
                $stmt->excute();
                $result = $stmt->get_result();
                $stmt->close();
                
                return $result
                    ? $this->conn->insert_id
                    : null;
                
            }catch(Exception $e){
                echo $e;
            }
        }
        
        public function getUsers(){
            
        }
    }
?>
