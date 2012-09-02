<?php 

require('connection.php');
require('conf.php');
require('secure.php');

class Database {


	private $connection; // This is our PDO object and connection taken from __construct.

	private $table; // This is our table chosen with selectTable.

	private $sql; // This is our raw sql statement that is passed around to methods.

	private $stmt; // This is our prepared statement passed around for methods.

	private $values; // This is our values array made up of data to be put into prepared statements.

	public $timestamps = TIMESTAMPS; // pulls in config to decide whether to use timestamps or not.

	public $secure = SECURE; // pulls in config to decide whether to use timestamps or not.

	public $last_id; //  stored the id of the last inserted row



	public function __construct(){
		$this->connection = Connection::connect()->connection;

	}


	public static function make(){
		return new self();

	}


	//-------*|   table   |*----------------------------------------------------------
 	//
 	//	Select a table for use with queries 		
 	//  															
 	//	example:  $example->selectTable($table);						
	//																		   
	//  @param   String $table 										
	//  
	//----------------------------------------------------------------------------------------


	public function table($table){
		$this->table = $table;
		$this->sql = "SELECT * FROM ".$this->table;
		return $this;
	}



	//-------*|   select   |*-----------------------------------------------------------------
 	//
 	//	Select rows from database use with where(), or_where() or where_email() and run with row() or all()
 	//  
 	//	example:  $example->select('title', 'content')->row();															
	//																	   
	//  @param   strings $input default = * 										
	//	@return  itself $this
	//  
	//----------------------------------------------------------------------------------------


	public function select(){

	$argcount = func_num_args();
	$args = func_get_args();

	$sql = "SELECT ";

	switch ($argcount) {
			case 0:
			$sql .= "*";
			break;

			case 1:
			$sql .= $args[0];
			break;
				
			
			default:
			for($i = 0; $i < $argcount - 1; $i++){
				$sql .= $args[$i].", ";
			} 

			$sql .= $args[$argcount - 1];
			break;
	}

	$sql .= " FROM ".$this->table;

	$this->sql = $sql;

	return $this;

	}

	//-------*|   order_by   |*-----------------------------------------------------------------
 	//
 	//	Orders results from select query
 	//  
 	//	example:  $example->select('title', 'content')->order_by('id', 'desc')->all();															
	//																	   
	//  @param   strings $input default = * 										
	//	@return  itself $this
	//  
	//----------------------------------------------------------------------------------------

	public function order_by($column, $type = 'desc'){

	if ($type == 'desc'){
		$this->sql .= " ORDER BY ".$column." DESC";
	}

	if ($type == 'asc'){
		$this->sql .= " ORDER BY ".$column;
	}

	return $this;


	}

		//-------*|   row   |*-----------------------------------------------------------------
 	//
 	//	Specifies to fetch the first row from a set of results
 	//  
 	//	example:  $example->select(array(title, content))->whereEmail('something@test.co.uk')->row('assoc');															
	//																	   
	//  @param   String $style (specifies what format to fetch results)	
	//									
	//	@return  Array or Object $result
	//  
	//----------------------------------------------------------------------------------------

	public function row($style = "assoc"){

		 //if(isset($this->sql)){
		//	$this->sql = "SELECT * FROM ".$this->table." LIMIT 1";
		//}

		$this->stmt = $this->connection->prepare($this->sql);

		$this->stmt->execute($this->values);

		switch ($style) {
			case 'assoc':
				$result = $this->stmt->fetch(PDO::FETCH_ASSOC);
				break;

			case 'object':
				$result = $this->stmt->fetch(PDO::FETCH_OBJ);
				break;

			case 'array':
				$result = $this->stmt->fetch(PDO::FETCH_NUM);
				break;			
			
			default:
				echo "choose from assoc, array or object for your argument when using row()";
				break;
		}
		
		return $result;

	}

	//-------*|   all   |*-----------------------------------------------------------------
 	//
 	//	Specifies to fetch all of the results
 	//  
 	//	example:  $example->select(array(title, content))->whereEmail('something@test.co.uk')->all();															
	//	
	//									
	//	@return  Array $result
	//  
	//----------------------------------------------------------------------------------------

	public function all(){

		$this->stmt = $this->connection->prepare($this->sql);

		$this->stmt->execute($this->values);

		$result = $this->stmt->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	//-------*|   where   |*-----------------------------------------------------------------
 	//
 	//	Specifies a condition to be used as an inline method with insert(), select() or delete().
 	//  
 	//	example:  $example->select(array(title, content))->where('id', '=', '4');															
	//																	   
	//  @param   String $column
	//	@param   String $condition
	//  @param   String $value	
	//									
	//	@return  itself $this
	//  
	//----------------------------------------------------------------------------------------

	public function where($column, $condition, $value){

		$this->sql .= " WHERE ".$column." ".$condition." ?";

		if (stristr($this->sql, 'DELETE')){
			$this->sql .= " LIMIT 1";
			$this->values[] = $value;
			return $this;
		}
		if (stristr($this->sql, 'UPDATE')){
			$this->values[] = $value;
			return $this;
		}

		$this->values[] = $value;

		return $this;
	}

	//-------*|   where_email   |*-----------------------------------------------------------------
 	//
 	//	Specifies a condition to be used as an inline method with insert(), select() or delete() for email column.
 	//  
 	//	example:  $example->select(array(title, content))->whereEmail('something@test.co.uk');															
	//																	   
	//  @param   String $value	
	//									
	//	@return  itself $this
	//  
	//----------------------------------------------------------------------------------------

	public function where_email($value){

		$this->sql .= " WHERE email = ? ";

		$this->values[] = $value; 

		return $this;

	}

	//-------*|   or_   |*-----------------------------------------------------------------
 	//
 	//	Specifies an extra or condition after a where method
 	//  
 	//	example:  $example->select(array(title, content))->where('id', '=', '4')->or_where('id', '=', '5');															
	//																	   
	//  @param   String $column
	//	@param   String $condition
	//  @param   String $value	
	//									
	//	@return  itself $this
	//  
	//----------------------------------------------------------------------------------------


	public function or_($column, $condition, $value){

		$this->sql .= " OR ".$column." ".$condition." ?";

		if (stristr($this->sql, 'DELETE')){
			$this->sql .= " LIMIT 1";
			return $this;
		}

		if (stristr($this->sql, 'UPDATE')){
			$this->values[] = $value;
			return $this;
		}

		$this->values[] = $value;

		return $this;

	}

		//-------*|   and_where   |*-----------------------------------------------------------------
 	//
 	//	Specifies an extra or condition after a where method
 	//  
 	//	example:  $example->select(array(title, content))->where('id', '=', '4')->or_where('id', '=', '5');															
	//																	   
	//  @param   String $column
	//	@param   String $condition
	//  @param   String $value	
	//									
	//	@return  itself $this
	//  
	//----------------------------------------------------------------------------------------


	public function and_($column, $condition, $value){

		$this->sql .= " AND ".$column." ".$condition." ?";

		if (stristr($this->sql, 'DELETE')){
			$this->sql .= " LIMIT 1";
			return $this;
		}

		if (stristr($this->sql, 'UPDATE')){
			$this->values[] = $value;
			return $this;
		}

		$this->values[] = $value;

		return $this;

	}

		//-------*|   insert   |*-----------------------------------------------------------------
 	//
 	//	Inserts rows into database
 	//
 	//  If @timestamps is true in config.php then created_at and updated_at columns will be auto updated with now()
 	//  
 	//	example:  $example->insert(array(							
	//		'title' => 'test',										
	//		'content' => 'test'										
 	//	));															
	//																	   
	//  @param   Array $table 										
	//	@return  boolean
	//  
	//----------------------------------------------------------------------------------------

	public function insert(array $input){

		if(empty($input)) return 'please enter an assoc array as array("column" => "value")';

		if($this->secure == true && isset($input['password'])){	
		$input['password'] = Secure::make($input['password']);
		}
		
		// pull keys from assoc array
		$fields = array_keys($input); 
		
		// pull values from assoc array
		$values = array_values($input); 

		// Check if table has been chosen, if not return a string telling them to choose one!
		if (!isset($this->table)) return "Please choose a table first using the selectTable function.";
		
		
		// begin query string concat......
		$sql = "INSERT INTO ".$this->table." ("; // INSERT INTO $table (

		if(count($fields) == 1){ 

			$sql .= $fields[0]."= ?"; 

		} else { 

			//loop through fields to add to query	
			for($i = 0; $i < count($fields) - 1; $i++){
				$sql .= $fields[$i].", ";
			}	
			$sql .= $fields[count($fields) - 1];

		}	

		if($this->timestamps == true){
		$sql .= ", created_at, updated_at) VALUES (";
		}

		if($this->timestamps == false){
		$sql .= " ) VALUES (";
		}	

		if(count($fields) == 1){ 

			$sql .= $fields[0]."?"; 

		} else { 

			// loop through values to add prepared statements
			for($i = 0; $i < count($values) -1; $i++){
				$sql .= "?".", ";
			}
			$sql .= "?";

		}	


		if($this->timestamps == true){
		$sql .= ", NOW(), NOW())";
		}

		if($this->timestamps == false){
		$sql .= ")";	
		}

		$this->sql = $sql;
		
		// assign values from array to bindings array that we can pass to execute.
		foreach($values as $v){
			$this->values[] = $v;
		}

 		return $this;
	
		
	}

	//-------*|   delete   |*-----------------------------------------------------------------
 	//
 	//	Delete rows from database
 	//  
 	//	example:  $example->delete(id);															
	//																	   
	//  @param   Array $input default = * 										
	//	@return  itself $this
	//  
	//----------------------------------------------------------------------------------------


	public function delete(){

	$sql = "DELETE FROM ".$this->table;

	$this->sql = $sql;

	return $this;

	}

		//-------*|   delete   |*-----------------------------------------------------------------
 	//
 	//	Delete rows from database
 	//  
 	//	example:  $example->delete(id);															
	//																	   
	//  @param   Array $input default = * 										
	//	@return  itself $this
	//  
	//----------------------------------------------------------------------------------------


	public function delete_all_where($column, $condition, $value){

	$sql = "DELETE FROM ".$this->table." WHERE ".$column." ".$condition." ?";

	$this->sql = $sql;

	$this->values[] = $value;

	return $this;

	}

		//-------*|   delete_id   |*-----------------------------------------------------------------
 	//
 	//	Delete rows from database by supplying the id of the row
 	//  
 	//	example:  $example->delete(id);															
	//																	   
	//  @param   String $id										
	//	@return  Boolean $result
	//  
	//----------------------------------------------------------------------------------------


	public function delete_id($id){

	$sql = "DELETE FROM ".$this->table." WHERE id = ? LIMIT 1";

	$this->sql = $sql;

	$this->stmt = $this->connection->prepare($this->sql);
	$result = $this->stmt->execute(array($id));

	return $result;

	}

		//-------*|   update   |*-----------------------------------------------------------------
 	//
 	//	Updates a row in database use with where(), or_where() and where_email() then tun with save().
 	//  
 	//	example:  $example->update(array('email' => 'test'))->where('id', '=', 2)->save();															
	//																	   
	//  @param   Array $input default = * 										
	//	@return  itself $this
	//  
	//----------------------------------------------------------------------------------------


	public function update(array $input){

	if($this->secure == true && isset($input['password'])){	
	$input['password'] = Secure::make($input['password']);
	}

	// pull keys from assoc array
	$fields = array_keys($input); 


		
	// pull values from assoc array
	$this->values =  array_values($input); 

	// Check if table has been chosen, if not return a string telling them to choose one!
	if (!isset($this->table)) return "Please choose a table first using the selectTable function.";

	$sql = "UPDATE ".$this->table." SET "; 


	if(count($fields) == 1){ 

		$sql .= $fields[0]."= ?"; 

	} else { 

		for($i = 0; $i < count($fields) - 1; $i++) {
			$sql .= $fields[$i]."= ?, ";
		}
		$sql .= $fields[count($fields) - 1]."= ?";

	}

	if($this->timestamps == true){
		$sql .= ", updated_at = now()";
	}

	$this->sql = $sql;

	return $this;


	}

		//-------*|   save   |*-----------------------------------------------------------------
 	//
 	//	Used to execute query
 	//  
 	//	example:  $example->insert(array('email' => 'this', 'password' => 'that'))->save();															
	//																	   
	//  										
	//	@return  boolean $result
	//  
	//----------------------------------------------------------------------------------------


	public function save(){
		$this->stmt = $this->connection->prepare($this->sql);
		$result = $this->stmt->execute($this->values);
		$this->last_id = $this->connection->lastInsertId();
		return $result;
	}


 	public function join($table, $join){

 	$this->sql .= " JOIN ".$table." ON ".$join;
 	return $this;

 	}

}



