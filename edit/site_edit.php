<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");

if(isset($get->token)) {
	switch($get->token) {
		case "edit_site":
			// read
			if(isset($get->site_id) && is_numeric(@$get->site_id)) {
				$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '$get->site_id' LIMIT 1;");
				if($result->num_rows != 0)
					$edit_site = $result->fetch_object();
				if(in_array($edit_site->site, $special_sites) && !contains($edit_site->site, 'imprint',
	'contact')) {
					request_failed();
					return false;
				}
			};
			
			if(isset($_POST['editor']) && !empty($post->title)) {
				$post->title = preg_replace("/[^a-zA-Z0-9_üÜäÄöÖ&#,.()[]{}*\/]/" , "" , $post->title);
				
				if(@$_GET['new'] == 1) {
					// new
					$db->query("INSERT INTO XENUX_sites(text, title) VALUES ('$post->text', '$post->title');");
					
					$result = $db->query("SELECT * FROM XENUX_sites WHERE title = '$post->title' ORDER by id DESC LIMIT 1;");
					$edit_site = $result->fetch_object();
					foreach($_POST as $key => $val) {
						if(strpos($key, 'contact_') !== false) {
							$contactperson_id = substr($key, 8);
							$db->query("INSERT INTO XENUX_site_contactperson(site_id, contactperson_id) VALUES('$edit_site->id', '$contactperson_id');");
						}
					}
				} else {
					// update
					$db->query("DELETE FROM XENUX_site_contactperson WHERE site_id = $edit_site->id;");
					foreach($_POST as $key => $val) {
						if(strpos($key, 'contact_') !== false) {
							$contactperson_id = substr($key, 8);
							$db->query("INSERT INTO XENUX_site_contactperson(site_id, contactperson_id) VALUES('$edit_site->id', '$contactperson_id');");
						}
					}
					$db->query("UPDATE XENUX_sites SET text = '$post->text', title = '$post->title' WHERE id = '$edit_site->id';");
				}
				
				if($edit_site->site == ''/* in_array($special_sites) || 'home'*/) {
					$link = "../?site=page&page_id=$edit_site->id";
				} else {
					$link = "../?site=$edit_site->site";
				}
				
				if(isset($_GET['gotosite'])) {
					header("location: $link");
				} else {
					echo $page_output;
				}
				
				echo "<p>Seite wurde gespeichert.</p>";
				echo "<p><a href=\"$link\">Zur Seite $edit_site->title</a></p>";
			
			} else {
			
				// edit
				echo $page_output;
				?>
				<script src="../wysiwyg/plugins/simple-image-browser/plugin.js"></script>
				<script>
				$(document).ready(function() {
					 $(window).on('beforeunload', function(){
						return 'Alle nicht gespeicherte Daten gehen verloren!';
					});
					$(document).on("submit", "form", function(){
						$(window).off('beforeunload');
					});
					
					CKEDITOR.config.extraPlugins = 'simple-image-browser';
					CKEDITOR.config.simpleImageBrowserURL = '../ajax/images.php';

					var editor = CKEDITOR.replace('text', {
						toolbar: [
							{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
							{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
							{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
							'/',
							{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
							{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
							{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
							'/',
							{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
							{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
							{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
							{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
							{ name: 'Simple Image Browser', items: [ 'Simple Image Browser' ] },
						],
						extraAllowedContent: {
							img: {
								attributes: [ '!src', 'alt', 'width', 'height' , 'data-*' ],
								classes: { tip: true }
							},
						},
					});
					editor.on( 'instanceReady', function() {
						console.log( editor.filter.allowedContent );
					} );
				});
				</script>
				<form action="" method="post" name="form">
					<input <?php if(empty($post->title) && isset($post->title))echo 'class="wrong"'; ?> type="text" placeholder="Seitenname" name="title" value="<?php echo @$edit_site->title; ?>">
					<textarea class="ckeditor nolabel" placeholder="Seiteninhalt" name="text" id="text"><?php echo @$edit_site->text ?></textarea>
					
					<div class="contact-persons">
						<h3>Ansprechpartner</h3>
						<?php
						$result = $db->query("SELECT * FROM XENUX_contactpersons ORDER BY name ASC;");
						while($row = $result->fetch_object()) {
							echo "<input ";
							if(isset($edit_site)) {
								$InnerResult = $db->query("SELECT * FROM XENUX_site_contactperson WHERE site_id = $edit_site->id AND contactperson_id = $row->id;");
								if($InnerResult->num_rows >= 1) echo 'checked';
							}
							echo " type=\"checkbox\" id=\"contact_$row->id\" name=\"contact_$row->id\" value=\"true\"><label for=\"contact_$row->id\">$row->name</label>";
						}
						?>
					</div>
					
					<input type="hidden" name="editor" value="editor" />
					<input type="submit" value="Seite speichern">
				</form>
				<?php
				return false;
			}
			break;
	}
}
?>

<script>
		$(document).ready(function() {
			$( ".menu_order > ul " ).nestedSortable({
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
					console.log("stoped");
					updateOrder();
				}
			});
			
			$('.disclose').bind('click', function() {
				$(this).closest('li').toggleClass('collapsed').toggleClass('expanded');
			});
			
			$('.menu_order .remove').bind('click', function() {
				var	id = $(this).parent().parent().attr('id');
					id = id.replace(/[^0-9]/g,'');
					console.log(id);
					remove(id);
			});
		});
		function updateOrder() {		
			var array = $('.menu_order > ul').nestedSortable('toArray', {startDepthCount: 0});
			console.log(array);
			
			$.ajax({
				url: '../ajax/edit.php',
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
					console.log('request failed: '+textStatus+xhr+errorThrown);
				}
			});
		}
		function remove(id) {
			$.ajax({
				url: '../ajax/edit.php',
				type: 'POST',
				dataType: 'json',
				data: {
					task: 'site_edit_remove',
					item_id: id,
				},
				success: function(response) {
					console.log(response);
					if(response.success == true) {
						location.reload();
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
			padding: 15px 10px;
			background: #fff;
			margin: 10px 0;
			border: 1px solid #777;
			cursor: move;
		}
		.menu_order ul > li.ignore {cursor: default;}
		.menu_order ul > li:nth-child(odd) {background: #fff;}
		.menu_order ul > li:nth-child(even) {background: #eee;}
		.menu_order > ul > li ul > li {padding: 5px;}
		.menu_order > ul > li ul > li {background: #e5e5e5 !important; padding-left: 20px; margin: 5px 0;}
		.menu_order > ul > li > ul > li ul {border-left: 1px dotted #555;}
		.menu_order > ul > li > ul > li ul > li {border: 0;}
		.menu_order > ul > li > ul > li ul > li {padding-bottom: 0;}

		.menu_order > ul li.collapsed > ul {
			display: none;
		}

		.menu_order > ul li > div > .disclose {
			cursor: pointer;
			display: inline-block;
			width: 20px;
			height: 20px;
			font-size: 1.2em;
			line-height: 1em;
		}
		.menu_order > ul li:not(.ignore)			> div > .disclose:before {content: '-';}
		.menu_order > ul li:not(.ignore).collapsed	> div > .disclose:before {content: '+';}

		.placeholder {outline: 1px dashed #4183C4;}
		.menu_order > ul li.error {background: #fbe3e4!important;border-color: transparent!important;}

		.menu_order > ul li div .remove-icon {
			height: 1.5rem;
			width: 1.5rem;
			display: inline-block;
			vertical-align: top;
			margin: 0;
			line-height: 1.5rem;
		}
	</style>
	<div class="menu_order">
		<ul><!-- menu -->
		
<?php
				$not_sortable = array
				(
					'imprint',
					'home',
					'contact',
				);
				$menu_order = "position_left ASC";
				
				$result1 = $db->query("SELECT * FROM XENUX_sites WHERE parent_id = 0 ORDER BY $menu_order;");
				while($rank1 = $result1->fetch_object()) {
					if(contains($rank1->site, 'news_list', 'news_view', 'event_list', 'event_view', 'error', 'page', 'search'))
						continue;
					
					echo "	<li ".((in_array($rank1->site, $not_sortable))?'class="ignore"':'')." id=\"list_$rank1->id\">
								<div>
									<span class=\"disclose\"></span>
									<a href=\"?site=$site&token=edit_site&site_id=$rank1->id&backbtn\" title=\"Klicken, um die Seite zu bearbeiten\">$rank1->title</a>
									 ".((!in_array($rank1->site, $not_sortable))?"<span style=\"margin-right: 10px;\" title=\"entfernen\" class=\"remove remove-icon clickable\"></span>":"")."
								</div>
								<ul>";
					
					$result2 = $db->query("SELECT * FROM XENUX_sites WHERE parent_id = $rank1->id ORDER BY $menu_order;");
					while($rank2 = $result2->fetch_object()) {
						echo "	<li id=\"list_$rank2->id\">
									<div>
										<span class=\"disclose\"></span>
										<a href=\"?site=$site&token=edit_site&site_id=$rank2->id&backbtn\" title=\"Klicken, um die Seite zu bearbeiten\">$rank2->title</a>
										<span style=\"margin-right: 5px;\" title=\"entfernen\" class=\"remove remove-icon clickable\"></span>
									</div>
									<ul>";
						
						$result3 = $db->query("SELECT * FROM XENUX_sites WHERE parent_id = $rank2->id ORDER BY $menu_order;");
						while($rank3 = $result3->fetch_object()) {
							echo "	<li id=\"list_$rank3->id\">
										<div>
											<span class=\"disclose\"></span>
											<a href=\"?site=$site&token=edit_site&site_id=$rank3->id&backbtn\" title=\"Klicken, um die Seite zu bearbeiten\">$rank3->title</a>
											<span title=\"entfernen\" class=\"remove remove-icon clickable\"></span>
										</div>
									</li>";
						}
						
						echo "		</ul>
								</li>";
					}
					
					echo "		</ul >
							</li>";
				}
?>

		</ul><!-- /menu -->
	</div>
<a class="btn" href="./?site=site_edit&token=edit_site&new=1&backbtn">neue Seite</a>