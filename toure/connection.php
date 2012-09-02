<?php 

require('conf.php');

class Connection {

	

	public function __construct(){

	$host = DB_HOST;
	$table = DB_NAME;
	$user = DB_USER;
	$pass = DB_PASS;		

	  $this->connection = new PDO("mysql:host=$host;dbname=$table", $user, $pass);


	}

	public static function connect(){
		return new self();

	}


}

?>