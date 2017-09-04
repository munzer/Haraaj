<?
$PL = new Lang;
$PL->Set(PluginFile."/lang/ar.php");
	appendTitle($PL->Get("plugin__name"));
	appendStyle(PluginFile."/inc/rtl/style.css");
?>


<button class="buttonPattern1 NewReservirationBut" onClick="$('#TableLightBox').fadeIn();">حجز طاولة <img src="img/w/cafe.png" /></button>
<button class="buttonPattern1 NewReservirationBut" onClick="$('#PartyLightBox').fadeIn();">حجز حفلة <img src="img/w/party.png" /></button>

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
	foreach($SqlGet->Table("reservation","*"," reservation_user='$sessions->id' ORDER BY reservation_id DESC") AS $K=>$V){
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
<button class="buttonPattern1" style="background:transparent;margin:0px 10px;float:right;color:#555;"
onClick="DeletFn('<? echo $V["reservation_id"]; ?>');"> حذف <img src="img/b/delete.png" /></button>
</row>

<?
	}
?>


	<script>
		function DeletFn(ID){
			CellID = $("#contener"+ID);
			CellOP = $("#contener"+ID).next("row");
				confirmbox("هل تود حذف الطلب ؟", function  x(){
					$.post('<? echo PluginFile; ?>/actions.php',{deleteOrder:ID},function(data){ });
					CellID.fadeOut();
					CellOP.fadeOut();
				});
		}
	</script>


</div>

	<script>
	$(".ResponsiveTable").ResponsiveTable();
	</script>
	
	
	<div class="lightBox shadow1" id="PartyLightBox">
	<div class="lightBoxBody shadow1">
	<div class="lightBoxBodyTitleBar"><st>حجز حفلة</st>
	<img class="lightBoxBodyTitleBarClose" src="img/w/close.png"/>
	</div>
	
	<div style="width:92%;float:right;background:#fff;padding:22px 4%;">
	
	<form ActionPage="<? echo PluginFile; ?>/actions.php?new&reservation_type=حفلة" id="PartyForm">
	
		<input data-para="reservation_time" input-type="anyThig||رجاء قم باختيار وقت الحجز" input-holder="وقت الحجز" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
		
		<input data-para="reservation_day" input-type="anyThig||قم بكتابة تاريخ و يوم الحجز" input-holder="اليوم" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
		
		<input data-para="reservation_count" input-type="number||ادخل العدد كـ رقم" input-holder="عدد الاشخاص" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
		
		<input data-para="reservation_requirements" input-type="selectMenu||رجاء اختر واحدة" input-holder="المدة" input-required="true||<? $L->Key("cannot_be_empty"); ?>"
		select-items="
		اختر المدة بالساعات||null,
		ساعة||1,
		ساعتين||2,
		ثلاث ساعات||3,
		اربع ساعات||4,
		خمس ساعات||5,
		ست ساعات||6
		" />
		
		<button class="buttonPattern1 shadow1" ActionType="submit"  style="float:right;padding:5px 15px;">حجز الان</button>
	
	</div>
	
	
	</form>
	
	</div>
	</div>	
	
			<div class="lightBox shadow1" id="TableLightBox" style="fdf">
			<div class="lightBoxBody shadow1">
			<div class="lightBoxBodyTitleBar"><st>حجز طاولة</st>
			<img class="lightBoxBodyTitleBarClose" src="img/w/close.png"/>
			</div>

			<div style="width:92%;float:right;background:#fff;padding:22px 4%;">

			<form ActionPage="<? echo PluginFile; ?>/actions.php?new&reservation_type=طاولة" id="TableForm">

				<input data-para="reservation_time" input-type="anyThig||رجاء قم باختيار وقت الحجز" input-holder="وقت الحجز" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
				
				<input data-para="reservation_day" input-type="anyThig||قم بكتابة تاريخ و يوم الحجز" input-holder="اليوم" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
				
				<input data-para="reservation_count" input-type="number||ادخل العدد كـ رقم" input-holder="عدد الاشخاص" input-required="true||<? $L->Key("cannot_be_empty"); ?>" />
	
	
				<button class="buttonPattern1 shadow1" ActionType="submit" id="submit_login" style="float:right;padding:5px 15px;">حجز الان</button>

			</div>


			</form>

			</div>
			</div>
	
	<script>
	var Type;
	$("#PartyForm,#TableForm").ApplayMaterialDesignForm({});
			$("#PartyForm,#TableForm").submit(function(){
				$(this).find("input").each(function(){
			$(this).ChekInputErrors();
		});
			$(this).FastSendData({
				TaskStart:function(){

				},
				TaskFinish:function(Data){					
					if(Data=="true"){
						$("body").sweet_alert({
						type:"true",
						title:"تم ارسال طلب الحجز بنجاح",
						butContent: "اغلاق",
						description : "سيتم اعادة تحميل الصفحة"
						});	
						setInterval(function(){ location.reload(); },4000);					
					}else{
						$("body").sweet_alert({
						type:"error",
						title: "لا يمكن اجراء العملية , حاول مجددا",
						butContent: "اغلاق",
						description : "حدث خطب ما !!"
						});	
					}
				}
				});
			});
			</script>
			
	
