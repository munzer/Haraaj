<?php
if(isset($_GET["AjaxPages"])){
	require_once("config.php");
	require_once("functions/master.php");
		$sessions = new sessions();
		$SqlGet   = new SqlGet;
		$sessions->me();
		$uid = $sessions->id;
			/// Initializing The Language File
			$L = new Lang;
			$L->Set("lang/ar.php");
}

	/// Activation Your Account
	if(isset($pg_page) && $pg_page!=="Activation" && $pg_page!=="logout" && $sessions->AcountActivation == "NotActive"){
		GoToPage("Activation");
	}
	
	/// AirPlanMode
	if(isset($_GET["page"]) && settings("AirPlanMode")){
		if($_GET["page"] !== "login" && $_GET["page"] !== "Dashboard" && $_GET["page"] !== "offline" && $sessions->user_acount_type !=="Admin"){
			GoToPage("offline");
		}
	}

		// Definition For The Site Root
		if(!defined("RootPath")){ define("RootPath",settings("SiteDomain")); }


		
	if(isset($_GET["page"])){

	if($_GET["page"]=="index" || $_GET["page"]=="categorie"){
		appendTitle($L->Get("index"));
		
	if($_GET["page"]=="index"){
		$JSQuery = "content_id[!=]500";
	}else if($_GET["page"]=="categorie"){
		$JSQuery = "content_cat[=]".$_GET["id"];
	}
	
		appendStyle("styles/".$L->Direction."/Prime.css");
		
	$categories = NEW categories;
	
	// Get Avilble Langs And Set Categories Array
	$CategorieArray = $L->Get("please_chose_one")."||null,";
	$AllCats = $categories->Get(array(
				"Select"=>"Parents",
				"Type"=>"cat"
		));
		?>
		
		<div class="PrimaryMenu shadow0">
		
		<?
		foreach($AllCats AS $ParentK=>$ParenV ){
			$CatTitle = $ParenV["Title"][$L->MyLang]; 
			$CatId    = $ParenV["Id"];
			$CatThumb = $ParenV["Thumb"];
		?>
			<a href="categorie/<? echo $CatId."/".$CatTitle; ?>">
				<cel>
					<img src="<? echo $CatThumb; ?>" />
					<? echo $CatTitle; ?>
				</cel>
			</a>
		<?
		}
		?>
		</div>
		
		<div class="FilterMenu">
			<FilterCel>
				<key>ترتيب حسب</key>
				<but class="ClickedFilterBut">السعر</but>
				<but>تاريخ الاضافة</but>
			</FilterCel>
			
			<FilterCel>
				<key>النوع</key>
				<but class="ClickedFilterBut">عرض</but>
				<but>طلب</but>
			</FilterCel>
			
			<FilterCel>
				<key>الصور</key>
				<but class="ClickedFilterBut">عرض</but>
				<but>بدون صور</but>
			</FilterCel>
		</div>
		
		<div class="DuTemplateContenet" style="margin-top:10px;">
			<div id="postsAfterThis"></div>
			<img src="img/loading.gif" class="DuTemplateLoadingMore" />
			<div class="Pagination"></div>
		</div>
		
	<script>
	$(document).ready(function(){
		Template = new DuBuildTemplate();
		TotalResult   = 0;
		CurrentResult = 1;
		
		Template.Apply({
			Block           : "primePost"
		});
		
		GetData = function(_Page){
			Template.Start({
				Filter          : "<? echo $JSQuery; ?>",
				ResultsPerPage  : "12",
				Page            : _Page,
				OrderBy         : "content_id",
				OrderKey        : "Desc",
				Table           : "content",
				
				IfLoading       : function(){
					ScrolUp("html,body");
					$(".DuTemplateLoadingMore").fadeIn();
				},
				IfFinish        : function(Data){
					$("#postsAfterThis").html(Data);
					$(".Pagination").fadeIn();
					$(".DuTemplateLoadingMore").fadeOut();
				},
				CountTotal      : function(Data){
					TotalResult = Data;
					
				}
			});
		}
		
		GetData(CurrentResult);

	});
	</script>

	<script>
	$(document).ready(function(){

	pagination = new Pagination({ Contener:".Pagination" });
		
		pagination.Build({
			Total         : TotalResult,
			PerPage       : 12,
			CurrentPage   : CurrentResult,
			ButtonsNumber : 10
		});
	});
	
	$(document).on("click",".Pagination button",function(){
		CurrentResult = $(this).text();
		pagination.Build({
			Total         : TotalResult,
			PerPage       : 12,
			CurrentPage   : CurrentResult,
			ButtonsNumber : 10
	});
			GetData(CurrentResult);
	});
	</script>
		
		<?
}else if($_GET["page"]=="content"){
	!$sessions->login ? GoToPage(RootPath . "/Subscriptions"):"";
	
	appendStyle("styles/".$L->Direction."/Prime.css");
	
	foreach($SqlGet->Table("payments"," * "," payment_user='".$sessions->id."' Order By payment_id DESC LIMIT 1") AS $K=>$V){ 
		// IF User Doesnt Hase a subsctiption
		if($V["payment_end_date"] < time() && $sessions->user_acount_type !== "Admin"){
			header("location: ../Subscriptions");
		}
	}
	
	$categories = NEW categories;
	// Get Avilble Langs And Set Categories Array
	$CategorieArray = $L->Get("please_chose_one")."||null,";
	$AllCats = $categories->Get(array(
				"Select"=>"Parents"
		));
		?>
		
		<div class="PrimaryMenu">
		
		<?
		foreach($AllCats AS $ParentK=>$ParenV ){
			$CatTitle = $ParenV["Title"][$L->MyLang]; 
			$CatId    = $ParenV["Id"];
		?>
			<a href="categorie/<? echo $CatId."/".$CatTitle; ?>"><cel><? echo $CatTitle; ?></cel></a>
		<?
		}
		?>
		</div>


	<? 
		$Get           = $SqlGet->Table("content",""," content_id='".$_GET["id"]."' ")[0];
		$Title         = $Get["content_title"];
		$Content       = $Get["content_content"];
		$Cat           = $Get["content_cat"];
		$ContentID     = $Get["content_id"];
	?>
	<div class="contentPageContener">
	
	<ContentSide class="shadow0">
		<div class="contentTitle"><? echo $Title; ?></div>
		
		<ContentBody>
		<? echo html_entity_decode($Content); ?>
		</ContentBody>
		
		<div class="shareBut">
			<icon><img src="img/w/share.png" /></icon>
			<text><? $L->Key("share"); ?></text>
		</div>
		
		<div class="shareButsContener">
		<? $CurrentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
		
			<a href='https://twitter.com/home?status=<? echo $CurrentUrl; ?>' target='_blank' >
				<but class="shadow2" style="background:#4CA2E5;"><img src="img/w/twitter.png "/></but>
			</a>
			
			<a href='https://www.facebook.com/sharer/sharer.php?u=<? echo $CurrentUrl ?>' target='_blank' >
				<but class="shadow2" style="background:#3A5798;"><img src="img/w/facebook.png "/></but>
			</a>
			
			<a 
			href='whatsapp://send?text=<? echo $CurrentUrl; ?>'
			data-action="whatsapp://send?text=<? echo $CurrentUrl; ?>"
			data-href='whatsapp://send?text=<? echo $CurrentUrl; ?>'
			data-text='whatsapp://send?text=<? echo $CurrentUrl; ?>'
			target="_blank" 
			class="ShowOnMobileOnly"
			>
				<but class="shadow2" style="background:#09C800;"><img src="img/w/whatsapp.png "/></but>
			</a>
			
		</div>
		
		<script>
		$(".shareBut").click(function(){
			Duanimate(this,"hide","flipInX","0.900");
			Duanimate(".shareButsContener","show","flipInX","0.900");
		});
		</script>
		
	</ContentSide>
	
	<sideBar class="shadow0">
		<div class="contentTitle"><? $L->Key("channels_from_same_cat"); ?></div>
		<?
			foreach($SqlGet->Table("content",""," content_cat = '$Cat'&& content_id != '$ContentID' Order By content_id ASC Limit 5") AS $Key => $Data){
		?>
		<a hef="content/<? echo $ContentID."/".$Title; ?>">
			<div class="suggestedPost">
				<thumb class="thumb" style="background-image:url('<? echo $Data["content_thumb"]; ?>');"></thumb>
				<suggestedTitle> <? echo $Data["content_title"]; ?> </suggestedTitle>
				<button><? $L->Key("start_streem"); ?></button>
			</div>
		</a>
		<? } ?>
	</sideBar>
	
	
	</div>
	
	
	
	<?
	
}else if($_GET["page"]=="Subscriptions"){
	appendTitle($L->Get("subscribe"));
	appendStyle("styles/".$L->Direction."/Subscriptions.css");
	?>
	
	<div class="FullWidthCover">
		<img src="img/show2.jpg" class="SubscriptionsCoverImage"/>
		
		<div class="SubscriptionsText">
			<h1>
				<? $L->Key("subscriptions_discript"); ?>
			</h1>
		</div>
	</div>
	
	<? if(!$sessions->login){ ?>
		<a href="join" >
			<button class="buttonPattern1 SubscriptionsGoToBut">
				<? $L->Key("create_your_acount"); ?>
			</button>
		</a>
	<? }else{ ?>
		<a href="Planes" >
			<button class="buttonPattern1 SubscriptionsGoToBut">
				<? $L->Key("show_subscriptions"); ?>
			</button>
		</a>
	<? } ?>
	
	
	<?
}else if($_GET["page"]=="login"){
	appendStyle("styles/".$L->Direction."/login_join.css");
	$sessions->login ? GoToPage("index"):"";
	appendTitle($L->Get("login"));
?>

	<div class="LoginErrorSBox shadow2"><img src="img/w/error_shild.png" /><span></span></div>
		<div class="login_join_contener shadow2">
			<div class="JLFormTitle">
				<img src="img/b/lock.png" />
				<h1><? $L->Key("login"); ?></h1>
				<h2><? $L->Key("login_to_continue"); ?></h2>
				</div>
		<form ActionPage="actions.php?login" id="LoginForm">
			<input data-para="user_email" input-type="anything||<? $L->Key("incorrect_email"); ?>" input-holder="<? $L->Key("email_adress"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />	
			<input data-para="user_password" type="password" input-type="password||Error Just Text" input-holder="<? $L->Key("password"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>"/>
			<button class="buttonPattern1 shadow1" ActionType="submit" id="submit_login">
				<? $L->Key("login"); ?>
			</button>
			
			<div class="loginUsingSocialBox">
				<cel>
					<img src="img/i/facebook.png" />
					<? $L->Key("login_using_facebook"); ?>
				</cel>
				<cel>
					<img src="img/i/twitter.png" />
					<? $L->Key("login_using_twitter"); ?>
				</cel>
			</div>
		</form>
		</div>
	
	<div class="Login_Joing_HelpBox shadow2">
		<? $L->Key("did_you_forget_password"); ?>
		<a href="passwordReset"><span><? $L->Key("click_here_to_reset"); ?></span></a>
	</div>
	
	<script>
	$("#LoginForm").ApplayMaterialDesignForm({});
			$("#LoginForm").submit(function(){
				$("#LoginForm").find("input").each(function(){
			$(this).ChekInputErrors();
		});
			$("#LoginForm").FastSendData({
				TaskStart:function(){

				},
				TaskFinish:function(Data){
					if(Data=="true"){
					Duanimate(".LoginErrorSBox","hide","pulse","500");
					window.location = "index";
					}else{
					if(Data=="false"){
					Duanimate(".Login_Joing_HelpBox","jello","pulse","0.900");
					$(".LoginErrorSBox span").html("<? $L->Key("wrong_email_or_password"); ?>");
					}else if(Data=="BlockedUser"){
					$(".LoginErrorSBox span").html("<? $L->Key("wrong_email_or_password"); ?>");
					}else{
					$(".LoginErrorSBox span").html("<? $L->Key("un_knowed_error"); ?>");
					}
					Duanimate(".LoginErrorSBox","show","pulse","500");	
					}
				}
				});
			});
			</script>
			


<? 
}else if($_GET["page"]=="join"){
	$sessions->login ? GoToPage("index"):"";
	appendStyle("styles/".$L->Direction."/login_join.css");
	appendTitle($L->Get("create_your_acount"));
?>


	<div class="login_join_contener shadow2">
		<div class="JLFormTitle">
			<img src="img/b/user.png" />
			<h1><? $L->Key("join"); ?></h1>
			<h2><? $L->Key("join_to_continue"); ?></h2>
		</div>
	<form ActionPage="actions.php?join" id="JoinForm">
	<input data-para="user_name" input-type="text||<? $L->Key("join_name_char_roll"); ?>" input-holder="<? $L->Key("name"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" input-min="6||<? $L->Key("join_name_min_lenght_roll"); ?> "/>	
	
	<input data-para="user_email" input-type="email||<? $L->Key("incorrect_email"); ?>" input-holder="<? $L->Key("email_adress"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />	
	
	<input data-para="user_password" type="password" input-type="password||Error Just Text" input-holder="<? $L->Key("password"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" input-min="6||<? $L->Key("password_most_than"); ?>" id="password"/>	
	
	<input data-para="repassword" type="password" input-type="password||Error Just Text" input-holder="<? $L->Key("password_re"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" input-repassword="#password||<? $L->Key("password_un_duplicate"); ?>"/>
	

	
	<div class="JoinTermsEccept">
		<? $L->Key("you_agree_by_click"); ?>
		<openTerms><u><? $L->Key("this_agremint"); ?></u></openTerms>
			<script>
				$("openTerms").click(function(){
					Duanimate("JoinTerms","toggle","ZoomIn","0.200");
				});
			</script>
		<JoinTerms>
			<?
			// Get Info Value Fom DB , By Lang
			$GFild = $SqlGet->Table("info",""," info_type='terms' &&  info_lang='". $L->MyLang."'");
				// Print It
				echo html_entity_decode($GFild[0]["info_value"]);
			?>
		</JoinTerms>
	</div>
	
	<button class="buttonPattern1 shadow1" ActionType="submit" id="submit_login"><? $L->Key("join"); ?></button>
	</form>
	
	<div class="loginUsingSocialBox">
		<cel>
			<img src="img/i/facebook.png" />
			<? $L->Key("login_using_facebook"); ?>
		</cel>
		<cel>
			<img src="img/i/twitter.png" />
			<? $L->Key("login_using_twitter"); ?>
		</cel>
	</div>
			
	<script>
	$("#JoinForm").ApplayMaterialDesignForm({});
			$("#JoinForm").submit(function(){
				$("#JoinForm").find("input").each(function(){
			$(this).ChekInputErrors();
		});
			$("#JoinForm").FastSendData({
				TaskStart:function(){

				},
				TaskFinish:function(Data){
					if(Data=="true"){
						window.location = "index";
					}else{
					if(Data=="WrongEmail"){
					var ErrorMesTitle   = "<? $L->Key("incorrect_email"); ?>";
					var ErrorMesContent = "<? $L->Key("please_try_agine"); ?>"
					}else if(Data=="NotAllowedUserName"){
					var ErrorMesTitle   = "<? $L->Key("not_allowed_username"); ?>";
					var ErrorMesContent = "<? $L->Key("join_name_char_roll"); ?>";
					}if(Data=="PhoneNumberError"){
					var ErrorMesTitle   = "<? $L->Key("incorrect_phone"); ?>";
					var ErrorMesContent = "<? $L->Key("incorrect_phone_roll"); ?>";
					}else if(Data=="ShortPassword"){
					var ErrorMesTitle   = "";
					var ErrorMesContent = "<? $L->Key("password_most_than"); ?>";
					}else if(Data=="ExistEmail"){
					var ErrorMesTitle   = "<? $L->Key("email_exist"); ?>";
					var ErrorMesContent = "<? $L->Key("click_here_to_reset"); ?>";
					}else{
					var ErrorMesTitle   = "<? $L->Key("un_knowed_error"); ?>";
					var ErrorMesContent = "<? $L->Key("send_adminstretor"); ?>";
					}
					
						$("body").sweet_alert({
						type: "error",
						title: ErrorMesTitle,
						butContent: "<? $L->Key("close"); ?>",
						description : ErrorMesContent
						});	
					}
				}
				});
			});
			</script>
		</div>

<?
////////////////////////////////////// Acount Settings Control Panils Pages  /////////////////////////////////////
}else if($pg_page=="CompleteYourAcount"){
	//$sessions->login ? GoToPage("index"):"";
	appendStyle("styles/".$L->Direction."/login_join.css");
	appendTitle($L->Get("create_your_acount"));
?>
	
	<div class="login_join_contener shadow2">
		<div class="JLFormTitle">
			<img src="img/b/user.png" />
			<h1><? $L->Key("complete_your_information"); ?></h1>
			<h2><? $L->Key("complete_your_information_to_complete_using_your_acount"); ?></h2>
		</div>
	<form ActionPage="actions.php?join" id="JoinForm">
	<input data-para="user_name" input-type="text||<? $L->Key("join_name_char_roll"); ?>" input-holder="<? $L->Key("name"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" input-min="6||<? $L->Key("join_name_min_lenght_roll"); ?> "/>	
	
	<input data-para="user_email" input-type="email||<? $L->Key("incorrect_email"); ?>" input-holder="<? $L->Key("email_adress"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />	
	
	<input data-para="user_password" type="password" input-type="password||Error Just Text" input-holder="<? $L->Key("password"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" input-min="6||<? $L->Key("password_most_than"); ?>" id="password"/>	
	
	<input data-para="repassword" type="password" input-type="password||Error Just Text" input-holder="<? $L->Key("password_re"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" input-repassword="#password||<? $L->Key("password_un_duplicate"); ?>"/>
	

	
	<div class="JoinTermsEccept">
		<? $L->Key("you_agree_by_click"); ?>
		<openTerms><u><? $L->Key("this_agremint"); ?></u></openTerms>
			<script>
				$("openTerms").click(function(){
					Duanimate("JoinTerms","toggle","ZoomIn","0.200");
				});
			</script>
		<JoinTerms>
			<?
			// Get Info Value Fom DB , By Lang
			$GFild = $SqlGet->Table("info",""," info_type='terms' &&  info_lang='". $L->MyLang."'");
				// Print It
				echo html_entity_decode($GFild[0]["info_value"]);
			?>
		</JoinTerms>
	</div>
	
	<button class="buttonPattern1 shadow1" ActionType="submit" id="submit_login"><? $L->Key("join"); ?></button>
	</form>
	
	</div>
	
		<script>
		$("#JoinForm").ApplayMaterialDesignForm({});
			$("#JoinForm").submit(function(){
				$("#JoinForm").find("input").each(function(){
			$(this).ChekInputErrors();
		});
		});
		</script>
	
<?
}else if($pg_page=="logout"){
	!$sessions->login ? GoToPage(RootPath . "/index"):"";
		$sessions->end();
			GoToPage(RootPath . "/index");
		
}else if($pg_page=="Activation"){
	appendTitle($L->Get("complete_activation"));
	appendStyle("styles/".$L->Direction."/AccountActive.css");
		/// Redirect If Not Login
		!$sessions->login ? GoToPage(RootPath . "/login"):"";
			/// Redirect If Acount Active
			if($sessions->AcountActivation !=="NotActive" ){ GoToPage(RootPath . "/index"); }
			appendTitle($L->Get("account_active"));
?>

	<?
	// Activation If User Came From Active Link
	if(isset($pg_accessToken) && !empty($pg_accessToken)){
		if(sql_count("users"," user_session='$pg_accessToken' ") > 0){
			update("users",array("user_acount_activation" =>"Active")," user_session='$pg_accessToken' ");
			?>
			<div class="YourActivationState shadow0">
				<img src="img/w/true.png" />
				<? $L->Key("you_account_has_activated_we_will_redirect_you_in_seconds"); ?>
			</div>
			
			<script>
				$(document).ready(function(){
					setTimeout(function(){
						Duanimate(".YourActivationState","none","pulse","0.900");
						window.location = "index";
					},4000);
				});
			</script>
			<?
		}
	}else{
	?>

	<a href="logout" >
		<button class="singerActiveLogout shadow">
			<? $L->Key("logout"); ?>
			<img src='img/w/shutdown.png' />
		</button>
	</a>

	<div class="activition_box shadow1" id="activeStep">
		<div class="activition_title"><? $L->Key("account_active"); ?></div>
			<div class="activition_note"><? $L->Key("last_step_we_send_active_code_to_your_email"); ?>
				<span id="my_email_now"> <? echo $sessions->email ?> </span>
				<? $L->Key("take_a_look_to_your_email_and_enter_code_inside_box_below"); ?>
				<b> (<? $L->Key("active_code_may_need_some_time_to_delever"); ?>) </b>
			</div>
	
	<input placeholder="<? $L->Key("enter_activation_code_here"); ?>" id="activeCodeInput"/>
	<button id="ActiveCodeComplete"><? $L->Key("complete_activation"); ?></button>
	
	<script>
		function ToggleBetweenActivePanils(){
			Duanimate("#activeStep","toggle","bounceOutLift","400");
			Duanimate("#openActiveHelpCinter","toggle","flipInX","800");
			Duanimate(".activition_proplems","toggle","pulse","300");
				$(".edit_email").hide();
				$(".resend_activation_email").hide();
		}
		
		$("#ActiveCodeComplete").click(function(){
			$("#activeStep").freez();
			ActivetionCode = $("#activeCodeInput").val();
			$.post("actions.php",{ActiveMyAcount:ActivetionCode},function(data){
				$("#activeStep").unfreez();
				if(data == "false"){
					$("body").sweet_alert({ type: "error",
						title       : "<? $L->Key("incorrect_activation_code"); ?>",
						butContent  : "<? $L->Key("ok"); ?> !",
						description : "<? $L->Key("correct_your_activation_code"); ?>"
					});	
				}else if(data == "true"){
					$("body").sweet_alert({
						type: "true",
						title       : "<? $L->Key("greate_evere_thins_ok"); ?>",
						butContent  : "<? $L->Key("ok"); ?> !",
						description : "<? $L->Key("you_account_has_activated_we_will_redirect_you_in_seconds"); ?>"
					});	
						setTimeout(function(){ window.location = "index"; },5000);
				}
			});
		});
	</script>
	
	</div>
	
	
	
	<div class="activition_box shadow1" style="display:none;"  id="openActiveHelpCinter">
		<div class="activition_title"><? $L->Key("activation_center"); ?></div>
		<div class="activition_note">
			<? $L->Key("if_you_think_there_is_proplem_in"); ?>
				<b><? $L->Key("email_adress"); ?></b>
			<? $L->Key("you_can_edit_your_email_mess"); ?>
		</div>
	
		<div class="activition_proplems_option" id="editEmailOp">
			<img src="img/b/pen.png" />
			<? $L->Key("edit"); echo" "; $L->Key("email_adress"); ?>
		</div>
			<div class="edit_email" style="display:none;">
				<input id="edit_email_input" placeholder="<? $L->Key("add_new_email_adress"); ?>"/>
				<button id="Update_Email_Now"><? $L->Key("update"); ?></button>

	<script>
	$("#Update_Email_Now").click(function(){
		Email = $("#edit_email_input").val();
		$.post("actions.php?EditMyInfo=HTML&key=user_email&value="+Email,function(data){		
			if(data == "true"){
			$("body").sweet_alert({
				type: "true",
				title       : "<? $L->Key("email_changed"); ?>",
				butContent  : "<? $L->Key("ok"); ?> !",
				description : "<? $L->Key("we_are_send_new_code_to_your_email"); ?>"
			});	
			$("#my_email_now").html(" "+Email+" ");
			ToggleBetweenActivePanils();
			}else{
				$("body").sweet_alert({
					type: "error",
					title       : "<? $L->Key("error"); ?>",
					butContent  : "<? $L->Key("ok"); ?> !",
					description : data
				});	
			}
		});
	});
	</script>
	</div>
	
	<div class="activition_proplems_option" id="ResendEmailOp">
		<img src="img/b/refresh.png" />
		<? $L->Key("resend_activation_code"); ?>
	</div>
		<div class="resend_activation_email" style="display:none;">
			<? $L->Key("if_you_dont_recive_code_you_can_resend_it"); ?> 
				<b style="color:#3780C2;cursor:pointer;" id="resend_activation_Now">
					<? $L->Key("by_click_here"); ?>
				</b> 
			, <? $L->Key("notice_you_cant_get_new_code_only_once_every_ten_minutes"); ?>
		</div>
	<script>

	$("#editEmailOp").click(function(){ Duanimate(".edit_email","toggle","bounceInLeft","400"); });
	$("#ResendEmailOp").click(function(){ Duanimate(".resend_activation_email","toggle","bounceInLeft","400"); 
	});
	
	$("#resend_activation_Now").click(function(){
		Email = "<? echo $sessions->email; ?>";
		$.post("actions.php",{ResendActivation:Email},function(data){
			if(data == "resended"){
				$("body").sweet_alert({
					type        : "true",
					title       : "<? $L->Key("email_resend_done"); ?>",
					butContent  : "<? $L->Key("thanks"); ?> !!",
					description : "<? $L->Key("see_your_inbox_to_get_active_code"); ?>"
				});	
				ToggleBetweenActivePanils();
			}else if(data == "SlowDown"){
				$("body").sweet_alert({
					type: "error",
					title       : "<? $L->Key("slow_down"); ?>ุง !!",
					butContent  : "<? $L->Key("ok"); ?> !",
					description : "<? $L->Key("we_working_on_resend_email_please_wait_ten_minutes"); ?>"
				});	
			}
		});
	});
	</script>
		<button id="backToActiveStep">
			<? $L->Key("back_to_activation_page"); ?>
		</button>
	</div>
	
	
	<div class="activition_proplems">
		<div class="activition_proplems_title" >
			<? $L->Key("is_email_wrong_click_here_to_show_solve"); ?>
		</div>
		<script>
			$(".activition_proplems,#backToActiveStep").click(function(){
				ToggleBetweenActivePanils();
			});
		</script>
	</div>
	
	<? } ?>
	
<?
}else if($pg_page=="passwordReset"){
	appendTitle($L->Get("reset_password"));
	appendStyle("styles/".$L->Direction."/passwordReset.css");
	/// Redirect If Ist Now Ready Login
	$sessions->login ? GoToPage("index"):"";
?>

	<div class="ResetContener" >
	<?
	/// If User Comes From Reset Link
	if(isset($pg_accessToken) && isset($pg_NewPassword) && !empty($pg_accessToken) && !empty($pg_NewPassword)){
	$AccsessToken = $pg_accessToken;
	$NewPassword  = $pg_NewPassword;
	/*
		if(sql_count("users","user_session='$AccsessToken' ") < 1){
			<? $L->Key("un_expier_link"); ?>
		}else{
			
		}
	*/
	
	?>
	
	<div class="ResetBody shadow0">
		<div class="ResetBodyTitle">
			<span style="color:#1EEE38;"><b><? $L->Key("password_change_done"); ?></b></span>
			<span style="color:#999;"><? $L->Key("will_direct_you_to_index"); ?></span>
		</div>
		
		<a href="index" >
			<div class="ResetHelpLink"><img src="img/b/home.png" /><? $L->Key("go_to_index_without_waite"); ?></div>
		</a>
		
		<a href="help/contact">
			<div class="ResetHelpLink"><img src="img/b/shild.png" /><? $L->Key("did_you_think_your_account_hacked"); ?></div>
		</a>
		</div>
	
	<?
	}else{
	?>
		<div class="ResetBodyNotice" id="ResetErrorHandler"></div>
		<div class="ResetBodyNotice" id="ResetResponseHandler" style="background-color:#FF8C00;">
			<thumb></thumb>
			<span></span>
		</div>
		
		
		<div class="ResetBody shadow0" id="InputYourEmail">
			<div class="ResetBodyTitle"><? $L->Key("password_reset_take_a_time"); ?></div>
				<form ActionPage="actions.php?ChekEmailForReset" id="CheckEmail">
					<input data-para="user_email" input-type="email||<? $L->Key("incorrect_email"); ?>" input-holder="<? $L->Key("email_adress"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
					<button class="buttonPattern1 shadow1" ActionType="submit" id="submit_login"><? $L->Key("next"); ?>
				</form>
		</div>		
		
		<div class="ResetBody shadow0" id="ChoseNewPassword" style="display:none;">
		<div class="ResetBodyTitle"><? $L->Key("chose_new_password"); ?></div>
			<form ActionPage="actions.php?ResetPassword" id="ResetForm">
				<input data-para="user_password" type="password" input-type="password||Error Just Text" input-holder="<? $L->Key("password"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" input-min="6||<? $L->Key("password_most_than"); ?>" id="password"/>	
				
				<input data-para="repassword" type="password" input-type="password||Error Just Text" input-holder="<? $L->Key("password_re"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" input-repassword="#password||<? $L->Key("password_un_duplicate"); ?>"/>

				<button class="buttonPattern1 shadow1" ActionType="submit" id="submit_login"><? $L->Key("change"); ?></button>
			</form>
			
			<script>
			$("#CheckEmail").ApplayMaterialDesignForm({});
					$("#CheckEmail").submit(function(){
						$("#CheckEmail").find("input").each(function(){
					$(this).ChekInputErrors();
				});
					$("#CheckEmail").FastSendData({
						TaskStart:function(){

						},
						TaskFinish:function(Data){

							if(Data.trim()=="NoUserFound"){
								$("#ResetErrorHandler").html("<? $L->Key("no_youser_found"); ?>");
								Duanimate("#ResetErrorHandler","show","pulse","0.500");
							}else{
								Duanimate("#ResetErrorHandler","hide","pulse","0.100");
								Duanimate("#InputYourEmail","hide","pulse","0.300");
								var JData = JSON.parse(Data);
								$("#ResetResponseHandler").children("span").html(JData["name"]);
								$("#ResetResponseHandler").children("thumb").css("background-image","url:("+JData["thumb"]+")");
								Duanimate("#ResetResponseHandler","show","ZoomIn","0.400");
								Duanimate("#ChoseNewPassword","show","ZoomIn","0.600");
							}
						}
						});
					});
					
			$("#ResetForm").ApplayMaterialDesignForm({});
					$("#ResetForm").submit(function(){
						$("#ResetForm").find("input").each(function(){
					$(this).ChekInputErrors();
				});
					$("#ResetForm").FastSendData({
						TaskStart:function(){

						},
						TaskFinish:function(Data){
							if(Data=="true"){
								
							}else{
							
								$("body").sweet_alert({
								type: "error",
								title: "<? $L->Key("password_most_than"); ?>",
								butContent: "X",
								description : ""
								});	
							}
						}
						});
					});
			</script>
		</div>
	
	<? } ?>
	</div>
	
		

<?
}else if($pg_page=="PayNow"){
	appendTitle($L->Get("complete_payment"));
	!$sessions->login ? GoToPage(RootPath . "/index"):"";
	appendStyle("styles/".$L->Direction."/Payments.css");
	appendJs("js/duastDashBoard.js");
	
	// Payments Class
	require_once("functions/Payments.php");
	$Payments = NEW Payments;
	
	// IF Buy Compleate
	if(isset($_GET["CompleteOrder"])){
		
		// IF Order Compleate
		if($_GET["CompleteOrder"] == "true"){
			
			// If user Pay Using PayPal
			if(isset($_GET["Method"]) && $_GET["Method"] =="PayPal"){
				$GetPayment = $Payments->StartPayPalTransaction(
								array(
									"Mod"       => settings("PayPalMod"),
									"PaymentId" => $_GET["paymentId"],
									"PayerID"   => $_GET["PayerID"]
								)
						);
				
				// If Payment sescessful Created
				if($GetPayment["State"]=="approved"){
					
						$PaymentComplete = $Payments->Create(
						array(
								"TransactionId"    => $_GET["paymentId"],
								"Token"            => $_GET["token"],
								"PayerID"          => $_GET["PayerID"],
								"User"             => $sessions->id,
								"State"            => $GetPayment["State"],
								"Amount"           => $GetPayment["Amount"],
								"Method"           => "PayPal",
								"Item"             => $_GET["Item"],
								"EndDate"          => $Payments->Planes($_GET["Item"])["EndDate"],
								"TrueUrl"          => settings("SiteDomain")."/PaymentsHistory?PaymentComplete=true",
								"FalseUrl"         => settings("SiteDomain")."/PaymentsHistory?PaymentComplete=false"
						)
						);
				}

			}
			
		}else{
		// IF Order Compleate Faild
			
			
		}
		
	}else{
	// If User Doesnt Select Serves
	if(!isset($_GET["Server"])){
		header("Location:Planes");
	}
	
	
	/// If User In Chose Payment Method Step
	
	// If Item In Avilble Items
	$itemFound = 0;
	foreach($Payments->Planes("*") AS $k=>$v){
		if($v["Item"] == $_GET["Server"]){
			$itemFound = 1;
		}
	}
	
	
	if($itemFound){
		
	$PaypalLink = $Payments->CreatePaypalPayLink(array(
				"Mod"         => settings("PayPalMod"),
				"Tax"         =>  settings("PayPalPaymentTax"),
				"Shipping"    => "0.00",
				"EndDate"     => $Payments->Planes($_GET["Server"])["EndDate"],
				"Subtotal"    => $Payments->Planes($_GET["Server"])["Amount"],
				"Total"       => $Payments->Planes($_GET["Server"])["Amount"]+settings("PayPalPaymentTax"),
				"Description" => "hello ",
				"TrueUrl"     => settings("SiteDomain")."/PayNow?CompleteOrder=true&Item=".$_GET["Server"]."&Method=PayPal",
				"FalseUrl"    => settings("SiteDomain")."/PayNow?CompleteOrder=false&Item=".$_GET["Server"]."&Method=PayPal"
			));
	}else{
		//If Serves Not Found
		header("Location:Planes");
	}
	
?>	

	<div class="ChoseYourPaymentMethod shadow0">
		<description><? $L->Key("chose_your_payment"); ?></description>
		
		
		<cell>
			<a href="<? echo $PaypalLink; ?>">
			<img src="img/i/paypal.png" />
			PayPal
			</a>
		</cell>
	</div>
<?	

	}
}else if($pg_page=="Planes"){
	!$sessions->login ? GoToPage(RootPath . "/index"):"";
	appendStyle("styles/".$L->Direction."/Payments.css");
	appendJs("js/duastDashBoard.js");
	appendTitle($L->Get("subscriptions"));
	
	// Payments Class
	require_once("functions/Payments.php");
	$Payments = NEW Payments;
?>	

	<div class="pricesContenerBody">
	
	<div class="PaySubscriptionNotic shadow1">
		<? $L->Key("please_chose_your_subscriptions"); ?>
	</div>
	
	<div class="PackageOffer shadow1">
		<price><? echo $Payments->Planes("DayPackage")["Amount"]; ?>$</price>
		<packageTitle><? $L->Key("DayPackage"); ?></packageTitle>
		<a href="PayNow?Server=<? echo $Payments->Planes("DayPackage")["Item"]; ?>">
			<button class="buttonPattern1"><? $L->Key("pay_now"); ?></button>
		</a>
	</div>	
	<div class="PackageOffer shadow1">
		<price><? echo $Payments->Planes("MonthPackage")["Amount"]; ?>$</price>
		<packageTitle><? $L->Key("MonthPackage"); ?></packageTitle>
		<a href="PayNow?Server=<? echo $Payments->Planes("MonthPackage")["Item"]; ?>">
			<button class="buttonPattern1"><? $L->Key("pay_now"); ?></button>
		</a>
	</div>	
	<div class="PackageOffer shadow1">
		<price><? echo $Payments->Planes("ThreeMonthPackage")["Amount"]; ?>$</price>
		<packageTitle><? $L->Key("ThreeMonthPackage"); ?></packageTitle>
		<a href="PayNow?Server=<? echo $Payments->Planes("ThreeMonthPackage")["Item"]; ?>">
			<button class="buttonPattern1"><? $L->Key("pay_now"); ?></button>
		</a>
	</div>	
	<div class="PackageOffer shadow1">
		<price><? echo $Payments->Planes("SixMonthPackage")["Amount"]; ?>$</price>
		<packageTitle><? $L->Key("SixMonthPackage"); ?></packageTitle>
		<a href="PayNow?Server=<? echo $Payments->Planes("SixMonthPackage")["Item"]; ?>">
			<button class="buttonPattern1"><? $L->Key("pay_now"); ?></button>
		</a>
	</div>
	<div class="PackageOffer shadow1">
		<price><? echo $Payments->Planes("YearPackage")["Amount"]; ?>$</price>
		<packageTitle><? $L->Key("YearPackage"); ?></packageTitle>
		<a href="PayNow?Server=<? echo $Payments->Planes("YearPackage")["Item"]; ?>">
			<button class="buttonPattern1"><? $L->Key("pay_now"); ?></button>
		</a>
	</div>
	
	</div>
	
<?	
}else if($pg_page=="MyAcount"){
	!$sessions->login ? GoToPage(RootPath . "/index"):"";
	appendStyle("styles/".$L->Direction."/PrimaryDashBoard.css");
	appendTitle($L->Get("my_account"));
	?>

	<div Class="AcountMenu">
	<a href="MyAcount/GeneralSettings">
		<cel>
			<img src="img/b/settings.png" />
			<span><? $L->Key("my_account_settings"); ?></span>
		</cel>
	</a>
	<a href="MyAcount/PaymentsHistory">
		<cel>
			<img src="img/b/refund.png" />
			<span><? $L->Key("payment_hestory"); ?></span>
		</cel>
	</a>
	</div>
	
	<?
	if($pg_SubPage=="PaymentsHistory"){
	appendStyle("styles/".$L->Direction."/Payments.css");
	appendJs("js/duastDashBoard.js");
	appendTitle($L->Get("payment_hestory"));
	
	// IF User Come From Pay Step
	if(isset($_GET["PaymentComplete"])){
		if($_GET["PaymentComplete"]=="true"){
			?>
			
			<script>
				$("body").sweet_alert({
				type  : "true",
				title : "<? $L->Key("pay_complete_sesc"); ?>",
				butContent  : "<? $L->Key("ok"); ?>",
				description : "<? $L->Key("you_can_now_complete_using_the_website"); ?>"
				});
			</script>
			
			<?
			
		}else{
			?>
			
			<script>
				$("body").sweet_alert({
				type  : "error",
				title : "<? $L->Key("oparation_fild"); ?>",
				butContent  : "<? $L->Key("ok"); ?>",
				description : "<? $L->Key("try_again"); ?>"
				});
			</script>
			
			<?
		}
	}

?>	

	<div class="pricesContenerBody">
	
	<div class="PaymentPageNotice shadow0">
		<img src="img/w/refund.png" />
		<? $L->Key("payments_logs"); ?>
	</div>
	
		<div class="ResponsiveTable shadow0">
		<row>
		<column><? $L->Key("id"); ?></column>
		<column><? $L->Key("payment_method"); ?></column>
		<column><? $L->Key("payed_history"); ?></column>
		<column><? $L->Key("payed_amount"); ?></column>
		<column><? $L->Key("details"); ?></column>
		</row>
		
	<? foreach($SqlGet->Table("payments"," * "," payment_user='".$sessions->id."' ") AS $K=>$V){ ?>
		<row>
		<column><? echo $V["payment_id"]; ?></column>
		<column><? echo $V["payment_method"]; ?></column>
		<column><? echo date("d m Y",$V["payment_date"]); ?></column>
		<column><? echo $V["payment_amount"]; ?> $</column>
		<column><? echo $L->Key($V["payment_item"]); ?></column>
		</row>
	<? } ?>
	
		</div>
		
		
		<script> $(".ResponsiveTable").ResponsiveTable(); </script>
		
	</div>
	
<?	
}else if($pg_SubPage=="GeneralSettings"){
	appendJs("js/duastDashBoard.js");
	// Upload Script
	appendJs("js/fine-uploader.min.js");
?>

	<div class="DashBoardContener">
	
	<div class="MyacountChangeThumb shadow0" >
		<thumb id="AccountPageThumb" class="thumb" 
		style="background-image:url('<? echo "thumb/xl/".$SqlGet->userinfo($sessions->id,"user_thumb"); ?>');"></thumb>
	<span id="SelectIMG"><? $L->Key("click_here_to_chose_new_thumb"); ?></span>
	
	<script>
	$(document).ready(function() {
		var UploadingNotif = "";
		var uploader = new qq.FineUploaderBasic({
			button        : document.getElementById("SelectIMG"),
			multiple      : false,
			request       : {
			endpoint      : "actions.php?ChangThumb=HTML",
			inputName     : "file"
			},
			validation    : {
				allowedExtensions : ['jpg', 'gif', 'png','JPG','PNG','GIF'],
				acceptFiles       : "image/jpeg,image/pjpeg,image/x-png,image/png"
			},
			callbacks     : {
			onUpload      : function() {
				
				$.FloatNotification({
				text              : "<? $L->Key("upolading_new_image"); ?> </br> ...",
				thumb             : "img/w/upload.png",
				GetId             : function(ID){ UploadingNotif = ID; },
				thumbBackground   : "#2F2933"
				});
				
			},
			onComplete    : function(id, fileName,XMLHttpRequest) {
				$("#AccountPageThumb").css("background-image","url("+XMLHttpRequest["Path"]+")");
				$("#AccountHeaderThumb").css("background-image","url("+XMLHttpRequest["Path"]+")");
				
					$("#"+UploadingNotif).remove();
					
					$.FloatNotification({
					text              : "<? $L->Key("image_was_upload"); ?> </br> ...",
					thumb             : "img/w/check.png",
					thumbBackground   : "#1BA261",
					HideAfter         : "4000"
					});
			}
			}
		});
	});
	</script>
	</div>

	
	
	<div class="FastEditSettingsContener shadow0">
		<div class="FastEditSettingsContener_title"><? $L->Key("general_settings"); ?></div>

		
			<!--Change Acount Information Fast-->
			<div class="FastEditSettingsCell" data-settings="user_name" data-actioPage="actions.php?EditMyInfo&"  >
			<label> <? $L->Key("name"); ?> </label>
			<InfoArea>
			<v><? echo $sessions->name; ?></v>
			<ActionArea>
			<Edit><? $L->Key("edit"); ?></Edit>
			<img src="img/b/close.png" class="CloseFastEdit"/>
			</ActionArea>
			</InfoArea>
			<HiddenArea>
			<ErrorReport><img src="img/b/info.png" /><span></span></ErrorReport>
			<input type="text" placeHolder="<?$L->Key("name"); ?>" />
			<button class="buttonPattern1"><? $L->Key("save"); ?></button>
			</HiddenArea>
			</div>
			
			
			<div class="FastEditSettingsCell"  data-notic="true" data-settings="user_email" data-actioPage="actions.php?EditMyInfo&"  >
			<label> <? $L->Key("email_adress"); ?> </label>
			<InfoArea>
			<v><? echo $sessions->email; ?></v>
			<ActionArea>
			<Edit><? $L->Key("edit"); ?></Edit>
			<img src="img/b/close.png" class="CloseFastEdit"/>
			</ActionArea>
			</InfoArea>
			<HiddenArea>
			<ErrorReport><img src="img/b/info.png" /><span></span></ErrorReport>
				<NoticReport>
				<img src="img/b/alert.png" />
				<span><? $L->Key("email_adress_should_re_active"); ?>!</span>
				</NoticReport>
			<input type="text" placeHolder="<?$L->Key("email_adress"); ?>" />
			<button class="buttonPattern1"><? $L->Key("save"); ?></button>
			</HiddenArea>
			</div>	
			
			<div class="FastEditSettingsCell" data-settings="user_phone" data-actioPage="actions.php?EditMyInfo&"  >
			<label> <? $L->Key("phone_number"); ?> </label>
			<InfoArea>
			<v><? echo $SqlGet->userinfo($sessions->id,"user_phone"); ?></v>
			<ActionArea>
			<Edit><? $L->Key("edit"); ?></Edit>
			<img src="img/b/close.png" class="CloseFastEdit"/>
			</ActionArea>
			</InfoArea>
			<HiddenArea>
			<ErrorReport><img src="img/b/info.png" /><span></span></ErrorReport>
			<input type="text" placeHolder="<?$L->Key("name"); ?>" />
			<button class="buttonPattern1"><? $L->Key("save"); ?></button>
			</HiddenArea>
			</div>
			
			<div class="FastEditSettingsCell" data-settings="user_birth_date" data-actioPage="actions.php?EditMyInfo&"  >
			<label> <? $L->Key("birth_date"); ?> </label>
			<InfoArea>
			<v><? echo date("d/m/Y",$SqlGet->userinfo($sessions->id,"user_birth_date")); ?></v>
			<ActionArea>
			<Edit><? $L->Key("edit"); ?></Edit>
			<img src="img/b/close.png" class="CloseFastEdit"/>
			</ActionArea>
			</InfoArea>
			<HiddenArea>
			<ErrorReport><img src="img/b/info.png" /><span></span></ErrorReport>
			<input type="text" placeHolder="<?$L->Key("birth_date_roll"); ?>" />
			<button class="buttonPattern1"><? $L->Key("save"); ?></button>
			</HiddenArea>
			</div>
			
			
			<script>
			$(".FastEditSettingsCell").each(function(){
				$(this).SetFastSettings();
			});
			</script>
		
		</div>

	</div>
	<? } ?>
	
<? }else if($pg_page=="addNew"){ 
	appendStyle("styles/".$L->Direction."/Prime.css");
	appendJs("js/duastDashBoard.js");
	appendJs("js/fine-uploader.min.js");
	$categories = NEW categories;
?>
	
	<div class="AddNewPostBody" style="background-color:transparent;">
	<form id="EditORNew" ActionPage="actions.php?AddOrEditPost">
	

	<div class="addNewContentPartitionTitle">
		التفاصيل الاساسية
		<Check></Check>
	</div>
	
	<div class="addNewContentPartition shadow1">
		<input data-para="title" input-type="arabic||<? $L->Key("join_name_char_roll"); ?>" input-holder="<? $L->Key("the_title"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" id="contentTitle"/>

		<?
		///Insializing Cats
		$CatsArr = "";
		Foreach($categories->Get(array("Select"=>"Parents","Type"=>"cat")) As $ParensK=>$ParensV){
			$CatsArr .= $ParensV["Title"][$L->MyLang]."||".$ParensV["Id"]."{UnSelected},";
				foreach($categories->Get(array("Select"=>"Childs","ParentOf"=>$ParensV["Id"])) AS $ChildK=>$ChildV){
					$CatsArr .= $ChildV["Title"][$L->MyLang]."||".$ChildV["Id"].",";
				}
		}
			$CatsArr = trim($CatsArr,",");
		
		?>
		
		<input data-para="cat" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="<? $L->Key("the_cat"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="<? echo $CatsArr; ?>"  id="contentCat" />	
		
	</div>
		<?
		/// Get Citys
		$CitysArr = "";
		Foreach($categories->Get(array("Select"=>"Parents","Type"=>"location")) As $ParensK=>$ParensV){
			$CitysArr   .= $ParensV["Title"][$L->MyLang]."||".$ParensV["Id"].",";
			$NabaHoodArr = "";

				foreach($categories->Get(array("Select"=>"Childs","ParentOf"=>$ParensV["Id"])) AS $ChildK=>$ChildV){
					$NabaHoodArr .= $ChildV["Title"][$L->MyLang]."||".$ChildV["Id"].",";
				}
					$NabaHoodArr = trim($NabaHoodArr,",");
		?>
			<script>
				$(document).ready(function(){
					City_<? echo $ParensV["Id"]; ?> = "<? echo $NabaHoodArr; ?>";
					
				});
			</script>
		<?
			
		}
			$CitysArr = trim($CitysArr,",");
		?>
	<div class="addNewContentPartitionTitle">
		العنوان
		<Check></Check>
	</div>
	
	<div class="addNewContentPartition shadow1">
		<input data-para="city" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="<? $L->Key("the_city"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="<? echo $CitysArr; ?>"  id="contentCity" />	

		<input data-para="NabaHood" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="الحي" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="اختر المينة اولا||null"  id="NabaHood" />	
		
		
		
		<script>
		$("#contentCity").change(function(){
			var SelectedId = $(this).val();
			var HoodsArr = window["City_"+SelectedId];
			$("#NabaHood").attr("select-items",HoodsArr);
			$("#NabaHood").trigger('ItemsChange');
			//alert(HoodsArr);
		});
		
		$("#contentCat").change(function(){
			var SelectedId = $(this).val();
			MoreFormsInfo(SelectedId);
		});
		</script>
		
		
		<input data-para="adress" input-type="arabic||<? $L->Key("cannot_be_empty"); ?>" input-holder="عنوان المنزل والشارع" input-required="true||مثال : شارع الرياض , منزل 435" id="contentTitlex"/>
		
		
		
	</div>
		

	<div class="addNewContentPartitionTitle" style="display:none;">
		مواصفات اضافية
		<Check></Check>
	</div>
	
	
	<div class="addNewContentPartition shadow1" id="CarsMoreInfo" style="display:none;">
		<?
		/// Get Models
		$CarsArr = "";
		Foreach($categories->Get(array("Select"=>"Parents","Type"=>"CarModel")) As $ParensK=>$ParensV){
			$CarsArr       .= $ParensV["Title"][$L->MyLang]."||".$ParensV["Id"].",";
			$CarsClassesArr = "";

				foreach($categories->Get(array("Select"=>"Childs","ParentOf"=>$ParensV["Id"])) AS $ChildK=>$ChildV){
					$CarsClassesArr .= $ChildV["Title"][$L->MyLang]."||".$ChildV["Id"].",";
				}
					$CarsClassesArr = trim($CarsClassesArr,",");
		?>
			<script>
				$(document).ready(function(){
					Car_<? echo $ParensV["Id"]; ?> = "<? echo $CarsClassesArr; ?>";
					
				});
			</script>
		<?
			
		}
			$CarsArr = trim($CarsArr,",");
			
		/// Modeles Years
		$ModelesYear = "اختر سنة الاصدار||null,";
		for($i = 0;$i<=30;$i++){
			$Year = date("Y",time())-$i;
			$ModelesYear .= $Year."||".$Year.",";
		}
			$ModelesYear = trim($ModelesYear,",");
		?>
		
		<input data-para="motor" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="الماركة" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="<? echo $CarsArr; ?>"  id="motor" />	

		<input data-para="motorClass" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="الموديل" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="اختر المينة اولا||null"  id="motorClass" />
		
		<input data-para="motorClass" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="سنة الاصدار" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="<? echo $ModelesYear; ?>"  id="motorClass" />	
		
		<script>
		$("#motor").change(function(){
			var SelectedId = $(this).val();
			var ClassesArr = window["Car_"+SelectedId];
			$("#motorClass").attr("select-items",ClassesArr);
			$("#motorClass").trigger('ItemsChange');
			//alert(HoodsArr);
		});
		</script>
		
	</div>
	
	<div class="addNewContentPartitionTitle" style="display:none;">
		نوع الطلب
		<Check></Check>
	</div>
	
	
	<div class="addNewContentPartition shadow1" id="NorrmalOrderType" style="display:none;">
		<input data-para="OrderType" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="نوع الطلب" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="
		اختر نوع الطلب||null
		,طلب||order
		,عرض||forPay
		"  id="OrderType" />
		
	</div>	
	
	<div class="addNewContentPartitionTitle" style="display:none;">
		نوع الطلب
		<Check></Check>
	</div>
	
	<div class="addNewContentPartition shadow1" id="HomesAndCars" style="display:none;">
		<input data-para="OrderType" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="نوع الطلب" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="
		اختر نوع الطلب||null
		,طلب||order
		,عرض||forPay
		,طلب ايجار||wantRint
		,عرض ايجار||forRint
		"  id="OrderType" />
		
	</div>
	
	<div class="addNewContentPartitionTitle">
		الصور و العرض
		<Check></Check>
	</div>
	
	
	<div class="addNewContentPartition shadow1" id="ImagesAria">
		<div class="buttonPattern1 UploadImgToPostBut" id="SelectPostThumb">
			<? $L->Key("click_here_to_upload_new_thumb"); ?>
		</div>
		<input data-para="youtube" input-type="arabic||<? $L->Key("join_name_char_roll"); ?>" input-holder="رابط يوتيوب للمنتج ان وجد" input-required="true||<? $L->Key("cannot_be_empty"); ?>" id="youtube"/>
	</div>
	
	
			
			<script>
			$(document).ready(function() {
				ImageNumber = 0;
				ImagesArray = "";
				var UploadingNotif = "";
				var uploader = new qq.FineUploaderBasic({
					button        : document.getElementById("SelectPostThumb"),
					multiple      : false,
					request       : {
					endpoint      : "actions.php?UploadPostThumb=HTML",
					inputName     : "file"
					},
					validation    : {
						allowedExtensions : ['jpg', 'gif', 'png','JPG','PNG','GIF'],
						acceptFiles       : "image/jpeg,image/pjpeg,image/x-png,image/png"
					},
					callbacks     : {
					onUpload      : function() {
						ImageNumber = ImageNumber+1;
						if(ImageNumber == 6){
							$("#SelectPostThumb").hide();
						}else{
							$("#SelectPostThumb").show();
						}
			
						$.FloatNotification({
						text              : "<? $L->Key("upolading_new_image"); ?> </br> ...",
						thumb             : "img/w/upload.png",
						GetId             : function(ID){ UploadingNotif = ID; },
						thumbBackground   : "#2F2933"
						});
						
					},
					onComplete    : function(id, fileName,XMLHttpRequest) {
						ImagesArray = ImagesArray+","+XMLHttpRequest["Path"];
						ImagesArray = ImagesArray.replace(/(^,)|(,$)/g,'');
						$("#post_thumb").val(ImagesArray);
						
						$("#ImagesAria").append('<div class="PostThumbUplodedCel shadow0 thumb" style="background-image:url('+XMLHttpRequest["Path"]+');" Imgurl="'+XMLHttpRequest["Path"]+'"><img src="img/w/delete.png" class="shadow0" onClick="RemovePostImage(this)"/></div>');
						
							$("#"+UploadingNotif).remove();
							$.FloatNotification({
							text              : "<? $L->Key("image_was_upload"); ?> </br> ...",
							thumb             : "img/w/check.png",
							thumbBackground   : "#1BA261",
							HideAfter         : "4000"
							});
					}
					}
				});
			});
			
			function RemovePostImage(Element){
				$(Element).parents(".PostThumbUplodedCel").remove();
				var ImgUrl = $(Element).parents(".PostThumbUplodedCel").attr("Imgurl");
				ImagesArray = ImagesArray.replace(ImgUrl,"");
				ImagesArray = ImagesArray.replace(',,',",");
				ImagesArray = ImagesArray.replace(/(^,)|(,$)/g,'');
				alert(ImagesArray+" +++ "+ImgUrl);
			}
			</script>
		
	<div class="addNewContentPartitionTitle">
		نص الاعلان
		<Check></Check>
	</div>
	
	
	<div class="addNewContentPartition shadow1" id="ContentText">
		<input data-para="content" input-type="textarea||<? $L->Key("incorrect_email"); ?>" input-holder="<? $L->Key("content"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" id="contentContent"/>
	</div>
	
		<input type="hidden" data-para="serial" value="<? echo $PostSerial; ?>"/>
		<input type="hidden" data-para="thumb"  id="post_thumb"/>
		
		<button class="buttonPattern1" ActionType="submit" id="">
			<? $L->Key("save"); ?>
			<img src="img/w/check.png"/>
		</button>
		
	</form>
		<script>
		/// More Information Showed As Passible Even The Categorie
		function MoreFormsInfo(CatId){
			$("#CarsMoreInfo").hide();
			$("#CarsMoreInfo").prev(".addNewContentPartitionTitle").hide();
			
			$("#NorrmalOrderType").hide();
			$("#NorrmalOrderType").prev(".addNewContentPartitionTitle").hide();
			
			$("#HomesAndCars").hide();
			$("#HomesAndCars").prev(".addNewContentPartitionTitle").hide();

			if(jQuery.inArray(CatId,["20"]) != -1){
				
				$("#CarsMoreInfo").show();
				$("#CarsMoreInfo").prev(".addNewContentPartitionTitle").show();
				
				$("#HomesAndCars").show();
				$("#HomesAndCars").prev(".addNewContentPartitionTitle").show();
				
			}else if(jQuery.inArray(CatId,["12"]) != -1){
				
				$("#HomesAndCars").show();
				$("#HomesAndCars").prev(".addNewContentPartitionTitle").show();
				
			}else if(jQuery.inArray(CatId,["9"]) != -1){
				
				$("#NorrmalOrderType").show();
				$("#NorrmalOrderType").prev(".addNewContentPartitionTitle").show();
				
			}
		}
		/// More Information Showed As Passible Even The Categorie
		</script>
			<script> $("#EditORNew").ApplayMaterialDesignForm({});</script>
			<script>
			$("#EditORNew").submit(function(){
				CheckedDon = false;
				$("#EditORNew").find("input").each(function(){

					$(this).ChekInputErrors();
					if($(this).ChekInputErrors()==false){
						CheckedDon = false;
						return false;
					}else{
						CheckedDon = true;
					}
					
				});
		
				if(CheckedDon == true){
					$("#EditORNew").FastSendData({
						TaskStart:function(){

						},
						TaskFinish:function(Data){

							if(Data == "true"){
								$("body").sweet_alert({
								type: "true",
								title: "<? $L->Key("posted_done"); ?>",
								butContent: "<? $L->Key("close"); ?>",
								description : "<? $L->Key("redirecting"); ?>"
								});

								setTimeout(function(){
									load_nav("DashBoard/Contents");
								},1000);
								
							}else{
								$("body").sweet_alert({
								type: "error",
								title: "<? $L->Key("un_knowed_error"); ?>",
								butContent: "<? $L->Key("close"); ?>",
								description : "<? $L->Key("try_again"); ?>"
								});
							}
						}
						});
				}
			});
			</script>
		
	
	</div>
	
	
<? }else if($pg_page=="Dashboard"){
/// LC Switch JS
appendStyle("js/lc_switch/lc_switch.css");
appendJs("js/lc_switch/lc_switch.js");

appendStyle("styles/".$L->Direction."/PrimaryDashBoard.css");
appendJs("js/duastDashBoard.js");

appendTitle($L->Get("control_panel"));
?>	

	<div class="MaterialMenu" id="settings_menu">
	
		<mobileMenu>
			<mobileIcon>
				<img src="img/w/menu.png" />
			</mobileIcon>
			<TheTitle><? $L->Key("settings_of_content"); ?><TheTitle>
		</mobileMenu>
		
		<a href="Dashboard/Contents">
			<img src="img/b/tv.png"/>
			<? $L->Key("channels"); ?>
		</a>
		<a href="Dashboard/General">
			<img src="img/b/cylinder.png"/><? $L->Key("general_settings"); ?>
		</a>		
		<a href="Dashboard/categories">
			<img src="img/b/categories.png"/><? $L->Key("categories"); ?>
		</a>
		<a href="Dashboard/terms">
			<img src="img/b/eye.png"/><? $L->Key("terms"); ?>
		</a>
		<a href="Dashboard/users">
			<img src="img/b/users.png"/><? $L->Key("users"); ?>
		</a>
		<a href="Dashboard/plugins">
			<img src="img/b/plugin.png"/><? $L->Key("more"); ?>
		</a>
		
			<script>
				$("#settings_menu").MaterialMenu({
					BackgroundColor :"#FFFFA6",
					LinksColor      :"#555",
					OpenedLinkColor :"#29D9C2",
					MobileContener  :"#29D9C2",
					MobileIconColor :"#2F2933"
				});
			</script>

	</div>
	
	

<? if($pg_Panil =="Contents"){
	appendTitle($L->Get("content"));
	
	$categories = NEW categories;
	
	// Get Avilble Langs And Set Categories Array
	$CategorieArray = $L->Get("please_chose_one")."||null,";
	$AllCats = $categories->Get(array(
				"Select"=>"Parents"
		));
	
	foreach($AllCats AS $ParentK=>$ParenV ){
		$CategorieArray .= $ParenV["Title"][$L->MyLang]."||".$ParenV["Id"].",";
	}
		$CategorieArray = trim($CategorieArray,",");
	
?>
	
	<? if(!isset($_GET["AddNew"]) && !isset($_GET["Edit"])){
		appendTitle($L->Get("content_managment"));
	?>
	
	<a href="Dashboard/Contents?AddNew">
		<button class="buttonPattern1 addNewContent">
			<img src="img/w/plus.png" />
			<? $L->Key("add_new_content"); ?>
		</button>
	</a>
	
		<div class="DuTemplateContenet" style="margin-top:10px;">
			<div id="postsAfterThis"></div>
			<img src="img/loading.gif" class="DuTemplateLoadingMore" />
			<div class="Pagination"></div>
		</div>
	
	<script>
	$(document).ready(function(){
		Template = new DuBuildTemplate();
		TotalResult   = 0;
		CurrentResult = 1;
		
		Template.Apply({
			Block           : "DashBoardPosts"
		});
		
		GetData = function(_Page){
			Template.Start({
				Filter          : "content_id[!=]500",
				ResultsPerPage  : "10",
				Page            : _Page,
				OrderBy         : "content_id",
				OrderKey        : "Desc",
				Table           : "content",
				
				IfLoading       : function(){
					ScrolUp("html,body");
					$(".DuTemplateLoadingMore").fadeIn();
				},
				IfFinish        : function(Data){
					$("#postsAfterThis").html(Data);
					$(".Pagination").fadeIn();
					$(".DuTemplateLoadingMore").fadeOut();
				},
				CountTotal      : function(Data){
					TotalResult = Data;
				}
			});
		}
		
		GetData(CurrentResult);

	});
	</script>

	<script>
	$(document).ready(function(){

	pagination = new Pagination({ Contener:".Pagination" });
		
		pagination.Build({
			Total         : TotalResult,
			PerPage       : 10,
			CurrentPage   : CurrentResult,
			ButtonsNumber : 10
		});
	});
	
	$(document).on("click",".Pagination button",function(){
		CurrentResult = $(this).text();
		pagination.Build({
			Total         : TotalResult,
			PerPage       : 10,
			CurrentPage   : CurrentResult,
			ButtonsNumber : 10
	});
			GetData(CurrentResult);
	});
	</script>
	
	
	<script>
		$(".DashBoardPostGropCel").mouseenter(function(){
			var optionsCel = $(this).children(".DashBoardPostCelOptions");	
			Duanimate(optionsCel,"show","zoomIn","0.600");
		});
		
		$(".DashBoardPostGropCel").mouseleave(function(){
			var optionsCel = $(this).children(".DashBoardPostCelOptions");	
			Duanimate(optionsCel,"hide","zoomOut","0.100");
		});
	</script>
	
	<? }else{ ?>
	
	<div class="ContentPostHeader shadow0">
		<img src="img/w/plus.png" />
		<? $L->Key("add_new_content"); ?>
	</div>
	
	<?
		$PostSerial = time().rand('0','1000');
	?>
	
	<div class="ContentPostBody shadow2"  style="background:#fff;">
	<?
	/// Add New Content Page
	if(isset($_GET["AddNew"]) || isset($_GET["Edit"])){
		// Upload Script
		appendJs("js/fine-uploader.min.js");
		
		if(isset($_GET["Edit"])){
			$Get           = $SqlGet->Table("content",""," content_id='".$_GET["Edit"]."' ")[0];
			$Title         = $Get["content_title"];
			$Content       = $Get["content_content"];
			$Cat           = $Get["content_cat"];
			$ContentID     = $Get["content_id"];
			$Thumb         = $Get["content_thumb"];
			$PostSerial    = $Get["content_serial"];
			?>
			<script>
			$(document).ready(function(){
				$("#contentTitle").val("<? echo $Title; ?>");
				$("#contentContent").prev('textarea').text("<? echo html_entity_decode($Content); ?>");
				$("#contentCat").val("<? echo $Cat; ?>");
				$("#MyPostThumb").css("background-image","url('<? echo $Thumb; ?>')");
				$("#post_thumb").val('<? echo $Thumb; ?>');
			});
			</script>
			<?
		}
	?>
	
	<form id="EditORNew" ActionPage="actions.php?AddOrEditPost">
	
		<input data-para="title" input-type="text||<? $L->Key("join_name_char_roll"); ?>" input-holder="<? $L->Key("the_title"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" id="contentTitle"/>

		<input data-para="cat" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="<? $L->Key("the_cat"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="<? echo $CategorieArray; ?>"  id="contentCat" />	


		<input data-para="content" input-type="textarea||<? $L->Key("incorrect_email"); ?>" input-holder="<? $L->Key("content"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" id="contentContent"/>
		
		<input type="hidden" data-para="serial" value="<? echo $PostSerial; ?>"/>
		<input type="hidden" data-para="thumb"  id="post_thumb"/>
		
		<div class="PostThumbUploadArea shadow0" id="SelectPostThumb">
			<span>
				<? $L->Key("click_here_to_chose_view_thumb"); ?>
			</span>
			<thumb class="thumb shadow0" id="MyPostThumb" style="background-image:url('img/w/landscape.png');">
			</thumb>
			
			<script>
			$(document).ready(function() {
				var UploadingNotif = "";
				var uploader = new qq.FineUploaderBasic({
					button        : document.getElementById("SelectPostThumb"),
					multiple      : false,
					request       : {
					endpoint      : "actions.php?UploadPostThumb=HTML",
					inputName     : "file"
					},
					validation    : {
						allowedExtensions : ['jpg', 'gif', 'png','JPG','PNG','GIF'],
						acceptFiles       : "image/jpeg,image/pjpeg,image/x-png,image/png"
					},
					callbacks     : {
					onUpload      : function() {
						
						$.FloatNotification({
						text              : "<? $L->Key("upolading_new_image"); ?> </br> ...",
						thumb             : "img/w/upload.png",
						GetId             : function(ID){ UploadingNotif = ID; },
						thumbBackground   : "#2F2933"
						});
						
					},
					onComplete    : function(id, fileName,XMLHttpRequest) {
						$("#MyPostThumb").css("background-image","url("+ XMLHttpRequest["Path"] +")");
						$("#post_thumb").val(XMLHttpRequest["Path"]);
						
							$("#"+UploadingNotif).remove();
							$.FloatNotification({
							text              : "<? $L->Key("image_was_upload"); ?> </br> ...",
							thumb             : "img/w/check.png",
							thumbBackground   : "#1BA261",
							HideAfter         : "4000"
							});
					}
					}
				});
			});
			</script>
		</div>
		
		<button class="buttonPattern1" ActionType="submit" id="">
			<? $L->Key("save"); ?>
			<img src="img/w/check.png"/>
		</button>
		
	</form>
	
	
	
			<script> $("#EditORNew").ApplayMaterialDesignForm({});</script>
			<script>
			$("#EditORNew").submit(function(){
				CheckedDon = false;
				$("#EditORNew").find("input").each(function(){

					$(this).ChekInputErrors();
					if($(this).ChekInputErrors()==false){
						CheckedDon = false;
						return false;
					}else{
						CheckedDon = true;
					}
					
				});
		
				if(CheckedDon == true){
					$("#EditORNew").FastSendData({
						TaskStart:function(){

						},
						TaskFinish:function(Data){

							if(Data == "true"){
								$("body").sweet_alert({
								type: "true",
								title: "<? $L->Key("posted_done"); ?>",
								butContent: "<? $L->Key("close"); ?>",
								description : "<? $L->Key("redirecting"); ?>"
								});

								setTimeout(function(){
									load_nav("DashBoard/Contents");
								},1000);
								
							}else{
								$("body").sweet_alert({
								type: "error",
								title: "<? $L->Key("un_knowed_error"); ?>",
								butContent: "<? $L->Key("close"); ?>",
								description : "<? $L->Key("try_again"); ?>"
								});
							}
						}
						});
				}
			});
			</script>
		
	
	<? } ?>
	<? } ?>
	
	</div>
	
<? } ?>

<? if(    $pg_Panil =="categories"
	|| $pg_Panil =="Locations"
	|| $pg_Panil =="CarsAndModels"
){ 

if($pg_Panil =="categories")    { $CatType = "cat"; }
if($pg_Panil =="Locations" )    { $CatType = "location"; }
if($pg_Panil =="CarsAndModels" ){ $CatType = "CarModel"; }


	appendTitle($L->Get("cat_management"));
	$NastedCats = 1;

	/* Get Categories */
	$categories = NEW Categories;
	
	?>
	
		<div class="MaterialMenu" id="general_menu">
	
		<mobileMenu>
			<mobileIcon>
				<img src="img/w/menu.png" />
			</mobileIcon>
			<TheTitle><? $L->Key("settings_of_content"); ?><TheTitle>
		</mobileMenu>
		
			<a href="DashBoard/categories">
				<cell><img src="img/w/signpost.png"/><? $L->Key("categories"); ?></cell>
			</a>
			<a href="DashBoard/Locations">
				<cell><img src="img/w/locations.png"/><? $L->Key("locations"); ?></cell>
			</a>
			<a href="DashBoard/CarsAndModels">
				<cell><img src="img/w/meter.png"/><? $L->Key("cars"); ?></cell>
			</a>
			
				<script>
				$("#general_menu").MaterialMenu({
					BackgroundColor:"#FF8C00",
					LinksColor     :"#fff",
					OpenedLinkColor:"#29D9C2",
					MobileContener  :"#29D9C2",
					MobileIconColor :"#2F2933",
					Display         :"block"
				});
				</script>
	</div>
	
	<?			
		
		$ParrentsArrayImplode = $L->Get("none")."||".",";
			if($categories->Get(array("Select"=>"Parents","Type"=>$CatType))){
				foreach($categories->Get(array("Select"=>"Parents","Type"=>$CatType)) AS $K=>$V){
					if(isset($_GET["Edit"])){
						if($V["Id"] !== $_GET["Edit"]){
							$ParrentsArrayImplode .= $V["Title"][$L->MyLang]."||".$V["Id"].",";
						}
					}else{
							$ParrentsArrayImplode .= $V["Title"][$L->MyLang]."||".$V["Id"].",";
					}
				}
			}
		$ParrentsArrayImplode = trim($ParrentsArrayImplode,",");
						
	// Get Avilble Langs
	$GetAvilbleLangs = $L->GetAvilbleLangs();
	$LangsArray      = "";
	foreach($GetAvilbleLangs AS $K=>$V){
		$LangsArray .= $K."||".$V.",";
	}
	$LangsArray = trim($LangsArray,",");
	
	
	/// Add New Categorie
	if(isset($_GET["AddNew"]) || isset($_GET["Edit"])){
		
	// If Its Edit Append Values By Js
	if(isset($_GET["Edit"])){
		$SelectedCat = $categories->Get(array(
						"Select" => "All",
						"Id"     => $_GET["Edit"],
						"Type"   => $CatType
				));
			
		// If Categories Found Not !
		if(!$SelectedCat){
			header("location: ../AccessDenied");
		}
		
	?>
		<script>
		$(document).ready(function(){

			$("#SelectCatThumb").css("background-image","url('<? echo $SelectedCat[0]["Thumb"]; ?>')");
			
			<? if(settings("MultiLangs")) { 
		
			foreach($L->GetAvilbleLangs() AS $K=>$V){
			?>
				$("#title-<? echo $K; ?>").val("<? echo $SelectedCat[0]["Title"][$K]; ?>");
			<?
			}
			
			}else{
			?>
				$("#title-<? echo $L->MyLang; ?>").val("<? echo $SelectedCat[0]["Title"][$L->MyLang]; ?>");
			<?
			}
			?>
			$("#CatId").val("<? echo $_GET["Edit"]; ?>");
			$("#CatParent").val("<? echo $SelectedCat[0]["Parent"]; ?>");
			
			
		});
		</script>
<?	
	}

	// Upload Script
	appendJs("js/fine-uploader.min.js");
	?>
	
	<script>
	$(document).ready(function() {
		var UploadingNotif = "";
		var uploader = new qq.FineUploaderBasic({
			button        : document.getElementById("SelectCatThumb"),
			multiple      : false,
			request       : {
			endpoint      : "actions.php?ChangCatThumb=HTML",
			inputName     : "file"
			},
			validation    : {
				allowedExtensions : ['jpg', 'gif', 'png','JPG','PNG','GIF'],
				acceptFiles       : "image/jpeg,image/pjpeg,image/x-png,image/png"
			},
			callbacks     : {
			onUpload      : function() {
				
				$.FloatNotification({
				text              : "<? $L->Key("upolading_new_image"); ?> </br> ...",
				thumb             : "img/w/upload.png",
				GetId             : function(ID){ UploadingNotif = ID; },
				thumbBackground   : "#2F2933"
				});
				
			},
			onComplete    : function(id, fileName,XMLHttpRequest) {
				$("#SelectCatThumb").css("background-image","url("+ XMLHttpRequest["Path"] +")");
				$("#CatThumb").val(XMLHttpRequest["Path"]);
				
				
					$("#"+UploadingNotif).remove();
					$.FloatNotification({
					text              : "<? $L->Key("image_was_upload"); ?> </br> ...",
					thumb             : "img/w/check.png",
					thumbBackground   : "#1BA261",
					HideAfter         : "4000"
					});
			}
			}
		});
	});
	</script>
	
	<div class="AddNewCatPanilBody shadow0">
		<div class="AddNewCatPanilDescription">
			<span>
			<? if(isset($_GET["Edit"])){ 
				$L->Key("edit");
			   }else{ 
				$L->Key("please_complete_the_failds_to_crete_new_cat");
			   } ?>
			</span>
				<div class="SelectCatThumb">
					<thumb id="SelectCatThumb" class="thumb"></thumb>
					<span>
					
						<? if(isset($_GET["Edit"])){
							$L->Key("edit_the_icon");
						}else{
							$L->Key("chose_new_icon");
						} ?>
					
					</span>
				</div>
		</div>
	
	
	<form id="EditORNew" ActionPage="actions.php?ManagCategories">
		
		<? if(settings("MultiLangs")) { 
		
			foreach($L->GetAvilbleLangs() AS $K=>$V){
		?>
				<input id="title-<? echo $K; ?>" data-para="title-<? echo $K; ?>" input-type="arabic||<? $L->Key("join_name_char_roll"); ?>" input-holder="<? echo $L->Key("the_title")." : ".$V;?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
		<?
			}
		
		}else{ ?>
		
			<input data-para="title-<? echo $L->MyLang; ?>" input-type="arabic||<? $L->Key("join_name_char_roll"); ?>" input-holder="<? $L->Key("the_title"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
			
		<? } ?>


				<input data-para="type" input-type="hidden" value="<? echo $CatType; ?>" />
			
			
			<!--If Nasted Cats Is On-->
			<? if($NastedCats){?>
				<input data-para="parent" input-type="selectMenu||" input-holder="<? $L->Key("nested_from"); ?>" input-required="false||<? $L->Key("cannot_be_empty"); ?>"
				select-items="<? echo $ParrentsArrayImplode; ?>" />	
			<? }else{ ?>
				<input data-para="parent" input-type="hidden" value="" />
			<? } ?>
				
				<input data-para="id"     type="hidden" value="" id="CatId"/>
				<input data-para="thumb"  type="hidden" value="" id="CatThumb"/>
			
			<button class="buttonPattern1" ActionType="submit" id=""><? $L->Key("save"); ?></button>
			
		</form>
	</div>
	
	<script>
	$("#EditORNew").ApplayMaterialDesignForm({});</script>
	<script>
				
		$("#EditORNew").submit(function(){
			$("#EditORNew").find("input").each(function(){
				$(this).ChekInputErrors();
			});
		
		$("#EditORNew").FastSendData({
				TaskStart:function(){
					$("body").freez();	
				},
				TaskFinish:function(Data){
					$("body").unfreez();
					if(Data == true){
						
						$("body").sweet_alert({
						type: "true",
						title: "<? $L->Key("cat_create_sesc"); ?>",
						butContent: "<? $L->Key("close"); ?>",
						description : "..."
						});
						
						setTimeout(function(){
							load_nav("DashBoard/<? echo $pg_Panil; ?>");
						},2000);
					}else{
						$("body").sweet_alert({
						type: "error",
						title: "<? $L->Key("un_knowed_error"); ?>",
						butContent: "<? $L->Key("close"); ?>",
						description : "<? $L->Key("try_again"); ?>"
						});
					}
				}
			});
		});
	</script>
	
	<?
	
	}else{
	
	?>
	<a href="DashBoard/<? echo $pg_Panil; ?>?AddNew">
		<button class="buttonPattern1 AddNewCatBut">
			<? $L->Key("add"); ?>
			<img src="img/w/plus.png" />
		</button>
	</a>
	
	<div class="SettingsBody" id="SetsContener">
	
	
	<? 
		$AllCats = $categories->Get(array(
						"Select"=>"Parents",
						"Type"=> $CatType
				));
				
	// If There is A Catogres			
	if($AllCats){
	?>
	<div class="ResponsiveTable shadow0">

		<row>
		<column><? $L->Key("id"); ?></column>
		<column><? $L->Key("the_title"); ?></column>
		<column><? $L->Key("create_history"); ?></column>
		<column><? $L->Key("creator_by"); ?></column>
		<column><? $L->Key("disition"); ?></column>
		</row>
		
		<? foreach($AllCats AS $ParentK=>$ParenV ){?>
			<row id="contener_<? echo $ParenV["Id"]; ?>">
			<column><? echo $ParenV["Id"]; ?></column>
			<column><? echo $ParenV["Title"][$L->MyLang]; ?></column>
			<column><? echo date("y m d",$ParenV["Date"]); ?></column>
			<column><? echo $SqlGet->UserInfo($ParenV["User"],"user_name"); ?></column>
			<column>
				<button class="buttonPattern1" style="background:transparent;margin:0px 10px;color:#555;"
				onClick="DeletCat('<? echo $ParenV["Id"]; ?>');"><img src="img/b/delete.png" /></button>
				
				<a href="DashBoard/categories?Edit=<? echo $ParenV["Id"]; ?>">
				<button class="buttonPattern1" style="background:transparent;margin:0px 10px;color:#555;"
				><img src="img/b/pen.png" /></button>
				</a>
				
			</column>
			</row>
			
			<? foreach($categories->Get(array("Select"=>"Childs","ParentOf"=>$ParenV["Id"],"Type"=> $CatType)) AS $ChildK=>$ChildV){?>
				<row id="contener_<? echo $ChildV["Id"]; ?>" style="background-color:#FFFFB0;">
				<column><? echo $ChildV["Id"]; ?></column>
				<column><? echo $ChildV["Title"][$L->MyLang]." -> ".$ParenV["Title"][$L->MyLang]; ?></column>
				<column><? echo date("y m d",$ChildV["Date"]); ?></column>
				<column><? echo $SqlGet->UserInfo($ChildV["User"],"user_name"); ?></column>
				<column>
					<button class="buttonPattern1" style="background:transparent;margin:0px 10px;color:#555;"
					onClick="DeletCat('<? echo $ChildV["Id"]; ?>');"><img src="img/b/delete.png" /></button>
					
					<a href="DashBoard/categories?Edit=<? echo $ChildV["Id"]; ?>">
					<button class="buttonPattern1" style="background:transparent;margin:0px 10px;color:#555;"
					><img src="img/b/pen.png" /></button>
					</a>
					
				</column>
				</row>
			<? } ?>
		
		<? } ?>
		
		<script>
		function DeletCat(ID){
			CellID = $("#contener_"+ID);
				confirmbox("<? $L->Key("do_you_want_delete_cat"); ?> ?", function  x(){
					$.post('actions.php',{deleteCat:ID},function(data){  });
					CellID.fadeOut();
				});
		}
		</script>
	</div>
	<? }else{ ?>
	
	<div class="ThereIsNoContent shadow1">
		<? $L->Key("there_is_no_content"); ?>
	</div>
	
	<? } ?>
	
	</div>
	
		<script> $(".ResponsiveTable").ResponsiveTable(); </script>
		
		
	<?
	}
?>
	
	


	

	
<? } ?>

<? if($pg_Panil =="terms" || $pg_Panil =="privacy" || $pg_Panil =="FaQ"|| $pg_Panil =="about"){ 
	appendTitle($L->Get("help_center_management"));
?>
   
	<div class="MaterialMenu" id="content_menu">
	
		<mobileMenu>
			<mobileIcon>
				<img src="img/w/menu.png" />
			</mobileIcon>
			<TheTitle><TheTitle>
		</mobileMenu>
		
			<a href="DashBoard/terms">
				<cell><img src="img/w/handshake.png"/><? $L->Key("terms"); ?></cell>
			</a>
			<a href="DashBoard/privacy">
				<cell><img src="img/w/eye.png"/><? $L->Key("privacy"); ?></cell>
			</a>
			<a href="DashBoard/FaQ">
				<cell><img src="img/w/questions.png"/><? $L->Key("trend_questions"); ?></cell>
			</a>
			<a href="DashBoard/about">
				<cell><img src="img/w/info.png"/><? $L->Key("about"); ?></cell>
			</a>
	</div>
	
				<script>
				$("#content_menu").MaterialMenu({
					BackgroundColor:"#FF8C00",
					LinksColor     :"#fff",
					OpenedLinkColor:"#29D9C2",
					MobileContener  :"#29D9C2",
					MobileIconColor :"#2F2933",
					Display         :"block"
				});
				</script>

	
	<?if($pg_Panil=="terms" || $pg_Panil=="privacy" || $pg_Panil=="about"){?>
		<div class="SettingsBody" id="SetsContener">
			<notice>
				<imgSide> <img src="img/b/info.png" /> </imgSide>
				<? $L->Key("this_form_eccept_html"); ?>
			</notice>
			
			<? foreach($L->GetAvilbleLangs() AS $K=>$V){?>
				<? $GFild = $SqlGet->Table("info",""," info_type='". $pg_Panil ."' &&  info_lang='". $K."'"); ?>
					<TermsAndPrivacyLangTag>
					[<? echo $V; ?>]
					</TermsAndPrivacyLangTag>
					<textarea data-lang="<? echo $K; ?>" class="TermsAndPrivacyInput shadow2" placeHolder="<? $L->Key("insert_text_here"); ?>"><? echo html_entity_decode($GFild[0]["info_value"]); ?></textarea>	
			<? } ?>
			
			<button class="TermsAndPrivacySaveBut buttonPattern1 shadow0" id="SaveSet">
				<? $L->Key("save"); ?>
			</button>
			
				<script>
					$("#SaveSet").click(function(){
						Arr = [];
							$("#SetsContener").children("textarea").each(function(){
								interiorArr = {};
								SetVal      = $(this).val();
								SetLang     = $(this).attr("data-lang");
									interiorArr.Lang = SetLang;
									interiorArr.Val  = SetVal;
										Arr.push(interiorArr);
						});
							Arr = JSON.stringify(Arr);
							$("#SaveSet").css("opacity","0.4");
								$.post("actions.php?EditTermsAndPrivacy=HTML&Fild=<? echo $pg_Panil; ?>&val="+Arr,function(Data){
									$.FloatNotification({
									text              : "<? $L->Key("seved"); ?> </br> ...",
									thumb             : "img/w/check.png",
									thumbBackground   : "#29D9C2",
									HideAfter         : "4000"
									});
								});
					});
				</script>
		</div>
	<? }?>
	
	
	<?if($pg_Panil=="FaQ"){
		appendTitle($L->Get("trend_questions"));
	?>
	<div class="SettingsBody">
		<div class="ContentFilterMenu">
			<button class="buttonPattern1" id="AddNewContent"><? $L->Key("add_new_item"); ?></button>
			<script> $("#AddNewContent").click(function(){ $("#EditSaQ").fadeIn(); }); </script>
		</div>
		
	<div class="lightBox" id="EditSaQ">
		<div class="AddNewContentSetting shadow2">
			<div class="lightBoxBodyTitleBar">
				<st><? $L->Key("QandA"); ?></st>
				<img class="lightBoxBodyTitleBarClose" src="img/w/close.png"/>
			</div>
				<div class="LangContent">
					<? foreach($L->GetAvilbleLangs() AS $K=>$V){?>
						<QPack key="<? echo $K; ?>">
							<div class="LangContentTitle"> [<? echo $V; ?>] </div>
							<input placeHolder="<? $L->Key("write_your_q_here"); ?>"/>
							<textarea placeHolder="<? $L->Key("write_your_a_here"); ?>"></textarea>
						</QPack>
					<? } ?>
					<button class="buttonPattern1" id="SaveQaS"><? $L->Key("save"); ?></button>
				</div>
		</div>
	</div>
					<script>
						// Start Processing
						$("#SaveQaS").click(function(){
							// Array To Hold Values
							var newQaS = [];
								$("QPack").each(function(){
									InteriorQaS = {};
									var Q = $(this).children("input").val();
									var A = $(this).children("textarea").val();
									var L = $(this).attr("key");
										InteriorQaS.Question = Q;
										InteriorQaS.Answer   = A;
										InteriorQaS.Lang     = L;
											// Push Values Info Array
											newQaS.push(InteriorQaS);
								});
							// Convert Json to Sringfy
							newQaS = JSON.stringify(newQaS);
								/// Send Questions and Answers To Server
								$.post("actions.php?QaS=New&value="+newQaS,function(Data){ });
								
							$("#EditSaQ").fadeOut();
						});
					</script>
		<?
		foreach($SqlGet->Table("info",""," info_type='Qas' &&  info_lang='". $L->MyLang ."' Group By info_serial Order By info_id DESC") AS $K=>$V){
			
			$ContentId     = $V["info_id"];
			$ContentArr    = unserialize($V["info_value"]);
			$Question      = $ContentArr["Question"];
			$Serial        = $V["info_serial"];
			$Answer        = $ContentArr["Answer"];
			$AllContentArr = array();
			
				/// Getting All Answers In All Langs
				foreach($SqlGet->Table("info",""," info_serial='$Serial' ") AS $KArr=>$VArr){
					$AllContentArr[$KArr] = unserialize($VArr["info_value"]);	
				}
					$AllContentArr = json_encode($AllContentArr);
		?>
		
			<div class="ContentSettingCel shadow0" id="ccCel_<? echo $ContentId; ?>">
				<content  id="CC_<? echo $ContentId; ?>">
					<? if(strlen($Question) > 30){
						echo substr($Question, 0, 30) . '...';
					}else{
						echo $Question;
					} ?>
				</content>
				<button onClick="DeletQ('<? echo $ContentId; ?>','<? echo $Serial; ?>');" >
					<img src="img/w/delete.png" />
				</button>
					<script>
					$("#CC_<? echo $ContentId; ?>").click(function(){
						$("#EditSaQ").show();
							$.each(<? echo $AllContentArr; ?>,function(C_key,C_value){
								$("QPack[key='"+C_value["Lang"]+"']").children("input").val(C_value["Question"]);
								$("QPack[key='"+C_value["Lang"]+"']").children("textarea").val(C_value["Answer"]);
							});
					});
					</script>
			</div>	
		<? } ?>
		
		<script>
		function DeletQ(ID,Serial){
			var CelCont = "#ccCel_"+ID;
			
			confirmbox("<? $L->Key("do_you_want_delete_q"); ?>?", function  x(){
			$.post('actions.php',{DeletQas:"HTML",Serial:Serial},function(data){  });
				Duanimate(CelCont,"hide","fadeOutLeft","0.600");
			});
			
		}
		</script>
	</div>
	<? }?> 
	
<? }else if($pg_Panil =="General"
   || $pg_Panil =="Conections"
   || $pg_Panil =="Seo"
   || $pg_Panil =="Support"
   || $pg_Panil =="Payments"
   || $pg_Panil =="Devolopers" ){ ?>
   
	<div class="MaterialMenu" id="general_menu">
	
		<mobileMenu>
			<mobileIcon>
				<img src="img/w/menu.png" />
			</mobileIcon>
			<TheTitle><? $L->Key("settings_of_content"); ?><TheTitle>
		</mobileMenu>
		
			<a href="DashBoard/General">
				<cell><img src="img/w/settings.png"/><? $L->Key("general"); ?></cell>
			</a>
			<a href="DashBoard/Conections">
				<cell><img src="img/w/call.png"/><? $L->Key("connetions_settings"); ?></cell>
			</a>
			<a href="DashBoard/Seo">
				<cell><img src="img/w/meter.png"/><? $L->Key("seo_settings"); ?></cell>
			</a>
			<a href="DashBoard/Support">
				<cell><img src="img/w/support.png"/><? $L->Key("support"); ?></cell>
			</a>
			<? if(!empty(settings("DevoloperSettings")) && settings("DevoloperSettings")== DevolopersAuth){ ?>
			<a href="DashBoard/Devolopers">  
				<cell><img src="img/w/code.png"/><? $L->Key("devolopers"); ?></cell>
			</a>
			<? } ?>
			<? if(settings("PaymentsSystem")){ ?>
			<a href="DashBoard/Payments">  
				<cell><img src="img/w/payments.png"/><? $L->Key("the_pay"); ?></cell>
			</a>
			<? } ?>
			
				<script>
				$("#general_menu").MaterialMenu({
					BackgroundColor:"#FF8C00",
					LinksColor     :"#fff",
					OpenedLinkColor:"#29D9C2",
					MobileContener  :"#29D9C2",
					MobileIconColor :"#2F2933",
					Display         :"block"
				});
				</script>
	</div>
	
	<? if($pg_Panil=="Payments"){ 
		appendTitle($L->Get("payment_settings"));
	?>
	<div class="SettingsBody">
	
	
		<div class="SiteSettingCell" data-settings="PayPalClientId" data-value="<?echo settings("PayPalClientId"); ?>">
		<icon><img src="img/w/user.png" /></icon>
		<key>[Paypal Clint ID]</key>
		<CurrentValue>*****************</CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
		<div class="SiteSettingCell"  data-settings="PayPalClientSecret" data-value="<?echo settings("PayPalClientSecret"); ?>">
		<icon><img src="img/w/fingerprint.png" /></icon>
		<key>[Paypal Client Secret]</key>
		<CurrentValue>****************</CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>			
		
		<div class="SiteSettingCell"  data-settings="PayPalMod" data-value="<?echo settings("PayPalMod"); ?>">
		<icon><img src="img/w/signpost.png" /></icon>
		<key>[Paypal Mod]</key>
		<CurrentValue><?echo settings("PayPalMod"); ?></CurrentValue>
		<EditArea>
		<notice><? $L->Key("paypal_mod_notic"); ?></notice>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>	
	
	<notice>
		<imgSide> <img src="img/b/price.png" /> </imgSide>
		<? $L->Key("tax_and_price"); ?> , <b><? $L->Key("add_price_notic"); ?></b>
	</notice>
	
		<div class="SiteSettingCell"  data-settings="PayPalPaymentTax" data-value="<?echo settings("PayPalPaymentTax"); ?>">
		<icon><img src="img/w/sale.png" /></icon>
		<key><? $L->Key("paypal_tax"); ?></key>
		<CurrentValue><?echo settings("PayPalPaymentTax"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>	
		
		<div class="SiteSettingCell"  data-settings="DayPackagePrice" data-value="<?echo settings("DayPackagePrice"); ?>">
		<icon><img src="img/w/true.png" /></icon>
		<key><? $L->Key("DayPackage"); ?></key>
		<CurrentValue><?echo settings("DayPackagePrice"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>		
		
		<div class="SiteSettingCell"  data-settings="MonthPackagePrice" data-value="<?echo settings("MonthPackagePrice"); ?>">
		<icon><img src="img/w/true.png" /></icon>
		<key><? $L->Key("MonthPackage"); ?></key>
		<CurrentValue><?echo settings("MonthPackagePrice"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>	
		
		<div class="SiteSettingCell"  data-settings="ThreeMonthPackagePrice" data-value="<?echo settings("ThreeMonthPackagePrice"); ?>">
		<icon><img src="img/w/true.png" /></icon>
		<key><? $L->Key("ThreeMonthPackage"); ?></key>
		<CurrentValue><?echo settings("ThreeMonthPackagePrice"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>		
		
		<div class="SiteSettingCell"  data-settings="SixMonthPackagePrice" data-value="<?echo settings("SixMonthPackagePrice"); ?>">
		<icon><img src="img/w/true.png" /></icon>
		<key><? $L->Key("SixMonthPackage"); ?></key>
		<CurrentValue><?echo settings("SixMonthPackagePrice"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>	
		
		<div class="SiteSettingCell"  data-settings="YearPackagePrice" data-value="<?echo settings("YearPackagePrice"); ?>">
		<icon><img src="img/w/true.png" /></icon>
		<key><? $L->Key("YearPackage"); ?></key>
		<CurrentValue><?echo settings("YearPackagePrice"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>	
	
	
		<script>
		$(".SiteSettingCell").each(function(){
		$(this).SetFastWebsiteSettings();
		});
		$(".lcs_check").lc_switch();
		</script>
	
	</div>
	<? } ?>	
	
	
	<? if($pg_Panil=="Support"){ 
		appendTitle($L->Get("support_settings"));
	?>
	<div class="SettingsBody">
	
	<notice>
		<imgSide> <img src="img/b/info.png" /> </imgSide>
		<? $L->Key("email_adress"); ?>
	</notice>
	
		<div class="SiteSettingCell"  data-settings="AllowToGuestsToSendSupport" data-value="<?echo settings("AllowToGuestsToSendSupport"); ?>">
		<icon><img src="img/w/user.png" /></icon>
		<key><? $L->Key("allow_users_to_send_support"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>
		
		<div class="SiteSettingCell"  data-settings="NotificateSupporterByEmail" data-value="<?echo settings("NotificateSupporterByEmail"); ?>">
		<icon><img src="img/w/notification.png" /></icon>
		<key><? $L->Key("notificate_supporters_by_email"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
	
		
		<script>
		$(".SiteSettingCell").each(function(){
		$(this).SetFastWebsiteSettings();
		});
		$(".lcs_check").lc_switch();
		</script>
	
	</div>
	<? } ?>
	
	<? if($pg_Panil=="Devolopers"){ 
		appendTitle($L->Get("devolopers_settings"));
	?>
	
	<div class="SettingsBody">
	
	<notice>
		<imgSide> <img src="img/b/info.png" /> </imgSide>
		<? $L->Key("images_from_server_side"); ?>
	</notice>

		<div class="SiteSettingCell"  data-settings="ImagesResize" data-value="<?echo settings("ImagesResize"); ?>">
		<icon><img src="img/w/compress.png" /></icon>
		<key><? $L->Key("image_qualite_size"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
		
		<div class="SiteSettingCell" data-settings="ImagesQuality" data-value="<?echo settings("ImagesQuality"); ?>">
		<icon><img src="img/w/images.png" /></icon>
		<key><? $L->Key("image_qualite_size"); ?></key>
		<CurrentValue><?echo settings("ImagesQuality"); ?></CurrentValue>
		<EditArea>
		<notice><? $L->Key("from_1_to_100"); ?></notice>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>	
		
		
	<notice>
		<imgSide> <img src="img/b/info.png" /> </imgSide>
		<? $L->Key("join_editional_inputs"); ?>
	</notice>
	
		<div class="SiteSettingCell"  data-settings="ShowJoinPhoneFild" data-value="<?echo settings("ShowJoinPhoneFild"); ?>">
		<icon><img src="img/w/call.png" /></icon>
		<key><? $L->Key("tellphone_fild"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
		
		<div class="SiteSettingCell"  data-settings="ShowBirthDatePhoneFild" data-value="<?echo settings("ShowBirthDatePhoneFild"); ?>">
		<icon><img src="img/w/calendar.png" /></icon>
		<key><? $L->Key("birth_date_fild"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>		
		
		<div class="SiteSettingCell"  data-settings="ShowUsernameFild" data-value="<?echo settings("ShowUsernameFild"); ?>">
		<icon><img src="img/w/user.png" /></icon>
		<key><? $L->Key("username_fild"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>			
		
		<div class="SiteSettingCell"  data-settings="ShowNameFild" data-value="<?echo settings("ShowNameFild"); ?>">
		<icon><img src="img/w/input.png" /></icon>
		<key><? $L->Key("name_fild"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
		
		
	<notice>
		<imgSide> <img src="img/b/info.png" /> </imgSide>
		<? $L->Key("langs"); ?>
	</notice>
	
		<div class="SiteSettingCell"  data-settings="MultiLangs" data-value="<?echo settings("MultiLangs"); ?>">
		<icon><img src="img/w/translate.png" /></icon>
		<key><? $L->Key("multi_langs"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
	
	
		<div class="SiteSettingCell"  data-settings="MultiLangs" data-value="<?echo settings("MultiLangs"); ?>">
		<icon><img src="img/w/settings.png" /></icon>
		<key><? $L->Key("multi_langs_in_settings"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
		
		
		<div class="SiteSettingCell" data-settings="AvilbleLangs" data-value="<?echo settings("AvilbleLangs"); ?>">
		<icon><img src="img/w/langs.png" /></icon>
		<key><? $L->Key("avilble_langs"); ?></key>
		<CurrentValue><?echo settings("AvilbleLangs"); ?></CurrentValue>
		<EditArea>
		<notice><? $L->Key("use_lang_latters_and_spilt_with_comma"); ?></notice>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
		
	<notice>
		<imgSide> <img src="img/b/info.png" /> </imgSide>
		<? $L->Key("accounts"); ?>
	</notice>
	
	
		<div class="SiteSettingCell"  data-settings="AccountMultiUse" data-value="<?echo settings("AcountsSystem"); ?>">
		<icon><img src="img/w/user_admin.png" /></icon>
		<key><? $L->Key("accounts_system"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	

		
		<div class="SiteSettingCell"  data-settings="AccountMultiUse" data-value="<?echo settings("AccountMultiUse"); ?>">
		<icon><img src="img/w/unlock.png" /></icon>
		<key><? $L->Key("multi_login_acounts"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
	
		<div class="SiteSettingCell"  data-settings="MessagingSystem" data-value="<?echo settings("MessagingSystem"); ?>">
		<icon><img src="img/w/message.png" /></icon>
		<key><? $L->Key("messaging_system"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
	
		<div class="SiteSettingCell"  data-settings="NotificationsSystem" data-value="<?echo settings("NotificationsSystem"); ?>">
		<icon><img src="img/w/notification.png" /></icon>
		<key><? $L->Key("notifications_system"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
		
	<notice>
		<imgSide> <img src="img/b/info.png" /> </imgSide>
		[Debugging]
	</notice>
	
	
		<div class="SiteSettingCell"  data-settings="DebuggingCallBacks" data-value="<?echo settings("DebuggingCallBacks"); ?>">
		<icon><img src="img/w/consol.png" /></icon>
		<key>[Show Call Backs]</key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
		
	<notice>
		<imgSide> <img src="img/b/info.png" /> </imgSide>
		<? $L->Key("payments"); ?>
	</notice>
	
		<div class="SiteSettingCell"  data-settings="PaymentsSystem" data-value="<?echo settings("PaymentsSystem"); ?>">
		<icon><img src="img/w/payments.png" /></icon>
		<key><? $L->Key("payments_system"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
		
		<script>
		$(".SiteSettingCell").each(function(){
		$(this).SetFastWebsiteSettings();
		});
		$(".lcs_check").lc_switch();
		</script>
	
	</div>
	<? }else if($pg_Panil=="Conections"){ 
		appendTitle($L->Get("connetions_settings"));
	?>
	
	<div class="SettingsBody">
	
	<notice>
		<imgSide> <img src="img/b/info.png" /> </imgSide>
		<? $L->Key("notice_if_you_make_item_empty_it_will_hide_the_function"); ?>
	</notice>
	
		<div class="SiteSettingCell" data-settings="OfficalFacebook" data-value="<?echo settings("OfficalFacebook"); ?>">
		<icon><img src="img/w/facebook.png" /></icon>
		<key><? $L->Key("facebook"); ?></key>
		<CurrentValue><?echo settings("OfficalFacebook"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
		<div class="SiteSettingCell" data-settings="OfficalTwitter" data-value="<?echo settings("OfficalTwitter"); ?>">
		<icon><img src="img/w/twitter.png" /></icon>
		<key><?echo $L->Key("twitter"); ?></key>
		<CurrentValue><?echo settings("OfficalTwitter"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
		<div class="SiteSettingCell" data-settings="OfficalInstagram" data-value="<?echo settings("OfficalInstagram"); ?>">
		<icon><img src="img/w/instagram.png" /></icon>
		<key><?echo $L->Key("instagram"); ?></key>
		<CurrentValue><?echo settings("OfficalInstagram"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
		<div class="SiteSettingCell" data-settings="OfficalYoutube" data-value="<?echo settings("OfficalYoutube"); ?>">
		<icon><img src="img/w/youtube.png" /></icon>
		<key><? $L->Key("youtube"); ?></key>
		<CurrentValue><?echo settings("OfficalYoutube"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
	
		
		<div class="SiteSettingCell" data-settings="OfficalPhone" data-value="<?echo settings("OfficalPhone"); ?>">
		<icon><img src="img/w/call.png" /></icon>
		<key><? $L->Key("phone_number"); ?></key>
		<CurrentValue><?echo settings("OfficalPhone"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>		
		
		<div class="SiteSettingCell" data-settings="OfficalEmail" data-value="<?echo settings("OfficalEmail"); ?>">
		<icon><img src="img/w/email.png" /></icon>
		<key><? $L->Key("offical_email"); ?></key>
		<CurrentValue><?echo settings("OfficalEmail"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
		
		
		
		<script>
		$(".SiteSettingCell").each(function(){
		$(this).SetFastWebsiteSettings();
		});
		$(".lcs_check").lc_switch();
		</script>
		
	
	</div>
	
	<? }else if($pg_Panil=="General"){ 
		appendTitle($L->Get("general_settings"));
	?>
	
	<div class="SettingsBody">
	
		<div class="SiteSettingCell" data-settings="SiteName" data-value="<?echo settings("SiteName"); ?>">
		<icon><img src="img/w/signpost.png" /></icon>
		<key><? $L->Key("website_name"); ?></key>
		<CurrentValue><?echo settings("SiteName"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>	
		
		<div class="SiteSettingCell" data-settings="SiteDomain" data-value="<?echo settings("SiteDomain"); ?>">
		<icon><img src="img/w/domain.png" /></icon>
		<key><? $L->Key("offical_domain"); ?></key>
		<CurrentValue><?echo settings("SiteDomain"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
		<div class="SiteSettingCell"  data-settings="JoinSystem" data-value="<?echo settings("JoinSystem"); ?>">
		<icon><img src="img/w/login.png" /></icon>
		<key><? $L->Key("join_system"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>		
		
		<div class="SiteSettingCell"  data-settings="JoinSystem" data-value="<?echo settings("JoinSystem"); ?>">
		<icon><img src="img/w/lock.png" /></icon>
		<key><? $L->Key("login_system"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>
		
		<div class="SiteSettingCell"  data-settings="UsersActivations" data-value="<?echo settings("UsersActivations"); ?>">
		<icon><img src="img/w/message.png" /></icon>
		<key><? $L->Key("account_activation_system"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>
				
		<div class="SiteSettingCell"  data-settings="AirPlanMode" data-value="<?echo settings("AirPlanMode"); ?>">
		<icon><img src="img/w/airplane.png" /></icon>
		<key><? $L->Key("airplane_mode"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
		
		<!-- login using facebook or twitter settings -->
		
		<div class="SiteSettingCell"  data-settings="TwitterLogin" data-value="<?echo settings("TwitterLogin"); ?>">
		<icon><img src="img/w/airplane.png" /></icon>
		<key><? $L->Key("login_using_facebook"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>	
		
		<div class="SiteSettingCell"  data-settings="FacebookLogin" data-value="<?echo settings("FacebookLogin"); ?>">
		<icon><img src="img/w/airplane.png" /></icon>
		<key><? $L->Key("login_using_twitter"); ?></key>
		<CurrentValue>
		<input type="checkbox" name="check-3" class="lcs_check" autocomplete="off" />
		</CurrentValue>
		</div>
		
		<div class="SiteSettingCell" data-settings="FacebookAppId" data-value="<?echo settings("FacebookAppId"); ?>">
		<icon><img src="img/w/domain.png" /></icon>
		<key><? $L->Key("facebook_app_id"); ?></key>
		<CurrentValue><?echo settings("FacebookAppId"); ?></CurrentValue>
		<EditArea>
		<input type="text" />
		<button class="buttonPattern1"><? $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
		<!-- login using facebook or twitter settings -->
		
		<div class="SiteSettingCell" data-settings="DevoloperSettings" data-value="<?echo settings("DevoloperSettings"); ?>">
		<icon><img src="img/w/code.png" /></icon>
		<key><? $L->Key("devolopers_settings"); ?></key>
		<CurrentValue><?echo settings("DevoloperSettings"); ?></CurrentValue>
		<EditArea>
		<notice style="background-color:#FDE1E2;">
		<? echo $L->Key("be_carfull_devoloper_settings_maybe_cuses_damege_and_decrese_security"); ?>
		</br>
		<? echo $L->Key("you_need_auth_code_to_contenu_please_end_the_programmer"); ?>
		</br> 
		<? echo $L->Key("to_cancel_devolopers_mode_save_value_empty"); ?>
		</notice>
		<input type="text" placeHolder="Othintication Code"/>
		<button class="buttonPattern1" style="background-color:#DD4F43;"><? echo $L->Key("save"); ?></button>
		</EditArea>
		</div>
		
		<script>
		$(".SiteSettingCell").each(function(){
		$(this).SetFastWebsiteSettings();
		});
		$(".lcs_check").lc_switch();
		</script>
	
	</div>
	
	<? } ?>


<? }else if($pg_Panil=="users"){ 
	appendTitle($L->Get("users_managments"));
?>
	
	<!--
	<div class="FilterMenu">
		<div class="FilterSearchBut shadow0"><img src="img/b/search.png" /></div>
	</div>
	-->
	
	<div class="DuTemplateContenet" style="margin-top:10px;">
			<div id="postsAfterThis"></div>
			<img src="img/loading.gif" class="DuTemplateLoadingMore" />
			<div class="Pagination"></div>
		</div>
	
	<script>
		Template = new DuBuildTemplate();
		TotalResult   = 0;
		CurrentResult = 1;
		
		Template.Apply({
			Block           : "DashBoardUsers"
		});
		
		GetData = function(_Page){
			Template.Start({
				Filter          : "user_id[!=]5000000",
				ResultsPerPage  : "10",
				Page            : _Page,
				OrderBy         : "user_id",
				OrderKey        : "Desc",
				Table           : "users",
				
				IfLoading       : function(){
					ScrolUp("html,body");
					$(".DuTemplateLoadingMore").fadeIn();
				},
				IfFinish        : function(Data){
					$("#postsAfterThis").html(Data);
					$(".Pagination").fadeIn();
					$(".DuTemplateLoadingMore").fadeOut();
				},
				CountTotal      : function(Data){
					TotalResult = Data;
				}
			});
		}
		
		GetData(CurrentResult);


	</script>

	<script>
	$(document).ready(function(){

	pagination = new Pagination({ Contener:".Pagination" });
		
		pagination.Build({
			Total         : TotalResult,
			PerPage       : 10,
			CurrentPage   : CurrentResult,
			ButtonsNumber : 10
		});
	});
	
	$(document).on("click",".Pagination button",function(){
		CurrentResult = $(this).text();
		pagination.Build({
			Total         : TotalResult,
			PerPage       : 10,
			CurrentPage   : CurrentResult,
			ButtonsNumber : 10
	});
			GetData(CurrentResult);
	});
	</script>
	
	
	
<?}else if($pg_Panil=="plugins" || $pg_Panil=="Plugin"){ 
	appendTitle($L->Get("plugins"));
?>

	<div class="SubMenu shadow0">
		<? foreach($SqlGet->Table("plugins",""," plugin_id!='0' ") AS $K=>$V){ ?>
			<a href="DashBoard/Plugin/<? echo $V["plugin_name"]; ?>">
				<cell>
					<img src="plugins/<? echo $V["plugin_name"]; ?>/w_logo.png"/>
					<? echo $V["plugin_name"]; ?>
				</cell>
			</a>
		<? } ?>
	</div>
	
	<?
	if(!isset($pg_Plugin)){
	?>
	
		
		
	<?
	}else if(isset($pg_Plugin)){
	define("PluginFile","plugins/".$pg_Plugin);
	require("plugins/".$pg_Plugin."/DashBoard.php");
	}
	
	
}
	}else if($pg_page=="offline"){
	if(!settings("AirPlanMode")){
		GoToPage("index");
	}
	appendTitle($L->Get("airplane_mode"));
	appendStyle("styles/".$L->Direction."/OfflineAndErrorPages.css");
	?>
	
	<!-- Hide Header On Offline Page -->
	<head><style>.header{display:none;}</style></head>
	
	<div class="OfflineImage"><img src="img/w/offline_mode_xl.png" id="ImgAnimate"/></div>
	<div class="OfflineMessage" ><? $L->Key("offline_mode_message"); ?></div>
	
	<script>
	Duanimate("#ImgAnimate","show","fadeInLeft","1.000");
	</script>
	
<?	
	}else if($pg_page=="help"){
	appendTitle($L->Get("help_center"));
	appendStyle("styles/".$L->Direction."/HelpAndSupport.css");
	?>
	
	<? if(!isset($pg_category) || $pg_category=="support"){ ?>
	<div class="HelpCenterCover shadow0" >
		<div class="HelpCenterCoverRight">
			<div class="HelpCenterWelcome"><? $L->Key("welcome_to_support_center"); ?></div>
		</div>
		
		<div class="HelpCenterCoverLeft" >
			<div class="HelpCenterCoverfindTitle">
		</div>

			<? if(!empty(settings("OfficalFacebook"))){ ?>
				<div class="HelpsocialCels">
					<a href="http://www.facebook.com/<? echo settings("OfficalFacebook"); ?>">
						<img class="footerContenerSocial" src="img/w/facebook.png"
						<? echo settings("OfficalFacebook"); ?>
					</a>
				</div>
			<? } ?>
			
			<? if(!empty(settings("OfficalInstagram"))){ ?>
				<div class="HelpsocialCels">
					<a href="http://www.instagram.com/<? echo settings("OfficalInstagram"); ?>">
						<img class="footerContenerSocial" src="img/w/instagram.png"/>
						<? echo settings("OfficalInstagram"); ?>
					</a>
				</div>
			<? } ?>
			
			<? if(!empty(settings("OfficalTwitter"))){ ?>
				<div class="HelpsocialCels">
					<a href="http://www.twitter.com/<? echo settings("OfficalTwitter"); ?>">
						<img class="footerContenerSocial" src="img/w/twitter.png"/>
						<? echo settings("OfficalTwitter"); ?>
					</a>
				</div>
			<? } ?>
			
			<? if(!empty(settings("OfficalYoutube"))){ ?>
				<div class="HelpsocialCels">
					<a href="http://www.youtube.com/<? echo settings("OfficalYoutube"); ?>">
						<img class="footerContenerSocial" src="img/w/youtube.png"/>
						<? echo settings("OfficalYoutube"); ?>
					</a>
				</div>
			<? } ?>
			
		</div>
	</div>

	
	
	
	<div class="HelpCenterCategories shadow0">
		<a href="help/privacy">
			<cel><img src="img/b/fingerprint.png" /> <span><? $L->Key("privacy"); ?></span></cel>
		</a>
		<a href="help/terms">
			<cel><img src="img/b/copy_right.png" /> <span><? $L->Key("terms"); ?></span></cel>
		</a>
		<a href="help/FaQ">
			<cel><img src="img/b/questions.png" /> <span><? $L->Key("trend_questions"); ?></span></cel>
		</a>
		<? if(!isset($sessions->id) && settings("AllowToGuestsToSendSupport") || isset($sessions->id)){ ?>
		<a href="help/contact">
			<cel><img src="img/b/call.png" /> <span><? $L->Key("contact_us"); ?></span></cel>
		</a>
		<? } ?>
	</div>
	
	<? } ?>
	
	
	<? if(isset($pg_category)){
	if($pg_category=="privacy" || $pg_category=="terms" || $pg_category=="about"){
	
	if($pg_category=="privacy"){ appendTitle($L->Get("privacy")); }
	if($pg_category=="terms")  { appendTitle($L->Get("terms")); }
	if($pg_category=="about")  { appendTitle($L->Get("about")); }
	?>
	
	<div class="HelpCenterBody shadow0">
		<div class="HelpCenterContentTitle"><? $L->Key($pg_category); ?></div>
	<?
	// Get Info Value Fom DB , By Lang
	$GFild = $SqlGet->Table("info",""," info_type='". $pg_category ."' &&  info_lang='". $L->MyLang."'");
		// Print It
		echo html_entity_decode($GFild[0]["info_value"]); ?>
	</div>
	
	<?
	}else if($pg_category=="contact"){
		appendTitle($L->Get("contact_us"));
		// Check If Guests Allowed To Send Support
		if(!isset($sessions->id) && !settings("AllowToGuestsToSendSupport")){
			GoToPage(RootPath . "/login");
		}
	?>
	
	<div class="ContactUsBody">
	
	<div class="ContactSendStatus shadow3" id="ThanksForContact">
	<img src="img/b/support.png" />
		<state>
			<? $L->Key("we_are_so_happe_to_recive_your_message_and_will_reply_soon"); ?>
		</br>
			<? $L->Key("we_send_a_ticket_code_and_link_to_your_email"); ?>
		</state>
	</div>
		<div class="ContactSendStatusHelp shadow4" id="GoToIndex">
			<a href="index"><? $L->Key("go_to_index"); ?></a>
		</div>
		<div class="ContactSendStatusHelp shadow4" id="ShoMoreContactOption">
			<? $L->Key("or_show_more_contact_options"); ?>
		</div>
	
		<script>
			$("#ShoMoreContactOption").click(function(){
				ScrolUp("html,body");
				Duanimate("#ThanksForContact","hide","bounceOutUp","0.700");
				Duanimate("#GoToIndex","hide","bounceOutUp","0.750");
				Duanimate("#ShoMoreContactOption","hide","bounceOutUp","0.790");
				
				Duanimate("#MoreContactUsOption","show","bounceInUp","0.800");
				Duanimate("#MoreContactUsOption a","show","ZoomIn","0.850");
				Duanimate("#ContactOptionGoToIndex","show","ZoomIn","0.900");
			});
		</script>
	
	
	<div class="ContactSendSocial" style="background:#4B8AF3;" id="MoreContactUsOption">
	<div class="ContactSendSocialTitle"><? $L->Key("you_can_also_call_as_on_phone"); ?></div>
	
	<div class="ContactSendSocialIconsContener">
	
			<? if(!empty(settings("OfficalFacebook"))){ ?>
				<a href="http://www.facebook.com/<? echo settings("OfficalFacebook"); ?>">
					<img  src="img/w/facebook.png"/>
					<? echo settings("OfficalFacebook"); ?>
				</a>
			<? } ?>
			
			<? if(!empty(settings("OfficalInstagram"))){ ?>
				<a href="http://www.instagram.com/<? echo settings("OfficalInstagram"); ?>">
					<img  src="img/w/instagram.png"/>
				</a>
			<? } ?>
			
			<? if(!empty(settings("OfficalTwitter"))){ ?>
				<a href="http://www.twitter.com/<? echo settings("OfficalTwitter"); ?>">
					<img src="img/w/twitter.png"/>
					<? echo settings("OfficalTwitter"); ?>
				</a>
			<? } ?>
			
			<? if(!empty(settings("OfficalYoutube"))){ ?>
				<a href="http://www.youtube.com/<? echo settings("OfficalYoutube"); ?>">
					<img  src="img/w/youtube.png"/>
				</a>
			<? } ?>
	</div>
	
	
			
	<div class="ContactSendSocialTitle"><? $L->Key("you_can_also_call_as_on_phone"); ?></div>
			<? if(!empty(settings("OfficalPhone"))){ ?>
				<a href="tel://<? echo settings("OfficalPhone"); ?>">
						<img  src="img/w/call.png"/>
						<? echo settings("OfficalPhone"); ?>
				</a>
			<? } ?>
	</div>
		<div class="ContactSendStatusHelp shadow0" style="text-align:right;" id="ContactOptionGoToIndex">
			<a href="index"><? $L->Key("go_to_index"); ?></a>
		</div>
	
	
	<div class="ContactUsTips shadow0" id="SendMesssageTips">
	<icon><img src="img/w/support.png" /></icon>
	<h2><? $L->Key("to_serve_you_better_be_suar_you_are_insert_correct_informations"); ?></h2>
	</div>
	
	<div class="ContactUsFormBody shadow0" id="SendMesssageContener">
		<form ActionPage="actions.php?Contact" id="ContactForm">
		
		<? if(!$sessions->login){ ?>
			<input data-para="name" input-type="text||<? $L->Key("join_name_char_roll"); ?>" input-holder="<? $L->Key("name"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
			
			<input data-para="email" input-type="anything||<? $L->Key("incorrect_email"); ?>" input-holder="<? $L->Key("email_adress"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
		<? } ?>
			
			<input data-para="messageType" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="<? $L->Key("messaging_reason"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
			select-items="
			<? $L->Key("click_to_select"); ?>||null,
			<? $L->Key("problem"); ?>||problem,
			<? $L->Key("other_reason"); ?>||other
			" />
			
			
			<input data-para="message" input-type="textarea||<? $L->Key("incorrect_email"); ?>" input-holder="<? $L->Key("post_your_proplem"); ?>" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
			
			
		
			<button class="buttonPattern1 shadow1" ActionType="submit" id="submit_login"><? $L->Key("send"); ?></button>

		</form>
	
	<script>
	$("#ContactForm").ApplayMaterialDesignForm({});
			$("#ContactForm").submit(function(){
				$("#ContactForm").find("input").each(function(){
					$(this).ChekInputErrors();
				});
			$("#ContactForm").FastSendData({
				TaskStart:function(){
				},
				TaskFinish:function(Data){
					ScrolUp("html,body");
					Duanimate(".LoginErrorSBox","hide","pulse","0.400");
					Duanimate("#SendMesssageTips","hide","pulse","0.500");
					Duanimate("#SendMesssageContener","hide","pulse","0.600");
					Duanimate("#ThanksForContact","show","bounceInUp","0.700");
					Duanimate("#GoToIndex","show","bounceInUp","0.800");
					Duanimate("#ShoMoreContactOption","show","bounceInUp","0.900");
					if(Data=="true"){

					}else{
					if(Data=="false"){
					$(".LoginErrorSBox span").html("<? $L->Key("wrong_email_or_password"); ?>");
					}else if(Data=="BlockedUser"){
					$(".LoginErrorSBox span").html("<? $L->Key("wrong_email_or_password"); ?>");
					}else{
					$(".LoginErrorSBox span").html("<? $L->Key("un_knowed_error"); ?>");
					}
					Duanimate(".LoginErrorSBox","show","pulse","500");	
					}
				}
				});
			});
	</script>
	</div>
	
	
	</div>
	<? }else if(isset($pg_category) && $pg_category=="FaQ"){ 
		appendTitle($L->Get("trend_questions"));
	?>
	<div class="FaQBody">
		<div class="FaQDiscription shadow0">
			<img src="img/w/questions.png" /> 
			<? $L->Key("this_page_will_help_you_to_solve_many__questions_in_your_mind"); ?>
		</div>
		
		<?
		foreach($SqlGet->Table("info",""," info_type='Qas' &&  info_lang='". $L->MyLang ."' ") AS $K=>$V){
			
			$ContentId     = $V["info_id"];
			$ContentArr    = unserialize($V["info_value"]);
			$Question      = $ContentArr["Question"];
			$Answer        = $ContentArr["Answer"];
		?>
		
		<div class="FaQCell shadow1">
			<Quition><img src="img/b/plus.png" /><? echo $Question; ?></Quition>
			<Answer id="Ans_<? echo $ContentId; ?>" ><? echo $Answer; ?></Answer>
		</div>
		
		<? } ?>
		
		<script>
		$(".FaQCell").children("Quition").click(function(){
			var Answer = $(this).next("Answer").attr("id");
			Duanimate("#"+Answer,"toggle","flipInX","0.20");
		});
		</script>
	
	</div>
	<?
	}
	}
	?>
	

<?	
	}else if($pg_page == "message"){
	appendTitle($L->Get("messages"));
	appendStyle("styles/".$L->Direction."/Messages.css");
	appendJs("js/Messages.js");
	!$sessions->login ? GoToPage("../index"):"";
	if(!isset($pg_user)){ GoToPage("../index"); }
	?>
	
	<div class="AllMessagesBody shadow0">
	
		<div class="messagesMenu">
			<div class="ChatHeaderThumb thumb" style="background-image:url('<? echo $SqlGet->userinfo($pg_user,"user_thumb"); ?>');"></div>
				<div class="ChatHeaderInfoHolder">
					<div class="ChatHeaderName">
					<? echo $SqlGet->userinfo($pg_user,"user_name"); ?>
					</div>
					<div class="ChatHeaderLastSeen">
					<? $L->Key("last_seen"); ?> : 
					<? echo time_about($SqlGet->userinfo($pg_user,"user_last_seen")); ?>
					</div>
				</div>
		</div>
		
	<div class="messageBody" id="SingleMessageBody">
	<? 
	$Condition = "message_to = '$pg_user' && message_from='$sessions->id' || message_to = '$sessions->id' && message_from='$pg_user' "; 
	$CountRow  = sql_count("messages","$Condition"); 
	?>
		<? if($CountRow > 6){ ?>
		<button class="MessageGetMoreBut" ><?$L->Key("more"); ?></button>
		<? } ?>
		<div id="messagesAfterThis"></div>
		
	<script>
	$("#SingleMessageBody").DuBuildTemplate({
	field                 :"*",
	table                 :"messages",
	limitFrom             :"<? echo $CountRow; ?>",
	limitNum              :"5",
	sortBy                :"message_id",
	sortType              :"Bottom",
	filter                :"<? echo encodeUrlFilter($Condition); ?>",
	template              :"templates/MessageCell.html",
	EventItem             :".MessageGetMoreBut",
	EventType             :"click",
	FirstTime:function(){

	},
	Loading:function(){
	$(".MessageGetMoreBut").fadeOut();
	},
	Excute:function(data){
		if(data !=="NoResult"){
				$("#messagesAfterThis").after(data);
				$(".MessageGetMoreBut").fadeIn();
		}else{
				$(".MessageGetMoreBut").fadeOut();
		}
	}
	});
	</script>
	
	
	</div>
	
	<div class="MessageWriteAria">
	<form id="MessageSendForm">
			<input class="MessageWriteAriaInput" placeHolder="type_your_message_here"/>
				<button class="MessageWriteAriaSendBut">
					<img src="img/w/send.png" />
				</button>
	</form>
	</div>
	
	<script>
	$(".MessageWriteAriaInput").keyup(function(){
	var Message     = $(this).val().trim();	
		if(Message !== ""){
			$(".MessageWriteAriaSendBut").css("opacity","1");
		}else{
			$(".MessageWriteAriaSendBut").css("opacity","0.7");
		}
	});
	
	$("#MessageSendForm").submit(function(){
		
		// Get The Current Time
		var currentdate    = new Date(); 
		var currentMinutes = currentdate.getMinutes();
		if(currentdate.getHours()>12){
			var currentHours   = currentdate.getHours()-12;
		}else{
			var currentHours   = currentdate.getHours();
		}
		// Get The Current Time
		
			var Time        = currentHours+":"+currentMinutes;
			var Message     = $(".MessageWriteAriaInput").val();
			var Thumb       = "<? echo $SqlGet->userinfo($sessions->id,"user_thumb"); ?>";
			var BubbleColor = "#FFFF9F";
			var SeenState   = "waiting";
			
			if(Message.trim() !== ""){
				AppendNewMessage(Message,Thumb,BubbleColor,Time,SeenState);
				$(".MessageWriteAriaInput").val("");
				$(".MessageWriteAriaInput").css("background-color","transparent");
			}else{
				$(".MessageWriteAriaInput").css("background-color","#FFEAEA");
				$(".MessageWriteAriaSendBut").css("opacity","0.7");
			}
			
	$.post("actions.php?SendNewMessage=Instent&Message="+Message+"&To="+"<? echo $pg_user; ?>",function(Data){
	});
				return false;
	});
	</script>
	
	
	</div>
	
	
	<?
	}else if($pg_page == "messages"){
	appendTitle($L->Get("messages"));
	appendStyle("styles/".$L->Direction."/Messages.css");
	appendJs("js/Messages.js");
		$Mes     = NEW Messages;
	?>
	
	<div class="AllMessagesBody shadow0">
		<div class="messagesMenu">
		
			<a href="messages" >
				<cel>
					<img src="img/w/chat.png" /><? $L->Key("messages"); ?>
					<bubble>5</bubble>
				</cel>
			</a>
			
			<!--
			<a href="supportInpux" >
				<cel>
					<img src="img/w/support.png" /><? $L->Key("support"); ?>
					<bubble>5</bubble>
				</cel>
			</a>
			-->
			
		</div>
	
	
	<?
		foreach($Mes->GetAllBoxes() AS $KM => $VM){
		$AnotherSide = ($VM["message_to"]==$sessions->id) ? $VM["message_from"] : $VM["message_to"];
		if($VM["message_from"]==$sessions->id){
			$MessageSeenIcon  = ($VM["message_read"]==0) ? "true"    : "double_true";
			$MessageSeenColor = ($VM["message_read"]==0) ? "" : "#555";
		}else{
			$MessageSeenIcon  = ($VM["message_read"]==0) ? "true"    : "double_true";
			$MessageSeenColor = ($VM["message_read"]==0) ? "#009713" : "#fff";
		}
		?>
		<a href="message/<? echo $AnotherSide; ?>">
			<div class="AllMessagesCel">
				<thumb class="thumb" style="background-image:url('<? echo $SqlGet->userinfo($AnotherSide,"user_thumb"); ?>');">
				</thumb>
				<nameAndMesBox>
				<name>
					<? echo $SqlGet->userinfo($AnotherSide,"user_name"); ?>
				</name>
				<message style="color:<? echo $MessageSeenColor;?>" >
					<? echo $VM["message_content"]; ?>
				</message>
				</nameAndMesBox>
					<MessageInfoBox>
					<messageState><img src="img/b/time.png" /></messageState>
					<messageTime><? time_about($VM["message_date"]); ?></messageTime>
					</MessageInfoBox>
			</div>
		</a>
		<? } ?>
		
		
		</div>
	<?}else if($pg_page == "MySupportTickets" || $pg_page == "ReceivedTickets"){
		appendTitle($L->Get("support_tickets"));
		appendStyle("styles/".$L->Direction."/HelpAndSupport.css");
		appendJs("js/Support.js");
		
		// Require Support Class
		require_once("functions/Support.php");
		$Support = NEW Support;
		
		// If Supporter Open Link And Its Not Login
		if($pg_page == "ReceivedTickets" && !isset($sessions->id)){
			GoToPage(RootPath . "/login");
		}
		
		// If Normal User Or Guest Open As Supporter Or Admin
		if($pg_page == "ReceivedTickets" && $sessions->user_acount_type !== "Admin" && $sessions->user_acount_type !== "Supporter"){
			GoToPage(RootPath . "/index");
		}
		
		
		// If Isset Serial Open Message Directly
		if(isset($pg_TicketSerial) && !empty($pg_TicketSerial)){
			if(sql_count("support","support_ticket_serial='$pg_TicketSerial' ") > 0){ ?>
				<script>
				$(document).ready(function(){
					$("ParentContener[data-serial='<? echo $pg_TicketSerial; ?>']").show();
				});
				</script>
			<?
			}else{
			?>
			
			<?
			}
		}
		
		
		/// Condition For Login And Un Login User
		if(isset($sessions->id)){
			$Arags = array("State" => "All");
		}else{
			$Arags = array("State" => "All","Serial" => $pg_TicketSerial);
		}
			$Contant = $Support->GetMessages($Arags);
		
	?>
	<? if($Contant == "NoResults"){ ?>
		<div class="NoTicketsResults shadow1">
		<img src="img/b/binoculars.png" />
		<span>
			<? $L->Key("doesnt_find_tickets"); ?>
			</br>
			
		<? if(isset($sessions->id)){
			if($sessions->user_acount_type == "Supporter" || $sessions->user_acount_type == "Admin"){?>
			<p>
			<? $L->Key("little_notic_the_support_cant_reply_to_them_tickets_so_invisible"); ?>
			</p>
			<? } ?>
		<? } ?>
			
		</span>
		</div>
	
	<? }else{ ?>
		<?
		foreach($Contant AS $K=>$V){ ?>
		
		
		<div class="TicketCelsContener">
		
			<div class="TicketCelFace shadow2" id="TicketFace_<? echo $V["Id"];?>">
				<thumb class="thumb" style="background-image:url(img/w/support.png);"></thumb>
				<content>
					<state>
						<? $L->Key("ticket_state"); ?> : 
						<? if($V["State"]=="Open"){ ?>
						<? $L->Key("opend"); ?>
						<? }else{ ?>
						<? $L->Key("closed"); ?>
						<? } ?>
					</state>
					<serial>
						<? $L->Key("ticket_number"); ?> : 
						<? echo $V["Serial"]; ?>
					</serial>
				</content>
			</div>
			
			<script>
				$("#TicketFace_<? echo $V["Id"];?>").click(function(){
					Duanimate("#ReplaysContener_<? echo $V["Id"];?>","toggle","pulse","0.400");
				});
			</script>
			
		<ParentContener id="ReplaysContener_<? echo $V["Id"];?>" data-serial="<? echo $V["Serial"];?>">
		
			<div class="TicketCel shadow0" style="background:#BDF271;">
				<TicketInfo>
				<? $L->Key("was_opened_by"); ?> : 
					<?
					if(!empty($V["Name"])){ 
						echo $V["Name"];
					}else{
						echo $SqlGet->userinfo($V["User"],"user_name");
					}
					?>
				- <? $L->Key("email_adress"); ?> : 
					<?
					if(!empty($V["Email"])){ 
						echo $V["Email"];
					}else{
						echo $SqlGet->userinfo($V["User"],"user_email");
					}
					?>
				- <? $L->Key("in_date"); ?> :
					<?
						echo date("d/m/Y",$V["OpenDate"]);
					?>
				</TicketInfo>
			</div>
			
			<?
			foreach($V["Messages"] AS $KM=>$VM){
					if(!isset($sessions->id)){
							if($VM["From"]=="-2"){
								$Thumb = "thumb/xl/user.png";
							}else{
								$Thumb = "thumb/xl/".$SqlGet->userinfo($VM["From"],"user_thumb");
							}
								// Set My Thumb
								$MyThumb = "thumb/xl/user.png";
					}else{
								$Thumb = "thumb/xl/".$SqlGet->userinfo($VM["From"],"user_thumb");
								
									// Set My Thumb
									$MyThumb = "thumb/xl/".$SqlGet->userinfo($sessions->id,"user_thumb");
					}
			?>
			
			<div class="TicketCel shadow0">
				<thumb class="thumb"
				<? if($VM["From"]==$sessions->id){
					echo "style='background-image:url($Thumb);'"; 
				}else{
					echo "style='background-image:url($Thumb);'";
				}
				?>
				></thumb>
				<content><? echo $VM["Message"]; ?></contact>
			</div>
			<? } ?>
			
			<? if($V["State"]=="Open"){ ?>
			
			<? if(isset($sessions->id)){
			if($sessions->user_acount_type=="Admin" || $sessions->user_acount_type=="Supporter"){	?>
			
			<div class="TicketOptions shadow0" id="TicketOptionsContener_<? echo $V["Id"]; ?>">
				<button class="buttonPattern1" id="CloseTicket_<? echo $V["Id"]; ?>">
					<? $L->Key("close_ticket"); ?>
					<img src="img/w/shutdown.png"/>
				</button>
			</div>
			
			<script>
			$("#CloseTicket_<? echo $V["Id"]; ?>").click(function(){
				confirmbox("<? $L->Key("do_you_want_close_ticket"); ?> ?", function  x(){
					$.post("actions.php?CloseTicket=HTML&Serial=<? echo $V["Serial"]; ?>",function(data){});
						Duanimate("#TicketReplayContener_<? echo $V["Id"]; ?>","hide","bounceOut","0.800");
						Duanimate("#TicketOptionsContener_<? echo $V["Id"]; ?>","hide","bounceOut","0.700");
					
				});
			});
			</script>
			<? } ?>
			<? } ?>
			
			
			<div class="TiketReplayCel shadow0" id="TicketReplayContener_<? echo $V["Id"]; ?>">
				<textarea id="ReplayVal_<? echo $V["Id"]; ?>"></textarea>
				<button class="buttonPattern1" id="SendReplay_<? echo $V["Id"]; ?>">
					<? $L->Key("send_reply"); ?>
				</button>
				
					<script>
						$("#SendReplay_<? echo $V["Id"]; ?>").click(function(){
							var ReplayVal = $("#ReplayVal_<? echo $V["Id"]; ?>").val();
								if(ReplayVal.trim() == ""){
									$("#ReplayVal_<? echo $V["Id"]; ?>").css("border-bottom","2px solid #DD5145");
								}else{
									$("#ReplayVal_<? echo $V["Id"]; ?>").css("border-bottom","2px solid #FF8C00");
									
									// Send Replay Now
									$.post("actions.php?ReplaySupport=HTML&Message="+ReplayVal+"&Serial=<? echo $V["Serial"]; ?>",function(DATA){
									});
									$("#ReplayVal_<? echo $V["Id"]; ?>").val('');
									$("#ReplayVal_<? echo $V["Id"]; ?>").parents(".TiketReplayCel").before("<div class='TicketCel shadow0'><thumb class='thumb' style='background-image:url(<? echo $MyThumb;?>);'></thumb><content>"+ReplayVal+"</contact></div>");
								}
						});
					</script>
			</div>
			<? }else{ ?>
			
			<div class="ClosedTicket">
				<? $L->Key("was_closed_by_support_in_date"); ?> :
				<? echo date("Y/m/d",$V["CloseDate"]); ?> </br>
			</div>
			<? } ?>
		
		</div>
		
		
		</ParentContener>
	<? } ?>
	
	
	
	<? } ?>
		
	
	<? }else if($pg_page == "AccessDenied"){
		appendTitle($L->Get("access_denied"));
		appendStyle("styles/".$L->Direction."/OfflineAndErrorPages.css");
	?>
		<!-- Change Background Color To page -->
		<head><style>.body,html{background-color:#DD5044;}</style></head>

			<div class="OfflineImage"><img src="img/w/fire_wall_xl.png" id="ImgAnimate"/></div>
			<div class="OfflineMessage" ><? $L->Key("accessdenied_mode_message"); ?></div>
			
				<script>
					Duanimate("#ImgAnimate","show","shake","1.000");
				</script>

	
	<? }else{
		GoToPage("index");
	}
}else{
		GoToPage("index");
	}
?>