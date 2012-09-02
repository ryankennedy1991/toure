<?php 

require("database.php");
require('conf.php');

class User {

	protected static $table = DEFAULT_USERS_TABLE;


	public static function exists($user, $col = 'email'){

		$db = Database::make();

		$db->select_table(static::$table);

		$result = $db->select($col)->where($col, '=', $user)->row();

		return ($result[$col] == $user) ? true : false;

	}
	



}