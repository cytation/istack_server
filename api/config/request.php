<?php
    
    function post($key){
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }
?>