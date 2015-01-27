<?php
$roles = array( // set role definations
	0 => 'Standartadministrator (darf Inhalte bearbeiten)',
	1 => 'erweiterteter Administrator (darf Inhalte & Ansprechpartner bearbeiten und Mails versenden)',
	2 => 'voller Administrator (darf Inhalte bearbeiten, Mails versenden, Rechte & Homepageeinstellungen ändern)',
	3 => 'Root (Rechte über Alles)'
);

$daysDE = array( // days in german
	1	=> 'Montag',
	2	=> 'Dienstag',
	3	=> 'Mittwoch',
	4	=> 'Donnerstag',
	5	=> 'Freitag',
//	6	=> 'Samstag',
//	7	=> 'Sonntag'
);

$mysql_data_type = array(
    1	=>	'tinyint',
	2	=>	'smallint',
    3	=>	'int',
    4	=>	'float',
    5	=>	'double',
    7	=>	'timestamp',
    8	=>	'bigint',
    9	=>	'mediumint',
    10	=>	'date',
    11	=>	'time',
    12	=>	'datetime',
	13	=>	'year',
	16	=>	'bit',
    252	=>	'text',
    253	=>	'varchar',
    254	=>	'char',
    246	=>	'decimal'
);

$special_sites = array(
	'news_list',
	'news_view',
	'error',
	'event_list',
	'event_view',
	'page',
	'search',
	'imprint',
	'contact',
);


$month_DE = array(
	1	=> "Januar",
	2	=> "Februar",
	3	=> "März",
	4	=> "April",
	5	=> "Mai",
	6	=> "Juni",
	7	=> "Juli",
	8	=> "August",
	9	=> "September",
	10	=> "Oktober",
	11	=> "November",
	12	=> "Dezember"
);
?>