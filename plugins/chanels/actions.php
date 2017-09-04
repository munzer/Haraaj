<?php
include("../../config.php");
header('Content-Type: text/html; charset=utf-8');
include("../../functions/master.php");
$sessions = new sessions();
$sessions->me();
$d        = time(); 
$MyId     = $sessions->id;
$SqlGet   = new SqlGet();

if(isset($pg_AddNew)){
	
	$pg_cat   = clean_string($pg_cat);
	$pg_name  = clean_string($pg_name);
	$pg_price = clean_string($pg_price);
	
	echo insert("chanels",array(
			"Chanel_cat"   =>$pg_cat,
			"Chanel_type"  =>$pg_name,
			"Chanel_code" =>$pg_price,
			"Chanel_user" =>$sessions->id,
		));
	
}else if(isset($pg_Delet)){
	
	echo $SqlGet->Delete("chanels"," Chanel_id='$pg_Delet' ");
	
}else if(isset($pg_Update)){

	$pg_value   = clean_string($pg_value);
	$pg_Update  = clean_string($pg_Update);
	$pg_item    = clean_string($pg_item);
	
	echo update("chanels",array($pg_item => $pg_value)," Chanel_id='$pg_Update' ");	
}


?>