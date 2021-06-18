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
        			  ORDER BY `date_posted` DESC";
        	
	        $result = mysqli_query($this->conn, $query);
	        return $result 
	        	? json_encode($result->fetch_all(MYSQLI_ASSOC)) 
	        	: null;
        }

        public function sendFirstOffer($product_id, $final_price,
        				$quantity, $date_posted, $payment_method,
        				$buyer_id, $farmer_id, $offer_message){

            # possible values for status
            # $completed = 0;
            # $processing = 1;
            # $declined = 2;
            # $cancelled = 3;
            # $onDelivery = 4;

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
        		return $stmt->execute()
        			? $id
        			: null;
        	}
        	return null;
        }
	}
?>