<?
require_once("config.php");
require_once("functions/master.php");
$sessions = new sessions();
$SqlGet   = new SqlGet();
$sessions->me();
$uid = $sessions->id;
/// Initializing The Language File
	$L = new Lang;
	$L->Set("lang/ar.php");
	
		// Definition For The Site Root
		if(!defined("RootPath")){ define("RootPath",settings("SiteDomain")); }	
?>
<html>
	<head>
		<?
		$cond_hatacss_slash = substr_count( $_SERVER['REQUEST_URI'] , "/");
		if($_SERVER['HTTP_HOST'] == "127.0.0.1" || $_SERVER['HTTP_HOST'] == "localhost" ){
			$domain=$_SERVER['HTTP_HOST']."/sooq/";
		}else{
			$domain=$_SERVER['HTTP_HOST']."/";
		} 
		?>
		<base href="<? echo "http://".$domain; ?>" />
		<script type="text/javascript" src="jq.js"></script>
		<script type="text/javascript" src="js/duastFramework.js"></script>
		<script type="text/javascript" src="js/duastDesign.js"></script>
		
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	
	<? appendStyle("styles/".$L->Direction."/style.css"); ?>
	
	<!--- Content Style--->
	<link rel="stylesheet" href="styles/<? echo $L->Direction; ?>/Content.css" type="text/css" />
	
	<link rel="stylesheet" href="styles/animate.css" type="text/css" />
	<link href="styles/Droid_arabic_Kufi/Droid_arabic_Kufi.css" rel="stylesheet" type="text/css"/>
	
	<script src="js/load_nav.js"></script>
	<script src="js/animation.js"></script>
	
	<link rel="stylesheet" type="text/css" href="js/preloader/css/materialPreloader.css">
	<script type="text/javascript" src="js/preloader/js/materialPreloader.js"></script>	
	
	<link rel="stylesheet" type="text/css" href="js/gromo/jquery.scrollbar.css">
	<script type="text/javascript" src="js/gromo/jquery.scrollbar.min.js"></script>
	</head>

<body>

<? if($sessions->AcountActivation !== "NotActive"){ ?>
	<div class="header">
		<a href="index">
			<img src="img/logo.png" class="logo"/>
		</a>
		
	<? if($sessions->login){ ?>
		<img src="img/w/menu.png" class="headerMenu" onClick="openMenu(this,'#IndexMenu');"/>
	<? } ?>
	
		<? if(!$sessions->login){ ?>
		<div class="headerButtonsAria">
			<a href="join"><button class="buttonPattern1"><? $L->Key("join"); ?></button></a>
			<a href="login"><button class="buttonPattern1"><? $L->Key("login"); ?></button></a>
		</div>
		<? }else{ ?>
		<div class="headerAccountAria">
		
			<div class="headerAccountThumb thumb" id="AccountHeaderThumb"
			style="background-image:url('<? echo "thumb/xl/".$SqlGet->userinfo($sessions->id,"user_thumb"); ?>');">
			</div>
			
			<button class="AddNewContentBut buttonPattern1">
				اضف اعلانك <img src="img/w/add.png" />
			</button>
			
			
			<? if(settings("MessagingSystem")){ ?>
			<div class="NotificationButton" id="openNotification">
				<img src="img/w/notification.png" />
					<bubble id="newNotificationBubble">?</bubble>
			</div>
			<? } ?>
			
			
			<? if(settings("NotificationsSystem")){ ?>
			<div class="NotificationButton" id="openMessages">
				<a href="messages">
					<img src="img/w/chat.png" />
						<bubble id="newMessagesBubble">?</bubble>
				</a>
			</div>
			<? } ?>
			
			
			<? if(settings("NotificationsSystem") || settings("MessagingSystem")){ ?>
			<script>
			/// This Part Will Help You To Get Notification And New Message Counter To Inform Users
				setInterval(function(){
					$.post("actions.php?GetNewNotifications",function(Data){
						var Data               = $.parseJSON(Data);
						var MessagesCount      = Data["MessagesCount"];
						var NotificationsCount = Data["NotificationsCount"];
							if(NotificationsCount>0){
								$("#newNotificationBubble").html(NotificationsCount).fadeIn();	
							}else{
								$("#newNotificationBubble").fadeOut();
							}
							if(MessagesCount>0){
								$("#newMessagesBubble").html(MessagesCount).fadeIn();	
							}else{
								$("#newMessagesBubble").fadeOut();
							}
					});
				},1000);
			</script>
			<? } ?>
				
		</div>
		<? } ?>
		</div>
		
			<div class="NotificationsMenu shadow2" id="notifications">
				<div class="NotificationsHeader" onClick="$('#notifications').hide();">
				<img src="img/b/close.png" /><? $L->Key("notifications"); ?></div>
					<div class="NotificationsBody">
						<cel>
						<thumb class="thumb"></thumb>
						<text>قام محمد بأضافة تعريق جديد لمفضلتك , انقر هنا لمشاهدته</text>
						<timeAbout> منذ شهر </timeAbout>
						</cel>
						<img class="NotificationsLoading" src="img/loading.gif"/>
					</div>
			</div>
			<script>
			$("#openNotification").DrobDivHere({
			Div:"#notifications",
			Position:"fixed"
			});
			</script>
	
	
	<div class="FloatHeaderMenu shadow2" id="IndexMenu">
	<a href="MyAcount/GeneralSettings"><? $L->Key("my_account_settings"); ?><img src="img/b/settings.png" /></a>
		<? if($sessions->user_acount_type == "Admin"){ ?>
	<a href="Dashboard/General"><? $L->Key("control_panel"); ?><img src="img/b/toggle.png" /></a>
		<? } ?>
		<? if($sessions->user_acount_type == "Admin" || $sessions->user_acount_type == "Supporter"){ ?>
	<a href="ReceivedTickets"><? $L->Key("support"); ?><img src="img/b/support.png" /></a>
		<? } ?>
	<a href="logout"><? $L->Key("logout"); ?><img src="img/b/shutdown.png" /></a>
	</div>
	
	
	<!-- Material Float Notifications Contenet-->
	<div class="MaterialFloatNotificationsContener"></div>
	<!-- Material Float Notifications Contenet-->
	
	<? } ?>


	<hidden style='display:none;'></hidden>
	<main_contenner>
		<? include("includes.php"); ?>
	</main_contenner>
	
	<!------------------- Confirm Box --------------------------------------->
	<confirmbox class="shadow3"><p>Dialog Text</p>
		<button id='confirmConf'><? $L->Key("yes"); ?></button>
		<button id='cancelConf' ><? $L->Key("no");  ?></button>
	</confirmbox>	
	
	<!------------------- Loading --------------------------------------->
	<LoadingPage>
	</LoadingPage>	
	
	<!------------------- Debuggings Stars --------------------------------------->
	<? if(settings("DebuggingCallBacks")){ ?>
	<Debugs_Calls id="LastDebugContener">
		<the_title> Debugging CallsBacks</the_title>
		<the_body id="LastDebugBody">
		<?
		appendStyle("styles/<? echo $L->Direction; ?>/Debugging.css");
		?>
			<script>
			// Get Last Debug
			$(document).ready(function(){
				NewDebug = "";
				setInterval(function(){
					$.post("Devolopers/LastCallsBackDebugging.html",function(Data){
						if(NewDebug !== Data){
							$("#LastDebugBody").append(Data);
							NewDebug = Data;
								$('#LastDebugBody').scrollTop(
									$('#LastDebugBody')[0].scrollHeight
								);
						}
					});
				},300);
			});
			</script>
		</the_body>
	</Debugs_Calls>
	<? } ?>
	<!------------------- Debuggings Ends --------------------------------------->
	
	
	<!------------------- All Dialogs and lightBoxs --------------------------------------->
	<div class="lightBox shadow1" id="DatePicker">
		<div class="lightBoxBody shadow1">
		<div class="lightBoxBodyTitleBar"><st>Example</st>
			<img class="lightBoxBodyTitleBarClose" src="img/w/close.png"/>
		</div>
			<div class="datePikerLightBoxContener">
				<cells></cells>
			</div>
		</div>
	</div>
	
	<script>
	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
		Duanimate(".lightBox","hide","pulse","900");
		}
	});
	$(".lightBoxBodyTitleBarClose").click(function(e) {
		Duanimate(".lightBox","hide","pulse","900");
	});
	</script>
	<!------------------- All Dialogs and lightBoxs --------------------------------------->
	
	<div class="footer shadow5">
	<div class="footerContener">
	
	<block>
		<div class="footerContenerTitle"><? $L->Key("web_site"); ?></div>
		<a href="help/about">
			<div class="footerContenerCel"><? $L->Key("about"); ?></div>
		</a>
		<a href="help/privacy">
			<div class="footerContenerCel"><? $L->Key("privacy"); ?></div>
		</a>
		<a href="help/terms">
			<div class="footerContenerCel"><? $L->Key("terms"); ?></div>
		</a>
		<a href="#" id="ChangeLangBox">
			<div class="footerContenerCel" style="color:#FFFFA6;">
			تغيير اللغة
			</div>
		</a>
		
		<script>
		$("#ChangeLangBox").click(function(){
			Duanimate(".choseYourLang","toggle","pulse","0.900");
			window.scrollTo(0,document.body.scrollHeight);
		});
		</script>
		
			<div class="choseYourLang">
				<?
					$GetAvilbleLangs = $L->GetAvilbleLangs();
					foreach($GetAvilbleLangs AS $K=>$V){?>
						<cel lang="<? echo $K; ?>"> <? echo $V; ?> </cel>
<?					
					}
				?>
				<script>
					$(".choseYourLang cel").click(function(){
						var NewLang = $(this).attr("lang");
						$.get("actions.php?ChangeSiteLang="+NewLang,function(data){
							if(data == true){
								 location.reload(); 
							}
						});
					});
				</script>
			</div>
	</block>
	
	
	<? if(!$sessions->login){ ?>
	<block>
		<div class="footerContenerTitle">
			<? $L->Key("accounts"); ?>
		</div>
		<a href="<? echo RootPath . "/join"?>" >
			<div class="footerContenerCel"><? $L->Key("join"); ?></div>
		</a>
		<a href="<? echo RootPath . "/login"?>" >
			<div class="footerContenerCel"><? $L->Key("login"); ?></div>
		</a>
		<a href="<? echo RootPath . "/passwordReset"?>" >
			<div class="footerContenerCel"><? $L->Key("reset_password"); ?></div>
		</a>
	</block>
	<? }else{ ?>	
	<block>
		<div class="footerContenerTitle">حسابي</div>
		<a href="<? echo RootPath . "/MyAcount/GeneralSettings"?>" >
			<div class="footerContenerCel">ادارة حسابي</div>
		</a>
		<a href="<? echo RootPath . "/logout"?>" >
			<div class="footerContenerCel">تسجيل خروج</div>
		</a>
	</block>
	<? } ?>
	
	<block>
		<div class="footerContenerTitle"><? $L->Key("support"); ?></div>
		<a href="<? echo RootPath . "/help" ?>">
			<div class="footerContenerCel"><? $L->Key("help_center"); ?></div>
		</a>
		<? if(!isset($sessions->id) && settings("AllowToGuestsToSendSupport") || isset($sessions->id)){ ?>
		<a href="<? echo RootPath . "/help/contact" ?>">
			<div class="footerContenerCel"><? $L->Key("contact_us"); ?></div>
		</a>
		<? } ?>
		<a href="<? echo RootPath . "/help/FaQ" ?>">
			<div class="footerContenerCel"><? $L->Key("trend_questions"); ?></div>
		</a>
	</block>	
	
	<block>
	<? if(!empty(settings("OfficalFacebook")) 
	   || !empty(settings("OfficalInstagram")) 
	   || !empty(settings("OfficalTwitter")) 
	   || !empty(settings("OfficalYoutube"))){ ?>
	<div class="footerContenerTitle"><? $L->Key("flow_us"); ?></div>
	
		<div class="SocialsMediaFooter" >
			<? if(!empty(settings("OfficalFacebook"))){ ?>
				<a href="http://www.facebook.com/<? echo settings("OfficalFacebook"); ?>">
					<img class="footerContenerSocial" src="img/w/facebook.png"/>
				</a>
			<? } ?>
			
			<? if(!empty(settings("OfficalInstagram"))){ ?>
				<a href="http://www.instagram.com/<? echo settings("OfficalInstagram"); ?>">
					<img class="footerContenerSocial" src="img/w/instagram.png"/>
				</a>
			<? } ?>
			
			<? if(!empty(settings("OfficalTwitter"))){ ?>
				<a href="http://www.twitter.com/<? echo settings("OfficalTwitter"); ?>">
					<img class="footerContenerSocial" src="img/w/twitter.png"/>
				</a>
			<? } ?>
			
			<? if(!empty(settings("OfficalYoutube"))){ ?>
				<a href="http://www.youtube.com/<? echo settings("OfficalYoutube"); ?>">
					<img class="footerContenerSocial" src="img/w/youtube.png"/>
				</a>
			<? } ?>
	
		</div>
	<? } ?>
	
	<? if(!empty(settings("OfficalPhone"))){ ?>
		<div class="footerContenerTitle"><? $L->Key("our_tellphones"); ?></div>
			<div class="footerContenerCel" style="font-size:18px;font-weight:bold;">
				<a href="tel://<? echo settings("OfficalPhone"); ?>">
					<? echo settings("OfficalPhone"); ?>
				</a>
			</div>
	<? } ?>
	
	</block>
	
	<img src="img/logo.png" class="footerLogo"/>
	</div>
	</div>
</body>

</html>