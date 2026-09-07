<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/whosonline/whosonline.install.php
Version=186
Updated=2026-sep-07
Type=Plugin
[END_SED]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) {
	die('Wrong URL.');
}

global $cfg;

if (!isset($res)) {
	$res = '';
}

$mysqlengine = $cfg['mysqlengine'];
$mysqlcharset = $cfg['mysqlcharset'];
$mysqlcollate = $cfg['mysqlcollate'];
$prefix = $cfg['sqldbprefix'];

$check = sed_sql_query("SHOW TABLES LIKE '{$prefix}online'");
if (sed_sql_numrows($check) == 0) {
	$res .= "Creating online table...<br />";
	sed_sql_query("CREATE TABLE {$prefix}online (
	  online_id int(11) NOT NULL auto_increment,
	  online_ip varchar(45) NOT NULL DEFAULT '',
	  online_name varchar(24) NOT NULL DEFAULT '',
	  online_lastseen int(11) NOT NULL DEFAULT '0',
	  online_location varchar(32) NOT NULL DEFAULT '',
	  online_subloc varchar(255) NOT NULL DEFAULT '',
	  online_userid int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (online_id),
	  KEY online_lastseen (online_lastseen),
	  KEY online_userid (online_userid),
	  KEY online_ip (online_ip)
	) ENGINE={$mysqlengine} DEFAULT CHARSET={$mysqlcharset} COLLATE={$mysqlcollate};");
	$res .= "Online table created.<br />";
}
