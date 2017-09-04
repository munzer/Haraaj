<?
////// This Class is For Uploadings * At This Relase Time It Work For Images Only
Class Upload{
	
	public $ImagesCompress;
	
	
	function __construct(){
		
		if(settings("ImagesResize")){
			$this->ImagesCompress = true;
		}else{
			$this->ImagesCompress = false;
		}
		
	}

	function NewImage($OPT){
		


		if(isset($OPT["files"])){
			// Get filename.
		    $temp = explode(".",$OPT["files"]["name"]);

		    // Get extension.
		     $extension = end($temp);
		    
		    
			// check that again on the server side.
			// Do not use $_FILES["file"]["type"] as it can be easily forged.
			
				$FileInfo = finfo_open(FILEINFO_MIME_TYPE);
					$Mime     = finfo_file($FileInfo,$OPT["files"]["tmp_name"]);
					if ((($Mime !== "image/gif")
					&& ($Mime   !== "image/jpeg")
					&& ($Mime   !== "image/JPEG")
					&& ($Mime   !== "image/jpg")
					&& ($Mime   !== "image/pjpeg")
					&& ($Mime   !== "image/x-png")
					&& ($Mime   !== "image/png"))
					&& !in_array($extension,$OPT["AllowedExtintions"])) {
						Die("UnAllowedExtintion");
					}
		
	    
		
		// Generate new random name. IF Not Isset 
		if(isset($OPT["name"])){
			$name = $OPT["name"] . "." . $extension;
		}else{
			$name = sha1(microtime()) .time(). "." . $extension;
		}	
		// Generate new random name. IF Not Isset 
		if(isset($OPT["Return"])){
			$Return = $OPT["Return"];
		}else{
			$Return = "Boolean";
		}
				
				$LastDir = "";
				foreach(explode("/",$OPT["dir"]) AS $K=>$V){
					$LastDir = $LastDir.$V."/";
						if(!file_exists($LastDir)){ 
							mkdir($LastDir);
						}
				}
				
				$Dir = $OPT["dir"];
			
			
		/// If Image Compressor Work;
		if($this->ImagesCompress){
			
			// Save files in the uploads folder.
			$UploadAction = move_uploaded_file($OPT["files"]["tmp_name"],$Dir."/".$name);
				if($UploadAction){
					
					
					if(isset($OPT["MaxHeight"])){
						$MaxHeight = $OPT["MaxHeight"];
					}else{
						$MaxHeight = 3400;
					}
					
					if(isset($OPT["MaxWidth"])){
						$MaxWidth = $OPT["MaxWidth"];
					}else{
						$MaxWidth = 2000;
					}
					
					
						require_once("easyphpthumbnail.class.php");
							$Image = new Gregphoto_Image($Dir."/".$name);
							$Image->setMaxwidth(intval($MaxWidth));
							$Image->setMaxHeight(intval($MaxHeight));
							$Image->setJpegQuality(intval(settings("ImagesQuality")));
							$Image->resize(Gregphoto_Image::BEST_FIT);
							$Compressor = $Image->saveThumbnail($Dir."/".$name);
				}
		}else{
			// Save files in the uploads folder.
			$UploadAction = move_uploaded_file($OPT["files"]["tmp_name"],$Dir."/".$name);
		}
		
			if($Return == "Boolean"){
					if($UploadAction){
						return true;
					}else{
						return false;
					}
			}else if($Return == "Path"){
						return $Dir."/".$name;
			}else if($Return == "Details"){
						if($UploadAction){
							return array("state"    => true,
									"Name"    => $name,
									"Size"    => $OPT["files"]["size"],
									"success" => true,
									"Path"    => $OPT["dir"]."/".$name
								);
						}else{
							return array("state"=> false);
						}
			}else{
						return"UnKnowReturnType";
			}
			
		}
	}

	function CopyImage($OPT){
		
		$ImgDir  = $OPT["ImgDir"];
		$CopyDir = $OPT["CopyDir"];
		$NewName = $OPT["NewName"];
		
			/// Create Dir If Not Exist
			$LastDir = "";
			foreach(explode("/",$OPT["CopyDir"]) AS $K=>$V){
			$LastDir = $LastDir.$V."/";
				if(!file_exists($LastDir)){ 
					mkdir($LastDir);
				}
			}
			
		
			/// If Image Compressor Work;
			if($this->ImagesCompress){
				
						// Save files in the Destination folder.
						if(isset($OPT["MaxHeight"])){
							$MaxHeight = $OPT["MaxHeight"];
						}else{
							$MaxHeight = 3400;
						}
						
						if(isset($OPT["MaxWidth"])){
							$MaxWidth = $OPT["MaxWidth"];
						}else{
							$MaxWidth = 2000;
						}
						
							require_once("easyphpthumbnail.class.php");
								$Image = new Gregphoto_Image($ImgDir);
								$Image->setMaxwidth(intval($MaxWidth));
								$Image->setMaxHeight(intval($MaxHeight));
								$Image->setJpegQuality(intval(settings("ImagesQuality")));
								$Image->resize(Gregphoto_Image::BEST_FIT);
								$Compressor = $Image->saveThumbnail($CopyDir."/".$NewName);
							return true;

			}else{
				// Save files in the uploads folder.
				$CopyAction = copy($ImgDir,$CopyDir."/".$NewName);
				if($CopyAction){ 
					return true;
				}else{
					return false;
				}
			}
		
	}
	
}

?>