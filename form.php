<?php 

class Form  {
	

	public static function inputToVars($input = array(), $form = array()){

		foreach($input as $done){
			foreach($form as $forms){
			$GLOBALS[$done] = $forms[$done];
			}
		}



	}






}