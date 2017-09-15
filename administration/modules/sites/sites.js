$(function() {
	$(".menu_order > ul").nestedSortable({
		listType: 'ul',
		forcePlaceholderSize: true,
		handle: 'div',
		helper:	'clone',
		items: 'li:not(.ignore)',
		opacity: .6,
		placeholder: 'placeholder',
		revert: 250,
		tabSize: 25,
		tolerance: 'pointer',
		toleranceElement: '> div',
		maxLevels: 3,

		isTree: true,
		expandOnHover: 700,
		startCollapsed: true,

		collapsedClass: 'collapsed',
		errorClass: 'error',
		expandedClass: 'expanded',

		stop: function() {
			console.log("stopped");
			updateOrder();
			toggleClasses();
		}
	});

	toggleClasses();

	$('.disclose:not(.disable)').bind('click', function() {
		console.log('toggle collapsed/expanded');
		$(this).closest('li').toggleClass('collapsed').toggleClass('expanded');
	});

	$('.menu_order .remove').bind('click', function() {
		if (
			confirm('<?= __('are you sure to delete this file?') ?>')
		) {
			var id = $(this).parent().parent().attr('id');
			id     = id.replace(/[^0-9]/g,'');
			console.log(id);
			remove(id);
		}
	});
});
function toggleClasses() {
	// #FIXME: works not smoothly
	console.log('toggleClasses');
	$('li').each(function(index) {
		if ($('>ul',this).length == 0 || $('>ul',this).is(':empty'))
		{
			$('div > .disclose',this).addClass('disable');
			$(this).removeClass('collapsed').removeClass('expanded');
		}
		else
		{
			$('div > .disclose',this).removeClass('disable');
			$(this).addClass('collapsed').addClass('expanded');
		}
	});
}
function updateOrder() {
	var array = $('.menu_order > ul').nestedSortable('toArray', {startDepthCount: 0});
	console.log(array);

	$.ajax({
		url: '{{ADMIN_URL}}/modules/sites/ajax.php',
		type: 'POST',
		dataType: 'json',
		data: {
			task: 'site_edit_update_order',
			items: array,
		},
		success: function(response) {
			console.log(response);
		},
		error: function(xhr, textStatus, errorThrown){
			console.log("request failed: %o ", textStatus,xhr,errorThrown);
		}
	});
}
function remove(id) {
	$.ajax({
		url: '{{ADMIN_URL}}/modules/sites/ajax.php',
		type: 'POST',
		dataType: 'json',
		data: {
			task: 'site_edit_remove',
			item_id: id,
		},
		success: function(response) {
			console.log(response);
			if (response.success == true) {
				$('#list_' + id).fadeOut(300, function() {
					$(this).remove();
				});
			}
		},
		error: function(xhr, textStatus, errorThrown){
			console.log("request failed: %o ", textStatus,xhr,errorThrown);
		}
	});
}
