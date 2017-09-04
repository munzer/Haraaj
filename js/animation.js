function alerts_box(text,subj,time){
$('#alerts_box_text').html(text);
if(subj=="error"){var background = "#EB584B"; Duanimate("alerts_box","show","shake","900");}else
if(subj=="sesc"){var background = "#4DB849"; Duanimate("alerts_box","show","rubberBand","100");}else
if(subj=="help") {var background = "#5F98D1"; Duanimate("alerts_box","show","swing","900");}
$('alerts_box').css("background-color",background).delay(time).fadeOut(333);
}

// Freez And Un Freez Page --------------------------------------->
	(function ( $ ) {
		$.fn.freez = function (options){
			$(this).css("pointer-events","none"); 
			$("body,html").css("cursor","progress"); 
		}
	}( jQuery ));
	(function ( $ ) {
		$.fn.unfreez = function (options){
			$(this).css("pointer-events","auto");
			$("body,html").css("cursor","auto");
		} 
	}( jQuery ));
// Freez And Un Freez Page ---------------------------------------<

function confirmbox(title,dothis){
	
	Duanimate("confirmbox","show","bounceIn","800");
	$('confirmbox').children("p").html(title);

			$('confirmbox').children("button").click(function(){
					var confirmSellectedBut = $(this).attr("id");
					if(confirmSellectedBut == "confirmConf"){
					dothis();
					Duanimate("confirmbox","hide","fadeOut","400");
					}else{
					Duanimate("confirmbox","hide","fadeOut","400");
					}

			});
			$(document).keypress(function(d){if(d.which == 13){ $('confirmbox').children("button").click(); }});
			$(document).keyup(function(d) { if (d.keyCode == 27){ $('confirmbox').fadeOut(111); } return false;  });

}


function Duanimate(TheElement,action,animate,speed){
speed = speed;
if(action == "hide"){ action = "none" ;}
else if(action == "show"){ action = "inline" ;}
else if(action == "toggle"){
	if( $(TheElement).is(":visible") ){
	action = "none" ;
	animate = animate.replace("In", "Out");
	}else{
	action = "inline";
	animate = animate.replace("Out", "In");
	}
}else if(action == "none"){
	action = "inline" ;
}


speedOut = speed.split(".").join("");
if(speed.toString().indexOf('.') == "-1"){
speed = "0."+speed;
speedOut = Math.floor(speed)+100;
}



$(TheElement).addClass("animated "+animate).css("animation-duration",speed+"s");

if ($(TheElement).css('display') == 'none'){
 $(TheElement).css("display",action); setTimeout(function () { $(TheElement).removeClass("animated "+animate);},speedOut);
}else{
$(TheElement).css("display",action); setTimeout(function () { $(TheElement).removeClass("animated "+animate);},speedOut);

}

return false;
}



function openMenu(but,ul){
var Xpage =  but.pageX;
if($(ul).parent().css("position")=="fixed"){
var Ypage =  $(but).height()+$(but).offset().top - $(window).scrollTop();
}else{
var Ypage =  $(but).height()+but.pageY;
}

var DropDownWidth = $(ul).width();
var DropDownPos   = Xpage-DropDownWidth;


if(DropDownPos>DropDownWidth){
$(ul).css("left",DropDownPos+"px");
}else{
$(ul).css("left",Xpage+"px");
}

$(ul).css("top",Ypage+"px");
if($(ul).is(':visible')){
Duanimate(ul,"hide","pulse","400");return false();
}else{
Duanimate(ul,"show","pulse","400");return false();
}
}



(function ( $ ) {
$.fn.sweet_alert = function (options){

        var settings = $.extend({
            // These are the defaults.
            type: "error",
            description: "no description",
            butContent: "button !!",
            title: "no title"
        }, options );
	 
	 
	if(settings.type == "error") { var img = "img/w/warning_alert.png"; var color = "F79196"; var animateType = "shake";}	
	if(settings.type == "InputEmpty") { var img = "img/w/pen.png"; var color = "F79196"; var animateType = "shake";}	
	if(settings.type == "true") { var img = "img/w/check.png"; var color = "4BB847";  var animateType = "flipInX";}	
	if(settings.type == "block") { var img = "img/w/error_shild.png"; var color = "F79196";  var animateType = "shake";}	
	var rand_but_id = Math.floor(Math.random() * 999) + 21;
			
			
			var rand_id = Math.floor(Math.random() * 999) + 21;
			
$(this).append('<div class="lightBox" id="sweetArertPop_'+rand_id+'"><sweet_alert id="sweetArertBox_'+rand_id+'" style="display:none;" class="shadow1"><img src="'+img+'"  style="background-color:#'+color+'"/><h1>'+settings.title+'</h1><h2>'+settings.description+'</h2><button id='+rand_but_id+' style="background-color:'+color+'">'+settings.butContent+'</button></sweet_alert></div>');

Duanimate("#sweetArertPop_"+rand_id,"show","zoomIn","400");
Duanimate("#sweetArertBox_"+rand_id,"show","shake","600");
	
$("#"+rand_but_id).click(function(){
$(this).parent("sweet_alert").parent(".lightBox").fadeOut(222);
$(this).parent("sweet_alert").parent(".lightBox").delay("555").remove();
});
 
}
}( jQuery ));


function du_attr(){
$("body").append("<div class='attr'></div>");
$(document).mousemove(function(P){
pagey = P.pageY;
pagex = P.pageX;
pagex_mouseoption = P.pageX;
pagey_mouseoption = P.pageY;
});

$("div,img,button,a,cell,input").mouseover(function(PElment){
var thisElmentText    = $(this).attr("help");


if (typeof thisElmentText !== typeof undefined && thisElmentText !== false) {
var thisElmentHeight = $(this).height();
var thisElmentWidth  = $(this).width()/2;

var div_width  =  parseInt($(this).offset().left);
var div_height =  parseInt($(this).offset().top);
var attrheight = parseInt($(".attr").height()*2 +2);
var attrwidth  = parseInt($(".attr").width()/2);

$(".attr").css("margin-left",thisElmentWidth-attrwidth+div_width+"px");
$(".attr").css("margin-top",div_height-attrheight+"px");
$(".attr").html(thisElmentText);
$(".attr").show();
}


}).mouseout (function(){
var thisElmentText    = $(this).attr("help");
if (typeof thisElmentText !== typeof undefined && thisElmentText !== false) { $(".attr").hide(); }
 });

}


////////////////////////////////////////////////////// ImagesLightBox
function OpenInLightBox(){
$("img,.OpenInLightBox").click(function(){
if($(this).attr("LightBox")=="true"){
var src = $(this).attr("src");
$("#droplightboxsrcHere").attr("src",src);
Duanimate("#openimage","show","pulse","400");
}
});
}

//// Scrol Up
function ScrolUp(Contener){
    $(Contener).animate({ scrollTop: 0 }, 600);
    return false;
 }
