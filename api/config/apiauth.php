<?php
    
    class Api {
        private $key = '*(&^iuHT*&TUHO*(YG&^G*PNH&*(FTKJV^$45kuhioyutiyeru';
        private $authorized = false;
        
        public function __construct(){
            $api_key = isset($_POST['api_key']) ? $_POST['api_key'] : null;
            if($api_key != null)
                $this->authorized = ($api_key == $this->key) ? true : false;
            else 
                $this->authorized = false;
        }
        
        public function notAuthorized(){
            return $this->authorized
                ? false
                : true;
        }
    }
?>