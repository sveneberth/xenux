$(document).ready(function() {
	// category accordion
	$("menu.main-menu-left").accordion({
		icons: false,
		active: default_active_menu_left,
		autoHeight: false,
		heightStyle: "content" 
	});
	$("menu.main-menu-left h2").dblclick(function() {
		$(this).next().find('>li:first-child>a')[0].click();
	});
});


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