<script>
	var url = window.location.href;
	var title = document.title;
	var newUrl = url.substring(0, url.indexOf('?')) + window.location.hash;
	// replace new url
	if(window.history.replaceState) {
		window.history.replaceState(null, null, newUrl);
	}
</script>
<script src="{{TEMPLATE_PATH}}/js/jquery.mjs.nestedSortable.js"></script>
<script>
	$(document).ready(function() {
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
				confirm('Bist du dir sicher diese Seite zu löschen? Das löschen kann nicht rückgängig gemacht werden!')
			) {
				var	id = $(this).parent().parent().attr('id');
				id = id.replace(/[^0-9]/g,'');
				console.log(id);
				remove(id);
			}
		});
	});
	function toggleClasses() {
		//FIXME: works not smoothly
		console.log('toggleClasses');
		$('li').each(function(index) {
			if($('>ul',this).length == 0 || $('>ul',this).is(':empty'))
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
			url: '{{URL_ADMIN}}/modules/sites/ajax.php',
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
			url: '{{URL_ADMIN}}/modules/sites/ajax.php',
			type: 'POST',
			dataType: 'json',
			data: {
				task: 'site_edit_remove',
				item_id: id,
			},
			success: function(response) {
				console.log(response);
				if(response.success == true) {
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
</script>
<style>
	.menu_order > ul,
	.menu_order > ul ul {
		margin: 0 0 0 25px;
		padding: 0;
		list-style-type: none;
	}
	.menu_order > ul {
		margin: 1em 0;
	}
	.menu_order > ul li {
		margin: 5px 0 0 0;
		padding: 0;
	}
	.menu_order ul > li {
		padding: 7px 10px;
		background: #fff;
		margin: 10px 0;
		border: 1px solid #777;
		cursor: move;
	}
	.menu_order   ul > li.non-public {opacity: .7;}		
	.menu_order   ul > li.ignore {cursor: default;}
	.menu_order   ul > li:nth-child(odd) {background: #fff;}
	.menu_order   ul > li:nth-child(even) {background: #eee;}
	.menu_order > ul > li 					ul > li {padding: 5px;}
	.menu_order > ul > li 					ul > li {padding-left: 20px; margin: 5px 0;}
	.menu_order > ul > li:nth-child(odd)	ul > li {background: #e5e5e5;}
	.menu_order > ul > li:nth-child(even)	ul > li {background: #fff;}
	.menu_order > ul > li >					ul > li ul {border-left: 1px dotted #555;}
	.menu_order > ul > li >					ul > li ul > li {border: 0;}
	.menu_order > ul > li >					ul > li ul > li {padding-bottom: 0;padding-right: 0;}

	.menu_order > ul li.collapsed > ul {display: none;}
	.menu_order > ul li > div {position: relative;}

	.menu_order > ul li > div > .disclose {
		cursor: pointer;
		display: inline-block;
		width: 20px;
		height: 20px;
		font-size: 1.2em;
		line-height: 25px;
	}
	.menu_order > ul li							> div > .disclose.disable {cursor: inherit;}
	.menu_order > ul li:not(.ignore)			> div > .disclose:before {content: '-';}
	.menu_order > ul li:not(.ignore).collapsed	> div > .disclose:before {content: '+';}
	.menu_order > ul li							> div > .disclose.disable:before {content: ''!important;}

	.placeholder {outline: 1px dashed #4183C4;}
	li.error {background: #fbe3e4!important;border-color: transparent!important;}

	
	.menu_order ul > li a {
		display: inline-block;
		line-height: 25px;
	}
	.menu_order ul > li a.edit {
		font-weight: 600;
	}
	.menu_order ul > li a.show {
		font-weight: 500;
		right: 40px;
		position: absolute;
	}
	.menu_order ul > li .remove-icon {
		background-image: url('{{TEMPLATE_PATH}}/images/remove.png');
		background-size: 100%;
		background-repeat: no-repeat;
		background-position: center;
		display: block;
		vertical-align: baseline;
		height: 25px;
		width: 25px;
		display: inline-block;
		vertical-align: top;
		margin: 0;
		line-height: 25px;
		right: 0;
		position: absolute;
	}
</style>

{{messages}}

<section class="box-shadow floating one-column-box no-margin">
	<p><?= __('by dragging the pages you can change the menu order') ?></p>
	<p><?= __('note that in an unpublished site, respective undersites are not listed in the menu') ?></p>

	<div class="menu_order">
		<ul>
			{{menu}}
		</ul>
	</div>
</section>