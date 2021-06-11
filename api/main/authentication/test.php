<?php
    
	// database updates
	// users.delivery_info	varchar (255)
	// users.delivery_contact varchar (15)
	// orders.final_price double
	// orders.quantity double
	// orders.payment_method

	// public key gqdvgqf7jmmqr64x
	// private key f08f0bea57cf56db5efd8ca2849fa360

    include_once '../../config/database.php';
    include_once '../../config/apiauth.php';
    
    $db = new Database();
    echo $db -> connection() -> insert_id;

?>
