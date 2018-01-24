var baseurl;
var isModified = false;

$(function() {
	baseurl = $( '[rel="baseurl"]' ).prop('href');

	// category accordion
	$( 'menu.main-menu-left > ul > li > a' ).click(function(e) {
		if (false == $( this ).next().is(':visible')) {
			$( 'menu.main-menu-left > ul ul' ).slideUp(300);

			$( 'menu.main-menu-left > ul > li' ).removeClass('open');
			$( this ).parent().addClass('open');
		}
		$( this ).next().slideToggle(300);
	});
	$( 'menu.main-menu-left > ul > li.active ul' ).show();

	// data table selector
	$( '.select-all-items' ).on('click', function() {
		if ($( this ).prop('checked')) {
			$( 'td.column-select > input' ).prop('checked', 'checked');
		} else {
			$( 'td.column-select > input' ).prop('checked', false);
		}
	})
	$( 'td.column-select > input' ).on('click', function() {
		$( '.select-all-items' ).prop('checked', false);
	})

	// hint lose data
	$(window).on('beforeunload', function() {
		if (isModified)
			return 'Alle nicht gespeicherte Daten gehen verloren!';
	});
	$(document).on("submit", "form", function(){
		$(window).off('beforeunload');
	});

	$('form input').on('change', function(e) {
		isModified = true;
	});
});


// replace get params in browser's url bar
var url = window.location.href;
var newUrl = url.substring(0, url.indexOf('?')) + window.location.hash;
// replace new url
if (window.history.replaceState) {
	window.history.replaceState(null, null, newUrl);
}


function notifyMe(title, text, click) {
	if (!Notification) {
		console.error('Notification are not supported in this browser');
		return false;
	}

	if (Notification.permission !== "granted")
		Notification.requestPermission();

	var notification = new Notification(title, {
		icon: $( 'link[rel="shortcut icon"]' ).prop('href'),
		body: text,
	});

	notification.onclick = click;
}
