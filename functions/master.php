<?php



/// التعامل مع البوست والجيت
extract(array_merge($_POST,$_GET) , EXTR_PREFIX_ALL, "pg" );

/// التحقق من مرجع الدوال بالفنكشنس بالدالة empty
function is_empty($var) {  return empty($var); }

/// Function To append Style To Header 
function appendStyle($Style){
	if (strpos($Style, 'rtl') !== false) {
	    echo "<head><link rel='stylesheet' href='$Style' type='text/css' /></head>";
	}else{
		$NewStyle         = str_replace("ltr","rtl",$Style);
		$OldFilesize      = filesize($NewStyle);
		
		//read the entire string
		$GetOriginalStyle = file_get_contents($NewStyle);
		
		//replace something in the file string - this is a VERY simple example
		$GetOriginalStyle = str_replace("right","XXRIGHT",$GetOriginalStyle);
		$GetOriginalStyle = str_replace("left","YYLEFT",$GetOriginalStyle);
		
		$GetOriginalStyle = str_replace("XXRIGHT","left",$GetOriginalStyle);
		$GetOriginalStyle = str_replace("YYLEFT","right",$GetOriginalStyle);
		$GetOriginalStyle = str_replace("rtl","ltr",$GetOriginalStyle);

		//write the entire string
		file_put_contents($Style,$GetOriginalStyle);
		echo "<head><link rel='stylesheet' href='$Style' type='text/css' /></head>";
	}
	
	
	
	}

/// Function To append JavaScript File Inside Document
function appendJs($JsFile){echo "<script type='text/javascript' src='$JsFile'></script>";}

/// Function To append Title Of Page
function appendTitle($Title){echo "<script> document.title='".$Title."'; </script>";}

/// Function To Go To Page
function GoToPage($Page){ header("location:$Page"); die(); }


/////////////////////////////////////////// مصفوفات الخصائص //////////////////////////////////////////
function MackePropertiesArr($array){
global $condb;
$array = serialize($array);
$array = mysqli_real_escape_string($condb,htmlspecialchars($array));
return $array;
}

function ExtractPropertiesArr($array){
$array   = html_entity_decode($array);
$array   = unserialize($array);
return $array;
}	

function EditItemPropertiesArr($array,$key,$val){
if(isset($array[$key])){
$array[$key] = $val;
return $array;
}else{
return false;
}
}
/////////////////////////////////////////// مصفوفات الخصائص //////////////////////////////////////////
	
/// تنظيف النصوص من الاتش تي ام ال
function clean_string($string){
$invalid_characters = array("$", "%", "#", "<", ">", "|");
		$flter_string = str_replace($invalid_characters, "", $string);
		return $flter_string;
}
/// تنظيف النصوص من الاتش تي ام ال
function clean_marks($string){
return strtolower(str_replace(array('  ', ' '), '_', preg_replace('/[^a-zA-Z0-9 s]/', '_', trim($string))));
}

function convert_space($string){
return str_replace(" ","_", $string);
}

function securInsert($string){ return addslashes($string); }
function securGet($string){    return stripslashes($string); }



	/// فحص العناصر
	function check_string($string){
	$string = clean_string($string);
	if(filter_var($string, FILTER_VALIDATE_EMAIL)) { $type = "email"; }
	else if (is_numeric($string))                                      { $type = "int";  }
	else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $string)){ $type = "html";  }
	else     { $type = "string";}
	return $type;
	}

				
	function encodeUrlFilter($query){
	$FILTER = $query;

	$FILTER = str_replace(">","[>]",$FILTER);
	$FILTER = str_replace("<","[<]",$FILTER);
	$FILTER = str_replace("!=","[!=]",$FILTER);
	$FILTER = str_replace("=","[=]",$FILTER);
	$FILTER = str_replace(" && ","[AND]",$FILTER);
	$FILTER = str_replace(" || ","[OR]",$FILTER);
	$FILTER = str_replace(" AND ","[AND]",$FILTER);
	$FILTER = str_replace(" OR ","[OR]",$FILTER);
	$FILTER = str_replace(" IN ","[IN]",$FILTER);
	$FILTER = str_replace(" LIKE ","[SearchQuery]",$FILTER);
	
	$FILTER = str_replace("'","QU__",$FILTER);
	$FILTER = str_replace("'","__QU",$FILTER);
  
	
	$FILTER = str_replace(" IN ","[IN]",$FILTER);

	$FILTER = str_replace("ORDER BY","ORDER=",$FILTER);
	$FILTER = str_replace(" ASC ","[UP]",$FILTER);
	$FILTER = str_replace(" DESC ","[DOWN]",$FILTER);
	$FILTER = str_replace(" asc ","[UP]",$FILTER);
	$FILTER = str_replace(" desc ","[DOWN]",$FILTER);

	$FILTER = str_replace("LIMIT"," LIMITFORM= ",$FILTER);
	$FILTER = str_replace("limit"," LIMITFORM= ",$FILTER);
	$FILTER = str_replace("Limit"," LIMITFORM= ",$FILTER);
	
	
	return $FILTER;
}
	
function decodeUrlFilter($query){
$ReturnedVal = "";

  $var  = html_entity_decode($query); 	
  $var  = explode('&', $var);
  $FILTER = $var[0];
  $FILTER = str_replace("FILTER=","",$FILTER);

  
  
  $FILTER = str_replace("[SearchQuery]"," LIKE ",$FILTER);
  $FILTER = str_replace("[IN]"," IN ",$FILTER);
  $FILTER = str_replace("[Quate]"," = ",$FILTER);
  $FILTER = str_replace("[=]"," = ",$FILTER);
  $FILTER = str_replace("[!=]"," != ",$FILTER);
  
  $FILTER = str_replace("[<]"," < ",$FILTER);
  $FILTER = str_replace("[>]"," > ",$FILTER);
  
  
  
  
  $FILTER = str_replace("[AND]"," AND ",$FILTER);
  $FILTER = str_replace("[OR]"," OR ",$FILTER);
  
  
  $FILTER = str_replace("QU__","'",$FILTER);
  $FILTER = str_replace("__QU","'",$FILTER);
  

		
   
	$FILTER = str_replace("[GROUP=]"," GROUP BY ",$FILTER);
	$FILTER = str_replace("[ORDER=]"," ORDER BY ",$FILTER);
	$FILTER = str_replace("[UP]"," ASC ",$FILTER);
	$FILTER = str_replace("[DOWN]"," DESC ",$FILTER);
	
			$FILTER = str_replace("[LIMITFORM=]"," LIMIT ",$FILTER);
			//$FILTER = explode("LIMIT",$FILTER)[0]." LIMIT ".str_replace("'","",explode("LIMIT",$FILTER)[1]);
			


		return $FILTER;


}


/// عد حقل معين من اس كيو ال
function sql_count($table,$rows){
global $condb;
if(empty($rows)){
	$condition = "";
}else{
	$condition = " WHERE $rows ";
}
$count_rows = $condb->query("SELECT * FROM $table $condition ");
return $count_rows->num_rows;	
}

function sqlexist($table,$cond){ global $condb; $exist = $condb->query("select * FROM $table WHERE $cond");
($exist->num_rows > 0) ? $exist_return = true : $exist_return = false; return $exist->num_rows; }

/// Delete From Sql
function delete($table,$Condition){
global $condb;
    // build the query
    $sql = "Delete From ".$table." WHERE ".$Condition;
    // run and return the query result resource
    if($condb->query($sql)){return "true";}else{return  "false";}
   
}


/// Insert Into DB
function insert($table,$rows){
global $condb;

    // retrieve the keys of the array (column titles)
    $fields = array_keys($rows);
    // build the query
    $sql = "INSERT INTO ".$table."
    (`".implode('`,`', $fields)."`)
    VALUES('".implode("','", $rows)."')";
    // run and return the query result resource
    if($condb->query($sql)){return "true";}else{return  "false";}
   
}

// Update Info DB
function update($table,$rows,$cond){

    // check for optional where clause
    $whereSQL = '';

    // start the actual SQL statement
    $sql = "UPDATE ".$table." SET ";

    // loop and build the column /
    $sets = array();
    foreach($rows as $column => $value)
    {
         $sets[] = "`".$column."` = '".$value."'";
    }
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL." WHERE ".$cond;

    
global $condb;
if($condb->query($sql)){ return "true"; }else{return "false";}
}



class sessions{
public $key;
public $id;
public $login;
public $name;
public $country;
public $gender;
public $email;
public $thumb;
public $minethumb;
public $username;
public $fbid;
public $login_key;
public $bio;
public $AcountActivation;
public $user_permission;
public $user_acount_type;

///جلب معلومات عضو باسم المستخدم
public $UserInfoUser;
public $UserInfoName;
public $UserInfoEmail;
public $UserInfoAdsCode;
public $UserInfoBio;

		function creat($UserNameOrEmail){
			global $condb;
			$sessionCode = md5(uniqid(rand(), true));
			if(check_string($UserNameOrEmail)=="email") { $cond = "user_email ='$UserNameOrEmail' ";}else
			if(check_string($UserNameOrEmail)=="string"){ $cond = "user_username='$UserNameOrEmail' ";}
				$condb->query("UPDATE users SET user_session='$sessionCode' WHERE $cond ");
					ini_set('session.gc_maxlifetime', 10000*60*60); // 3 hours
					ini_set('session.gc_probability', 1);
					ini_set('session.gc_divisor', 100);
					$_SESSION["key"] = $sessionCode;
							return true;
		}

function get($attr){  if(isset($_SESSION["key"])){ return $this->key = $_SESSION["$attr"]; }  }

	public function me(){
		if($this->get("key")){
			global $condb;
			$G_myInfo           = $condb->query("select * from users WHERE user_session ='".$_SESSION['key']."' LIMIT 1");
			if($G_myInfo->num_rows>0){
			$G_myInfo_fetch     = $G_myInfo->fetch_array(MYSQLI_ASSOC);
			$this->login 	      = true;
			$this->id 	      = $G_myInfo_fetch["user_id"];
			$this->email 	      = $G_myInfo_fetch["user_email"];
			$this->thumb 	      = "thumb/larg/".$G_myInfo_fetch["user_thumb"];
			$this->mini_thumb   = "thumb/mini/".$G_myInfo_fetch["user_thumb"];
			$this->username     = $G_myInfo_fetch["user_name"];
			$this->name         = $G_myInfo_fetch["user_name"];
			$this->user_acount_activation = $G_myInfo_fetch["user_acount_activation"];
			$this->user_session     = $G_myInfo_fetch["user_session"];
			$this->user_acount_type = $G_myInfo_fetch["user_acount_type"];
			$this->AcountActivation = $G_myInfo_fetch["user_acount_activation"];
			}else{
			$this->login 	           = false;	
			$this->user_acount_type  = false;	
			}
		}
		}
		function end(){  session_destroy();  }

}




	
function getBrowser()
{
    $u_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
      elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
        if (preg_match('/NT 6.2/i', $u_agent)) { $platform .= ' 8'; }
            elseif (preg_match('/NT 6.3/i', $u_agent)) { $platform .= ' 8.1'; }
            elseif (preg_match('/NT 6.1/i', $u_agent)) { $platform .= ' 7'; }
            elseif (preg_match('/NT 6.0/i', $u_agent)) { $platform .= ' Vista'; }
            elseif (preg_match('/NT 5.1/i', $u_agent)) { $platform .= ' XP'; }
            elseif (preg_match('/NT 5.0/i', $u_agent)) { $platform .= ' 2000'; }
        if (preg_match('/WOW64/i', $u_agent) || preg_match('/x64/i', $u_agent)) { $platform .= ' (x64)'; }
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/opera/i',$u_agent))
    {
        $bname = 'internet explorer';
        $ub = "MSIE";
    }
    else if(preg_match('/Trident/i', $u_agent) && !preg_match('/opera/i',$u_agent))
    { /// explore 11
        $bname = 'internet explorer';
        $ub = "internet explorer";
    }
    else if(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'mozilla firefox';
        $ub = "firefox";
    }   
    else if(preg_match('/opr/i',$u_agent))
    {
        $bname = 'opera';
        $ub = "opera";
    } 
    else if(preg_match('/chrome/i',$u_agent))
    {
        $bname = 'google chrome';
        $ub = "chrome";
    }
    else if(preg_match('/safari/i',$u_agent))
    {
        $bname = 'apple safari';
        $ub = "safari";
    }
    else if(preg_match('/opera/i',$u_agent))
    {
        $bname = 'opera';
        $ub = "opera";
    } 

    else if(preg_match('/netscape/i',$u_agent))
    {
        $bname = 'netscape';
        $ub = "netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= @$matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
   
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
   
    return array(
        'userAgent' => $u_agent,
        'name'      => $ub,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}



function time_about($before){
$timestamp  = time() - $before ; 
$seconds     = $timestamp;
$qminute      = $seconds / 60;
$minute        = intval($qminute);
$qhours        = $seconds / 60/ 60 ;
$hours          = intval($qhours);
$qdays         = $seconds / 60/ 60 /24;
$days           = intval($qdays);
$qmonths      = $seconds / 60/ 60  /24 /30;
$months        = intval($qmonths);
$qyears        = $seconds / 60/ 60 /24 /30 /12;
$years          = intval($qyears);
$ida              = $timestamp;

if($seconds < 20){$since = "الان";}
else if($seconds > 20 && $seconds < 60){$since = "منذ $seconds ثانية";}
else if($minute > 11 and $minute < 61 ){$since = "منذ $minute دقيقة";}
else if($minute == 1){$since = "منذ دقيقة";}
else if($minute == 2){$since = "منذ دقيقتين";}
else if($minute  > 3 and $minute  < 11){$since = "منذ $minute دقائق";}
else if($minute == 60){$since = "منذ ساعة تماما";}
else if($hours == 1){$since = "منذ ساعة";}
else if($hours  == 2){$since = "منذ ساعتين";}
else if($hours  > 2 and $hours  < 11){$since = "منذ $hours ساعات";}
else if($hours  > 11 and $hours < 24 ){$since = "منذ $hours ساعة";}
else if($days == 1){$since = "منذ البارحة";}
else if($days == 2){$since = "منذ يومين";}
else if($days < 11){$since = "منذ $days ايام";}
else if($days == 11 or $days > 11 and $days < 30){$since = "منذ $days يوم";}
else if($months == 1){$since = "منذ شهر";}
else if($months == 3){$since = "منذ شهرين";}
else if($months >3 and $months<11){$since = "منذ $months شهور";}
else if($months <10 and $months < 13){$since = "منذ $months شهر";}
else if($years == 1){$since = "منذ عام";}		  
else if($years == 2){$since = "منذ عامين";}		
else if($years >2 and $years< 11){$since = "منذ $years اعوام";}		
else if($years > 10 and $years< 11){$since = "منذ $years عام";}else{
$since = "غير معلوم";
}

return $since;
}


function settings($option,$Arrow="set_value"){
	global $condb;
	if(sql_count("settings","set_key='$option' LIMIT 1") >0){
		$G_Settings          = $condb->query("select $Arrow from settings WHERE set_key='$option' LIMIT 1");
		$G_Settings_fetch = $G_Settings->fetch_array(MYSQLI_ASSOC);
				if($G_Settings_fetch["$Arrow"] == "true"){
				return true;
			}else
				if($G_Settings_fetch["$Arrow"] == "false"){
				return false;
			}else {
				return $G_Settings_fetch["$Arrow"];
			}
	}else{
		if(insert("settings",array("set_key"=>$option))){
			return "Create ".$option." successful";
		}
	}
}

class Lang{
	Public $LanguageFile;
	Public $MyLang;
	Public $Direction;
	
		function __construct(){
			if(isset($_SESSION["MyLang"]) && !empty($_SESSION["MyLang"])){
				$this->MyLang = $_SESSION["MyLang"];
			}else{
				$this->MyLang = "ar";
			}
				if($this->MyLang == "en" ||
				   $this->MyLang == "fr")
				{
					$this->Direction = "ltr";	
				}else{
					$this->Direction = "rtl";
				}
		}
		
		public function Set($LanguageFileAppend){
			require($LanguageFileAppend);
			$this->LanguageFile = $Strings;
		}
		
		public function Key($WordKey){
			if (!array_key_exists($WordKey,$this->LanguageFile)) {
				echo "Error find Key Word";
			}else{
				echo $this->LanguageFile["$WordKey"];
			}
		}
		
		public function Get($WordKey){
			if (!array_key_exists($WordKey,$this->LanguageFile)) {
				echo "Error find Key Word";
			}else{
				return $this->LanguageFile["$WordKey"];
			}
		}
		
		public function GetAvilbleLangs(){
			$AvilbleLangsArr = array();
			$AvilbleLAngs    = explode(",",settings("AvilbleLangs"));
				foreach($AvilbleLAngs AS $K=>$V){
					$TheLangKey                   = explode(":",$V)[0];
					$TheLangName                  = explode(":",$V)[1];
					$AvilbleLangsArr[$TheLangKey] = $TheLangName;
				}
			return $AvilbleLangsArr;
		}
		
}

/// Sql Select Class
Class SqlGet{
	
	Public $Results;

		function Table($Table,$Fields,$Condition){
		Global $condb;
			$Results = array();
			if(!Empty($Condition)){
				$Condition = "WHERE ".$Condition;
			}
			if(Empty($Fields)){
				$Fields = "*";
			}
				$GetQuery         = $condb->query("select $Fields FROM $Table $Condition");
				while($QueryFetch = $GetQuery->fetch_array(MYSQLI_ASSOC)){
					$Results[]         = $QueryFetch;
				}
		Return $Results;
		}
		
		function Delete($Table,$Condition){
		Global $condb;
			if(!Empty($Condition)){
				$Condition = "WHERE ".$Condition;
					if($condb->query("delete FROM $Table $Condition ")){
						echo "true";
					}else{
						echo "false";
					}
			}
		}
		
		function UserId($Condition){
			Global $condb;
			Return $condb->query("select user_id FROM users where $Condition ")->fetch_array(MYSQLI_ASSOC)["user_id"];
		}
		
		function UserInfo($UserId,$Field){
			Global $condb;
			if(is_numeric($UserId)){
				$Condition = " user_id='$UserId' ";
			}else{
				$Condition = " user_username='$UserId' ";
			}
			Return $condb->query("select $Field FROM users where $Condition ")->fetch_array(MYSQLI_ASSOC)["$Field"];
		}
		
		
	public $NotificationsConTexts = array( "your_reservration_was_eccept"   => "تم قبول حجزك , سيتم الاتصال بك" );
		/// Notification Driver
		function AddNotification(){
		global $condb;

		
		
		
		}
		
}


/// This Class Is Help You To Handle With Messages Inside The Website And Chats
Class Messages{
	
	function Send($Params){
		global $condb;
		global $sessions;
		$Results = array();
		
			// Check If Type Was Isset
			if(isset($Params["type"])){
				$Type = $Params["type"];
			}else{
				$Type = "";
			}	
			
			// Check If Extra Parameters Array Was Isset
			if(isset($Params["extra"])){
				$Extra = serialize($Params["extra"]);
			}else{
				$Extra = "";
			}
			
			if($sessions->login){
				$Sender = $sessions->id;
			}else{
				$Sender = "-1";
			}
			
			if(isset($session->id) && $Params["to"] == $session->id){
				return "Error:You Cant Send Message To Your Self";
			}else{
				return insert("messages",
					 array("message_to"     => $Params["to"],
						"message_from"   => $Sender,
						"message_type"   => $Type,
						"message_content"=> $Params["message"],
						"message_extra"  => $Extra
						)
				);
			}
	}
	
	function GetAllBoxes(){
		global $condb;
		global $sessions;
		$Results = array();
			$GetQuery = $condb->query("select * FROM messages where
			message_to='$sessions->id' ||
			message_from ='$sessions->id'
			GROUP By message_to 
			Order By message_id DESC");
			while($QueryFetch = $GetQuery->fetch_array(MYSQLI_ASSOC)){
				$Results[]         = $QueryFetch;
			}
		return $Results;
		
	}	
	
	
	function Get(/*$Limit*/){
		global $condb;
		global $sessions;
		$Results = array();
			$GetQuery         = $condb->query("select * FROM messages where
			message_from='$sessions->id' && message_to ='$sessions->id'
			||
			message_to='$sessions->id' && message_from ='$sessions->id'
			Order By message_id DESC");
			while($QueryFetch = $GetQuery->fetch_array(MYSQLI_ASSOC)){
				$Results[]         = $QueryFetch;
			}
		
	}
}


Class Categories{
	/// This Class Give You Full Control On Categories
	
	/// Get Categories Info By Id
	function GetInfo($Params){
		global $condb;
		global $sessions;
		
		$CatGet = $condb->query("select * FROM categories WHERE categories_id='".$Params["Id"]."' ")->fetch_array(MYSQLI_ASSOC);
		if(isset($Params["Fild"]) == "categories_title" && Isset($Params["Lang"])){
			$Lang = $Params["Lang"];
			return unserialize($CatGet["categories_title"])[$Lang];
		}else{
			return $CatGet["Fild"];
		}
	}
	
	// Get Categorie
	function Get($Params){
		global $condb;
		global $sessions;
		$Results = array();
		
		if(isset($Params["Id"])){
			$IdCond = "categories_id = '".$Params["Id"]."'";
		}else{
			$IdCond = "categories_id != '-1'";
		}
		
			// Get All Parents Categorie
			if($Params["Select"] == "Parents"){
				/// If Type Inserted
				if(isset($Params["Type"])){ $TypeCond = " && categories_type ='".$Params["Type"]."' "; }
				else{ $TypeCond = " "; }
				
				if(sql_count("categories"," $IdCond && categories_parent='' $TypeCond") > 0){
				$GetQuery = $condb->query("select * FROM categories WHERE $IdCond $TypeCond && categories_parent='' Order By categories_id DESC");
					while($QueryFetch = $GetQuery->fetch_array(MYSQLI_ASSOC)){
						if($QueryFetch["categories_parent"]==""){
							$InnerResults = array();
							$InnerResults["Id"]     = $QueryFetch["categories_id"];
							$InnerResults["User"]   = $QueryFetch["categories_user"];
							$InnerResults["Parent"] = $QueryFetch["categories_parent"];
							$InnerResults["Title"]  = unserialize($QueryFetch["categories_title"]);
							$InnerResults["Date"]   = $QueryFetch["categories_date"];
							$InnerResults["Thumb"]  = $QueryFetch["categories_thumb"];
							array_push($Results,$InnerResults);
						}
					}
				}else{
					return false;
				}
			}
			
			// Get Childs Of Categorie
			if($Params["Select"] == "Childs"){
				/// If Type Inserted
				if(isset($Params["Type"])){ $TypeCond = " && categories_type ='".$Params["Type"]."' "; }
				else{ $TypeCond = " "; }
				
				if(isset($Params["ParentOf"]) == "Childs"){
					$GetQuery = $condb->query("select * FROM categories WHERE categories_parent='".$Params["ParentOf"]."' $TypeCond ");
					while($QueryFetch = $GetQuery->fetch_array(MYSQLI_ASSOC)){
							$InnerResults = array();
							$InnerResults["Id"]     = $QueryFetch["categories_id"];
							$InnerResults["User"]   = $QueryFetch["categories_user"];
							$InnerResults["Parent"] = $QueryFetch["categories_parent"];
							$InnerResults["Title"]  = unserialize($QueryFetch["categories_title"]);
							$InnerResults["Date"]   = $QueryFetch["categories_date"];
							$InnerResults["Thumb"]  = $QueryFetch["categories_thumb"];
							array_push($Results,$InnerResults);
					}
				}else{
					return false;	
				}
			}	
			
			// Get Childs Of Categorie
			if($Params["Select"] == "All"){
				/// If Type Inserted
				if(isset($Params["Type"])){ $TypeCond = " && categories_type ='".$Params["Type"]."' "; }
				else{ $TypeCond = " "; }
				
				if(isset($Params["Id"]) == "Childs"){
					$GetQuery = $condb->query("select * FROM categories WHERE categories_id='".$Params["Id"]."' $TypeCond ");
					while($QueryFetch = $GetQuery->fetch_array(MYSQLI_ASSOC)){
							$InnerResults = array();
							$InnerResults["Id"]     = $QueryFetch["categories_id"];
							$InnerResults["User"]   = $QueryFetch["categories_user"];
							$InnerResults["Parent"] = $QueryFetch["categories_parent"];
							$InnerResults["Title"]  = unserialize($QueryFetch["categories_title"]);
							$InnerResults["Date"]   = $QueryFetch["categories_date"];
							$InnerResults["Thumb"]  = $QueryFetch["categories_thumb"];
							array_push($Results,$InnerResults);
					}
				}else{
					return false;	
				}
			}

			return $Results;

		
	}
	
	// Add New
	function Create($Params){
		global $condb;
		global $sessions;
			if($sessions->login){
				$InsertSql =  insert("categories",
							array(
								"categories_type"    => $Params["Type"],
								"categories_parent"  => $Params["Parent"],
								"categories_title"   => $Params["Title"],
								"categories_thumb"   => $Params["Thumb"],
								"categories_date"    => time(),
								"categories_user"    => $sessions->id,
							)
						);
						
				if($InsertSql){
					return true;
				}else{
					return false;
				}
			}
	}	
	
	// Update
	function Update($Params){
		global $condb;
		global $sessions;
			if($sessions->login){
				$UpdateSql =  Update("categories",
							array(
								"categories_type"    => $Params["Type"],
								"categories_title"   => $Params["Title"],
								"categories_thumb"   => $Params["Thumb"],
								"categories_parent"  => $Params["Parent"],
							),
							" categories_id= '".$Params["Id"]."'"
						);
						
				if($UpdateSql){
					return true;
				}else{
					return false;
				}
			}
	}
	
	// Remove Categorie
	function Remove($Params){
		global $condb;
		global $sessions;
			if($sessions->login){
				echo delete("categories"," categories_id='".$Params["Id"]."' ");
					if(isset($Params["DeleteParents"]) && $Params["DeleteParents"]=='true'){
						echo delete("categories"," categories_parent='".$Params["Id"]."' ");
					}
			}
	}
}


/// Pagination
Class Pagination{
	public $Total           = 0;
	public $pageNumber      = 0;
	public $ResultsPerPage  = 0;
	
	public $PaginationsCelNumbers = 0;
	
	
	function SetTotal($Val){
		$this->Total = $Val;
	}	
	
	function pageNumber($Val){
		$this->pageNumber = $Val;
	}	
	
	function ResultsPerPage($Val){
		$this->ResultsPerPage = $Val;
	}	
	
	function GetLimit(){
		if( ($this->ResultsPerPage*$this->pageNumber - $this->ResultsPerPage) < $this->Total){
			
			
			if(($this->ResultsPerPage*$this->pageNumber) - ($this->ResultsPerPage*$this->pageNumber - $this->ResultsPerPage*($this->ResultsPerPage-1)) > $this->Total){
				return ($this->ResultsPerPage*$this->pageNumber-$this->ResultsPerPage).",".($this->ResultsPerPage);
			}else{
				return ($this->ResultsPerPage*$this->pageNumber).",".$this->ResultsPerPage;
			}
			
		}else{
			return 0;
		}
		
	}
	
}

/// Get Posts
Class Content{
	
	function Get($Aragments){
		$Results = array();
		
		// If Result Was Filtering
		if(isset($Aragments["Filters"])){
			$Filters = decodeUrlFilter($Filters);
		}else{
			 $Filters = "content_id !='-1'";
		}
		
		
	}
	
}
?>