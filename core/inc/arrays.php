<?php
#FIXME: need this???

$roles = array( // set role definations
	0 => 'Standartadministrator (darf Inhalte bearbeiten)',
	1 => 'erweiterteter Administrator (darf Inhalte & Ansprechpartner bearbeiten und Mails versenden)',
	2 => 'voller Administrator (darf Inhalte bearbeiten, Mails versenden, Rechte & Homepageeinstellungen ändern)',
	3 => 'Root (Rechte über Alles)'
);

$phpFileUploadErrors = array(
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
);
?>