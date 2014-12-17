<?php
include_once('../core/inc/config.php'); // include config
error_reporting(0);

$return				= array();
$return['success']	= true;

$return['$_REQUEST'] = $_REQUEST;

switch($_REQUEST['task']) {
	case 'site_edit_update_order':
		foreach($_REQUEST['items'] as $key => $val) {
			if($key == 0)
				continue;

			$item_id		= $db->real_escape_string($val['item_id']);
			$parent_id		= $db->real_escape_string($val['parent_id']);
			$position_left	= $db->real_escape_string($val['left']);
		
			$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '$item_id' LIMIT 1;");
			if(!$result)
				$return['err_db'] = $db->error;
			$row = $result->fetch_object();
		
			if(contains($row->site, 'imprint', 'home', 'contact')) {
				$return['errmsg'][] = "can't move page '$row->title' (not allowed)";
				continue;
			}
			
			$result = $db->query("UPDATE XENUX_sites SET parent_id = '$parent_id', position_left = '$position_left' WHERE id = '$item_id';");
			if(!$result)
				$return['err_db'] = $db->error;
		}
		break;
	case "site_edit_remove":
		$item_id	= $db->real_escape_string($_REQUEST['item_id']);
		
		$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '$item_id' LIMIT 1;");
		if(!$result)
			$return['err_db'] = $db->error;
		$row = $result->fetch_object();
		
		if($row->site == 'home') {
			$return['success'] = false;
			$return['errmsg'] = "can't delete page (not allowed)";
			break;
		}
			
			
		$result = $db->query("UPDATE XENUX_sites SET parent_id = 0 WHERE parent_id = '$item_id';");
		if(!$result)
			$return['err_db'] = $db->error;
		
		$result = $db->query("DELETE FROM XENUX_sites WHERE id = '$item_id';");
		if(!$result)
			$return['err_db'] = $db->error;
		break;
	case "universal_remove":
		$id	= $db->real_escape_string($_REQUEST['id']);
		$table	= $db->real_escape_string($_REQUEST['table']);
		
		$result = $db->query("DELETE FROM $table WHERE id = '$id';");
		if(!$result)
			$return['err_db'] = $db->error;
		break;
}


header('Content-type: application/json');  
echo json_encode($return);
$db->close(); //close the connection to the db
?>