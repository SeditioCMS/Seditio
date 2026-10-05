<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/revisions/revisions.uninstall.php
Version=186
Updated=2026-oct-01
Type=Plugin
Author=Seditio Team
Description=Uninstallation script for revisions plugin
[END_SED]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) {
	die('Wrong URL.');
}

global $cfg;

$db_revisions = isset($cfg['sqldbprefix']) ? $cfg['sqldbprefix'] . 'revisions' : 'sed_revisions';

$sql = sed_sql_query("DROP TABLE IF EXISTS `$db_revisions`;");
