<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/whosonline/whosonline.setup.php
Version=186
Updated=2026-sep-07
Type=Plugin
Author=Seditio Team
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=whosonline
Name=Who's online
Description=Lists the members online
Version=3.0
Date=2026-sep-07
Author=Seditio Team
Copyright=
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
timeout=01:string::900:Online timeout in seconds (default 900 / 15 minutes)
cache_ttl=02:string::30:Aggregated online counts cache TTL in seconds (0 to disable)
showavatars=03:radio::1:Display avatars of users?
miniavatar_x=04:string::16:The size of a mini-avatars on the axis x, in pixels
miniavatar_y=05:string::16:The size of a mini-avatars on the axis y, in pixels
[END_SED_EXTPLUGIN_CONFIG]
==================== */
