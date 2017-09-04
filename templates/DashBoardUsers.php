	<div class='DashBoardUsersCell' data-id="{{user_id}}">
		<div class="DashBoardUsersCellThumb thumb" style="background-image='{{user_thumb}}'"></div>
		<div class="DashBoardUsersCellName">{{user_name}}</div>
		<img class="DashBoardUsersCellShowMore" src="img/b/row_down.png" />
		
			<div class="DashBoardUsersOptionsContener" id="UsersOptions_{{user_id}}">
			
				<cel data-action="block" data-value="{{user_account_state}}" data-para="user_account_state">
				<img src="img/b/block_user.png"/>
				<span>Loading</span>
				</cel>
				
				<cel data-action="ChangeRank">
				<img src="img/b/change_user.png"/>
				<span>تغيير الرتبة</span>
				</cel>		
				
				<cel class="ChoseRank" style="display:none;">
					<div class="changeRankCell" account-type="NormalUser">
						<img src="img/w/user.png" />
						عضوية عادية
					</div>
					<div class="changeRankCell" account-type="Admin">
						<img src="img/w/user_support.png" />
						عضوية دعم فني
					</div>
					<div class="changeRankCell" account-type="Supporter">
						<img src="img/w/user_admin.png" />
						عضوية مدير
					</div>
				</cel>
				
				<cel data-action="chatWith">
				<img src="img/b/chat.png"/>
				<span>مراسلة</span>
				</cel>
				
				<cel data-action="showInfo">
				<img src="img/b/info.png"/>
				<span>عرض معلومات</span>
				</cel>			

				
				<cel data-action="showInfo">
					<div class="ShowUserInformationCell" >
						<Prop>الاسم</Prop>
						<Tex>{{user_name}}</Tex>
						
						<Prop>البريد الالكتروني</Prop>
						<Tex>{{user_email}}</Tex>
						
						<Prop>رقم الهاتف</Prop>
						<Tex>{{user_phone}}</Tex>
						
						<Prop>العمر</Prop>
						<Tex>{{user_birth_date}}</Tex>
						
						<Prop>اخر ظهور</Prop>
						<Tex>{{user_last_seen}}</Tex>

					</div>
				</cel>
			
			</div>
	
	
	</div>
	

	
	
	<script>
	if("{{user_account_state}}" !== "Blocked"){
		$("#UsersOptions_{{user_id}} cel[data-action='block'] span").html("حظر");
		$("#UsersOptions_{{user_id}} cel[data-action='block'] img").attr("src","img/b/block_user.png");
	}else{
		$("#UsersOptions_{{user_id}} cel[data-action='block'] span").html("فك حظر");
		$("#UsersOptions_{{user_id}} cel[data-action='block'] img").attr("src","img/b/user.png");
	}
	
	
	// Change User Acount Type
	$(".changeRankCell").click(function(){
	var id     = $(this).parents(".DashBoardUsersCell").attr("data-id");
	var Value  = $(this).attr("account-type");
	var Param  = "user_acount_type";

	$.post("actions.php?UsersManagement=html&user_id="+id+"&param="+Param+"&val="+Value,function(Data){
		alert(Data);
	});
	return false();
	});
	
	$(".DashBoardUsersOptionsContener cel").click(function(){
	var id     = $(this).parents(".DashBoardUsersCell").attr("data-id");
	var action = $(this).attr("data-action");
	var Value  = $(this).attr("data-value");
	var Param  = $(this).attr("data-para");
	
	if(action=="block"){
		if(Value !== "Blocked"){
			$(this).children("span").html("فك حظر");
			$(this).children("img").attr("src","img/b/user.png");
			$(this).attr("data-value","Blocked");
			Value = "Blocked";
		}else{
			$(this).children("span").html("حظر");
			$(this).children("img").attr("src","img/b/block_user.png");
			$(this).attr("data-value","");
			Value = "";
		}
		
			<!-- Send Server Request-->
			$.post("actions.php?UsersManagement=html&user_id="+id+"&param="+Param+"&val="+Value,function(SetData){
				if(SetData=="true"){ alert(); }
			});
		
	}else	if(action=="ChangeRank"){
		$(this).next("cel").slideToggle();
		
	}
	
	
	return false();
	});
	
	</script>