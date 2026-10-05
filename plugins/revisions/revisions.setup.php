<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/revisions/revisions.setup.php
Version=186
Updated=2026-oct-01
Type=Plugin
Author=Seditio Team
Description=Universal version control and checkpoint system for pages, structure, and custom modules
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=revisions
Name=Revisions & Checkpoints
Description=Universal version control and checkpoint system
Version=186
Date=2026-oct-01
Author=Seditio Team
Copyright=
Notes=
SQL=
Auth_guests=0
Lock_guests=RW12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
max_revisions=01:select:5,10,15,20,30,50:15:Max revisions per item (depth limit)
enable_compression=02:radio:0,1:1:Compress revision data with gzip in database
auto_prune_days=03:select:0,30,60,90,180,365:90:Auto prune revisions older than (days, 0=disabled)
[END_SED_EXTPLUGIN_CONFIG]

==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}
