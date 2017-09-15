<?php
// include Xenux-Loader
include_once(dirname(dirname(dirname(__DIR__))) . '/core/xenux-load.php');

if (!DEBUG_MODE)
	error_reporting(0);

if (!$app->user->isLogin())
	ErrorPage::view(404);

$return = array();

if (!isset($_REQUEST['task']) || empty(@$_REQUEST['task']))
{
	ErrorPage::view(405);
}

switch ($_REQUEST['task'])
{
	case 'site_edit_update_order':
		foreach ($_REQUEST['items'] as $key => $val) {
			if ($key == 0)
				continue;

			$item_id   = $val['item_id'];
			$parent_id = $val['parent_id'];
			$sortindex = $val['left'];

			$return['success'] = $XenuxDB->Update('sites', [
				'parent_id' => $parent_id,
				'sortindex' => $sortindex
			],
			[
				'id' => $item_id
			]) !== false;
		}
		break;
	case 'site_edit_remove':
		$item_id = $_REQUEST['item_id'];

		$return['success'] = $XenuxDB->Update('sites', [
			'parent_id' => 0
		],
		[
			'parent_id' => $item_id
		]) !== false;
		$return['success'] = $XenuxDB->Delete('sites', [
			'where'=> [
				'id' => $item_id
			]
		]);
		break;
}


header('Content-type: application/json');
echo json_encode($return);

// close the connection to the database
$XenuxDB->closeConnection();
