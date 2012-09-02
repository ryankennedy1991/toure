<?php

class Secure {
		
	public static function make($value)
	{
		if (function_exists('openssl_random_pseudo_bytes')){
			$salt = openssl_random_pseudo_bytes(16);

		} else {

			$salt = substr(Str::shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, 40);
		
		}

		$salt = substr(strtr(base64_encode($salt), '+', '.'), 0 , 22);

		return crypt($value, '$2a$10$'.$salt);
	}



	public static function check($value, $hash)
	{
		return crypt($value, $hash) === $hash;
	}

}