<?php
include("../../config.php");
ob_start();
header('Content-Type: text/html; charset=utf-8');
include("../../functions/master.php");
$sessions = new sessions();
$sessions->me();
$d          = time(); 
$MyId       = $sessions->id;
$Login      = $sessions->key;
$SqlGet     = new SqlGet;
$Pagination = new Pagination;
$L          = new Lang;


if(isset($_GET["Block"]) && isset($_GET["Page"])){
	
	if($_GET["Block"] == "DashBoardPosts"){
	$categories = NEW categories;
		
		$Filter     = decodeUrlFilter($_GET["Filter"]);
		$Page       = $_GET["Page"];
		$OrderBy    = $_GET["OrderBy"];
		$OrderKey   = $_GET["OrderKey"];
		
		
		$TotalResult = sql_count("content"," $Filter ");
		
			$Pagination->SetTotal($TotalResult);
			$Pagination->pageNumber($_GET["Page"]);
			$Pagination->ResultsPerPage($_GET["ResultsPerPage"]);
			
			$Limit = $Pagination->GetLimit();
			
			foreach($SqlGet->Table("content",""," $Filter ORDER BY $OrderBy $OrderKey LIMIT $Limit") AS $Key => $Data){
				$Id         = $Data["content_id"];
				$Title      = $Data["content_title"];
				$Cat        = $categories->GetInfo(array(
					"Id"    => $Data["content_cat"],
					"Fild"  => "categories_title",
					"Lang"  => $L->MyLang
				));
				$Thumb     = $Data["content_thumb"];
				$UserThumb = $SqlGet->userinfo($Data["content_user"],"user_thumb");
				$UserName  = $SqlGet->userinfo($Data["content_user"],"user_name");
			?>
			
				<div class="DashBoardPostGropCel shadow0" id="contener<? echo $ID; ?>">
					<div class="DashBoardPostCel">
						<postTitle>
							<? echo $Title; ?>
							<postCat>
								<? echo $Cat; ?>
							</postCat>
						</postTitle>
						
						<thumb class="thumb" style="background-image:url('<? echo $Thumb; ?>');">
						</thumb>
					</div>
					
					<div class="DashBoardPostCelOptions" id="PostOptions_<? echo $ID; ?>">
						<button onClick="DeletePost('<? echo $Id; ?>');">
							حذف <img src="img/b/delete.png" />
						</button>
						<a href="DashBoard/Contents?Edit=<? echo $Id; ?>" >
							<button>
								تعديل 
								<img src="img/b/pen.png" />
							</button>
						</a>
						<a href="content/<? echo $Id; ?>" >
							<button>
								عرض 
								<img src="img/b/link.png" />
							</button>
						</a>
						
						<userInfo>
							<thumb class="thumb" style="background-image:url('thumb/xl/<? echo $UserThumb; ?>');" ></thumb>
							<name> <? echo $UserName; ?> </name>
						</userInfo>
					</div>
				</div>
				
				<script>
				$(".DashBoardPostGropCel").mouseenter(function(){
					Duanimate($(this).children(".DashBoardPostCelOptions"),"show","slideInDown","0.200");
					return false;
				});
				$(".DashBoardPostGropCel").mouseleave(function(){
					Duanimate($(this).children(".DashBoardPostCelOptions"),"hide","ZoomOut","0.100");
					return false;
				});
				</script>
			<?
			}
			
	?>
	<script>
		function DeletePost(ID){
			CellID = $("#contener"+ID);
				confirmbox("هل تود حذف المحتوى ؟", function  x(){
					$.post('actions.php',{deletePost:ID},function(data){ });
					CellID.fadeOut();
					CellOP.fadeOut();
				});
		}
	</script>
	<?
	}		
	
	
	
	
	if($_GET["Block"] == "primePost"){
	$categories = NEW categories;
		
		$Filter     = decodeUrlFilter($_GET["Filter"]);
		$Page       = $_GET["Page"];
		$OrderBy    = $_GET["OrderBy"];
		$OrderKey   = $_GET["OrderKey"];
		
		
		$TotalResult = sql_count("content"," $Filter ");
		
			$Pagination->SetTotal($TotalResult);
			$Pagination->pageNumber($_GET["Page"]);
			$Pagination->ResultsPerPage($_GET["ResultsPerPage"]);
			
			$Limit = $Pagination->GetLimit();
			
			foreach($SqlGet->Table("content",""," $Filter ORDER BY $OrderBy $OrderKey LIMIT $Limit") AS $Key => $Data){
				$Id         = $Data["content_id"];
				$Title      = $Data["content_title"];
				$Cat        = $categories->GetInfo(array(
					"Id"    => $Data["content_cat"],
					"Fild"  => "categories_title",
					"Lang"  => $L->MyLang
				));
				$Thumb     = $Data["content_thumb"];
				$UserThumb = $SqlGet->userinfo($Data["content_user"],"user_thumb");
				$UserName  = $SqlGet->userinfo($Data["content_user"],"user_name");
			?>
			
					<div class="PrimeContentBlock">
						<a href="content/<? echo $Id; ?>/<? echo str_replace(" ","-",$Title); ?>">
							<thumb class="thumb" style="background-image:url('<? echo $Thumb; ?>')"></thumb>
							<theTitle><? echo $Title; ?></theTitle>
							<button>بدأ البث</button>
						</a>
					</div>
				

			<?
			}
			
	
	}	
	
	
	if($_GET["Block"] == "DashBoardUsers"){
		
		$Filter     = decodeUrlFilter($_GET["Filter"]);
		$Page       = $_GET["Page"];
		$OrderBy    = $_GET["OrderBy"];
		$OrderKey   = $_GET["OrderKey"];
		
		
		$TotalResult = sql_count("users"," $Filter ");
		
			$Pagination->SetTotal($TotalResult);
			$Pagination->pageNumber($_GET["Page"]);
			$Pagination->ResultsPerPage($_GET["ResultsPerPage"]);
			
			$Limit = $Pagination->GetLimit();
			
			foreach($SqlGet->Table("users",""," $Filter ORDER BY $OrderBy $OrderKey LIMIT $Limit") AS $Key => $Data){
				$ID           = $Data["user_id"];
				$AccountState = $Data["user_account_state"];
				$Name         = $Data["user_name"];
				$Rank         = $Data["user_acount_type"];
				
				if(empty($Data["user_birth_date"])){
					$Age          = "لم يتم الاضافة";
				}else{
					$Age          = $Data["user_birth_date"];
				}
				
				if(empty($Data["user_phone"])){
					$Phone        = "لم يتم الاضافة";
				}else{
					$Phone        = $Data["user_phone"];
				}
				
				$Email        = $Data["user_email"];
				
				$LastSeen     = "...";
			?>
				<div class="DashBoardUsersCell shadow1">
				<div class="DashBoardUsersCellThumb thumb" style="background-image='{{user_thumb}}'"></div>
				<div class="DashBoardUsersCellName"><? echo $Name; ?></div>
				<img class="DashBoardUsersCellShowMore" src="img/b/menu.png" 
				onClick='Duanimate("#UsersOptions_<? echo $ID; ?>","toggle","ZoomIn","0.200");'/>
				
				<div class="DashBoardUsersOptionsContener" id="UsersOptions_<? echo $ID; ?>">
			
				<cel id="block<? echo $ID; ?>" data-value="<? echo $AccountState; ?>" >
					<? if($AccountState !== "Blocked"){ ?>
						<span>حظر</span>
						<img src="img/b/block_user.png" />
					<? }else{ ?>
						<span>فك خظر</span>
						<img src="img/b/user.png" />
					<? } ?>
					
					<script>
					$("#block<? echo $ID; ?>").click(function(){
					var Value  = $(this).attr("data-value");
					var id     = "<? echo $ID; ?>";
					var Param  = "user_account_state";
					
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
					});
					</script>
				</cel>
				
				<cel onClick='Duanimate("#ChoseRank<? echo $ID; ?>","toggle","ZoomIn","0.200");'>
				<img src="img/b/change_user.png"/>
				<span>تغيير الرتبة</span>
				</cel>		
				
				<cel class="ChoseRank" id="ChoseRank<? echo $ID; ?>" style="display:none;">
					<div class="changeRankCell" account-type="NormalUser" data-id="<? echo $ID; ?>" 
					style="<? if($Rank == "NormalUser"){ echo"background:#FF8C00;";} ?>">
						<img src="img/w/user.png" />
						عضوية عادية
					</div>
					<div class="changeRankCell" account-type="Admin" data-id="<? echo $ID; ?>" 
					style="<? if($Rank == "Admin"){ echo"background:#FF8C00;";} ?>">
						<img src="img/w/user_support.png" />
						عضوية دعم فني
					</div>
					<div class="changeRankCell" account-type="Supporter" data-id="<? echo $ID; ?>" 
					style="<? if($Rank == "Supporter"){ echo"background:#FF8C00;";} ?>">
						<img src="img/w/user_admin.png" />
						عضوية مدير
					</div>
					
					<script>
					$(".changeRankCell").click(function(){
						var id     = $(this).attr("data-id");
						var Value  = $(this).attr("account-type");
						var Param  = "user_acount_type";
						
						$("#ChoseRank<? echo $ID; ?>").children(".changeRankCell").css("background","#2F2933");
						
						$(this).css("background","#FF8C00");
					$.post("actions.php?UsersManagement=html&user_id="+id+"&param="+Param+"&val="+Value,function(Data){
						//alert(Data);
					});
						return false();
					});
					</script>
					
				</cel>
				
				<a href="message/<? echo $ID; ?>">
					<cel data-action="chatWith">
					<img src="img/b/chat.png"/>
					<span>مراسلة</span>
					</cel>
				</a>
				
				<cel onClick='Duanimate("#showInfo<? echo $ID; ?>","toggle","ZoomIn","0.200");'>
					<img src="img/b/info.png"/>
					<span>عرض معلومات</span>
				</cel>

				
				<cel id="showInfo<? echo $ID; ?>" style="display:none;">
					<div class="ShowUserInformationCell" >
						<Prop>الاسم</Prop>
						<Tex><? echo $Name; ?></Tex>
						
						<Prop>البريد الالكتروني</Prop>
						<Tex><? echo $Email; ?></Tex>
						
						<Prop>رقم الهاتف</Prop>
						<Tex><? echo $Phone; ?></Tex>
						
						<Prop>العمر</Prop>
						<Tex><? echo $Age; ?></Tex>
						
						<Prop>اخر ظهور</Prop>
						<Tex><? echo $LastSeen; ?></Tex>

					</div>
				</cel>
			
			</div>
			
			
				</div>
			<?
			}
			
	?>
	<script>
		function DeletePost(ID){
			CellID = $("#contener"+ID);
				confirmbox("هل تود حذف المحتوى ؟", function  x(){
					$.post('actions.php',{deletePost:ID},function(data){ });
					CellID.fadeOut();
					CellOP.fadeOut();
				});
		}
	</script>
	<?
	}
	

}
?>