$(document).ready(function() {
	// category accordion
	$("menu.main-menu-left > ul > li > a").click(function(e) {
	//	e.preventDefault();

		if(false == $(this).next().is(':visible')) {
			$('menu.main-menu-left > ul ul').slideUp(300);

			$("menu.main-menu-left > ul > li").removeClass('open');
			$(this).parent().addClass('open');
		}
		$(this).next().slideToggle(300);
	});
	$('menu.main-menu-left > ul > li.active ul').show();

});

var baseurl = $('[rel=baseurl]').attr('href');

function notifyMe(title, text, click) {
	if(!Notification) {
		console.error('Notification are not supported in this browser');
		return false;
	}

	if (Notification.permission !== "granted")
		Notification.requestPermission();

	var notification = new Notification(title, {
	icon: $('link[rel="shortcut icon"]').attr('href'),
	body: text,
	});

	notification.onclick = click;
}