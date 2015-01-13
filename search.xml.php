<?php
	include_once('core/inc/config.php'); // include config
//	header('Content-Type: application/opensearchdescription+xml; charset=utf-8');
	header('Content-Type: application/xml; charset=utf-8');
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
	<ShortName><?php echo $main->hp_name; ?></ShortName>
	<LongName><?php echo $main->hp_name; ?></LongName>
	<Description><?php echo $main->meta_desc; ?></Description>
	<InputEncoding>UTF-8</InputEncoding>
	<Language>*</Language>
	<Image width="16" height="16" type="image/x-icon"><?php echo (substr($main->favicon_src, 0, 1)=='/') ?  XENUX_URL . $main->favicon_src : $main->favicon_src; ?></Image>
	<Url type="text/html" template="<?php echo XENUX_URL; ?>/?site=search&amp;q={searchTerms}"/>
</OpenSearchDescription>