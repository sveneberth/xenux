<!DOCTYPE html>
<html lang="<?= translator::getLanguage() ?>">
	<head>
		<meta charset="UTF-8" />
		<title>{{errorcode}} {{status}}</title>
	</head>
	<body>
		<h1>{{errorcode}} {{status}}</h1>
		<p>{{message}}</p>
		<hr />
		<?= $_SERVER['SERVER_SIGNATURE'] ?>
	</body>
</html>
