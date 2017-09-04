<?
 
 /// This Class Built To help You To Send Emails And Template It
Class Support{
	

	
		/// Send Support Message Or Report 
		function Send($Params){
				global $condb;
				global $sessions;
				
					// Requir Email Handler File
					require_once("functions/EmailHandlerClass.php");
					
					// Include Lang File
					include("lang/ar.php");

				$MultiTickets      = false;
				$EmailNotification = false;
				$TicketState       = "Open";
				
				
					/// If The Sender is User
					if(isset($sessions->id)){
						$From   = $sessions->id;
						$Name   = "";
						$Email  = "";
						
							///Check If There Is Opened Ticket
							if(sql_count("support","support_user='".$sessions->id."'  && support_state='Open' ") > 0){
								$GSupport = $condb->query("select * From support Where support_user='".$sessions->id."' ")->fetch_array(MYSQLI_ASSOC);
								
								// Get Serial 
								$TicketSerial   = $GSupport["support_ticket_serial"];
								
								// Get Last Messages
								$TicketMessages = unserialize($GSupport["support_messages"]);
								
							}else{
								$TicketSerial   = "";
								$TicketMessages = "";
							}
					}else{
						$From   = "-2";
						$Name   = $Params["Name"];
						$Email  = $Params["Email"];
						
							///Check If There Is Opened Ticket
							if(sql_count("support","support_email='$Email' && support_state='Open' ") > 0){
								$GSupport = $condb->query("select * From support Where support_email='$Email' ")->fetch_array(MYSQLI_ASSOC);
								
								// Get Serial 
								$TicketSerial   = $GSupport["support_ticket_serial"];
								
								// Get Last Messages
								$TicketMessages = unserialize($GSupport["support_messages"]);
								
							}else{
								$TicketSerial   = "";
								$TicketMessages = "";
							}
					}
					
					
				/// Preper Messages Content	
				if(!empty($TicketMessages)){
					$NewMessages = $TicketMessages;
					array_push($NewMessages,
							array(
							"Message" => $Params["Message"],
							"From"    => $From,
							"Date"    => time()
						)
					);
				
					$Messages = serialize($NewMessages);
				}else{
					$NewMessages = array();
					array_push($NewMessages,
							array(
							"Message" => $Params["Message"],
							"From"    => $From,
							"Date"    => time()
						)
					);
					
						$Messages = serialize($NewMessages);
				}
				
				
				// Send Notificate Email To Supporters And Admins
				if(settings("NotificateSupporterByEmail")){
						$L = new Lang;
						$L->Set("lang/ar.php");
						
						// Email Link
						$Link = "<a href='" . settings("SiteDomain")."/ReceivedTickets'>
						" . settings("SiteDomain")."/ReceivedTickets
						</a>";
						
						$EmailHandler = new EmailHandler;
						// Build A New Email Handler Template Before Send It
						$EmailTemplate = $EmailHandler->MakeTemplate(array(
						"TemplateLink" => "templates/EmailsTemplates/Prime.html",
						"KeyWords"     => array(
								"TITLE"        => $L->Get("new_support_messages"),
								"WEBSITENAME"  => settings("SiteName"),
								"LOGO"         => settings("SiteDomain") . "/img/logo.png",
								"CONTENT"      => $L->Get("email_new_ticket_update") .  " </br></br> ". $Link,
								"FOOTER"       => $L->Get("email_footer_message")." ".settings("SiteName")
						)));
						
						$GSupporters = $condb->query("select * From users Where user_acount_type='Admin' ||  user_acount_type='Supporter' ");
				
							// Get All Supporters
							while($S = $GSupporters->fetch_array(MYSQLI_ASSOC)){
									// Send !
									$EmailHandler->Send(array(
									"To"      => $S["user_email"],
									"Title"   => $L->Get("new_support_messages"),
									"Message" => $EmailTemplate
									));
									
							}
						
						
					}

					
				if(!empty($TicketSerial)){
					return update("support",
					 array("support_messages"      => $Messages,
						"support_last_replay"   => $From,
						"support_state"         => "Open"
						),
						" support_ticket_serial='".$TicketSerial."' "
					);
				}else{
					/// Create Rand Serial
					$NewTicketSerial = time().rand("0",100);
					
						// Get Person Email To Preper Send Email
						if(isset($sessions->id)){
							$UserEmail = $condb->query("select user_email From users Where user_id='".$sessions->id."' ")->fetch_array(MYSQLI_ASSOC)["user_email"];
						}else{
							$UserEmail =  $Email;
						}
							
							// Send Notificate Email To Person Who Send Support Message
								// Set Lang For Send Email
								$LS = new Lang;
								$LS->Set("lang/ar.php");
								
								$EmailHandler = new EmailHandler;
								// Email Link
								$Link = "<a href='" . settings("SiteDomain")."/MySupportTickets?TicketSerial=" . $NewTicketSerial . "'>
								" . settings("SiteDomain")."/MySupportTickets?TicketSerial=" . $NewTicketSerial . "
								</a>";
								
								// Build A New Email Handler Template Before Send It
								$EmailTemplate = $EmailHandler->MakeTemplate(array(
								"TemplateLink" => "templates/EmailsTemplates/Prime.html",
								"KeyWords"     => array(
										"TITLE"        => $LS->Get("new_ticket_open"),
										"WEBSITENAME"  => settings("SiteName"),
										"LOGO"         => settings("SiteDomain") . "/img/logo.png",
										"CONTENT"      => $LS->Get("your_ticket_is") .  " ". $Link ." ". $LS->Get("we_well_notice_you"),
										"FOOTER"       => $LS->Get("email_footer_message")." ".settings("SiteName")
								)));
								
									// Send !
									$EmailHandler->Send(array(
									"To"      => $UserEmail,
									"Title"   => $LS->Get("new_support_messages"),
									"Message" => $EmailTemplate
									));	
								
		
					return insert("support",
					 array("support_user"          => $From,
						"support_name"          => $Name,
						"support_email"         => $Email,
						"support_ticket_serial" => $NewTicketSerial,
						"support_messages"      => $Messages,
						"support_last_replay"   => $From,
						"support_date"          => $Params["Date"],
						"support_state"         => "Open"
						));
				}
				
		}
		
		
	/// Get My Support Tickets
	function GetMessages($Params){
		global $condb;
		global $sessions;


		
			/// To Get All Messages You Need To SetUp And Filter By Params
			$State         = $Params["State"];
			$ReturnedArray = array();
				

				
		
			if(!isset($sessions->id)){
				// Check If Serial Passed
					if(isset($Params["Serial"])){
						$Serial =  $Params["Serial"];
					}else{
						return "NoResults";
					}
			}

			
			// Get All Messages If Youser Logined
			if(isset($sessions->id)){
				if($sessions->user_acount_type == "Supporter" || $sessions->user_acount_type == "Admin"){
					$Condition = "support_user !='".$sessions->id."'";
				}else{
					$Condition = "support_user  ='".$sessions->id."'";
				}
			}else{
					$Condition = "support_ticket_serial ='".$Serial."'";
			}
				
			
			if(sql_count("support"," $Condition ") > 0){
				$GSupport = $condb->query("select * From support Where $Condition ORDER BY support_id DESC");
				
				// Add Support Keys
				while($V = $GSupport->fetch_array(MYSQLI_ASSOC)){
					$MessageingInfo = array("OpenDate"   => $V["support_date"],
								   "LastRepled" => $V["support_last_replay"],
								   "Serial"     => $V["support_ticket_serial"],
								   "User"       => $V["support_user"],
								   "Id"         => $V["support_id"],
								   "CloseDate"  => $V["support_close_date"],
								   "State"      => $V["support_state"],
								   "Name"       => $V["support_name"],
								   "Email"      => $V["support_email"],
								   "Messages"   => unserialize($V["support_messages"])
								   );
									
									// Push Messages
									array_push($ReturnedArray,$MessageingInfo);
				}
										// Return This Array
										return $ReturnedArray;
				
			}else{
				return "NoResults";
			}
			

	}
	
	/// Replay On Support Message
	function Reply($Params){
		global $condb;
		global $sessions;
		
			// Requir Email Handler File
			require_once("functions/EmailHandlerClass.php");
			
		$TicketSerial = $Params["Serial"];
		$Messages     = $Params["Serial"];
		
		
			// Check IF Serial Forget Passed
			if(!isset($TicketSerial) || $TicketSerial==""){
				return "NoSerialPassed";
			}
			
			// Check IF Message Forget Passed
			if(!isset($Messages) || $Messages==""){
				return "NoSerialPassed";
			}
			
			// Check If User Login Or Not
			if(isset($sessions->id)){
				$From   = $sessions->id;
			}else{
				$From   = "-2";
			}
			
			///Check If There Is Opened Ticket
			if(sql_count("support","support_ticket_serial='".$TicketSerial."' ") > 0){
			$GSupport = $condb->query("select * From support Where support_ticket_serial='".$TicketSerial."' ")->fetch_array(MYSQLI_ASSOC);
			
				if($GSupport["support_state"]=="Open"){
					$Messages = unserialize($GSupport["support_messages"]);
					
						array_push($Messages,
							array(
							"Message" => $Params["Message"],
							"From"    => $From,
							"Date"    => time()
							)
						);
						
						$Messages = serialize($Messages);
					
						
						// Get Person Email To Preper Send Email
						if(!empty($GSupport["support_email"])){
							$UserEmail = $GSupport["support_email"];
						}else{
							$UserEmail = $condb->query("select user_email From users Where user_id='". $GSupport["support_user"] ."' ")->fetch_array(MYSQLI_ASSOC)["user_email"];
						}
							
							
							// Send Notificate Email To Person Who Send Support Message
							$EmailHandler = new EmailHandler;
							
								// Set Lang For Send Email
								$LS = new Lang;
								$LS->Set("lang/ar.php");
								
								// Email Link
								$Link = "<a href='" . settings("SiteDomain")."/MySupportTickets?TicketSerial=" . $GSupport["support_ticket_serial"] . "'>
								" . settings("SiteDomain")."/MySupportTickets?TicketSerial=" . $GSupport["support_ticket_serial"] . "
								</a>";
								
								// Build A New Email Handler Template Before Send It
								$EmailTemplate = $EmailHandler->MakeTemplate(array(
								"TemplateLink" => "templates/EmailsTemplates/Prime.html",
								"KeyWords"     => array(
										"TITLE"        => $LS->Get("new_ticket_open"),
										"WEBSITENAME"  => settings("SiteName"),
										"LOGO"         => settings("SiteDomain") . "/img/logo.png",
										"CONTENT"      => $LS->Get("new_update_on_your_ticket") .  " ". $Link ,
										"FOOTER"       => $LS->Get("email_footer_message")." ".settings("SiteName")
								)));
								
									if( (isset($sessions->id)) 
									&&  ($sessions->user_acount_type !='Admin'
									||  $sessions->user_acount_type  !='Supporter')){
										// Send User Email Copy
										$EmailHandler->Send(array(
										"To"      => $UserEmail,
										"Title"   => $LS->Get("new_support_messages"),
										"Message" => $EmailTemplate
										));
									}
								
												// Send Notificate Email To Supporters And Admins
				if(settings("NotificateSupporterByEmail")){
					if($sessions->user_acount_type !='Admin' && $sessions->user_acount_type !='Supporter'){
					
						$LS = new Lang;
						$LS->Set("lang/ar.php");
						
						
						// Email Link
						$Link = "<a href='" . settings("SiteDomain")."/ReceivedTickets'>
						" . settings("SiteDomain")."/ReceivedTickets
						</a>";
						
						$EmailHandler = new EmailHandler;
						
						// Build A New Email Handler Template Before Send It
						$EmailTemplate = $EmailHandler->MakeTemplate(array(
						"TemplateLink" => "templates/EmailsTemplates/Prime.html",
						"KeyWords"     => array(
								"TITLE"        => $LS->Get("new_support_messages"),
								"WEBSITENAME"  => settings("SiteName"),
								"LOGO"         => settings("SiteDomain") . "/img/logo.png",
								"CONTENT"      => $LS->Get("email_new_ticket_update") .  " </br></br> ". $Link,
								"FOOTER"       => $LS->Get("email_footer_message")." ".settings("SiteName")
						)));
						
						$GSupporters = $condb->query("select * From users Where user_acount_type='Admin' ||  user_acount_type='Supporter' ");
				
							// Get All Supporters
							while($S = $GSupporters->fetch_array(MYSQLI_ASSOC)){
									// Send !
									$EmailHandler->Send(array(
									"To"      => $S["user_email"],
									"Title"   => $LS->Get("new_support_messages"),
									"Message" => $EmailTemplate
									));
									
							}
						
						
						}
					}
					
								
								
					return update("support",
					 array("support_messages"      => $Messages,
						"support_last_replay"   => $From,
						),
						" support_ticket_serial='".$TicketSerial."' "
					);
				}else{
					return "YouCantReplayOnThis";
				}
			
			}else{
				return "NotExistTicket";
			}
			
			
	}
	
	function CloseTicket($Serial){
		global $condb;
		global $sessions;

		if($sessions->user_acount_type == "Supporter" || $sessions->user_acount_type == "Admin"){
			return update("support",
					array("support_state"  => "Closed",
					"support_close_date"   => time(),
					),
					" support_ticket_serial='".$Serial."' "
					);
		}else{
			return "NotAllowed";
		}
	}
	
}


?>