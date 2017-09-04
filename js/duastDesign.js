/// Matterial Design Forms
$.fn.ApplayMaterialDesignForm = function(options){
var defaults = {  
AutoCheck:"XX"
};
	/// Buttons Actions
	var Form = this;
	Form.children("button").each(function(){
	var ActionType = $(this).attr("ActionType");
		if(ActionType == "submit"){
			Form.submit(function(){
				return false;
			});
		}	
	});
	
	
	
	
	/// Creations By Jquery
	$(document).ready(function(){
	Form.find("input,textarea").each(function(){
	if(typeof $(this).attr("input-type") !== typeof undefined){
		var input_type  = $(this).attr("input-type").split("||")[0]; 

		if(input_type == "datepicker"){
			Feld = $(this);
				DayHolder   = $(this).attr("day-holder");
				MonthHolder = $(this).attr("month-holder");
				YearHolder  = $(this).attr("year-holder");
			Feld.css("display","none");
			Feld.wrap("<div class='InputPattern1'>");
			Feld.before("<label style='color:#777;'>"+Feld.attr("input-holder")+"</label>");
			Feld.after("<error></error>");
			Feld.before("<datePiker><dayPick>"+DayHolder+"</dayPick><monthPick>"+MonthHolder+"</monthPick><yearPick>"+YearHolder+"</yearPick></datePiker>");
				Feld.prev("datePiker").find("dayPick,monthPick,yearPick").click(function(){
				$(this).ConvertToDatePicker();
				});
		}else if(input_type == "textarea"){
			
			Feld = $(this);
			Feld.wrap("<div class='InputPattern1'>");
			Feld.before("<label>"+Feld.attr("input-holder")+"</label>");
			Feld.before("<textarea></textarea>");
			Feld.after("<error></error>");
			Feld.attr("placeholder",Feld.attr("input-holder"));
			Feld.css("display","none");
			Feld.prev("textarea").keyup(function(){
			Feld.val($(this).val()).change();	
			});
			
		}else if(input_type == "selectMenu"){
			
			
			SelectField = $(this);
			AllItems    = SelectField.attr("select-items");
			AllItemsArr = AllItems.split(',');
				
			SelectField.css("display","none");
			SelectField.wrap("<div class='InputPattern1'>");
			SelectField.before("<label>"+SelectField.attr("input-holder")+"</label>");
			SelectField.after("<error></error>");
			SelectField.before("<SelectMenuHolder><T></T></SelectMenuHolder>");
			SelectField.prev("SelectMenuHolder").append("<SelectMenu  class='shadow2'></SelectMenu>");
			
			function BuildIt(NewElement){
				NewElement.prev("SelectMenuHolder").children("SelectMenu").empty();
				AllItems    = NewElement.attr("select-items");	
				AllItemsArr = AllItems.split(',');
				NewElement.prev("SelectMenuHolder").find("T").html(AllItemsArr[0].split('||')[0]);
				for(i in AllItemsArr){
					var item=AllItemsArr[i];
						if(item.split('||')[1] !== "null"){
							if(item.split('||')[1].includes("{UnSelected}")){
								NewElement.prev("SelectMenuHolder").children("SelectMenu").append("<cell style='background:#FF8C00;color:#fff;' class='unActive' value='"+item.split('||')[1]+"'>"+item.split('||')[0]+"</cell>");
							}else{
								NewElement.prev("SelectMenuHolder").children("SelectMenu").append("<cell value='"+item.split('||')[1]+"'>"+item.split('||')[0]+"</cell>");
							}
						}
				}
			
			}
			
			BuildIt($(this));
			
			$(this).on("ItemsChange",function(){
				BuildIt($(this));
				//alert($(this).attr("select-items"));
			});
			
			SelectField.prev("SelectMenuHolder").children("SelectMenu").css("bottom","-"+(SelectField.prev("SelectMenuHolder").children("SelectMenu").height()+10)+"px");

			SelectField.prev("SelectMenuHolder").children("t").on("click",function(){
				$(this).parent("SelectMenuHolder").children("SelectMenu").toggle();
			});
			
			
			SelectField.prev("SelectMenuHolder").on("click","cell",function(){
				$(this).parent("SelectMenu").hide();
				
					$(this).parents("SelectMenuHolder").children("T").html( $(this).html() );
					$(this).parents("SelectMenuHolder").children("T").css("color","#2F2933");
					$(this).parents("SelectMenuHolder").children("T").css("font-weight","bold");
					$(this).parents("SelectMenuHolder").next("input").val($(this).attr("value")).change();
					
			});
			
			
			
			
		}else if(input_type == "hidden"){
			Feld = $(this);
			Feld.hide();
		}else{
			Feld = $(this);
			Feld.wrap("<div class='InputPattern1'>");
			Feld.before("<label></label>");
			Feld.after("<error></error>");
			Feld.attr("placeholder",Feld.attr("input-holder"));
		}
		
		/// validition For Inputs Error While Typing ON The KeyBoard For Those Input Type
		if(input_type=="email"      ||
		   input_type=="text"       ||
		   input_type=="password"   ||
		   input_type=="repassword" ||
		   input_type=="number"     
		)
		{
			Feld.keyup(function(){
				$(this).ChekInputErrors();
			});
		}
		
		
	}
	});
	});
	
	
	/// Date Picker Picke
	$.fn.ConvertToDatePicker = function(){
	var targetPick = this.prop("tagName").toLowerCase();
	var PickerHolder = this;

		iSDateComplets = function(PickerHolder){
		var DateContener   = $(PickerHolder).parent("datePiker");
		var ValueOfDay     = DateContener.find("daypick").html();
		var ValueOfMonth   = DateContener.find("monthPick").html();
		var ValueOfYear    = DateContener.find("yearPick").html();
		var DateCompile    = ValueOfDay+"-"+ValueOfMonth+"-"+ValueOfYear;
			if(isNaN(ValueOfDay) || isNaN(ValueOfMonth) || isNaN(ValueOfYear)){
				return "false";
			}else{
				PickLastVal = PickerHolder.parent("datepiker").next("input").val(DateCompile).change();
				PickerHolder.parent("datepiker").next("input").ChekInputErrors();
				return "true";
			}
		}
		
		
		$("#DatePicker").find(".datePikerLightBoxContener cells").html("");
		if (targetPick == "daypick") {
		var windowsTitle = "please select day";
		for(i=1;i<=30;i++){ $("#DatePicker").find(".datePikerLightBoxContener cells").append("<day>"+i+"</day>"); }
			$("#DatePicker").find("day").click(function(){
			$(this).css("background","#2F2933");
			$(this).css("color","#fff");
			DayValue = $(this).html();
			PickerHolder.html(DayValue);
			iSDateComplets(PickerHolder);
			Duanimate("#DatePicker","hide","pulse","500");
			});
		}else if (targetPick == "monthpick") {
		var windowsTitle = "please select a month";
		for(i=1;i<=12;i++){ $("#DatePicker").find(".datePikerLightBoxContener cells").append("<month>"+i+"</month>"); }
			$("#DatePicker").find("month").click(function(){
			$(this).css("background","#2F2933");
			$(this).css("color","#fff");
			MonthValue = $(this).html();
			PickerHolder.html(MonthValue);
			iSDateComplets(PickerHolder);
			Duanimate("#DatePicker","hide","pulse","500");
			});
		}else if (targetPick == "yearpick") {
		var windowsTitle = "please select a year";
		for(i=2017;i>=1940;i--){ $("#DatePicker").find(".datePikerLightBoxContener cells").append("<year>"+i+"</year>");
		}
			$("#DatePicker").find("year").click(function(){
			$(this).css("font-weight","bold");
			YearValue = $(this).html();
			PickerHolder.html(YearValue);
			iSDateComplets(PickerHolder);
			Duanimate("#DatePicker","hide","pulse","500");
			});
		}

			Duanimate("#DatePicker","show","pulse","500");
			$("#DatePicker").find("st").html(windowsTitle);
	}

			// The animation When foucos In
			Form.find("input").focusin(function(){
			input_holder         = $(this).attr("input-holder");
				$(this).css("border-bottom","2px solid #2F2933");
				$(this).prev("label").css("color","#2F2933");
				$(this).prev("label").html(input_holder);
				var label = $(this).prev("label");
				$(this).attr("placeholder","");
				Duanimate(label,"show","flipInX","0.400");
			});
			
			// The animation When foucos Out
			Form.find("input").focusout(function(){
			input_holder         = $(this).attr("input-holder");
				$(this).prev("label").css("color","#666");
				var placeholder = $(this).attr("input-holder");
				var inputValue  = $(this).val().trim();
				var error_empty = $(this).attr("error-empty");
			});
			

};



	/// Submit Button Click
	$.fn.FastSendData = function(options){
	var defaults = {  
	TaskFinish: function () {},
	TaskStrart: function () {}
	};
	var options = $.extend(defaults, options);
	
			var actionPage = this.attr("ActionPage");
			actionParams   = "";
										

			this.find("input").each(function(event){

	
			
			if(typeof $(this).attr("data-para") !== typeof undefined && $(this).attr("data-para") !== false){
				if(typeof $(this).attr("validition-complete") !== typeof undefined && $(this).attr("validition-complete") !== false){
					if($(this).attr("validition-complete") !=="true"){
					consol.log("There is input not validate");
					return false;
					}
				}else{
					consol.log("No Complete Attr In Form");
					return false;	
				}

				var InputValue = $(this).val().trim();	
				var SqlFild    = $(this).attr("data-para");
				actionParams += "&"+SqlFild+"="+InputValue;
			}
			});
					var JoinData = "";
					$.ajax({
						method: "GET",
						'async': false,
						url: actionPage+actionParams,
						data: JoinData,
						
							'beforeSend': function () {
								options.TaskStart();
							},
							'success': function (data) {
								JoinData = data;
								options.TaskFinish(JoinData);
							}
					});
										
			

	};

	
	//// Check For Every Errors In Inputs Filds
	$.fn.ChekInputErrors = function(){
	this.attr("validition-complete","false");
	
	Error_holder         = this.next("error");
	InputValue           = this.val();
	input_holder         = this.attr("input-holder");
	input_type           = this.attr("input-type");
	input_min            = this.attr("input-min");
	input_max            = this.attr("input-max");
	input_required       = this.attr("input-required");
	input_repassword     = this.attr("input-repassword");

		//// Check If Input Empty
		if (typeof input_required !== typeof undefined && input_required !== false) {
		input_required_Error = this.attr("input-required").split("||")[1];
		input_required_Set   = this.attr("input-required").split("||")[0];
		if(input_required_Set=="true"){
			if(InputValue==""){
			Error_holder.html(input_required_Error);
			return false;
			}else{
			Error_holder.html("");
			}
		}
		}
		
		//// Check Feld is repassword
		if (typeof input_repassword !== typeof undefined && input_repassword !== false) {
		var input_repassword_Error    = this.attr("input-repassword").split("||")[1];
		var input_repassword_set      = this.attr("input-repassword").split("||")[0];
		var indexFeld = $(input_repassword_set).val();
		if(this.val()!==indexFeld){
		Error_holder.html(input_repassword_Error);
		return false;
		}else{
		Error_holder.html();	
		}
		}
		

		
		//// Check The Minimum length Of Input
		if (typeof input_min !== typeof undefined && input_min !== false) {
			input_min_Error      = this.attr("input-min").split("||")[1];
			input_min_set      = this.attr("input-min").split("||")[0];
			if(InputValue.length<input_min_set){
				Error_holder.html(input_min_Error);
				return false;
			}else{
				Error_holder.html("");
			}
		}
				

		
		//// Check The Maximum length Of Input
		if (typeof input_max !== typeof undefined && input_max !== false) {
			input_max_Error    = this.attr("input-max").split("||")[1];
			input_max_set      = this.attr("input-max").split("||")[0];
			if(InputValue.length>input_max_set){
				Error_holder.html(input_max_Error);
				return false;
			}else{
				Error_holder.html("");
			}
		}
		
		//// Check The Type Of Input
		if (typeof input_type !== typeof undefined && input_type !== false) {
			input_type_Error = this.attr("input-type").split("||")[1];
			input_type_Set   = this.attr("input-type").split("||")[0];
		if(input_type_Set=="string"){
			if(/^[a-zA-Z0-9-]*$/.test(InputValue) == false || InputValue=="") {
			Error_holder.html(input_type_Error);
			}else{
			Error_holder.html("");
			}
		}else if(input_type_Set=="text"){
			if(/^[a-zA-Z0-9-]*$/.test(InputValue) == false || InputValue=="") {
			Error_holder.html(input_type_Error);
			}else{
			Error_holder.html("");
			}
		}else if(input_type_Set=="email" || InputValue==""){
			if(!InputValue.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)){
			Error_holder.html(input_type_Error);
			}else{
			Error_holder.html("");
			}
		}else if(input_type_Set=="number" || InputValue==""){
			if(!$.isNumeric(InputValue)){
			Error_holder.html(input_type_Error);
			}else{
			Error_holder.html("");
			}
		}else if(input_type_Set=="password" || InputValue==""){
			Error_holder.html("");
		}else if(input_type_Set=="arabic" || InputValue==""){
			Error_holder.html("");
		}else if(input_type_Set=="selectMenu"){
			Error_holder.html("");
		}
		}

		this.attr("validition-complete","true");
		
		
	}
	

/////// Disable Elemint Or Un Disable For Click
$.fn.DisableButton   = function(){ this.css("opacity","0.5"); this.attr('disabled','true');}
$.fn.UnDisableButton = function(){ this.css("opcity","1");    this.removeAttr('disabled'); }


///////// Pagination /////////////
function Pagination(options){
	
	 Total         = "";
	 PerPage       = "";
	 CurrentPage   = "";
	 ButtonsNumber = "";
	 Element       = $(options.Contener);
	 Class         = this;
	
	
	this.Build = function(options){
		Total         = options.Total;
		PerPage       = options.PerPage;
		CurrentPage   = options.CurrentPage;
		ButtonsNumber = options.ButtonsNumber;

		Element.empty();
		
		
		var Array = [];
		

		if(CurrentPage == Math.floor(ButtonsNumber/2)){
			StartFrom = CurrentPage-1;
		}else if(CurrentPage > Math.floor((Total/PerPage)-ButtonsNumber)){
				if(Math.floor((Total/PerPage)-ButtonsNumber)>0){
					StartFrom = CurrentPage-ButtonsNumber+Math.floor(ButtonsNumber/2);
				}else{
					StartFrom = 1
				}
		}else if(CurrentPage > Math.floor(ButtonsNumber/2)){
			StartFrom = CurrentPage-Math.floor(ButtonsNumber/2);
		}else{
			StartFrom = 1;
		}

		Count = 0;
		for(i=StartFrom;i<=Math.floor(Total/PerPage)+1;i++){
		Count++;
			if((Total/PerPage) == 0){ break; }
			Array.push(i);
			if(Count == ButtonsNumber){ break; }
		}
		
		var min = Math.min.apply(Math, Array);
		var max = Math.max.apply(Math, Array);
		
		//alert(ButtonsNumber-Array.length);
		


		//Element.append("<button>"+i+"</button>");
		$.each(Array, function( index, value ) {
			if(CurrentPage == value){
				var Active = "#000";
			}else{
				var Active = "#1F77B8";
			}
			Element.append("<button style='background:"+Active+";'>"+Math.floor(value)+"</button>");
		});
		
		
	}
		$(document).on("click",".Pagination button",function(){
		MyCurrentPage = $(this).text();
		Class.Build({
			Total         : Total,
			PerPage       : PerPage,
			CurrentPage   : MyCurrentPage,
			ButtonsNumber : ButtonsNumber
		});
	});

}
///////// Pagination /////////////



	

////// Build a Templates , is a Function Help You To Spilt A Design From Programming
function DuBuildTemplate(){
	var MyClass = this;
	
	ActionPage         = "";
	Block              = "";
	Page               = "";
	Filter             = "";
	ResultsPerPage     = "";
	Table              = "";
	
	this.Apply = function(options){
		var options        = $.extend( {}, this.defaults, options );
		Block              = options.Block;
	};
			
	this.Start = function(options){
		var options        = $.extend( {}, this.defaults, options );
		Page               = options.Page;
		Filter             = options.Filter;
		ResultsPerPage     = options.ResultsPerPage;
		OrderBy            = options.OrderBy;
		OrderKey           = options.OrderKey;
		Table              = options.Table;
		
		var JoinData = "";
		$.ajax({
			method: "GET",
			'async': false,
			url: "plugins/SystemBlocks/GetBlocks.php?Block="+Block+"&Filter="+Filter+"&Page="+Page+"&ResultsPerPage="+ResultsPerPage+"&OrderBy="+OrderBy+"&OrderKey="+OrderKey,
			data: JoinData,
			
				'beforeSend': function () {
					options.IfLoading();
				},
				'success': function (data) {
					JoinData = data;
					options.IfFinish(JoinData);
				}
		});
		
	$.get("actions.php?countThis=HTML&Table="+Table+"&Filter="+Filter,function(Data){
		options.CountTotal(Data);
	});
	
					

	};
	
	
};


/// Responseve Tables Using Jquery
$.fn.ResponsiveTable = function(){
	
	TablesHead = [];
	var ColumnsCotener = $(this);
	var ColumnsCount   = this.children("row").first().children("column").length-1;
	var ColumnsWidth   = 100/this.children("row").first().children("column").length;
	
		this.children("row").first().find("column").each(function(){
		TablesHead.push($(this).html());
		});
		
	this.children("row").first().addClass("ResponsiveTableRowHead");
	this.children("row").find("column").css("width",ColumnsWidth+"%");

		$(document).ready(function(){
			ScreenWidth = $(window).width();
		});

		$(window).resize(function(){
			ScreenWidth = $(window).width();
		});

		$(document).ready(function(){
			if(ScreenWidth<600){
			ColumnsCotener.children("row").first().hide();
				ColumnsCotener.children("row").each(function(Kr,Vr){
					$(this).find("column").each(function(Kc,Vc){
						$(this).wrap("<mobileRow></mobileRow>");
						$(this).before("<columnKey>"+TablesHead[Kc]+"</columnKey>");
						$(this).css("width","70%");
					});
				});
			}
		});

}


//// The Function Use To Move Div Under Clicked Item , Also Its Built To Notifictions

$.fn.DrobDivHere = function(options){
	var defaults = {  
		Div:"XX",
		Position:"absolute",
	};

	ElementSelector     = $(options.Div);
	ElementWidth        = ElementSelector.width();
	ElementHeight       = ElementSelector.height();
	DocumentScrol       = 0;
	ClickedDivOffset    = this.offset().top + this.height() + 30;

	$(window).scroll(function() {
		DocumentScrol = $(window).scrollTop();
	});

	this.click(function(e){
			ElementSelector.css('left',e.pageX-(ElementWidth/2) + 'px' );
		if(options.Position=="fixed"){
			ElementSelector.css('top',(ClickedDivOffset/2)+e.pageY-DocumentScrol + 'px' ); 
		}else{
			ElementSelector.css('top',(ClickedDivOffset/2)+e.pageY+ 'px' ); 
		}
			ElementSelector.css('position',options.Position); 
		Duanimate(options.Div,"toggle","bounceIn","800");
	});
};


//// Material  Design  Notifications

(function ($) {
$.FloatNotification = function(options){
	var defaults = {
		thumb            :"XX",
		text             :"You Dont Pass Text!",
		button           :"NoButton",
		HideAfter        :"none",
		thumbBackground  :"#f0f0f0"
	}
	
	var options = $.extend({}, defaults, options);
	
	
	if(options.button !== "NoButton"){
		var button = "<button>"+options.button+"</button>";
	}else{
		var button = "";
	}
	
	
	var NotificationID = "FloatNotification"+Math.floor((Math.random() * 10000) + 1);
	
	var Contener = $(".MaterialFloatNotificationsContener");

	    Contener.append("<cel id='"+NotificationID+"' style='display:nonee;' class='shadow2'>\
					<TopBar>\
						<img src='img/b/close.png' />\
					</TopBar>\
					<Thumb class='thumb' style='background-image:url("+options.thumb+");background-color:"+options.thumbBackground+";'></Thumb>\
					<Text>"+options.text+"</Text>\
					<ButtonsContener>"+button+"</ButtonsContener>\
				</cel>");

				Duanimate(".MaterialFloatNotificationsContener cel","none","slideInUp","0.900");

				
					//// Closde Notification
					$("#"+NotificationID).children("TopBar").children("img").click(function(){
						Duanimate("#"+NotificationID,"hide","fadeOutLeft","0.900");
							setTimeout(function(){
									$("#"+NotificationID).remove();
							},1000);
					});
					
					/// Hide Notification After
					if(options.HideAfter !== "none"){
						setTimeout(function(){
							Duanimate("#"+NotificationID,"hide","flipOutX","0.600");
								setTimeout(function(){
									$("#"+NotificationID).remove();
								},1000);
						},options.HideAfter);
					}

						options.GetId.call(this,NotificationID);
					
	/*
	var CellsCount   = 0;
	var CellsHeight  = 0;
	Contener.children("cel").each(function(k,v){
	CellsCount  = CellsCount+1;
	CellsHeight = CellsHeight + $(this).height();
	});
	
	
	if(CellsHeight>window.innerHeight){
		var MinCel = CellsCount;
		Contener.children("cel").each(function(k,v){
		if(k==MinCel){ $(this).hide(); }
		MinCel = MinCel-1;
		});
	}
	
	*/
	

}
}(jQuery));



//// Material  Design  Notifications
(function ($) {
$.fn.MaterialMenu = function(options){

	var contener = this;
	var defaults = {
	BackgroundColor : "",
	LinksColor      : "",
	OpenedLinkColor : "",
	}
	var options     = $.extend({}, defaults, options);
	
	var CurrentLink = (window.location.href).replace("#", "");
	
	var ScreenWidth = $(window).width();

	
	//Change Links Colors
	contener.children("a").css("color",options.LinksColor);
	
	contener.children("mobileMenu").click(function(){
		contener.children("a").toggle();
	});	
	
	
	//Opened Link Hover After PAge Load
	if(ScreenWidth > 500){
	//Change Background Color
	contener.css("background-color",options.BackgroundColor);
	
	contener.children("a").each(function(){
		var Href = $(this).prop("href");
		if(Href == CurrentLink){
			$(this).css("border-bottom","3px solid"+options.OpenedLinkColor);
		}
	});
	}else{

	// IF Visbilte OF Menu IS Always Show
	if(options.Display == "block"){
		contener.children("mobileMenu").css("display","none");
		contener.children("a").css("display","block");
	}
	
	contener.children("mobileMenu").css("background-color",options.MobileContener);
	contener.children("mobileMenu").children("mobileIcon").css("background-color",options.MobileIconColor);
	contener.children("a").each(function(){
		var Href = $(this).prop("href");
			//Change Background Color
			$(this).css("background-color",options.BackgroundColor);
			if(Href == CurrentLink){
				$(this).css("opacity","0.8");
			}
		});	
	}
	

}
}(jQuery));