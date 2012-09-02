<?php


require('user.php');

class Validation {
	

 	protected $values = array(); // holds the values from form or input 

 	protected $rules = array(); // holds the rules and attributes they apply to

 	protected $results = array(); // holds the boolean results of each test

 	protected $messages = array(); // holds custom error messages

 	public $errors = array(); // holds public errors


   	/**
 	* -------*|   __construct   |*-----------------------------------------------------------------
 	*
 	* Takes input array and rules array and assigns to the realated member variables
	*									
	* @param  Array $input
	* @param  Array $rules
	*  
	*/


public function __construct($input, $rules, $messages = null){

	foreach ($rules as $key => &$rule)
		{
			$rule = (is_string($rule)) ? explode('|', $rule) : $rule;
		}

		$this->messages = (isset($messages)) ? $messages : null;

		$this->rules = $rules;
		$this->values = $input;
}

public static function make($input, $rules, $messages = null){

	if(isset($messages)){  
		return new static($input, $rules, $messages);
	} else {
		return new static($input, $rules);
	}

}

	/**
 	* -------*|   check - protected   |*-----------------------------------------------------------------
 	*	
 	* 	checks rules and input and runs specialised 'prepare_methods' depending on values 
 	*   in array.
	*									 
	*/

protected function check(){

	foreach ($this->rules as $input => $rules) {
		for($i = 0; $i < count($rules); $i++){
			if(strpos($rules[$i], ':') == true){
				$method = Str::findBefore($rules[$i], ':');
				$param = Str::findAfter($rules[$i], ':');
				$this->results[] = call_user_func(array($this, $method), $input, $this->values[$input], $param);
			} else {
				$this->results[] = call_user_func(array($this, $rules[$i]), $input, $this->values[$input]);
			}
		}
	}

}

	/**
 	* -------*|   passed  |*-----------------------------------------------------------------
 	* 
 	*	Returns a boolean value based on whether the validations all returned true or not. 
 	*   										
	*	
	*  	@return  Bool
	*  
	*/


public function passed(){

	$this->check();

	return (in_array(false, $this->results)) ? false : true;

}

	/*-------*|   failed  |*-----------------------------------------------------------------
 	*
 	*	Returns a boolean value based on whether the validations all returned true or not. 
 	*   										
	*	
	*  	@return  Bool
	*  
	----------------------------------------------------------------------------------------*/

public function failed(){
	
	return ! $this->passed();
}

	/*-------*|   log_error  |*-----------------------------------------------------------------
 	*
 	*	used by conditional methods to log functions into the $this->errors array for user
 	*   										
	*	
	*  	@return  Bool Internal
	*  
	----------------------------------------------------------------------------------------*/

protected function log_error($input, $message, $rule = null){
	$needle = $input."_".$rule;
	if(array_key_exists($needle, $this->messages)) {
		$this->errors[$input][$rule] = $this->messages[$input."_".$rule];
		return false;
	} else {
		$this->errors[$input][$rule] = $message;
		return false;
	}
}

	/*-------*|   get_errors  |*-----------------------------------------------------------------
 	*
 	*	returns all the errors gathered from validations 
 	*   										
	*	
	*  	@return  array @errors
	*  
	----------------------------------------------------------------------------------------*/

public function get_errors($field = null){

	return (isset($field)) ? $this->errors[$field] : $this->errors;

}



	/*-------*|   CONDITIONS  |*-----------------------------------------------------------------
	---------------------------------------------------------------------------------------------
	*	
	*	Below are the conditions to be used with the Validation Class conditions are specified
	*	using an assoc array and multiple conditions are seperated like so:
	*	
	*	$rules = array(
	*		'password' => 'required|min:6',
	*		'passwordconfirmation' => 'same:password'
	*	);
	*
	*	Any conditions that are not met return a default error message or custom messages which
	*	are specified using an assoc array and seperating the input name and rule by an underscore
	* 	like so:
	*
	*	$messages = array(
	*		'password_required' => 'Please enter a password!',
	*		'password_min' => 'Password must be 6 characters'
	*	);
	*
	*/

	/*-------*|   required  |*-----------------------------------------------------------------
	*
 	*	Checked whether input is empty or not 
	----------------------------------------------------------------------------------------*/

protected function required($input, $value){


	if (is_null($value))
		{
			return $this->log_error($input, "$input is required", "required");
		}
		elseif (is_string($value) and trim($value) === '')
		{
			return $this->log_error($input, "$input is required", "required");
		}
		elseif ( ! is_null($_FILES[$input]) and is_array($value) and $value['tmp_name'] == '')
		{
			return $this->log_error($input, "$input is required", "required");
		}

		return true;

}

	/*-------*|   string  |*-----------------------------------------------------------------
	*
 	*	Checks whether input is a string and logs error 
	----------------------------------------------------------------------------------------*/

protected function string($input, $value){

	if(is_string($value)){
		return true;
	} else {
		return $this->log_error($input, "$input must be a string", 'string');
	}

}

	/*-------*|   numeric  |*-----------------------------------------------------------------
	*
 	*	Checks whether input is numeric and logs error 
	----------------------------------------------------------------------------------------*/

protected function numeric($input, $value){

	if(is_numeric($value)){
		return true;
	} else {
		return $this->log_error($input, "$input must be a number", 'numeric');
	}	

}

	/*-------*|   int  |*-----------------------------------------------------------------
	*
 	*	Checks whether input is an integer and logs error 
	----------------------------------------------------------------------------------------*/

protected function int($input, $value){

	if(is_int($value)){
		return true;
	} else {
		return $this->log_error($input, "$input must be a number", 'int');
	}

}

	/*-------*|   unique  |*-----------------------------------------------------------------
	*
 	*	Queries database to check whether input exists in colum using table specified in config 
	----------------------------------------------------------------------------------------*/

protected function unique($input, $value){
	if(User::exists($value)){
		return $this->log_error($input, "$input already exists", 'unique');
	} else {
		return true;
	}	
}

	/*-------*|   same  |*-----------------------------------------------------------------
	*
 	*	Checks if two input are the same value 
	----------------------------------------------------------------------------------------*/

protected function same($input, $value, $param){
	
	if($this->values[$input] == $this->values[$param]){
		return true;
	} else {
		return $this->log_error($input, "$input must match $param", "same");
	}

}

	/*-------*|   min  |*-----------------------------------------------------------------
	*
 	*	Specifies a minimum length for input with param seperated by colon = min:6
	----------------------------------------------------------------------------------------*/

protected function min($input, $value, $param){

	return (Str::length($value) > $param) ? true : $this->log_error($input, "$input must be $param characters or more!", 'min');

}

	/*-------*|   max  |*-----------------------------------------------------------------
	*
 	*	Specifies a maximum length for input with param seperated by colon = max:6
	----------------------------------------------------------------------------------------*/

protected function max($input, $value, $param){

	return (Str::length($value) < $param) ? true : $this->log_error($input, "$input must be $param characters or more!", 'max');

}

	/*-------*|   in  |*-----------------------------------------------------------------
	*
 	*	Checks if an item can be found inside of input array = in:products
	----------------------------------------------------------------------------------------*/

protected function in($input, $value, $param){

		return (in_array($value, $param)) ? true : $this->log_error($input, "$param is not in $value", 'in');
	}

	/*-------*|   valid_email  |*-----------------------------------------------------------------
	*
 	*	Checks for a valid email address
	----------------------------------------------------------------------------------------*/

protected function valid_email($input, $value){

		return (filter_var($value, FILTER_VALIDATE_EMAIL)) ? true : $this->log_error($input, "Please enter a valid email address", "valid_email");
	}

	/*-------*|   valid_url  |*-----------------------------------------------------------------
	*
 	*	Checks for a valid url
	----------------------------------------------------------------------------------------*/

protected function valid_url($input, $value){

		return (filter_var($value, FILTER_VALIDATE_URL)) ? true : $this->log_error($input, "Please enter a valid url", "valid_url");

	}



}

