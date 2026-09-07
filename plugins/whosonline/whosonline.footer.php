<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/whosonline/whosonline.footer.php
Version=186
Updated=2026-sep-07
Type=Plugin
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=whosonline
Part=footer
File=whosonline.footer
Hooks=footer.main
Order=10
Lock=0
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}

if (function_exists('sed_whosonline_track')) {
	sed_whosonline_track($location, isset($sys['sublocation']) ? $sys['sublocation'] : '');
}
