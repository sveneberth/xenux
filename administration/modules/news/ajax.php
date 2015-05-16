<?php
// include Xenux-Loader
include_once(dirname(dirname(dirname(__DIR__))) . "/core/xenux-load.php");

if(!(defined('DEBUG') && DEBUG == true))
	error_reporting(0);

if(!$app->user->isLogin())
	ErrorPage::view(404);

$return = array();

switch($_REQUEST['task']) {
	case 'site_edit_update_order':
		foreach($_REQUEST['items'] as $key => $val) {
			if($key == 0)
				continue;

			$item_id		= $XenuxDB->escapeString($val['item_id']);
			$parent_id		= $XenuxDB->escapeString($val['parent_id']);
			$sortindex		= $XenuxDB->escapeString($val['left']);
		
			$return['success']= $XenuxDB->Update('sites',['parent_id'=>$parent_id,'sortindex'=>$sortindex],['where'=>"id = '{$item_id}'"]);
		}
		break;
	case "site_edit_remove":
		$item_id	= $XenuxDB->escapeString($_REQUEST['item_id']);
		
		$return['success'] = $XenuxDB->Update('sites',['parent_id'=>0],['where'=>"parent_id = '{$item_id}'"]);
		$return['success'] = $XenuxDB->Delete('sites',['where'=>"id = '{$item_id}'"]);
			
		break;
	case "getImageUrls":
		$images	= array();

		$images = $XenuxDB->getList('files', [
											'selectOnly'	=> 'id, type, mime_type, filename',
											'where'			=> "type = 'file' AND mime_type LIKE '%image/%'",
											'orderBy'		=> 'filename',
											'orderDir'		=> 'ASC'
											]);

		if($images)
		{
			foreach ($images as $image)
			{
				$return[]['url'] = URL_MAIN."/file/".SHA1($image->id);
			}
		}
		break;
}


header('Content-type: application/json');  
echo json_encode($return);

// close the connection to the database
$XenuxDB->closeConnection();
?>