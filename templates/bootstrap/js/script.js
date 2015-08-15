/**
* open fancybox for images in .container
*/
var gallery = [];
var pictureTranslation = XENUX.translation.pictureXofY;
$(document).ready(function ($) {
	var numImages = $(".container img")
					.filter(function() {
						return ($(this).parent().prop("tagName") != 'A');
					})
					.length;
	$(".container img").each(function (i) {
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
$(function() {
	$(".language-selector")
	.iconselectmenu({
		change: function( event, data ) {
			window.location.href = XENUX.path.sitepath+'?lang=' + $(this).val();
		}
	})
	.iconselectmenu( "menuWidget")
	.addClass('language-selector-menu');
})