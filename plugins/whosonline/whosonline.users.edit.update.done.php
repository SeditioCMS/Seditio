<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/whosonline/whosonline.users.edit.update.done.php
Version=186
Updated=2026-sep-07
Type=Plugin
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=whosonline
Part=users.edit.update.done
File=whosonline.users.edit.update.done
Hooks=users.edit.update.done
Order=10
Lock=0
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}

if (!empty($db_online) && !empty($rusername) && !empty($urr['user_name']) && $rusername != $urr['user_name']) {
	sed_sql_query("UPDATE $db_online SET online_name='" . sed_sql_prep($rusername) . "' WHERE online_userid = " . (int)$id);
}
sed_cache_clear('sed_whosonline');
