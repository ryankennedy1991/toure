<?php

include('PasswordHash.php');

class Secure extends PasswordHash {

	public function __construct(){
		parent::__construct(8, FALSE);	
	}
		
	public function make($value){

		if (strlen($value) < 72){
			return $this->HashPassword($value);
		} else {
			return "Password must be less that 72 characters";
		}

		// Below is the old security but i love it so much it has to stay here.
		/*
		if (function_exists('openssl_random_pseudo_bytes')){
			$salt = openssl_random_pseudo_bytes(16);

		} else {

			$salt = substr(Str::shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, 40);
		
		}

		$salt = substr(strtr(base64_encode($salt), '+', '.'), 0 , 22);

		return crypt($value, '$2a$10$'.$salt);*/
	}



	public function check($value, $hash){

		return $this->CheckPassword($value, $hash);
		//return crypt($value, $hash) === $hash; This is the old version, i love it so much it has to stay.
	}

}