<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/revisions/revisions.common.php
Version=186
Updated=2026-oct-01
Type=Plugin
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=revisions
Part=common
Hooks=common
File=revisions.common
Order=1
Lock=0
[END_SED_EXTPLUGIN]

==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}

global $cfg, $db_revisions;

$db_revisions = isset($cfg['sqldbprefix']) ? $cfg['sqldbprefix'] . 'revisions' : 'sed_revisions';

require_once SED_ROOT . '/plugins/revisions/inc/revisions.functions.php';

if ($path_lang = sed_langfile('revisions', 'plugin')) {
	require($path_lang);
}
