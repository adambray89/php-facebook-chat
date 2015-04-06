<?php
ob_start();
session_start();
// MYSQL
$mysql_config = array(
	'db'		=>		'',
	'host'		=>		'localhost',
	'options'	=>		array(),
	'pass'		=>		'',
	'user'		=>		'',
);

// SITE
define('SITE',true);

// USER IP
function getIP() {
	return getenv('HTTP_CLIENT_IP')?: getenv('HTTP_X_FORWARDED_FOR')?: getenv('HTTP_X_FORWARDED')?: getenv('HTTP_FORWARDED_FOR')?: getenv('HTTP_FORWARDED')?: getenv('REMOTE_ADDR');	
}

define('IP', getIP());
?>