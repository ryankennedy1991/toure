<?php 


class Str {

	/*
	* toLower converts input string into lower case.
	*
	*	example:    $lower = Str::toLower('STRING');
	*
	*	
	*	@param  | 	string  $inputstr
	*	@return |   string
	*
	**************************************************************************/


	public static function toLower($inputstr){

		if(is_string($inputstr)){
		return (MB_STRING) ? mb_strtolower($inputstr) : strtolower($inputstr);
		}
		return "Make sure input is a string! You fool!"; 
	}

	/*
	* toUpper converts input string into upper case.
	*
	*	example:    $upper = Str::toUpper('STRING');
	*
	*	
	*	@param  | 	string  $inputstr
	*	@return |   string
	*
	**************************************************************************/

	public static function toUpper($inputstr){

		if(is_string($inputstr)){
		return (MB_STRING) ? mb_strtoupper($inputstr) : strtolower($inputstr);
		}
		return "Make sure input is a string! You fool!"; 
	}

	/*
	* length returns the length of the input string
	*
	*	example:    $upper = Str::length('STRING');
	*
	*	
	*	@param  | 	string  $inputstr
	*	@return |   string
	*
	**************************************************************************/

	public static function length($inputstr){

		if(is_string($inputstr)){
		return (MB_STRING) ? mb_strlen($inputstr) : strlen($inputstr);
		}
		return "Make sure input is a string! You fool!"; 
	}

	/*
	* Reverses a string
	*
	*	example:    $upper = Str::reverse('STRING');
	*
	*	
	*	@param  | 	string  $inputstr
	*	@return |   string
	*
	**************************************************************************/

	public static function reverse($inputstr){

		if(is_string($inputstr)){
		return strrev($inputstr);
		}
		return "Make sure input is a string! You fool!"; 
	}


	/*
	* converts a string into an array using a seperator
	*
	*	example:    $array = Str::toArray('STRING');
	*
	*	
	*	@param  | 	string  $splitter
	*	@param  | 	string  $str
	*	@return |   string
	*
	**************************************************************************/

	public static function toArray($splitter, $str){

		if(is_string($str)){
		return explode($splitter, $str);
		}
		return "Make sure input is a string! You fool!"; 
	}

	/*
	* Shuffles the input strings characters randomly
	*
	*	example:    $shuffle = Str::shuffle('STRING');
	*
	*	
	*	
	*	@param  | 	string  $str
	*	@return |   string
	*
	**************************************************************************/

	public static function shuffle($str){

		if(is_string($str)){
		return str_shuffle($str);
		}
		return "Make sure input is a string! You fool!"; 
	}

	public static function random($length, $type = 'alnum')
	{
		return substr(self::shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, $length);
	}


	public function findBefore($haystack,$needle){

    // Returns everything before $needle (exclusive).
    return substr($haystack,0,strpos($haystack,$needle));
    
	}

	public function findAfter($haystack,$needle){

    // Returns everything after $needle (exclusive).
    return substr(strrchr($haystack, $needle), 1);
    
	}

	public function contains($haystack,$needle){

    // Returns everything after $needle (exclusive).
    return strpos($haystack, $needle);
    
	}










}