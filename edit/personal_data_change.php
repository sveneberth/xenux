<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(isset($_POST['form'])) {
	foreach($_POST as $key => $val) {
		$$key = mysql_real_escape_string($val);
	}
	if(!empty($vorname) and !empty($nachname) and !empty($email)) {
		$sql = "UPDATE XENUX_users Set vorname = '$vorname', nachname = '$nachname', username = '$username', email = '$email' WHERE id = '".$_SESSION['userid']."'";
		$erg = mysql_query($sql);
		echo "Die Daten wurden geändert!";
	}
} else {
	$sql = "SELECT * FROM XENUX_users WHERE id = '".$_SESSION['userid']."'";
	$erg = mysql_query($sql);
	$row = mysql_fetch_array($erg);
	foreach($row as $key => $val) {
		$$key = $val;
	}
}
?>
<p>Hier kannst du deine Daten ändern.</p>
<form action="" method="post">
	<span <?php if(empty($vorname) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Vorname</span><br />
	<input type="text" name="vorname" value="<?php echo @$vorname; ?>" /><br /><br />
	<span <?php if(empty($vorname) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Nachname</span><br />
	<input type="text" name="nachname" value="<?php echo @$nachname; ?>" /><br /><br />
	<span <?php if(empty($username) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Benutzername</span><br />
	<input type="text" name="username" value="<?php echo @$username; ?>" /><br /><br />
	<span <?php if(empty($vorname) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail</span><br />
	<input type="text" name="email" value="<?php echo @$email; ?>" /><br /><br />
	<input type="hidden" name="form" value="form" />
	<input type="submit" value="ändern" />
</form>
<style>
.transparent {
	position: fixed;
	left: 0;
	top: 0;
	background: rgba(0,0,0,.7);
	height: 100%;
	width: 100%;
	z-index: 500;
}
.message {
	position: fixed;
	background: #fff;
	border: 3px solid #1B1B1B;
	padding: 10px;
	z-index: 1000;
	font-family: Tahoma;
	border-radius: 8px;
	color: #222;
}
.message > h3 {
	margin: 0 0 10px 0;
}
.message > #closemessage {
	position: absolute;
	right: 5px;
	top: 5px;
	color: #222;
	height: 18px;
	width: 18px;
	text-decoration: none;
	line-height: 16px;
	font-weight: bold;
	border-radius: 20px;
	text-align: center;
	vertical-align: middle;
	font-size: 20px;
}
.message > #closemessage:hover {
	background: #222;
	color: #fff;
}
.message > .content {
}
</style>
<script>
function messagebox(width, height, topic, text) {
	$("body").append("<div class=\"transparent\"></div>");
	$("body").append("<div class=\"message\"></div>");
	$(".message").append("<a id=\"closemessage\" href=\"javascript:void(0)\">&times;</a>");
	$(".message").append("<h3>"+topic+"</h3>");
	$(".message").append("<div class=\"content\">"+text+"</h3>");
	$(".message").css("height", height+"%");
	$(".message").css("width", width+"%");
	$(".message").css("top", ((100-height)/2-10)+"%");
	$(".message").css("left", ((100-width)/2)+"%");
	$( ".message" ).draggable();
	$("#closemessage").click(function() {
		$(".transparent").remove();
		$(".message").remove();
	})
}
function delacc() {
	messagebox(30,20,'Meldung',"Möchten du wirklich deinen Account löschen? Das lsöchen kann nicht rückgängig gemacht werden!<br /><input type=\"button\" id=\"confirmdelacc\" value=\"löschen bestätigen\"/>");
	$("#confirmdelacc").click(function() {
		$(".transparent").remove();
		$(".message").remove();
		$.ajax({
			url: "delete_acc.php?userid=<?php echo $login['id']; ?>",
			success: function(content) {
				messagebox(30,20,'Meldung',content);
			}
		});
	})
}
</script>
<p><a href="javascript:delacc();">Account löschen</a></p>