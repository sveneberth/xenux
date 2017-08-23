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
				<select name="action" class="select action-select">
					<option value="-1"><?= __('choose action') ?></option>
					<option value="remove"><?= __('remove') ?></option>
				</select>
				<input type="submit" class="btn action-btn" value="<?= __('apply action') ?>">
			</div>

			<table class="data-table">
				<thead>
					<tr class="table-head">
						<th class="column-select"><input type="checkbox" class="select-all-items"></th>
						<th class="column-id"><?= __('ID') ?></th>
						<th class="column-text headerSortDown"><?= __('username') ?></th>
						<th class="column-text"><?= __('firstname') ?></th>
						<th class="column-text "><?= __('lastname') ?></th>
						<th class="column-actions"></th>
					</tr>
				</thead>
				<tbody>
					{{users}}
				</tbody>
			</table>

			<p class="amount-entries">{{amount}} <?= __('entries') ?></p>
		</form>
	</section>
</div>
