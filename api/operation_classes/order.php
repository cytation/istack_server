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
                        `orders`.`date_estimate_delivery`,
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
                        `offers`.`id` = (SELECT MAX(`id`) 
                                         FROM `offers` 
                                         WHERE `order_id` = `orders`.`id`)
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

        public function getAllActiveFromBuyer($buyer_id){
            $query = "SELECT                            
                        `orders`.`id`,                            
                        `orders`.`product_id`,                            
                        `orders`.`final_price`,                            
                        `orders`.`quantity`,                           
                        `orders`.`date_estimate_delivery`,
                        `orders`.`date_posted` AS `order_date_posted`,                            
                        `orders`.`payment_method`,                            
                        `orders`.`buyer_id`,                             
                        `orders`.`farmer_id`,                            
                        `orders`.`courrier_id`,                            
                        `orders`.`status`,                               
                        `offers`.`offer_message`,                            
                        `offers`.`date_posted` AS `offer_date_posted`,
                        `products`.`name` AS `product_name`,
                        `store_info`.`name` AS `store_name`,
                        `product_images`.`url` AS `image_url`
                    FROM `orders`       
                    LEFT JOIN `products`
                    ON `products`.`id` = `orders`.`product_id`
                    LEFT JOIN `store_info`
                    ON `store_info`.`farmer_id` = `orders`.`farmer_id`
                    LEFT JOIN `product_images`
                    ON `product_images`.`id` = (SELECT MAX(`id`) 
                                                FROM `product_images` 
                                                WHERE `product_id` = `products`.`id`)
                    LEFT JOIN `offers`
                    ON `offers`.`order_id` = `orders`.`id` AND 
                        `offers`.`id` = (SELECT MAX(`id`) 
                                         FROM `offers` 
                                         WHERE `order_id` = `orders`.`id`)
                    WHERE `orders`.`buyer_id` = '$buyer_id'                       
                    AND (`status` = '$this->PROCESSING' OR                           
                         `status` = '$this->ON_DELIVERY' OR                            
                         `status` = '$this->NEGOTIATION')        
                    ORDER BY `orders`.`date_posted`";

            $result = mysqli_query($this->conn, $query);
            return $result 
                ? json_encode($result->fetch_all(MYSQLI_ASSOC))
                : null;
        }

        public function countFarmerItems($farmer_id){
            $query = "SELECT count(`id`) AS `active_orders` FROM `orders` 
                      WHERE `orders`.`farmer_id` = '$farmer_id'                     
                      AND (`status` = '$this->PROCESSING' OR                           
                           `status` = '$this->ON_DELIVERY' OR                            
                           `status` = '$this->NEGOTIATION')";
            $res = mysqli_query($this->conn, $query);
            return $res ? $res->fetch_assoc() : 0;
        }

        public function setDeliveryDate($date, $order_id){
            try{
                $query = "UPDATE `orders` SET `date_estimate_delivery` = '$date' 
                          WHERE `id` = '$order_id'";

                $result = mysqli_query($this->conn, $query);
                return (mysqli_affected_rows($this->conn) == 1)
                    ? 1
                    : 0;
            }catch(Exception $e){
                throw $e;
            }
        }

        public function setAsOnDelivery($order_id){
            try{
                $query = "UPDATE `orders` SET `status` = '$this->ON_DELIVERY' 
                          WHERE `id` = '$order_id'";

                $result = mysqli_query($this->conn, $query);
                return (mysqli_affected_rows($this->conn) == 1)
                    ? 1
                    : 0;
            }catch(Throwable $e){
                throw $e;
            }
        }

        # the order has been delivered
        public function setAsCompleted($order_id){
            try{
                $query = "UPDATE `orders` SET `status` = '$this->COMPLETED'
                          WHERE `id` = '$order_id'";

                $result = mysqli_query($this->conn, $query);
                return (mysqli_affected_rows($this->conn) == 1)
                    ? 1
                    : 0;
            }catch(Throwable $e){
                throw $e;
            }
        }

        # the order has not been delivered, put back to processing status
        public function setAsProcessing ($order_id){
            try{
                $query = "UPDATE `orders` SET `status` = '$this->PROCESSING'
                          WHERE `id` = '$order_id'";

                $result = mysqli_query($this->conn, $query);
                return (mysqli_affected_rows($this->conn) == 1)
                    ? 1
                    : 0;
            }catch(Throwable $e){
                throw $e;
            }
        }

        public function getHistory($user_type, $id){
        $buyer = '0';
        $farmer = '1';
        $courrier = '2';
        try{
            $farmer_query = "SELECT 
                                 `users`.`first_name`,
                                 `users`.`last_name`,
                                 `orders`.`date_completed`,
                                 `products`.`name` AS `product_name`,
                                 `product_images`.`url` AS image_url,
                                 `orders`.`final_price`,
                                 `orders`.`quantity`
                             FROM `orders`
                             LEFT JOIN `users` ON
                                `users`.`id` = `orders`.`buyer_id`
                             LEFT JOIN `products` ON
                                `products`.`id` = `orders`.`product_id`
                             LEFT JOIN `product_images` ON
                                `product_images`.`product_id` = `products`.`id`
                             WHERE `farmer_id` = '$id' AND
                                   `status` = '$this->COMPLETED'
                             ORDER BY `orders`.`date_completed` DESC";

            $buyer_query = "SELECT 
                                 `orders`.`date_completed`,
                                 `store_info`.`name` AS `store_name`,
                                 `products`.`name` AS `product_name`,
                                 `product_images`.`url` AS `image_url`,
                                 `orders`.`final_price`,
                                 `orders`.`quantity`
                             FROM `orders`
                             LEFT JOIN `store_info` ON
                                `orders`.`farmer_id` = `store_info`.`farmer_id`
                             LEFT JOIN `products` ON
                                `products`.`id` = `orders`.`product_id`
                             LEFT JOIN `product_images` ON
                                `product_images`.`product_id` = `products`.`id`
                             WHERE `buyer_id` = '$id' AND
                                   `status` = '$this->COMPLETED'
                             ORDER BY `orders`.`date_completed` DESC";

            $query = "";
            if($user_type == $buyer)
                $query = $buyer_query;
            else if ($user_type == $farmer)
                $query = $farmer_query;

            $result = mysqli_query($this->conn, $query);

            return $result ? json_encode($result->fetch_all(MYSQLI_ASSOC)) : 0;
        }catch(Throwable $e){
            throw $e;
        }   
    }
    }

?>
