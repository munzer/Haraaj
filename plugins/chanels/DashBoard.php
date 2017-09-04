<?
$PL = new Lang;
$PL->Set(PluginFile."/lang/ar.php");
	appendTitle($PL->Get("plugin__name"));
	appendStyle(PluginFile."/inc/rtl/style.css");
	
	$Cat = new categories;
		$Arr = $Cat->Get(array("Select"=>"Parents"));
			$NewCatsArray = "";
				foreach($Arr AS $K=>$V){
					$NewCatsArray .= $V["Title"]."||".$V["Id"].",";
				}
					$NewCatsArray = trim($NewCatsArray,",");
?>

<div class="DashBoard_About shadow1">
<img src="img/w/mouse.png" />
<p>
دبل كلك على العنصر لتعديله
</p>
</div>


	<button class="buttonPattern1 addNewBut">
		اضافة جديد
		<img src="<? echo PluginFile; ?>/img/add.png" />
	</button>
	
	
	<div class="lightBox" style="display:block;">
		<div class="lightBoxBody shadow2">
			<div class="lightBoxBodyTitleBar">
				<st>اضف | عدل</st>
				<img class="lightBoxBodyTitleBarClose" src="img/w/close.png"/>
			</div>
			
			<div class="lightBoxBodyContent">
			
				<form id="EditORNew" ActionPage="<? echo PluginFile; ?>/actions.php?AddNew">
										
					<input data-para="cat" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="القسم" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
					select-items="<? echo $NewCatsArray; ?>" />
					
					<input data-para="title" input-type="text||<? $L->Key("join_name_char_roll"); ?>" input-holder="اسم القناة" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
							
					<input data-para="code" input-type="textarea||<? $L->Key("join_name_char_roll"); ?>" input-holder="كود القناة" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
					
					
					<button class="buttonPattern1 SubmitBut" ActionType="submit" id="submit_login"><? $L->Key("login"); ?></button>
					
				</form>
			</div>
				
				<script> $("#EditORNew").ApplayMaterialDesignForm({});</script>
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
						alert(Data);
						$("body").unfreez();
					}
				});
			});
				</script>
			
			
		</div>
	</div>
	
	
	<div class="ResponsiveTable shadow0">
		<row>
		<column>المعرف</column>
		<column>اسم القناة</column>
		<column>كود القناة</column>
		<column>مضافة بواسطة</column>
		<column>اجراء</column>
		</row>
		
<?
	foreach($SqlGet->Table("chanels","*"," Chanel_id!='-1' ORDER BY Chanel_id DESC") AS $K=>$V){
?>
		<row id="Cel_<?  echo $V["Chanel_id"]; ?>">
			<column class="NoAction">
				<? echo $V["Chanel_id"]; ?>
			</column>
			<column update-type="selectMenu" update-page="<? echo PluginFile; ?>/actions.php?Update=<? echo $V["Chanel_id"]; ?>&item=Chanel_cat&value=">
				<t>
					<? if($V["Chanel_cat"]=="HotDrunks"){  echo "المشروبات الحارة"; }  ?>
					<? if($V["Chanel_cat"]=="coldDrunks"){ echo "المشروبات الباردة"; }  ?>
					<? if($V["Chanel_cat"]=="Sweets"){     echo "الحلويات"; }  ?>
				</t>
				<select style="display:none;width:auto;height:40px;border:0;">
				<option value="coldDrunks">المشروبات الباردة</option>
				<option value="HotDrunks">المشروبات الحارة</option>
				<option value="Sweets">الحلويات</option>
				</select> 
			</column>
			<column update-type="text" update-page="<? echo PluginFile; ?>/actions.php?Update=<? echo $V["Chanel_id"]; ?>&item=Chanel_type&value=">
				<? echo $V["Chanel_type"]; ?>
			</column>
			<column update-page="<? echo PluginFile; ?>/actions.php?Update=<? echo $V["Chanel_id"]; ?>&item=Chanel_price&value=">
				<? echo $V["Chanel_price"]; ?>
			</column>
			
			<column class="NoAction">
				<img class="ResponsiveTableIcon" src="<? echo PluginFile; ?>/img/delete.png" onClick="DeletP('<? echo  $V["Chanel_id"]; ?>');"/>
			</column>
		</row>
		
<?	} ?>

		<script>
			$("column[class!=NoAction]").click(function(){
				$(this).InstantUpdatble();
			});
			
				function DeletP(ID){
					var Cel = "#Cel_"+ID;
					
					confirmbox("هل تود حذف العنصر ؟", function  x(){
					$.post('<? echo PluginFile; ?>/actions.php',{Delet:ID},function(data){});
						Duanimate(Cel,"hide","fadeOutLeft","0.600");
					});
					
				}
		</script>
		
	</div>
	
	<script> $(".ResponsiveTable").ResponsiveTable(); </script>