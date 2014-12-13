//---Popup---------------------------------------------------------------------
$(document).ready(function() {
	$("body").append("<div class=\"transparent\"></div>");
})
function popupopen() {
	$( ".popup").show();
	$( ".transparent").show();
};
function popupclose(field1, field2) {
	$( ".popup" ).hide();
	$( ".transparent" ).hide();
	$( ".field1" ).val( field1 )
	$( ".field2" ).val( field2 )
};
function popupclosewithoutcontent() {
	$( ".popup" ).hide();
	$( ".transparent" ).hide();
}



//---Menu----------------------------------------------------------------------
function openmobilemenu() {
	$('html,body').animate({
		scrollTop: 0
	}, 500);
	$(".topmenu.mainmenu").toggle("fast");
	$(".topmenu.mobilemenu").fadeToggle("fast");
	$(".logo").fadeToggle("fast");
//	$(".transparent").fadeToggle("fast");
}

$(window).scroll(function() { // scroll -> move menu
    $('.mainmenu').css('top', 50 - $(this).scrollTop());
});

$(window).resize(function () { // resize window
	if($(window).width() > 600) { // normal view
		$(".topmenu.mobilemenu, .transparent").hide();
		$(".logo").show();
		$(".topmenu.mainmenu, tr.head").show();
		$(".topmenu.mainmenu li span:not(.sb-icon-search)").remove();
		$(".topmenu.mainmenu").css('min-height', '');
	} else { // responsive view
		$(".topmenu.mobilemenu").show();
		$(".topmenu.mainmenu").hide();
		
		$(".topmenu.mainmenu").css('min-height', $(window).height() - 50);
		
		if($(".topmenu.mainmenu li a span").length == 0) { 
			$(".topmenu.mainmenu li").has("ul").children("a").append("<span></span>");
		}
	}
});

$(document).ready(function () { // after DOM load
	if($(window).width() <= 600) {
		$(".topmenu.mainmenu li").has("ul").children("a").append("<span></span>");
		$(".topmenu.mainmenu").css('min-height', $(window).height() - 50);
	}
	
	$(".topmenu.mainmenu li span").live('click', function(e) {
		$(this).parent().parent().children('ul').slideToggle('fast');
		e.preventDefault();
		return false;
	})
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
	$("body").css("font-size", actfontsize+"px");
	
	// click events
	$('.fontsize .decrease').click(fontsizedecrease);
	$('.fontsize .reset')	.click(fontsizereset);
	$('.fontsize .recrease').click(fontsizerecrease);
});

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



//---add label before input----------------------------------------------------
$(document).ready(function() {
	var i = 1;
	$('input[type="text"], input[type="email"], input[type="number"], input[type="password"], input[type="color"], textarea').each(function() {
		if(!$(this).hasClass('nolabel')) {
			if(typeof $(this).attr('id') !== 'undefined') {
				var id = $(this).attr('id');
			} else {
				$(this).attr('id', 'input'+i);
				var id = $(this).attr('id');
				i++;	
			}
			var placeholder = $(this).attr('placeholder');
			$(this).before('<label for="'+id+'">'+placeholder+'</label>');
		}
	})
})



//--- Scroll to Top -----------------------------------------------------------
$(window).scroll(function () {
	var top = $('#top').offset().top;
	var scroll_top = $(window).scrollTop();
	if(scroll_top > top) {
		$('.toTop').fadeIn();
	} else {
		$('.toTop').fadeOut();
	}
});