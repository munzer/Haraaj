<?php
include("../../config.php");
header('Content-Type: text/html; charset=utf-8');
include("../../functions/master.php");
$sessions = new sessions();
$sessions->me();
$d        = time(); 
$MyId     = $sessions->id;
$SqlGet   = new SqlGet();

if(isset($pg_ConfirmOrder)){
	
	echo update("reservation",array('reservation_state' => 'eccepted')," reservation_id='$pg_ConfirmOrder' ");
	
}else if(isset($pg_deleteOrder)){
	
	$SqlGet->Delete("reservation"," reservation_id='$pg_deleteOrder' ");
	
}else if(isset($pg_new)){
	
extract($_GET);
   
   if(!isset($reservation_requirements)){$reservation_requirements="-";}
	if(insert("reservation",array(
				"reservation_type"             =>$reservation_type,
				"reservation_day"              =>$reservation_day,
				"reservation_time"             =>$reservation_time,
				"reservation_count"            =>$reservation_count,
				"reservation_requirements"     =>$reservation_requirements,
				"reservation_user"      	   =>$MyId,
				"reservation_date"      	   =>$d
				)) == true){
					echo"true";
					
				}
					
}


?>