<?php
	class DeliveryInfo {
		private $conn;
        
        # Table Fields
        # id                    int
        # buyer_id              int 
        # contact_info          varchar
        # address               varchar
        # delivery_instructions varchar

        public function __construct($db){
            $this->conn = $db;
        }
        
        public function dismiss(){
            $this->conn->close();
        }

        public function get($buyer_id){
            try{
                $query = "SELECT * FROM `buyer_delivery_info` WHERE `buyer_id` = '$buyer_id'";
                $res = mysqli_query($this->conn, $query);
                $info = $res->fetch_assoc();
                return $info
                    ? json_encode($info)
                    : 0;
            }catch(Exception $e){
                throw $e;
            }
        }

        public function upload($buyer_id, $contact_info, $address, 
                               $delivery_instructions){
            try{
                $query = "INSERT INTO `buyer_delivery_info` 
                          (`buyer_id`,`contact_info`,`address`,`delivery_instructions`)
                          VALUES (?,?,?,?)";

                $stmt = mysqli_prepare($this->conn, $query);
                $stmt->bind_param('isss', $buyer_id, $contact_info, $address, $delivery_instructions);
                $stmt->execute();
                $stmt->close();

                return $this->conn->insert_id;
            }catch(Exception $e){
                throw $e;
            }
        }

        public function update($buyer_id, $contact_info, $address, 
                               $delivery_instructions){
            try{
                $query = "UPDATE `buyer_delivery_info` 
                          SET `contact_info` = ?,
                              `address` = ?,
                              `delivery_instructions` = ?
                          WHERE `buyer_id` = ?";

                $stmt = mysqli_prepare($this->conn, $query);
                $stmt->bind_param('sssi', $contact_info, $address, $delivery_instructions, $buyer_id);
                $stmt->execute();

                $result = $stmt->get_result();
                $stmt->close();

                return (mysqli_affected_rows($this->conn) > 0);
            }catch(Exception $e){
                throw $e;
            }
        }
	}
?>