/**
* Menu
*/
function openmobilemenu() {
	$('html,body').animate({
		scrollTop: 0
	}, 500);
	$(".topmenu.mainmenu").toggle("fast");
	$(".topmenu.mobilemenu").fadeToggle("fast");
	$(".logo").fadeToggle("fast");
}

$(window).scroll(function() { // scroll -> move menu
    $('.mainmenu').css('top', 50 - $(this).scrollTop());
});

$(window).resize(function() { // resize window
	if($(window).width() > 600) { // normal view
		$(".topmenu.mobilemenu, .transparent").hide();
		$(".logo").show();
		$(".topmenu.mainmenu, tr.head").show();
		$(".topmenu.mainmenu li span:not(.sb-icon-search)").remove();
		$(".topmenu.mainmenu").css('min-height', '');
	} else { // responsive view
		$(".topmenu.mobilemenu").show();
		$(".topmenu.mainmenu").hide();
		
		$(".topmenu.mainmenu").css('min-height', $(document).height() - 50);
		
		if($(".topmenu.mainmenu li a span").length == 0) { 
			$(".topmenu.mainmenu li").has("ul").children("a").append("<span></span>");
		}
	}
});

$(document).ready(function () { // after DOM load
	if($(window).width() <= 600) {
		$(".topmenu.mainmenu li").has("ul").children("a").append("<span></span>");
		$(".topmenu.mainmenu").css('min-height', $(document).height() - 50);
	}
	
	$(".topmenu.mainmenu li span").live('click', function(e) {
		$(this).parent().parent().children('ul').slideToggle('fast');
		e.preventDefault();
		return false;
	})
});



/*
* FontSize
*/
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
	$('.fontsize .increase').click(fontsizerecrease);
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


/**
* Scroll to Top
*/
$(window).scroll(function () {
	var top = $('#top').offset().top;
	var scroll_top = $(window).scrollTop();
	if(scroll_top > top) {
		$('.toTop').fadeIn();
	} else {
		$('.toTop').fadeOut();
	}
});



/**
* open fancybox for images in main
*/
var gallery = [];
var pictureTranslation = XENUX.translation.pictureXofY;
$(document).ready(function ($) {
	var numImages = $("main img")
					.filter(function() {
						return ($(this).parent().prop("tagName") != 'A');
					})
					.length;
	$("main img").each(function (i) {
		if($(this).parent().prop("tagName") == 'A') return true;
		gallery[i] = {
			href: ($(this).hasClass('cloud-image') && !empty($(this).attr("data-src"))) ? $(this).attr("data-src") : $(this).attr("src"),
			title: pictureTranslation.replace('x', i+1).replace('y', numImages)
		};
		$(this).bind("click", function () {
			$.fancybox(gallery, {
				type: "image",
				padding: 10,
				index: i,
				cyclic: true
			});
			return false;
		}).css('cursor', 'pointer');
	});
});


/**
* calendar equal hight
*/
$(document).ready(function(){
	$('.calendar ul.calendar_dates li.week-line').each(function(){  
		var highestBox = 0;

		$('li.calendar_day', this).each(function() {
			var height = $(this).outerHeight();
			if(height > highestBox)
				highestBox = height; 
		});

		$('li.calendar_day',this).css('height',highestBox);
	});
});


/**
* language selector
*/
$(document).ready(function() {
	$.widget( "custom.iconselectmenu", $.ui.selectmenu, {
		_renderItem: function(ul, item) {
			var li = $("<li>", {
				text: item.label,
				"class": item.element.attr("data-option-class")
			});

			if(item.disabled) {
				li.addClass( "ui-state-disabled" );
			}

			$("<span>", {
				style: item.element.attr("data-style"),
				"class": "ui-icon " + item.element.attr("data-class")
			})
			.appendTo(li);

			return li.appendTo(ul);
		}
	});
});