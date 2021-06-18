<?php

    class Order {

        private $COMPLETED = 0;
        private $PROCESSING = 1;
        private $DECLINED = 2;
        private $CANCELLED = 3;
        private $ON_DELIVERY = 4;
        private $NEGOTIATION = 5;

		private $conn;
        
        public function __construct($db){
            $this->conn = $db;
        }
        
        public function dismiss(){
            $this->conn->close();
        }

        public function getInfo($order_id){
        	$query = "SELECT * FROM `orders` WHERE `id` = '$order_id'";
        	$result = mysqli_query($this->conn, $query);
        	return $result
        		? json_encode($result->fetch_assoc())
        		: null;
        }

        # gets all the active orders containing the ff:
        # latest offer message, buyer name, product name 
        public function getAllActiveFromFarmer($farmer_id){
            $query = "SELECT                            
                        `orders`.`id`,                            
                        `orders`.`product_id`,                            
                        `orders`.`final_price`,                            
                        `orders`.`quantity`,                           
                        `orders`.`date_posted` AS `order_date_posted`,                            
                        `orders`.`payment_method`,                            
                        `orders`.`buyer_id`,                             
                        `orders`.`farmer_id`,                            
                        `orders`.`courrier_id`,                            
                        `orders`.`status`,                            
                        `users`.`first_name`,                            
                        `users`.`last_name`,                            
                        `offers`.`offer_message`,                            
                        `offers`.`date_posted` AS `offer_date_posted`,
                        `products`.`name` AS `product_name`
                    FROM `orders`       
                    LEFT JOIN `users`
                    ON `users`.`id` = `orders`.`buyer_id`
                    LEFT JOIN `products`
                    ON `products`.`id` = `orders`.`product_id`
                    LEFT JOIN `offers`
                    ON `offers`.`order_id` = `orders`.`id` AND 
                        `offers`.`id` = (SELECT MAX(`id`) FROM `offers` WHERE `order_id` = `orders`.`id`)
                    WHERE `orders`.`farmer_id` = '$farmer_id'                       
                    AND (`status` = '$this->PROCESSING' OR                           
                         `status` = '$this->ON_DELIVERY' OR                            
                         `status` = '$this->NEGOTIATION')        
                    ORDER BY `orders`.`date_posted`";


            $result = mysqli_query($this->conn, $query);
            return $result 
                ? json_encode($result->fetch_all(MYSQLI_ASSOC))
                : null;
        }
    }



?>
