<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login['role'] < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}

$rechte = array(
				'0' => 'Standartadministrator (darf Inhalte bearbeiten)',
				'1' => 'erweiterteter Administrator (darf Inhalte&Ansprechpartner bearbeiten und Mails versenden)',
				'2' => 'voller Administrator (darf Inhalte bearbeiten, Mails versenden, Rechte&Homepageeinstellungen ändern)',
				'3' => 'Root (Rechte Über Alles)'
				);				

if(!empty($_GET['id'])) {
	$id = mysql_real_escape_string($_GET['id']);
	if(isset($_POST['form'])) {
		$role_new = mysql_real_escape_string($_POST['role']);
		$sql = "UPDATE XENUX_users Set role = '$role_new' WHERE id = '".$_GET['id']."'";
		$erg = mysql_query($sql);
		echo 'Die Rechte wurden geändert!<br />';
		return;
	} else {
		if($_GET['id'] == $login['id']) {
			echo '<p>Du kannst nicht deine eigenen Rechte bearbeiten!</p>';
		} else {
			$sql = "SELECT * FROM XENUX_users WHERE id = '".$_GET['id']."'";
			$erg = mysql_query($sql);
			$row = mysql_fetch_array($erg);
			foreach($row as $key => $val) {
				$$key = $val;
			}
			if($role >= $login['role']) {
				echo '<p>Du kannst nicht bei Benutzern, die die gleichen oder höhere Rechte haben als du, die Rechte ändern!</p>';
			} else {
				echo 'Du bearbeitest gerade die Rechte für '.$username.' ('.$vorname.' '.$nachname.')!';
				?>
				<form action="" method="post">
					Recht:<br />
					<select name="role" size="1" style="width: 100%">
					<option value="0"<?php if($role==0) echo 'selected="selected"'; ?>><?php echo $rechte[0]; ?></option>
					<option value="1"<?php if($role==1) echo 'selected="selected"'; ?>><?php echo $rechte[1]; ?></option>
					<?php
					if($login['role'] == '3'){
						echo '<option value="2"';
						if($role == 2) echo 'selected="selected"';
						echo '>'.$rechte[2].'</option>';
					}
					?>
					</select><br /><br />
					<input type="hidden" name="form" value="form" />
					<input type="submit" value="ändern" />
				</form>
				<?php
				exit;
			}
		}
	}
}
?>
<p>Hier kannst du die Benutzerrechte ändern.</p>
<br />
<table id="table1" class="responsive-table">
	<tr class="head"><th>Benutzername</th><th>Bürgerlicher Name</th><th>Rechte</th><th></th></tr>
	<?php
	$sql = "SELECT * FROM XENUX_users";
	$erg = mysql_query($sql);
	while($row = mysql_fetch_array($erg)) {
		echo "<tr>";
		echo "<td data-title=\"Benutzername\">".$row['username']."</td>";
		echo "<td data-title=\"Bürgerlicher Name\">".$row['vorname']." ".$row['nachname']."</td>";
		echo "<td data-title=\"Rechte\">".$rechte[$row['role']]."</td>";
		
		echo "<td data-title=\"\"><a id=\"edit_href\" style=\"font-size: 1em;\" href=\"?site=$site&id=".$row['id']."\">Rechte&nbsp;ändern</a> <a id=\"edit_href\" style=\"font-size: 1em;\" href=\"?site=mail&id=".$row['id']."\">Mail</a></td>";
		echo "</tr>";
	}
	?>
</table>