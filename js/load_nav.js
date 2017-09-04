	/// load links without refresh
	$(document).on('click','a',function(e){
	var Link = $(this).attr("href");
	if(Link !=="#"){
	load_nav(Link);
	}
	  e.preventDefault();
	});


$(document).ready(function(){
	preloader = new $.materialPreloader({
  // top or bottom
  position: 'top',
  // height of the preloader
  height: '3px',

  // colors
  col_1: '#2F2933',
  col_2: '#fff',
  col_3: '#2F2933',
  col_4: '#ccc',

  // fadeIn aniamtion speed
  fadeIn: 200,

  // fadeOut animation speed
  fadeOut: 200
});
	});
	
		
										$(document).ready(function(){
					past_history_num = history.length;
					pasthareff = $(location).attr('pathname');
					nowhareff = $(location).attr('pathname');
					setInterval(function(){ 
					nowhareff = $(location).attr('pathname');
					now_history_num   = history.length;
					if(pasthareff !== nowhareff){
					///$('.virsual_box').fadeIn(333);
					
					
					
					load_nav(nowhareff);
					pasthareff              = nowhareff;
					
					past_history_num = now_history_num;
					

				}
					
					},200);
					});
					
					
					layout_num = 0;
		function load_nav(elem){
		preloader.on();
		$("LoadingPage").fadeIn();

		layout_num++
		ajaxgolink = elem;
		///$('.virsual_box').fadeIn(333);
		

			
			if(nowhareff !== ajaxgolink){
			history.pushState({}, '', ajaxgolink);
			pasthareff = $(location).attr('pathname');
			///$("#qqq").append(ajaxgolink+"</br>");
			}
			
			
					$.get("includes/"+ajaxgolink,function(data){
					///alert(data);
					$("main_contenner").empty();
					$('main_contenner').html(data);
					
					preloader.off();
					$("LoadingPage").fadeOut();
					});
				


		return false;
		}
		
		
		
function check_but(id){
var edit_set_id            = $("#"+id);
var status = (edit_set_id.attr("val") == '0') ? ' 1 ' : '0';
if(edit_set_id.attr("val") == '0'){
edit_set_id.children('check').animate({height:18,width:18}, "fast").animate({height:22,width:22}, "fast");
edit_set_id.children('check').css("background-image","url('img/w_check.png')");
edit_set_id.children('check').css("background-color","#F0535A");
}else{
edit_set_id.children('check').animate({height:25,width:25}, "fast").animate({height:22,width:22}, "fast");
edit_set_id.children('check').css("background-image","url('')");
edit_set_id.children('check').css("background-color","#fff");
}

edit_set_id.attr("val",status);

}
				
		
function fast_edit_toggle(id){
var edit_set_id            = $("#"+id);
var updateMyInfoRow = $("#"+id).attr("name");
var togBut = edit_set_id.find('input');
var status = (togBut.is(':checked')) ? ' 0 ' : '1';

if ($("#"+id).attr("name")){
var SetType="myInfo";
var updateMyInfoRow = $("#"+id).attr("name");
}else if ($("#"+id).attr("set")){
var SetType="set";
var updateMyInfoRow = $("#"+id).attr("set");
}

$.post('actions.php' , {updateType:SetType,updateMyInfoRow:updateMyInfoRow,updateMyInfoVal:status} , function(data){ });
}
		
		

		
function fast_edit_myinfo(id){
var edit_set_id            = $("#"+id);

if ($("#"+id).attr("name")){
var SetType="myInfo";
var updateMyInfoRow = $("#"+id).attr("name");
}else if ($("#"+id).attr("set")){
var SetType="set";
var updateMyInfoRow = $("#"+id).attr("set");
}

$( $("#"+id).children("editarea").children("button") ).click(function(){


if(edit_set_id.children("editarea").children("input").length){
var edit_set_val = edit_set_id.children("editarea").children("input").val();
}else{
var edit_set_val = edit_set_id.children("editarea").children("editble").html();
}


edit_set_id.children("contentarea").children("content").html(edit_set_val);

if(edit_set_val ==''){
		edit_set_id.children("editarea").css("background","#FBCED0");
}else{
	
		$.post('actions.php' , {updateType:SetType,updateMyInfoRow:updateMyInfoRow,updateMyInfoVal:edit_set_val} , function(updateMyInfoDate){
		if(updateMyInfoDate == "true"){
			
		
		 alerts_box("تم الحفظ","sesc","4000");
		 edit_set_id.children("editarea").css("background","#fff");
		 edit_set_id.children("contentarea").slideDown().delay(1000);
		edit_set_id.children("editarea").slideUp().delay(1000);	
		}else if(updateMyInfoDate == "exist"){
		 alerts_box("عفوا العنوان مستخدم من قبل","error","4000");
		 edit_set_id.children("editarea").css("background","#FBCED0");
		}else if(updateMyInfoDate == "reload"){
		 alerts_box("رجاء قم بتفعيل بريدك الجديد","error","4000");
		  location.reload();
		}else{
		 alerts_box("ادخل قيمة صالحة رجاء","error","4000");	
		}
		});
}

return false();
});

$( $("#"+id).children("editarea").children("cancel") ).click(function(){
edit_set_id.children("contentarea").slideDown().delay(1000);
edit_set_id.children("editarea").slideUp().delay(1000);
return false;
});

edit_set_id.children("editarea").slideDown();
edit_set_id.children("contentarea").slideUp();
edit_set_id.children("editarea").children("editble,input").focus();
}


/// الجداول المتجاوبة
function ResponsiveTable(){
var Table = $(".ResponsiveTable");
var Row   = $(".ResponsiveTable").children("row");
var cell  = $(".ResponsiveTable").children("row").children("cell");
var CelWidth = cell.first().attr("data-width");
Row.first().children("cell").addClass("ResponsiveTableHead");
cell.css("width",CelWidth);
}
