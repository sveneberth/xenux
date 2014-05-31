function popupopen() {
	$( "#popup").show();
	$( "#field").show();
	console.log('opened Popup');
};
function popupclose(field1, field2) {
	$( "#popup" ).hide();
	$( "#transparent" ).hide();
	$( "#field1" ).val( field1 )
	$( "#field2" ).val( field2 )
	console.log('closed Popup with content');
};
function popupclosewithoutcontent() {
	$( "#popup" ).hide();
	$( "#transparent" ).hide();
	console.log('closed Popup without content');
}

// menu
function openmobilemenu() {
	$('html,body').animate({
		scrollTop: 0
	}, 500);
	$( ".mainmenu" ).toggle("fast");
	console.log("toggle mobilemenu");
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
$(window).resize(function () {
	if($( window ).width() > 600) {
		$( "#mobilemenu" ).hide();
	}
});
