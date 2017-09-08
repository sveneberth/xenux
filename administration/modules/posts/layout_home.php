<script>
	$(function() {
		$( '.data-table' ).tablesorter({
			headers: {
				0: {sorter: false},
				4: {sorter: false},
				5: {sorter: false}
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
					<option value="draft"><?= __('safe as draft') ?></option>
					<option value="publish"><?= __('publish') ?></option>
					<option value="trash"><?= __('remove') ?></option>
				</select>
				<input name="apply-action" type="submit" class="btn action-btn" value="<?= __('apply action') ?>">

				<select name="filter" class="select filter-select">
					<option value="publish" <?php if(@$_GET['filter'] == 'publish') echo 'selected'; ?>><?= __('public posts') ?> ({{amountPublish}})</option>
					<option value="draft" <?php if(@$_GET['filter'] == 'draft') echo 'selected'; ?>><?= __('drafts') ?> ({{amountDraft}})</option>
					<option value="trash" <?php if(@$_GET['filter'] == 'trash') echo 'selected'; ?>><?= __('trash') ?> ({{amountTrash}})</option>
				</select>
				<input name="apply-filter" type="submit" class="btn filter-btn" value="<?= __('apply filter') ?>">
			</div>

			<table class="data-table">
				<thead>
					<tr class="table-head">
						<th class="column-select"><input type="checkbox" class="select-all-items"></th>
						<th class="column-id"><?= __('ID') ?></th>
						<th class="column-title headerSortUp"><?= __('title') ?></th>
						<th class="column-date"><?= __('createDate') ?></th>
						<th class="column-author"><?= __('author') ?></th>
						<th class="column-actions"></th>
					</tr>
				</thead>
				<tbody>
					{{posts}}
				</tbody>
			</table>

			<p class="amount-entries">{{amount}} <?= __('entries') ?></p>
		</form>
	</section>
</div>
