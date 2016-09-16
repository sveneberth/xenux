<script>
/* #FIXME: DEBUG
	var url = window.location.href;
	var title = document.title;
	var newUrl = url.substring(0, url.indexOf('?')) + window.location.hash;
	// replace new url
	if(window.history.replaceState) {
		window.history.replaceState(null, null, newUrl);
	}
	*/
	$(function() {
		// #TODO: build table sorting with php, the tablesorter sucks
		$( '.data-table' ).tablesorter({
			headers: {
				0: {
					sorter: false
				}
			}
		});

		$( '.select-all-items' ).on('click', function() {
			console.debug('select all');
			$( '.column-select > input' ).trigger('click');
		})
	})
</script>

{{messages}}

<div class="grid-row">
	<section class="box-shadow grid-col">

		<form method="get">
			{# #TODO: build actions #}
			<div class="action">
				<select name="action">
					<option value="-1">Aktion wählen</option>
					<option value="private">privat setzen</option>
					<option value="public">öffentlich zugänglich machen</option>
					<option value="remove">löschen</option>
				</select>
				<input type="submit" class="button action" value="Übernehmen">
			</div>

			<table class="data-table">
				<thead>
					<tr class="table-head">
						<th class="column-select"><input type="checkbox" class="select-all-items"></th>
						<th class="column-id"><?= __('ID') ?></th>
						<th class="column-title"><?= __('title') ?></th>
						<th class="column-create-date"><?= __('createDate') ?></th>
						<th class="column-start-date"><?= __('startDate') ?></th>
						<th class="column-end-date"><?= __('endDate') ?></th>
						<th class="column-actions"></th>
					</tr>
				</thead>
				<tbody>
					{{events}}
				</tbody>
			</table>

			<p class="amount-entries">{{amount}} <?= __('entries') ?></p>
		</form>
	</section>
</div>
