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
	
	echo insert("pircelist",array(
			"pirce_list_cat"   =>$pg_cat,
			"pirce_list_type"  =>$pg_name,
			"pirce_list_price" =>$pg_price
		));
	
}else if(isset($pg_Delet)){
	
	echo $SqlGet->Delete("pircelist"," pirce_list_id='$pg_Delet' ");
	
}else if(isset($pg_Update)){

	$pg_value   = clean_string($pg_value);
	$pg_Update  = clean_string($pg_Update);
	$pg_item    = clean_string($pg_item);
	
	echo update("pircelist",array($pg_item => $pg_value)," pirce_list_id='$pg_Update' ");	
}


?>