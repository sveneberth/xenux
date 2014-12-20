<?php
include_once('../core/inc/config.php'); // include config
error_reporting(0);

$return				= array();
$return['success']	= true;

$return['$_REQUEST'] = $_REQUEST;
$return['$_FILES'] = $_FILES;

switch($_REQUEST['task']) {
	case 'upload':
		$parent_folder	= $_REQUEST['parent_folder'];
		foreach($_FILES as $key => $file) {
			$name			= $file['name'];
			$tmpname		= $file['tmp_name'];
			$mime_type		= $file['type'];
			$size			= $file['size'];
			$lastModified	= filemtime($tmpname);
				
			$hndFile = fopen($tmpname, "r");
			$data = addslashes(fread($hndFile, $size));
			
			$result = $db->query("INSERT INTO XENUX_files(type, mime_type, data, filename, size, lastModified, parent_folder_id) VALUES('file', '$mime_type', '$data', '$name', '$size', '".date("Y-m-d H:i:s", $lastModified)."', '$parent_folder');");
			if(!$result)
				$return['err_db'] = $db->error;
		}
		break;
	case 'create_folder':
		if(!empty($_REQUEST['folder_name'])) {
			$folder_name	= $_REQUEST['folder_name'];
			$parent_folder	= $_REQUEST['parent_folder'];
			$result = $db->query("INSERT INTO XENUX_files(type, filename, lastModified, parent_folder_id) VALUES('folder', '$folder_name', NOW(), '$parent_folder');");
			if(!$result) 
				$return['err_db'] = $db->error;
		}
		break;
	case 'dirs_list':
		$result = $db->query("SELECT * FROM XENUX_files WHERE type = 'folder';");
		if(!$result) 
			$return['err_db'] = $db->error;
		while($row = $result->fetch_object()) {
			$return['data'][] = $row;
		}
		break;
	case 'get_entry':
		$result = $db->query("SELECT * FROM XENUX_files WHERE id = '{$_REQUEST['id']}' LIMIT 1;");
		if(!$result) 
			$return['err_db'] = $db->error;
		$row = $result->fetch_object();
			$return['data'] = $row;
		break;
	case 'dir_list':
		$result = $db->query("SELECT id, type, filename, mime_type, size, parent_folder_id, DATE_FORMAT(lastModified, '%d.%m.%Y %H:%i:%s') as lastModified FROM XENUX_files WHERE parent_folder_id = '{$_REQUEST['folder']}' ORDER by filename ASC;");
		if(!$result)
			$return['err_db'] = $db->error;
		
		if( $result->num_rows == 0)
			$return['data'] = "no entrys";
		
		while($row = $result->fetch_object()) {
			$return['data'][] = $row;
		}
		break;
	case 'breadcrumb':
		$folder = $_REQUEST['folder'];

		$breadcrumb = array();
		while($folder != 0) {
			$result = $db->query("SELECT id, filename, parent_folder_id FROM XENUX_files WHERE id = '$folder' LIMIT 1;");
			if(!$result)
				$return['err_db'] = $db->error;
			$row = $result->fetch_object();
			$folder = $row->parent_folder_id;
			$breadcrumb[] = $row;
		}
		$breadcrumb = array_reverse($breadcrumb);

		$return['data'] = $breadcrumb;
		break;
	case 'remove':
		$id = $_REQUEST['id'];
		
		if($id == 0) {
			$result = $db->query("TRUNCATE TABLE `XENUX_files`;");
			if(!$result)
				$return['err_db'] = $db->error;
			break;
		}

		$result = $db->query("SELECT id, type FROM XENUX_files WHERE id = '$id' LIMIT 1;");
		if(!$result)
			$return['err_db'] = $db->error;
		
		if($result->num_rows == 0) {
			$return['errmsg'] = "to be removed file not exists";
			$return['success'] = false;
			break;
		}
		
		$row = $result->fetch_object();
		$return['row'] = $row;
		
		if($row->type == 'file') {
			$result = $db->query("DELETE FROM XENUX_files WHERE id = '$id';");
			if(!$result)
				$return['err_db'] = $db->error;
		} elseif($row->type == 'folder') {
			$arrDelete = array();
			$arrFolder = array();
			$arrDelete[] = $id;
			$arrFolder[] = $id;
						
			while(!empty($arrFolder)) {
				$arrTemp = $arrFolder;
				$arrFolder = array();
				
				foreach($arrTemp as $val) { // for every file/folder
					$result = $db->query("SELECT id, type, parent_folder_id FROM XENUX_files WHERE parent_folder_id = '$val';");
					if(!$result)
						$return['err_db'] = $db->error;
						
					while($row = $result->fetch_object()) { // for every file/folder 
						if($row->type == 'folder') {
							$arrFolder[] = $row->id;
						}
						$arrDelete[] = $row->id;
					}
				}
			}
			$return['arrDelete'] = $arrDelete;
			foreach($arrDelete as $val) { // delete all
				$result = $db->query("DELETE FROM XENUX_files WHERE id = '$val';");
				if(!$result)
					$return['err_db'] = $db->error;
			}
		}
		break;
	case 'move':
		$id = $_REQUEST['id'];
		$to = $_REQUEST['to'];
		
		if($id == $to) {
			$return['errmsg'] = "can't move folder in self";
			$return['success'] = false;
			break;
		}
		
		$result = $db->query("UPDATE XENUX_files SET parent_folder_id = '$to' WHERE id = '$id';");
		if(!$result)
			$return['err_db'] = $db->error;
		
		break;
	case 'rename':
		$id = $_REQUEST['id'];
		$newName = $db->real_escape_string($_REQUEST['newName']);
		
		$result = $db->query("UPDATE XENUX_files SET filename = '$newName' WHERE id = '$id';");
		if(!$result)
			$return['err_db'] = $db->error;
		
		break;
	case "list_all_dirs":
		$id = 0;
		$arrAll			= array();
		$arrFolder		= array();
		$arrAll[]		= $id;
		$arrFolder[]	= $id;
					
		while(!empty($arrFolder)) {
			$arrTemp = $arrFolder;
			$arrFolder = array();
			
			foreach($arrTemp as $val) { // for every file/folder
				$result = $db->query("SELECT id, type, parent_folder_id, filename FROM XENUX_files WHERE parent_folder_id = '$val' ORDER by filename ASC;");
				if(!$result)
					$return['err_db'] = $db->error;
					
				while($row = $result->fetch_object()) { // for every file/folder 
					if($row->type == 'folder') {
						$arrFolder[] = $row->id;
						$arrAll[] = $row->id;
					}
				}
			}
		}
		foreach($arrAll as $val) {
			$folder = $val;
			$breadcrumb = array();
			while($folder != 0) {
				$result = $db->query("SELECT id, filename, parent_folder_id FROM XENUX_files WHERE id = '$folder' LIMIT 1;");
				if(!$result)
					$return['err_db'] = $db->error;
				$row = $result->fetch_object();
				$folder = $row->parent_folder_id;
				$breadcrumb[] = $row;
			}
			$breadcrumb = array_reverse($breadcrumb);

			$return['breadcrumbs'][$val] = $breadcrumb;
		}	

		foreach($return['breadcrumbs'] as $key => $val) {
			$temp = "root";
			foreach($val as $vall)	{
				$temp .= "/".$vall->filename;
			}
			$return['data'][$key] = $temp;
		}
		break;
}


header('Content-type: application/json');  
echo json_encode($return);
$db->close(); //close the connection to the db
?>