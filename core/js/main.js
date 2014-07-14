//---Popup---------------------------------------------------------------------
$(document).ready(function() {
	$("body").append("<div id=\"transparent\"></div>");
})
function popupopen() {
	$( "#popup").show();
	$( "#transparent").show();
};
function popupclose(field1, field2) {
	$( "#popup" ).hide();
	$( "#transparent" ).hide();
	$( "#field1" ).val( field1 )
	$( "#field2" ).val( field2 )
};
function popupclosewithoutcontent() {
	$( "#popup" ).hide();
	$( "#transparent" ).hide();
}

//---Menu----------------------------------------------------------------------
function openmobilemenu() {
	$('html,body').animate({
		scrollTop: 0
	}, 500);
	$( ".mainmenu" ).toggle("fast");
	if($("#transparent").is(":visible")) {
		$( "#transparent" ).fadeOut("fast");
	} else {
		$( "#transparent" ).fadeIn("fast");
	}
}
function openmenupoints(name) {
	console.log("toggle mobilemenu point "+name);
	if($("ul#"+name).is(':visible')) {
		if($(".openpoints."+name).attr('src') != "../core/images/down.png") {
			$(".openpoints."+name).attr("src","core/images/right.png");
		} else {
			$(".openpoints."+name).attr("src","../core/images/right.png");
		}
	} else {
		if($(".openpoints."+name).attr('src') != "../core/images/right.png") {
			$(".openpoints."+name).attr("src","core/images/down.png");
		} else {
			$(".openpoints."+name).attr("src","../core/images/down.png");
		}
	}
	$( "#"+name ).slideToggle("fast");
}
$(window).scroll(function(){
    $('.mainmenu').css('top', 60 - $(this).scrollTop());
});
$(window).resize(function () {
	if($( window ).width() > 600) {
		$( "#mobilemenu" ).hide();
		$( "#transparent" ).hide();
		$("tr.head").show();
	}
});

//---Messagebox----------------------------------------------------------------
function messagebox(width, height, topic, text) {
	$("body").append("<div class=\"transparent\"></div>");
	$("body").append("<div class=\"message\"></div>");
	$(".message").append("<a id=\"closemessage\" href=\"javascript:void(0)\">&times;</a>");
	$(".message").append("<h3>"+topic+"</h3>");
	$(".message").append("<div class=\"content\">"+text+"</h3>");
	$(".message").css("height", height+"%");
	$(".message").css("width", width+"%");
	$(".message").css("top", ((100-height)/2-10)+"%");
	$(".message").css("left", ((100-width)/2)+"%");
	$( ".message" ).draggable();
	$("#closemessage").click(function() {
		$(".transparent").remove();
		$(".message").remove();
	})
}

//---FontSize------------------------------------------------------------------
$(document).ready(function() {
	if(typeof $.cookie("fontsize") == "undefined") {
		$.cookie("fontsize", 16);
	}
	var actfontsize = $.cookie("fontsize");
	var actfontsize = parseInt(actfontsize.replace(/[^0-9]/g, ''));
	$("body").css("font-size", actfontsize+"px")
})
function fontsizerecrease() {
	var actfontsize = $.cookie("fontsize");
	var actfontsize = parseInt(actfontsize.replace(/[^0-9]/g, ''));
	var newfontsize = actfontsize + 2;
	if(newfontsize <= 24) {
		$("body").css("font-size", newfontsize+"px");
		$.cookie("fontsize", newfontsize);
	}
}
function fontsizereset() {
	$("body").css("font-size", "16px");
	$.cookie("fontsize", 16);
}
function fontsizedecrease() {
	var actfontsize = $.cookie("fontsize");
	var actfontsize = parseInt(actfontsize.replace(/[^0-9]/g, ''));
	var newfontsize = actfontsize - 2;
	if(newfontsize >= 10) {
		$("body").css("font-size", newfontsize+"px");
		$.cookie("fontsize", newfontsize);
	}
}