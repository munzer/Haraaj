<?


Class DuTemplate{
	
	function build($Template,$ContentArray){
	$Template     = "templates/".$Template.".php";
	$ContentArray = $ContentArray;
	require_once("$Template");
	
	}	
	
}


?>