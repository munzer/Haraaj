$.fn.SetFastSettings = function(){
			var DataSettings  = this.closest(".FastEditSettingsCell").attr("data-settings");
			var DataactioPage = this.closest(".FastEditSettingsCell").attr("data-actioPage");
			this.attr("id",DataSettings);
			var parentId      = this.closest(".FastEditSettingsCell").attr("id");
			var ShowedVal     = this.closest(".FastEditSettingsCell").find("v");
			var NewHiddenId   = this.closest(".FastEditSettingsCell").attr("id")+"HiddenArea";
			var DataNotic     = this.closest(".FastEditSettingsCell").attr("data-notic");
			var ErrorReport       = this.find("ErrorReport");
			var NoticReport       = this.find("NoticReport");
			$(this).closest(".FastEditSettingsCell").find("HiddenArea").attr("id",NewHiddenId);
			/// Stop Input If Empty
			this.find("button").css("opacity","0.5");
			/// Active Input If Input Is Not Empty
				this.find("input").keyup(function(){
					if($(this).val().trim() !==""){
						$(this).next("button").css("opacity","1");
					}else{
						$(this).next("button").css("opacity","0.5");
					}
				});
				
				this.mouseover(function(){$(this).find("ActionArea").show();});
				this.mouseout(function(){$(this).find("ActionArea").hide();});
			
				this.find("ActionArea Edit").click(function(){
					if(typeof DataNotic !== typeof undefined && DataNotic !== false){
						NoticReport.show();
					}
						Duanimate("#"+NewHiddenId,"toggle","flipInX","500");
						$(this).hide();
						$(this).next("img").show();
				});
			
				this.find("ActionArea img").click(function(){
					if(typeof DataNotic !== typeof undefined && DataNotic !== false){
						NoticReport.hide();
					}
						Duanimate("#"+NewHiddenId,"toggle","flipInX","500");
						$(this).hide();
						$(this).prev("Edit").show();
				});
			$("#"+NewHiddenId).find("button").click(function(){
			var DataValue = $(this).prev("input").val();
					$(this).closest(".FastEditSettingsCell").find("ActionArea").children("Edit").show();
					$(this).closest(".FastEditSettingsCell").find("ActionArea").children("img").hide();
				$.post(DataactioPage+"key="+DataSettings+"&value="+DataValue,function(Data){
					if(Data=="RefreshPage"){
						window.location.reload();
					}else if(Data=="true"){
						Duanimate("#"+NewHiddenId,"toggle","flipInX","500");	
						ShowedVal.html(DataValue);
					}else{
						ErrorReport.children("span").html(Data);	
						ErrorReport.show();	
					}
				});
			});
};
	

	$.fn.SetFastWebsiteSettings = function(){
	var Contener = this;
		this.children("key").click(function(){
			Contener.children("EditArea").toggle();
			KeyPlaceHolder = $(this).html();
				if(typeof Contener.children("EditArea").children("input").attr("placeholder") == typeof undefined || Contener.children("EditArea").children("input").attr("placeholder") == false){
					Contener.children("EditArea").children("input").attr("placeholder",KeyPlaceHolder);
				}
		});
		
	if(Contener.children("EditArea").length>0){
		this.children("EditArea").children("button").click(function(){
			var InputVal = Contener.children("EditArea").children("input").val();
			var SetKey   = Contener.attr("data-settings");
				Contener.children("EditArea").children("button").css("opacity","0.5");
			$.post("actions.php?SetWebSiteSettings=Html&Key="+SetKey+"&Value="+InputVal,function(SetData){
				Contener.children("EditArea").children("button").css("opacity","1");
				if(SetData.trim()=="true"){
					Contener.children("EditArea").toggle();
					Contener.children("CurrentValue").html(InputVal);
				}else{
					Contener.children("EditArea").toggle();
						$("body").sweet_alert({
						type: "error",
						title: "",
						butContent: "X",
						description : SetData
						});
				}
			});
		});
	}else if(Contener.children("CurrentValue").children("input").length>0){


	$(document).ready(function(){
	SetKey   = Contener.attr("data-settings");
	InputVal = Contener.attr("data-value");
	if(InputVal){ Contener.children("CurrentValue").find(".lcs_check").lcs_on(); }
	
	
	Contener.children("CurrentValue").delegate('.lcs_check', 'lcs-statuschange', function() {
	NewSetKey   = Contener.attr("data-settings");
	NewInputVal = Contener.attr("data-value");
	
	if(NewInputVal=="true" || NewInputVal=="1"){
		NewInputVal = "false";
		Contener.attr("data-value","false");
	}else{
		
		NewInputVal = "true";
		Contener.attr("data-value","1");
	}
	

	$.post("actions.php?SetWebSiteSettings=Html&Key="+NewSetKey+"&Value="+NewInputVal,function(DDDD){ });	
	
	});
	
	});
	
	}
	};
	
	//// ->>>>>>>>>>>>>>>>>>>>>> Instent Updatble Plugin <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<- ///
	(function ($) {
		$.fn.InstantUpdatble = function(options){

			var contener   = this;
			var defaults   = {}
			var options    = $.extend({}, defaults, options);
			var UpdateType = contener.attr("update-type");
			var UpdatePage = contener.attr("update-page");

				function UpdateValNow(Value){
					$.get(UpdatePage+Value,function(Data){});
						return false;
				}

			if(UpdateType == "text"){
				contener.attr("contenteditable","true");
				contener.focus();
					contener.keydown(function(e) {
						if(e.which == 13) {
							contener.attr("contenteditable","false");
							Value = contener.text().trim();
							UpdateValNow(Value);
						}
					});
					contener.focusout(function(){
						contener.attr("contenteditable","false");
						Value = contener.text().trim();
						UpdateValNow(Value);
					});
				return false();
			}else if(UpdateType == "selectMenu"){
				var OldVal = contener.children("t").text();
				contener.children("t").hide();
				contener.children("select").show();

					contener.children("select").change(function(){
						var Value = $(this).find('option:selected').val();
						var Text  = $(this).find('option:selected').text();
						contener.children("t").html(Text).show();
						UpdateValNow(Value);
						return false();
					});
					
					$(document).mouseup(function(e){
						if (!contener.is(e.target) && contener.has(e.target).length === 0) {
							contener.children("t").show();
							contener.children("select").hide();
						}
					});
			}
			
			
		}
	}(jQuery));
	//// ->>>>>>>>>>>>>>>>>>>>>> Instent Updatble Plugin <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<- ///