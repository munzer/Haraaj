<?
ob_start(); 
/*
	require_once("../functions/UploadClass.php");
	$Upload = new Upload;
	
		$Date  = time();
		$year  = date('Y', $Date);
		$month = date('m', $Date);
		$day   = date('d', $Date);
	
			$UploadState           = $Upload->NewImage(array(
			"name"                 =>time(),
			"dir"                  =>"images/$year/$month",
			"files"                => $_FILES["file"],
			"Return"               => "Details",
			"AllowedExtintions"    => array("gif", "jpeg", "jpg", "png")
			));
	
	echo json_encode($UploadState);
	
	
	*/
	
	echo "Fuck";
	echo "You";
	
$Text   = "<span style='color:#4B8BF4;'> Page: </span></br>".$_SERVER['REQUEST_URI']." </br> <span style='color:#4B8BF4;'> Response: </span> </br> ".ob_get_contents() . "</br>"; // Store buffer in variable
$myfile = file_put_contents('../Devolopers/LastCallsBackDebugging.html',$Text);

?>