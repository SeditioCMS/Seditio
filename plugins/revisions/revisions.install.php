<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/revisions/revisions.install.php
Version=186
Updated=2026-oct-01
Type=Plugin
Author=Seditio Team
Description=Installation script for revisions plugin
[END_SED]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) {
	die('Wrong URL.');
}

global $cfg;

$db_revisions = isset($cfg['sqldbprefix']) ? $cfg['sqldbprefix'] . 'revisions' : 'sed_revisions';

$sql = sed_sql_query("CREATE TABLE IF NOT EXISTS `$db_revisions` (
  `rev_id` int(11) NOT NULL AUTO_INCREMENT,
  `rev_type` varchar(32) NOT NULL,
  `rev_itemid` varchar(64) NOT NULL,
  `rev_date` int(11) NOT NULL DEFAULT '0',
  `rev_userid` int(11) NOT NULL DEFAULT '0',
  `rev_user_name` varchar(100) NOT NULL DEFAULT '',
  `rev_version` int(11) NOT NULL DEFAULT '1',
  `rev_title` varchar(255) NOT NULL DEFAULT '',
  `rev_comment` varchar(255) NOT NULL DEFAULT '',
  `rev_hash` varchar(32) NOT NULL DEFAULT '',
  `rev_datas` mediumtext NOT NULL,
  PRIMARY KEY (`rev_id`),
  KEY `rev_type_itemid` (`rev_type`, `rev_itemid`),
  KEY `rev_date` (`rev_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");
