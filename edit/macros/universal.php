<?php
include('../../core/inc/config.php');

$table = $_GET['table'];
foreach($_POST as $key => $val) {
	if(is_array($val)) {
		foreach($val as $key1 => $val1) {
			$$key1 = $db->real_escape_string($val1);
		}
	} else {
		$$key = $db->real_escape_string($val);
	}
}

$return = array();
$return['data'] = ""; 
$return['$_POST'] = print_r($_POST, true); 

$result = $db->query("SELECT * FROM $table;");
$columns = $result->fetch_fields();
foreach($columns as $key => $val) {
	if($val->name == 'id') unset($columns[$key]);
}

switch($token) {
	case "load":
		if(!in_array(@$_POST['order_column'], $columns)) {
			$order_column = $columns[key($columns)]->name;
		} else {
			$order_column = $_POST['order_column'];
		}
		if(!contains(@$_POST['order_direction'], "ASC", "DESC")) {
			$order_direction = "ASC";
		} else {
			$order_direction = $_POST['order_direction'];
		}
		
		$return['data'] .= "<tr>";
		foreach($columns as $val) {
			if($database_table[$table][$val->name]['show_in_table']) {
				$return['data'] .= "<th data-column-name=\"$val->name\" data-column-type=\"{$mysql_data_type[$val->type]}\">{$database_table[$table][$val->name]['title']}</th>";
			}
		}
		$return['data'] .= "<th style=\"width:2rem;\"></th></tr>";
		
		$sql_stmt  = "SELECT * FROM $table WHERE (";
		$first = true;
		foreach($columns as $val) {
			$sql_stmt .= (($first)?'':'OR ')."$val->name LIKE '%".$searchtxt."%'";
			$first = false;
		}
		$sql_stmt .= ") ORDER by $order_column $order_direction;";
		
		$result = $db->query($sql_stmt);
		while($row = $result->fetch_object()) {
			$return['data'] .= "<tr data-id=\"$row->id\">";
			foreach($columns as $val) {
				if($database_table[$table][$val->name]['show_in_table']) {
					$return['data'] .= "<td class=\"clickable\" title=\"klicken zum bearbeiten\" data-column-name=\"$val->name\" data-column-type=\"{$mysql_data_type[$val->type]}\" data-value=\"".$row->{$val->name}."\" data-name=\"{$database_table[$table][$val->name]['title']}\">".(($val->name=='role')?$roles[$row->{$val->name}]:(($mysql_data_type[$val->type] == 'tinyint')?(($row->{$val->name})?'Ja':'Nein'):$row->{$val->name}))."</td>";
				}
			}
			$return['data'] .= "<td><span title=\"entfernen\" class=\"remove remove-icon clickable\"></span></td></tr>";
		}
		$return['status'] = 'successfull';
		break;
		
	case "update":
		$result = $db->query("UPDATE $table SET $column = '$value' WHERE id = '$id';");
		$return['status'] = 'successfull';
		break;
		
	case "remove":
		/* specials */
		if($table == 'teacher') {
			$result	= $db->query("SELECT * FROM teacher WHERE id = '$id' LIMIT 1;");
			$row 	= $result->fetch_object();
			$result	= $db->query("DELETE FROM teacher_subject WHERE teacher_id = '$id';"); // delete associated subjects
			$result	= $db->query("DELETE FROM roomplanner WHERE teacher_id = '$id';"); // delete all booked rooms
			$result	= $db->query("DELETE FROM users WHERE username = '$row->short';"); // delete associated user
		}
		if($table == 'subject') {
			$result	= $db->query("SELECT * FROM subject WHERE id = '$id' LIMIT 1;");
			$row 	= $result->fetch_object();
			$result	= $db->query("DELETE FROM teacher_subject WHERE subject_id = '$id';"); // delete associated teacher
		}
		
		$result = $db->query("DELETE FROM $table WHERE id = '$id';");
		$return['status'] = 'successfull';
		break;
		
	case "add":
		if($table == 'users') {
			$password = password_hash(@$_POST['values']['password'], PASSWORD_DEFAULT, $pw_hash_options); // hash pw
		}
		
		$sql_stmt  = "INSERT INTO $table(";
		$first = true;
		foreach($columns as $val) {
			$sql_stmt .= (($first)?'':',')."$val->name";
			$first = false;
		}
		$sql_stmt .= ") VALUES(";
		$first = true;
		foreach($columns as $val) {
			$sql_stmt .= (($first)?'':',')."'".@${$val->name}."'";
			$first = false;
		}
		$sql_stmt .= ");";
		$result = $db->query($sql_stmt);
		
		/* specials */
		if($table == 'teacher') {
			$password_hash = password_hash('default', PASSWORD_DEFAULT, $pw_hash_options); // hash pw
			$result = $db->query("INSERT INTO users(name, username, password) VALUES ('$first_name $last_name', '$short', '$password_hash');");
		}
		
		$return['status'] = 'successfull';
		break;
		
	case "load roles":
		foreach($roles as $key => $val) {
			if($key < 4 && $key > 0) {
				$return['data'] .= "<option value=\"$key\" ";
				if(@$SelectedRole == $key) $return['data'] .= 'selected';
				$return['data'] .=  ">$val</option>";
			}
		}
		$return['status'] = 'successfull';
		break;
	case "check username":
		if(preg_match("/[^a-zA-Z0-9_-]/", $username)) { // if the username contains unallowed chars
			$return['ContainsUnallowedChars'] = true;
		} else {
			$return['ContainsUnallowedChars'] = false;
		}
		$result = $db->query("SELECT * FROM users WHERE username = '$username';");
		$num = $result->num_rows;
		if($num >= 1) {
			$return['UsernameExist'] = true;
		} else {
			$return['UsernameExist'] = false;
		}
		$return['status'] = 'successfull';
		break;
}

header('Content-type: application/json');  
echo json_encode($return);
$db->close(); //close the connection to the db
?>