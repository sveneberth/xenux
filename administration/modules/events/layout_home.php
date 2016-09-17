<script>
	$(function() {
		$( '.data-table' ).tablesorter({
			headers: {
				0: {sorter: false},
				6: {sorter: false}
			}
		});
	})
</script>

{{messages}}

<div class="grid-row">
	<section class="box-shadow grid-col">
		<form method="get">
			<div class="actionbar clearfix">
				<select name="action" class="action-select">
					<option value="-1">Aktion wählen</option>
					<option value="private">privat setzen</option>
					<option value="public">öffentlich zugänglich machen</option>
					<option value="remove">löschen</option>
				</select>
				<input type="submit" class="action-btn" value="Übernehmen">
			</div>

			<table class="data-table">
				<thead>
					<tr class="table-head">
						<th class="column-select"><input type="checkbox" class="select-all-items"></th>
						<th class="column-id"><?= __('ID') ?></th>
						<th class="column-title"><?= __('title') ?></th>
						<th class="column-date"><?= __('createDate') ?></th>
						<th class="column-date headerSortUp"><?= __('startDate') ?></th>
						<th class="column-date"><?= __('endDate') ?></th>
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
