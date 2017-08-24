<?php
	include(__DIR__ . "/core/xenux-load.php");
	header('Content-Type: application/xml; charset=utf-8');
?>
<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
	<ShortName><?= $app->getOption('hp_name') ?></ShortName>
	<LongName><?= $app->getOption('hp_name') ?></LongName>
	<Description><?= $app->getOption('meta_desc') ?></Description>
	<InputEncoding>UTF-8</InputEncoding>
	<Language>*</Language>
	<Url type="text/html" template="<?= URL_MAIN ?>/search?q={searchTerms}"/>
</OpenSearchDescription><?php $XenuxDB->closeConnection(); ?>
