<?
$PL = new Lang;
$PL->Set(PluginFile."/lang/ar.php");
	appendTitle($PL->Get("plugin__name"));
	appendStyle(PluginFile."/inc/rtl/style.css");
?>


<div class="DashBoard_About shadow1">
<img src="img/w/true.png" />
<p>
تساعدك هذه الصفحة على معرفة الحجوزات والتواريخ
و ادارتها
</p>
</div>


<div class="ResponsiveTable shadow0">

<row>
<column>المعرف</column>
<column>نوع الحجز</column>
<column>اليوم</column>
<column>الوقت</column>
<column>العدد</column>
<column>المدة المطلوبة</column>
<column>حالة الطلب</column>
<column>اجراء</column>
</row>

<?
	foreach($SqlGet->Table("reservation","*"," reservation_user!='0' ORDER BY reservation_id DESC") AS $K=>$V){
?>

<row id="contener<? echo $V["reservation_id"]; ?>">
<column><? echo $V["reservation_id"]; ?></column>
<column><? echo $V["reservation_type"]; ?></column>
<column><? echo $V["reservation_day"]; ?></column>
<column><? echo $V["reservation_time"]; ?></column>
<column><? echo $V["reservation_count"]; ?></column>
<column><? echo $V["reservation_requirements"]; ?></column>
<column>
<? if($V["reservation_state"]=="eccepted"){ echo"تم التأكيد"; }else{ echo"بلانتظار"; } ?>
</column>

<column>
<button class="buttonPattern1" style="background:transparent;" onClick="$(this).parents('row').next('row').toggle();"><img src="img/b/menu.png"/></button>
</column>
</row>

<row style="display:none;">
<div style="direction:rtl;width:92%;float:right;font-size:13px;text-align:right;padding:10px 4%;margin:10px 0%;border-bottom:1px solid #ccc;">
اسم العضوية :
<? echo $SqlGet->UserInfo($V["reservation_user"],"user_name"); ?> 
</br>
رقم الهاتف :
<? echo $SqlGet->UserInfo($V["reservation_user"],"user_phone"); ?> 
</div>
<button class="buttonPattern1" style="background:transparent;margin:0px 10px;float:right;color:#555;"
onClick="DeletFn('<? echo $V["reservation_id"]; ?>');"> حذف <img src="img/b/delete.png" /></button>


<? if($V["reservation_state"]!=="eccepted"){ ?>
<button class="buttonPattern1" style="background:transparent;margin:0px 10px;float:right;color:#555;"
	onClick="ConfirmOrder('<? echo $V["reservation_id"]; ?>');"> تأكيد <img src="img/b/true.png" /></button>
<? } ?>

</row>

<?
	}
?>


	<script>
	$(".ResponsiveTable").ResponsiveTable();
	
		function DeletFn(ID){
			CellID = $("#contener"+ID);
			CellOP = $("#contener"+ID).next("row");
				confirmbox("هل تود حذف الطلب ؟", function  x(){
					$.post('<? echo PluginFile; ?>/actions.php',{deleteOrder:ID},function(data){ });
					CellID.fadeOut();
					CellOP.fadeOut();
				});
		}
		
		function ConfirmOrder(ID){
			CellOP = $("#contener"+ID).next("row");
				confirmbox("هل تود تأكيد الطلب ؟", function  x(){
					$.post('<? echo PluginFile; ?>/actions.php',{ConfirmOrder:ID},function(data){ });
					CellOP.fadeOut();
					
						$("body").sweet_alert({
						type:"true",
						title:"تم ارسال اشعار بتأكيد الحجز",
						butContent: "اغلاق",
						description : "سيتم اعادة تحميل الصفحة"
						});	
						setInterval(function(){ location.reload(); },4000);	
				});
		}
	</script>


</div>



<?



?>
