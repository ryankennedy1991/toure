<?php 

function load($classname){

		$dir = __DIR__;
		$classname = strtolower($classname);
		$require = $dir.'/'.$classname.'.php';
		include($require); 
	}

spl_autoload_register('load');

?>