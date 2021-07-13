<?php
	class Offer {
		private $conn;
        
        public function __construct($db){
            $this->conn = $db;
        }
        
        public function dismiss(){
            $this->conn->close();
        }

        public function getOffers($order_id){
        	$query = "SELECT * FROM `offers` 
        			  WHERE `order_id` = '$order_id'
        			  ORDER BY `date_posted` ASC";
        	
	        $result = mysqli_query($this->conn, $query);
	        return $result 
	        	? json_encode($result->fetch_all(MYSQLI_ASSOC)) 
	        	: 0;
        }

        # values for order status
        # $completed = 0;
        # $processing = 1;
        # $declined = 2;
        # $cancelled = 3;
        # $onDelivery = 4;
        # $negotiation = 5;

        public function sendFirstOffer($product_id, $final_price,
        				$quantity, $date_posted, $payment_method,
        				$buyer_id, $farmer_id, $offer_message){

            # default value for order status if initializing offers.
            $negotiation = 5; 

        	$query = "INSERT INTO `orders` (`product_id`, `final_price`, `quantity`, 
        			  		 `date_posted`, `payment_method`, `buyer_id`, 
        			  		 `farmer_id`, `status`)
        			  VALUES ('$product_id', '$final_price', '$quantity', '$date_posted',
        			  		  '$payment_method', '$buyer_id', '$farmer_id', '$negotiation')"; 
        	$result = mysqli_query($this->conn, $query);
        	$id = $this->conn->insert_id;
            
        	if($result){
        		$query = "INSERT INTO `offers` (`date_posted`, `offer_message`, `price`, 
        					 `quantity`, `sender_id`, `receiver_id`, `order_id`, `product_id`)
        			      VALUES (?,?,?,?,?,?,?,?)";
        		$stmt = $this->conn->prepare($query); 
        		$stmt->bind_param('ssddiiii', $date_posted,
        						$offer_message, $final_price, $quantity, $buyer_id, 
                                $farmer_id, $id, $product_id);
        		$res = $stmt->execute();
                $stmt->close();

                return $res ? 1 : 0;
        	}
        	return 0;
        }

        public function sendOffer($product_id, $final_price,
                        $quantity, $date_posted, $order_id,
                        $sender_id, $receiver_id, $offer_message){
            try{
                $query = "INSERT INTO `offers` (`date_posted`, `offer_message`, `price`, 
                             `quantity`, `sender_id`, `receiver_id`, `order_id`, `product_id`)
                          VALUES (?,?,?,?,?,?,?,?)";
                $stmt = $this->conn->prepare($query); 
                $binded = $stmt->bind_param('ssddiiii', $date_posted,
                                $offer_message, $final_price, $quantity, $sender_id, 
                                $receiver_id, $order_id, $product_id);

                $res = $stmt -> execute();
                $stmt->close();

                return $res ? 1 : 0;
            }catch(Exception $e){
                throw $e;
            }
        }

        public function acceptOffer($order_id, $quantity, $final_price, $product_id){

            $processing = 1;

            $update_order_query = "UPDATE `orders` 
                                   SET `status` = '$processing',
                                       `final_price` = '$final_price', 
                                       `quantity` = '$quantity'
                                   WHERE `id` = '$order_id'";
            $update_product_query = "UPDATE `products`
                                     SET `in_stock` = in_stock - '$quantity'
                                     WHERE `id` = '$product_id'";
            try{
                mysqli_begin_transaction($this->conn, MYSQLI_TRANS_START_READ_WRITE);
                mysqli_query($this->conn, $update_order_query);
                mysqli_query($this->conn, $update_product_query);
                mysqli_commit($this->conn);
            }catch(Throwable $e){
                $mysqli_rollback($this->conn);
                return 0;
            }
            return 1;
        }

        public function cancelOffer($order_id){
            $cancelled = 3;
            try{
                $query = "UPDATE `orders` 
                          SET `status` = '$cancelled'
                          WHERE `id` = '$order_id'";
                mysqli_query($this->conn, $query);
            }catch(Exception $e){
                throw $e;
            }
            return mysqli_affected_rows($this->conn)
                ? 1
                : 0;
        }

        public function declineOffer($order_id){
            $declined = 2;
            try{
                $query = "UPDATE `orders` 
                          SET `status` = '$declined'
                          WHERE `id` = '$order_id'";
                mysqli_query($this->conn, $query);
            }catch(Exception $e){
                throw $e;
            }
            return mysqli_affected_rows($this->conn)
                ? 1
                : 0;
        }

	}
?>