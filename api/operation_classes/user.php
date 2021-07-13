<?php
	class User{
		private $conn;
        
        public function __construct($db){
            $this->conn = $db;
        }
        
        public function dismiss(){
            $this->conn->close();
        }

        # gets user info
        public function getInfo($id){
        	$query = "SELECT * FROM `users` WHERE `id` = '$id'";
        	$res = mysqli_query($this->conn, $query);
        	return $res ? json_encode($res->fetch_assoc()) : 0;
        }

        # warning: old images are names with .jpg extension. (i.e. 12_123412.jpg)
        # new images are plain names without .jpg extension. (i.e. 12_213453)
        public function uploadImage($old_image, $user_id){
            $image = $_FILES['image']['name'];
            $image_name = $image . '.jpg';
            $image_path = "../../content/user_images/".$image_name;
            $old_image_path = "../../content/user_images/".$old_image;
            if(move_uploaded_file($_FILES['image']['tmp_name'],$image_path)){
                $query = "UPDATE `users` SET `image_url` = '$image_name' 
                          WHERE `id` = $user_id";
                $result = mysqli_query($this->conn, $query);
                if($result){
                    if($old_image != 'default_image_male.jpg' &&
                       $old_image != 'default_image_female.jpg' && 
                       $old_image != 'default_image_undefined.jpg'){
                        echo 'deleting ';
                        if(file_exists($old_image_path))
                            if(unlink($old_image_path))
                                echo 'success';
                            else echo 'failed';
                        else 'image does not exist';
                    }
                    return 200;
                }
                else
                    unlink($image_path);
            }
            return 418;
        }

        public function updateUserInfo($first_name, $last_name, $birthday, 
                                       $province, $municipality, $barangay,
                                       $zipcode, $gender, $id){
            $query = "UPDATE `users` SET 
                            `first_name` = ?,
                            `last_name` = ?,
                            `birthday` = ?,
                            `province` = ?,
                            `municipality` = ?,
                            `barangay` = ?,
                            `zipcode` = ?,
                            `gender` = ?
                      WHERE `id` = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('sssssssii', $first_name, $last_name, $birthday,
                              $province, $municipality, $barangay, $zipcode, $gender, $id);
            $stmt->execute();
            return (mysqli_affected_rows($this->conn) == 1)
                ? 1 : 0;
        }
    }
?>