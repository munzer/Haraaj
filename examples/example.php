<html>
<head>
<title>DUAST Frame Work</title>
<script type="text/javascript" src="../jq.js"></script>
<script type="text/javascript" src="../js/animation.js"></script>
	<link rel="stylesheet" href="../styles/rtl/style.css" type="text/css" />
	<link rel="stylesheet" href="../styles/animate.css" type="text/css" />
	
	
	<script type="text/javascript" src="fine-uploader.min.js"></script>
	
	
</head>
<body>

<?
include("../config.php");
include("DuTemplateSystem.php");
$DuTemplate = new DuTemplate();
require_once("../functions/master.php");
$sessions = new sessions();
$sessions->me();
$uid = $sessions->id;

		$SqlGet   = new SqlGet;
		$date     = time();


		require_once("../functions/Support.php");
		

		$Support = new Support;
		
		if(isset($sessions->id)){
			$Name  = "";
			$Email = "";
		}else{
			$Name  = "ahmed alrobe3y";
			$Email = "abnhamoda@gmail.com";
		}
		
		/*
		echo $Support->Send(
				array(
				"To"      => "Anyone",
				"Email"   => $Name,
				"Name"    => $Email,
				"Message" => "Hello World !!",
				"From"    => $sessions->id,
				"Date"    => time(),
				"State"   => "opened"
				)
		);
		*/
		
		print_r(
		$Support->GetMessages(
			array(
			"State" => "All"
			)
		)
		);
?>


