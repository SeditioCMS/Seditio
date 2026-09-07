<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/whosonline/whosonline.uninstall.php
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

if (!empty($sed_uninstall_drop_tables)) {
	$prefix = $cfg['sqldbprefix'];
	sed_sql_query("DROP TABLE IF EXISTS {$prefix}online");
	$res .= "Online table dropped.<br />";
} else {
	$res .= "Whosonline plugin uninstalled. Online table preserved.<br />";
}
