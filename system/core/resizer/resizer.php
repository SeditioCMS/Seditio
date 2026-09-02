<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=resizer/resizer.php
Version=186
Updated=2026-sep-02
Type=Core
Author=Amro
Description=Image Resizer
[END_SED]
==================== */

if (!defined('SED_CODE')) exit();

define('SED_RESIZER', TRUE);
$location = 'Resizer';
$z = 'resizer';

require(SED_ROOT . '/system/functions.php');
require(SED_ROOT . '/datas/config.php');
require(SED_ROOT . '/system/common.php');

$filename = $_GET['file'];

$resized_filename = sed_resize($filename);

if (is_readable($resized_filename)) {
	$ext = mb_strtolower(pathinfo($resized_filename, PATHINFO_EXTENSION));
	$mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg'));
	sed_sendheaders($mime, 200, 86400);
	print file_get_contents($resized_filename);
}
