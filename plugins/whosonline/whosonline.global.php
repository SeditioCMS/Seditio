<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/whosonline/whosonline.global.php
Version=186
Updated=2026-sep-07
Type=Plugin
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=whosonline
Part=global
File=whosonline.global
Hooks=global
Order=10
Lock=0
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}

require_once(SED_ROOT . '/plugins/whosonline/inc/whosonline.functions.php');
sed_whosonline_count();
