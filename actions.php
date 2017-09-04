<?php
include("config.php");
ob_start();
header('Content-Type: text/html; charset=utf-8');
include("functions/master.php");
$sessions = new sessions();
$sessions->me();
$d        = time(); 
$MyId     = $sessions->id;
$Login    = $sessions->key;
$SqlGet   = new SqlGet;


///  Joint To The Website Process
if(isset($pg_join)){
   extract($_GET);
	if(empty($user_name)){    die("EmptyName");}
	if(empty($user_email)){   die("EmptyEmail");}
	if(empty($user_password)){die("EmptyPassword");}
	
	$user_password   = md5($user_password);
	
		if(check_string($user_email)!=="email") { die("WrongEmail"); }
		if(check_string($user_name) !=="string"){ die("NotAllowedUserName"); }
		if(strlen($user_password) < 6)          { die("ShortPassword"); }
		
	
			if(sql_count("users","user_email='$user_email' ") > 0){
				die("ExistEmail");
			}else{
				$SessionKey       = time().rand('0','99999').substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 10)), 0, 10);
				$ActiveAcountCode = rand('10000','900000');
				$DefaultThumb = "DefaultThumb.png";
				if(insert("users",array(
				"user_name"              => $user_name,
				"user_email"             => $user_email,
				"user_acount_activation" => "NotActive",
				"user_acount_type"       => "NormalUser",
				"user_password"          => $user_password,
				"user_thumb"             => $DefaultThumb,
				"user_join_date"         => $d,
				"user_session"           => $SessionKey,
				"user_active_code"       => $ActiveAcountCode
				)) == true){
				$sessions->creat("$user_email");
					// Get Email Handler Class To Send The Activation Code To It
					require_once("functions/EmailHandlerClass.php");
					$EmailHandler = new EmailHandler;
					
					
					// Set Lang For Send Email
					include("lang/ar.php");
					$L = new Lang;
					$L->Set("lang/ar.php");
					
					// Activation Link
					$ActivationLink = "<a href='" . 
								settings("SiteDomain") . 
								"/Activation/?accessToken=".$_SESSION["key"]."' >" .
								$L->Get("click_here_to_active_your_acount") .
								"</a>".$L->Get("or_user_this_code") . " " . $ActiveAcountCode;
					
					// Build A New Email Handler Template Before Send It
					$EmailTemplate = $EmailHandler->MakeTemplate(array(
					"TemplateLink" => "templates/EmailsTemplates/Prime.html",
					"KeyWords"     => array(
							"TITLE"        => $L->Get("account_active"),
							"WEBSITENAME"  => settings("SiteName"),
							"LOGO"         => settings("SiteDomain") . "/img/logo.png",
							"CONTENT"      => $ActivationLink,
							"FOOTER"       => $L->Get("email_footer_message")." ".settings("SiteName")
					)));
					
					// Send !
					$EmailHandler->Send(array(
					"To"      => $user_email,
					"Title"   => $L->Get("account_active"),
					"Message" => $EmailTemplate
					));
					
					
				echo "true";
				}
			}
}
	
// Activation Acount By Code
if(isset($pg_ActiveMyAcount) && $Login){
	if (check_string($pg_ActiveMyAcount) == "int"){
		if(sql_count("users","user_session='".$sessions->key."' && user_active_code='".$pg_ActiveMyAcount."'")){ 
			echo update("users",array("user_acount_activation"=>"Active")," user_session='".$sessions->key."' ");
		}else{
			echo"false";
		}
	}else{
		echo"false";
	}
}

if(isset($pg_login) && !empty($pg_user_email) && !empty($pg_user_password)){
if(check_string($pg_user_email)=="email"){ $cond = "user_email ='$pg_user_email' ";}else
if(check_string($pg_user_email)=="string"){ $cond = "user_username='$pg_user_email' ";}

$pg_password_md5 = md5($pg_user_password);
	
/// Look If User Exist
	if(sql_count("users"," user_password='$pg_password_md5' || user_password='$pg_user_password'  && $cond") > 0){
		/// التحقق من ان العضوية غير محظورة من الدخول
		if(sql_count("users","user_password='$pg_password_md5' || user_password='$pg_user_password' && user_acount_activation !='Blocked'  && $cond") > 0){
		$sessions->creat("$pg_user_email");
		echo"true"; 
		}else{
		echo "BlockedUser";	
		}
	}else{
		echo 'false';
	}
}

/// Edit WebSite Settings Fast
if($sessions->login && $sessions->user_acount_type=="Admin" && isset($pg_SetWebSiteSettings)){
	
	/// Warring !! Not Permition Yet , Every One Can Change Settings
	if(isset($pg_Key) && !empty($pg_Key) && isset($pg_Value)){
		$Key   = clean_string($pg_Key);
		$Value = clean_string($pg_Value);
		echo update("settings",array("set_Value"=>$Value)," set_Key='$Key' ");
	}
	
}

/// Edit My Information Fast
if($sessions->login && isset($pg_EditMyInfo)){
	include("lang/ar.php");
	$L = new Lang;
	$L->Set("lang/ar.php");

	$Key   = $pg_key;
	$value = clean_string($pg_value);
		if(!empty($Key) && !empty($value)){
		 
			if ($Key == "user_email" ){
				if(sqlexist("users"," user_email='$value' ")){
					$L->Key("email_exist");
					die("");
				}else{
					if(check_string($value)!=="email"){
						$L->Key("incorrect_email");
						die("");
					}
				}
			}else  if ($Key == "user_phone" ){
				
				if(check_string($value)!=="int"){
					$L->Key("incorrect_phone"); 
					die();
				}
				
			}else  if ($Key == "user_birth_date" ){
				
				$value = $_GET["value"];
				if (!preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/",$value)){
				$L->Key("error");echo" , ";$L->Key("birth_date_roll");
				die("");
				}
				
				$value = strtotime(str_replace('/', '-',$value));

	    
			}else  if ($Key == "user_username" ){
					if(sqlexist("users"," user_username='$value' ")){
						$L->Key("username_exist");
						die("");
						}else{
							if(check_string($value)!=="string"){
								$L->Key("not_allowed_username");
								die("");
							} 
						}
			}
			
			if(update("users",array($Key=>"$value")," user_id='$MyId' ")){
				echo "true";
			}else{
				echo "un_knowed_error";
			}
		}
}

/// Change Thumb
if($sessions->login && isset($pg_ChangThumb)){
	require_once("functions/UploadClass.php");
	$Upload = new Upload;
	
		$Date  = time();
		$year  = date('Y', $Date);
		$month = date('m', $Date);
		$day   = date('d', $Date);
	
			$UploadThumb          = $Upload->NewImage(array(
			"name"                =>time(),
			"dir"                 =>"thumb/xxl/$year/$month",
			"files"               => $_FILES["file"],
			"MaxHeight"           => "500",
			"MaxWidth"            => "500",
			"Return"              => "Details",
			"AllowedExtintions"   => array("gif", "jpeg", "jpg", "png")
			));	
			
			$CreateMiniThumb      =  $Upload->CopyImage(array(
			"ImgDir"              => $UploadThumb["Path"],
			"CopyDir"             => "thumb/xl/$year/$month",
			"NewName"             =>  $UploadThumb["Name"],
			"MaxHeight"           => "100",
			"MaxWidth"            => "100"
			));
	
				$ThumbLink = substr($UploadThumb["Path"], strpos($UploadThumb["Path"],"xxl/")+4 );
					update("users",array("user_thumb"=>$ThumbLink)," user_session='".$sessions->key."' ");
						echo json_encode($UploadThumb);
				
}

/// Upload Categorie Thumb
if($sessions->login && isset($pg_UploadPostThumb)){
	require_once("functions/UploadClass.php");
	$Upload = new Upload;
	
		$Date  = time();
		$year  = date('Y', $Date);
		$month = date('m', $Date);
		$day   = date('d', $Date);
	
			$UploadThumb          = $Upload->NewImage(array(
			"name"                => time(),
			"dir"                 =>"images/xxl/$year/$month",
			"files"               => $_FILES["file"],
			"MaxHeight"           => "800",
			"MaxWidth"            => "800",
			"Return"              => "Details",
			"AllowedExtintions"   => array("gif", "jpeg", "jpg", "png")
			));	
	
	
			$CreateMiniThumb      =  $Upload->CopyImage(array(
			"ImgDir"              => $UploadThumb["Path"],
			"CopyDir"             => "images/xl/$year/$month",
			"NewName"             =>  $UploadThumb["Name"],
			"MaxHeight"           => "200",
			"MaxWidth"            => "200"
			));
			
						echo json_encode($UploadThumb);
}

/// Upload Post Thumb
if($sessions->login && isset($pg_ChangCatThumb)){
	require_once("functions/UploadClass.php");
	$Upload = new Upload;
	
		$Date  = time();
		$year  = date('Y', $Date);
		$month = date('m', $Date);
		$day   = date('d', $Date);
	
			$UploadThumb          = $Upload->NewImage(array(
			"name"                =>time(),
			"dir"                 =>"images/uploaderIcons/$year/$month",
			"files"               => $_FILES["file"],
			"MaxHeight"           => "80",
			"MaxWidth"            => "80",
			"Return"              => "Details",
			"AllowedExtintions"   => array("gif", "jpeg", "jpg", "png")
			));	
	
	
						echo json_encode($UploadThumb);
}

/// ReSend ActivationCode
if(isset($pg_ResendActivation)){
$ActiveCode =  $condb->query("select user_active_code from users WHERE user_session='".$sessions->key."' LIMIT 1")->fetch_array(MYSQLI_ASSOC)["user_active_code"];
$ActiveCode = "رمز التفعيل الخاص بك هو :".$ActiveCode;

if(isset($_SESSION["ActiveCodeDelay"]) AND $d - $_SESSION["ActiveCodeDelay"] < 600){
	echo "SlowDown";
}else{
	if(SendEmail($sessions->email,"تفعيل حسابك",$ActiveCode)){
	$_SESSION["ActiveCodeDelay"] = time();
	echo"resended";
	}
}
}


/// Get New Notification (notifications,messages) On Button Bubble
if($sessions->login && isset($pg_GetNewNotifications)){
$NotificationCount =  sql_count("notifications"," notif_recever='$sessions->id' && notif_read='0' ");
$MessageCount      =  sql_count("messages"," message_to='$sessions->id' && message_read='0' ");
echo json_encode(array("NotificationsCount"=>$NotificationCount,"MessagesCount"=>$MessageCount));
}


/// Get Template
if(isset($pg_DuBuildTemplate)){
$Field    = $pg_field;
$Table    = $pg_table;
$LimitFrom= $pg_limit_from;
$LimitNum = $pg_limit_num;
$SortBy   = $pg_sort_by;
$SortType =  $pg_sort_type;
$Filter   = decodeUrlFilter($pg_filter);

	$ReturnedAtrray = array();
	if(!empty($Table)
		&& !empty($Field)
		&& $LimitNum!==""
		&& $LimitFrom!==""
		&& !empty($SortBy)
		&& !empty($SortType)){
		/// Stop Getting User Sensitive information 
		if(strpos($Field,'user_password') !== false
		|| strpos($Field,'user_email')    !== false
		&& $sessions->user_acount_type !=="Admin"
		&& $sessions->user_acount_type !=="Supervisor"
		){
			die("AuthorizationFailure");
		}
		
		if($SortType=="topToBottom"){
		$LimitCond = "DESC LIMIT $LimitFrom,$LimitNum";
		}		
		
		if(!empty($Filter)){ 
			$Condition = " WHERE ".$Filter;
		}else{
			$Condition = " ";
		}
		
		if($SortType=="Bottom"){
		
		if($LimitFrom<$LimitNum){
			if($pg_limit_from>0){
				$LimitFrom = 0;
				$LimitNum  =  $LimitNum-$LimitFrom;
			}else{
				$LimitNum  = 0;
				$LimitFrom = 0;		
			}
		}else{
			$LimitFrom = $LimitFrom - $LimitNum;
		}
		//echo "0,".$LimitNum;
		

		$LimitCond = "ASC LIMIT $LimitFrom,$LimitNum";
		}
		
		$GetData  = $condb->query("select $Field from $Table $Condition ORDER By $SortBy $LimitCond");
		if($GetData->num_rows<1){
			echo "NoResult";
		}else{
			while($DataFetch = $GetData->fetch_array()){
					if($Table == "messages"){
						
		
						///  Come From User (Message)
							$DataFetch["user_name_from"] = $SqlGet->userinfo($DataFetch["message_from"],"user_name");	
						
						
			
						///  To User (Message)
							$DataFetch["user_name_to"]   = $SqlGet->userinfo($DataFetch["message_to"],"user_name");
							
						
						
						///  Come From User (Thumb)
							$DataFetch["user_thumb_from"] = $SqlGet->userinfo($DataFetch["message_from"],"user_thumb");
								
						

						/// To User (Thumb)
							$DataFetch["user_name_to"] = $SqlGet->userinfo($DataFetch["message_to"],"user_thumb");
							

						
						$DataFetch["message_time"]   = date('M  d  h:i',$DataFetch["message_date"]);	

						
						if($DataFetch["message_to"]==$sessions->id){
							$DataFetch["bubbleColor"] = "#BDF271";
							if($DataFetch["message_read"]=="0"){
								$DataFetch["SeenIcon"]    = "true.png";
							}else{
								$DataFetch["SeenIcon"]    = "double_true.png";
							}
						}else{
							$DataFetch["bubbleColor"] = "#FFFFA6";
							$DataFetch["SeenIcon"]    = "eye.png";
						}
					}
				$ReturnedAtrray[] = $DataFetch; 
			}
			///print_r($ReturnedAtrray);
			echo json_encode($ReturnedAtrray);
		}
		
		}
	
}


/// Contact Us Send Message
if(isset($pg_Contact)){
require_once("functions/Support.php");
$Support = NEW Support;
	
	// Check If User Login
	if(!$sessions->login){
		$Name        = $pg_name;
		$Email       = $pg_email;
		$From        = $pg_email;
	}else{
		$Name        = "";
		$Email       = "";
	}
	
	$Message     = $pg_message;
	
		echo $Support->Send(
			array(
			"To"      => "Anyone",
			"Email"   => $Email,
			"Name"    => $Name,
			"Message" => $Message,
			"Date"    => time(),
			"State"   => "opened"
			)
		);
	
}

/// Send Replay
if(isset($pg_ReplaySupport)){
	require_once("functions/Support.php");
	$Support = NEW Support;
	
		$Serial   = $pg_Serial;
		$Message  = $pg_Message;
		
			echo $Support->Reply(array(
				"Serial"  => $Serial,
				"Message" => $Message
			));
}

// Close The Ticket
if($sessions->login && isset($pg_CloseTicket)){
	require_once("functions/Support.php");
	$Support = NEW Support;
		$Serial   = $pg_Serial;
			echo $Support->CloseTicket($Serial);
}


/// Content Manegment (QaS)
if($sessions->login && $sessions->user_acount_type=="Admin" && isset($pg_QaS)){
	// Create A Serial Numbers For Quition
	$Serial       = time()."".rand("1","200");
		foreach(json_decode($_GET["value"],true) AS $K=>$V){
			$ContentArray = serialize($V);
				insert("info",array(
				"info_type"    => "Qas",
				"info_value"   => $ContentArray,
				"info_lang"    => $V["Lang"],
				"info_serial"  => $Serial,
				"info_date"    => time()
				));
		}
}

/// Delete Content (QaS)
if($sessions->login && $sessions->user_acount_type=="Admin" && isset($pg_DeletQas)){
	$Serial = $pg_Serial;
	if(!empty($Serial)){
		echo $SqlGet->Delete("info"," info_serial='".$Serial."' ");
	}
}

/// Edit Terms And Privacy
if($sessions->login && $sessions->user_acount_type=="Admin" && isset($pg_EditTermsAndPrivacy)){
	if($sessions->user_acount_type="Admin"){
		
			foreach(json_decode($pg_val,true) AS $K=>$V){
			if(sql_count("info"," info_type='".$pg_Fild."' && info_lang='". $V["Lang"] ."' ") > 0){
					update("info",array(
					"info_value"   => htmlentities($V["Val"]),
					"info_date"    => time()
					)," info_type='".$pg_Fild."' && info_lang='". $V["Lang"] ."' ");
			}else{
					insert("info",array(
					"info_type"    => $pg_Fild,
					"info_value"   => htmlentities($V["Val"]),
					"info_lang"    => $V["Lang"],
					"info_date"    => time()
					));
			}
		}
	}
}

/// Management Users
if(isset($pg_UsersManagement)){
	$Param  = $pg_param;
	$Value  = $pg_val;
	$UserId = $pg_user_id;
	
		if(!empty($Param)){
			
			/// Block And Un Block
			if($Param == "user_account_state"){
				if($sessions->user_acount_type == "Admin" && $UserId !== $sessions->id ){
					echo update("users" , array("user_account_state"=>$Value)," user_id='" .$UserId. "' ");
				}
			}	

			
			/// Change Rank
			if($Param == "user_acount_type" && $Value !== ""){
				if($sessions->user_acount_type == "Admin" && $UserId !== $sessions->id ){
					echo update("users" , array("user_acount_type"=>$Value)," user_id='" .$UserId. "' ");
				}
				
			}
		}
}


/// IM Messaging Querys And Oparations
if($sessions->login && isset($pg_SendNewMessage)){
	$Mes     = NEW Messages;
	$Message = $pg_Message;
	$To      = $pg_To;
				echo $Mes->Send(array(
					 "message" => $Message,
					 "to"      => $To,
					 "type"    => "",
					 "extra"   => ""
					));
}

/// Reset The Password Process
/// STEP 1 : -> Check If User Exist
if(isset($pg_ChekEmailForReset)){
	$user_email = $pg_user_email;
		if(sql_count("users","user_email='$user_email' ") < 1){
			echo"NoUserFound";
		}else{
			$Id    = $SqlGet->UserId("user_email='$user_email'");
			$Thumb = $SqlGet->userinfo($Id,"user_thumb");
			$Name  = $SqlGet->userinfo($Id,"user_name");
			echo json_encode(array("name"=>$Name,"thumb"=>$Thumb));
			
			/// Now Create Session By Email That We Use To Reset (To Pass It To Next Process)
			$_SESSION["EmailForReset"] = $user_email;
		}
}/// STEP 2 : -> Send Change Pass Email
if(isset($pg_ResetPassword)){
	$user_password = md5($pg_user_password);
	$user_email    = $_SESSION["EmailForReset"];
	$Id            = $SqlGet->UserId("user_email='$user_email'");
	$SessionId     = $SqlGet->userinfo($Id,"user_session");

echo $changePasswordLink = "WebSite.com/passwordReset/?accessToken=" . $SessionId . "&NewPassword=".$user_password;
}




/// Start Management OF Categories (Delete,Add,Edit)
if($sessions->login && $sessions->user_acount_type=="Admin" && isset($pg_ManagCategories)){
	$Categories = new Categories;
		
		
			
			/// Initializing The Language File
			$L = new Lang;
			$L->Set("lang/ar.php");
			
			// Create Multi Langs Categorie If Multi Lang Active
			if(settings("MultiLangs")) {
			
				$AppendArray = array();
				
				foreach($L->GetAvilbleLangs() AS $K=>$V){
					$AppendArray[$K] = $_GET["title-$K"];
				}
				
				$AppendArray = serialize($AppendArray);
			}else{
				$AppendArray = serialize(array($L->MyLang => $_GET["title-".$L->MyLang]));
			}
			
			// Edit Categorie If Isset The Id , Else It Mein Create New One
			if(empty($pg_id)){
			echo $Categories->Create(
							array(
								"Parent" => $pg_parent,
								"Title"  => $AppendArray,
								"Type"   => $pg_type,
								"Thumb"  => $pg_thumb
							)
						);
			}else{
			echo $Categories->Update(
					array(
						"Id"     => $pg_id,
						"Parent" => $pg_parent,
						"Title"  => $AppendArray,
						"Type"   => $pg_type,
						"Thumb"  => $pg_thumb
					)
					);	
			}
}

if($sessions->login && $sessions->user_acount_type=="Admin" && isset($pg_deleteCat) && !empty($pg_deleteCat)){
	$Categories = new Categories;
	$Categories->Remove(array(
		"Id" => $pg_deleteCat
	));
}
/// End   Management OF Categories (Delete,Add,Edit)



/// Posts Management
if($sessions->login && isset($pg_AddOrEditPost)){

	if(empty($pg_serial)){
		
		echo insert("content",
				array(
					"content_serial"  => $pg_serial,
					"content_cat"     => $pg_cat,
					"content_title"   => $pg_title,
					"content_thumb"   => $pg_thumb,
					"content_content" => htmlentities($pg_content),
					"content_date"    => time(),
					"content_user"    => $sessions->id,
					"content_state"   => "posted"
				)
			);
		
	}else{
		
			echo update("content",
				array(
					"content_cat"     => $pg_cat,
					"content_title"   => $pg_title,
					"content_thumb"   => $pg_thumb,
					"content_content" => htmlentities($pg_content),
					"content_date"    => time(),
					"content_state"   => "posted"
				)," content_serial = '".$pg_serial."' "
			);
		
	}
}


if(isset($pg_countThis)){
	echo sql_count($pg_Table,decodeUrlFilter($pg_Filter));
}


///// Change InterFace Language
if(isset($pg_ChangeSiteLang) && !empty($pg_ChangeSiteLang)){
	$L = new Lang;
		if (array_key_exists($pg_ChangeSiteLang,$L->GetAvilbleLangs())) {
			$_SESSION["MyLang"] = $pg_ChangeSiteLang;
			echo true;
		}
}

	/*************************************[ The Debugging System ]*******************************/
	if(settings("DebuggingCallBacks")){
	 // Store buffer in variable
	$Text   = "<span style='color:#4B8BF4;'> Page: </span></br>".$_SERVER['REQUEST_URI']." </br> <span style='color:#4B8BF4;'> Response: </span> </br> ".ob_get_contents() . "</br>";
	
	 // add Buffer Fo File
	$myfile = file_put_contents('Devolopers/LastCallsBackDebugging.html',$Text);
	}
	/*************************************[ The Debugging System ]*******************************/
?>