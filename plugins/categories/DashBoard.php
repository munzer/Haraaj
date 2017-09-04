<?
$PL = new Lang;
$PL->Set(PluginFile."/lang/ar.php");
	appendTitle($PL->Get("plugin__name"));
	appendStyle(PluginFile."/inc/rtl/style.css");
?>

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
				
					<input data-para="name" input-type="text||<? $L->Key("join_name_char_roll"); ?>" input-holder="عنوان التصنيف" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />

					<input data-para="cat" input-type="selectMenu||<? $L->Key("please_chose_one"); ?>" input-holder="النوع" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
					select-items="
					انقر للاختيار||null,
					المشروبات الباردة ||coldDrunks,
					المشروبات الحارة ||HotDrunks,
					الحلويات ||Sweets
					" />	
					
					
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
						$("body").unfreez();
					}
				});
			});
				</script>
				
	</div>
	</div>

<? foreach($SqlGet->Table("categories","*"," categories_id!='-1' ORDER BY categories_id DESC") AS $K=>$V){ ?>
	
		
<? } ?>

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