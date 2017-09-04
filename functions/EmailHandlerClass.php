<?
 
 /// This Class Built To help You To Send Emails And Template It
Class EmailHandler{
	
	
	/// Send Email 
	/// :: Its Recive 3 parameters As array : array("Title"=>"Example","To"=>"Example","Message"=>"Example");
	function Send($OPT){
	$To      = $OPT["To"];
	$Title   = $OPT["Title"];
	$Message = $OPT["Message"];
	
		if(!isset($To) || !isset($Title) || !isset($Message)){
			return false;
		}else{
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/html; charset=utf-8";
			$headers[] = "From: ".settings("SiteName")." <".settings("OfficalEmail").">";
			$headers[] = "Bcc: JJ Chong <".settings("OfficalEmail").">";
			$headers[] = "Reply-To: Recipient Name <".settings("OfficalEmail").">";
			$headers[] = "X-Mailer: PHP/".phpversion();
			$sm        = mail($To,$Title, $Message , implode("\r\n", $headers));
					if($sm){
						return true;
					}else{ 
						return false;
					}
		}			
	}
	
	/// This Function Make an Email From Html Template , its Receve To Parameters , The Frist One is The Template Link , The Second Is Array (Every Key Well Replac With Value).
	function MakeTemplate($OPT){
	
		if(!isset($OPT["TemplateLink"]) && empty($OPT["TemplateLink"])){
		return "false";	
		}
		
		$TemplateLink      = settings("SiteDomain") . "/" . $OPT["TemplateLink"];
		$TemplateContent   = file_get_contents($TemplateLink);
		$KeyWords          = $OPT["KeyWords"];

		foreach($KeyWords AS $K=>$V){
			$TemplateContent = str_replace("{{".$K."}}",$V,$TemplateContent);
		}
		
		return $TemplateContent;

				
	}
	
	
}


?>