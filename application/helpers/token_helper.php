<?php
	function generate_refresh_token(){
            $new_pass='';
            return $new_pass = randomPassword(24);
     }
     
	function generate_access_token(){
            $new_pass='';
            return $new_pass = randomPassword(24);
            
     }
     
             
    function randomPassword($lmt=8) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $lmt; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    
    function debug($data){
		echo '<pre>';
		print_r($data);	
		echo '</pre>';
	}
?>
