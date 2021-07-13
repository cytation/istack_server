<?php
    class Product{
        private $conn;
        
        public function __construct($db){
            $this->conn = $db;
        }
        
        public function dismiss(){
            $this->conn->close();
        }
        
        public function add($farmer_id, $name, $description, $category, $price,
                            $unit, $in_stock, $date_posted, $posted, $deleted){
            $query = "INSERT INTO `products` 
                            (`farmer_id`, `name`, `description`, 
                            `category`, `price`, `unit`, `in_stock`, 
                            `date_posted`, `posted`, `deleted`)
                      VALUES (?,?,?,?,?,?,?,?,?,?);";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bind_param('issidsdsii', $farmer_id, $name, 
                            $description,  $category, $price, $unit, 
                            $in_stock, $date_posted, $posted, $deleted);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $id = $this->conn->insert_id;
            return $id ? $id : null;
        }

        public function getInfo($product_id){
            $query = "SELECT * FROM `products`
                      WHERE `id` = '$product_id'";
            $result = mysqli_query($this->conn, $query);
            return $result
                ? json_encode($result->fetch_assoc())
                : null;
        }
    
        public function getFarmerProducts($farmer_id){
            $query = "SELECT * FROM `products`
                      WHERE `farmer_id` = '$farmer_id' AND `posted` = '1';";
            $result = mysqli_query($this->conn, $query);
            return $result
                ? json_encode($result->fetch_all(MYSQLI_ASSOC))
                : null;
        }

        public function countFarmerItems($farmer_id){
            $query = "SELECT count(`id`) AS `active_products` FROM `products`
                      WHERE `farmer_id` = '$farmer_id' AND `posted` = '1';";
            $result = mysqli_query($this->conn, $query);
            return $result
                ? $result->fetch_assoc()
                : 0;
        }
        
        public function getImages($product_id){
            $query = "SELECT * FROM `product_images` 
                      WHERE `product_id` = '$product_id';";
            $result = mysqli_query($this->conn, $query) ;
            return $result
                ? json_encode($result->fetch_all(MYSQLI_ASSOC))
                : null;
        }
        
        public function getInfoAsBuyer($product_id, $buyer_id){
            $query = "SELECT 
                `products`.`id` AS `product_id`,
                `products`.`farmer_id`,
                `products`.`name`,
                `products`.`description`,
                `products`.`category`,
                `products`.`price`,
                `products`.`unit`,
                `products`.`in_stock`,
                `products`.`date_posted`,
                `products`.`deleted`,
                `products`.`posted`,
                `products`.`rate1`,
                `products`.`rate2`,
                `products`.`rate3`,
                `products`.`rate4`,
                `products`.`rate5`,
                `saved_products`.`id` AS `save_id`,
                `orders`.`id` AS `order_id`
            FROM `products` 
            LEFT JOIN `saved_products`
            ON `saved_products`.`product_id` = `products`.`id` AND `saved_products`.`buyer_id` = '$buyer_id'
            LEFT JOIN `orders` 
            on `orders`.`product_id` = `products`.`id` AND `orders`.`buyer_id` = '$buyer_id' 
            WHERE `products`.`id` = '$product_id' 
            LIMIT 1;";
            
            $result = mysqli_query($this->conn, $query);
            return $result
                ? json_encode($result->fetch_assoc())
                : null;
        }

        public function getSaved($buyer_id){
            $query = "SELECT 
                        `products`.`id`,
                        `products`.`farmer_id`,
                        `products`.`price`,
                        `products`.`name`,
                        `product_images`.`url`,
                        `store_info`.`name` AS `store_name`
                    FROM `saved_products`
                    LEFT JOIN `products` ON
                    `products`.`id` = `saved_products`.`product_id`
                    LEFT JOIN `product_images` ON
                    `product_images`.`id` = (SELECT max(`id`) FROM `product_images` WHERE
                                             `products`.`id` = `product_images`.`product_id`)
                    LEFT JOIN `store_info` ON
                    `store_info`.`farmer_id` = `products`.`farmer_id`
                    WHERE `saved_products`.`buyer_id` = '$buyer_id'
                    ORDER BY `id` DESC";
            
            $result = mysqli_query($this->conn, $query);
            return $result
                ? json_encode($result->fetch_all(MYSQLI_ASSOC))
                : null;
        }
        
        public function save($product_id, $buyer_id){
            $query = "INSERT INTO `saved_products` (`product_id`,`buyer_id`) 
                      VALUES ('$product_id', '$buyer_id');";
            return mysqli_query($this->conn, $query);
        }
        
        public function unsave($product_id, $buyer_id){
            $query = "DELETE FROM  `saved_products` 
                      WHERE `buyer_id` = '$buyer_id' AND `product_id` = '$product_id';";
            return mysqli_query($this->conn, $query);
        }
        
        public function uploadImage($product_id){
            $image = $_FILES['image']['name'];
            $image_name = $image . '.jpg';
            $image_path = "../../content/product_images/".$image.'.jpg';
            if(move_uploaded_file($_FILES['image']['tmp_name'],$image_path)){
                $query = "INSERT INTO `product_images`(`product_id`, `url`)  
                          VALUES ('$product_id', '$image_name')";
                $result = mysqli_query($this->conn, $query);
                if($result)
                    return 200;
                else
                    unlink($image_path);
            }
            return 418;
        }

        public function listWithImage($farmer_id){
            $query = "SELECT 
                        `products`.`id`,
                        `products`.`farmer_id`,
                        `products`.`name`,
                        `products`.`description`,
                        `products`.`category`,
                        `products`.`price`,
                        `products`.`unit`,
                        `products`.`in_stock`,
                        `products`.`date_posted`,
                        `products`.`deleted`,
                        `products`.`posted`,
                        `products`.`rate1`,
                        `products`.`rate2`,
                        `products`.`rate3`,
                        `products`.`rate4`,
                        `products`.`rate5`,
                        `product_images`.`url`
                      FROM `products`
                      LEFT JOIN `product_images` ON 
                        `product_images`.`product_id` = `products`.`id` AND
                        `product_images`.`id` = (SELECT max(`id`) FROM `product_images`
                                                 WHERE `product_images`.`product_id` = `products`.`id`)
                     WHERE `products`.`farmer_id` = '$farmer_id'
                     ORDER BY `products`.`date_posted` DESC";
            try{
                $result = mysqli_query($this->conn, $query);
                return $result 
                    ? json_encode($result->fetch_all(MYSQLI_ASSOC))
                    : 0;
            }catch(Throwable $e){
                throw $e;
            }
        }
    }
    
?>