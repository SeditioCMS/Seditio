<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome & Seditio Team
http://www.neocrome.net
http://www.seditio.org
[BEGIN_SED]
File=system/common.php
Version=175
Updated=2012-dec-31
Type=Core
Author=Neocrome
Description=Common
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

if (get_magic_quotes_gpc()) //New in 175
{ 
    function sed_disable_mqgpc(&$value) 
    { 
        $value = stripslashes($value); 
    } 
    $gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST); 
    array_walk_recursive($gpc, 'sed_disable_mqgpc'); 
} 

error_reporting(E_ALL ^ E_NOTICE);  

/* ======== Connect to the SQL DB======== */

require('system/database.'.$cfg['sqldb'].'.php');
$connection_id = sed_sql_connect($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $cfg['mysqldb']);
unset($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword']);
sed_sql_set_charset($connection_id, 'utf8');

mb_internal_encoding('UTF-8'); // New v171

/* ======== Configuration settings (from the DB) ======== */

$sql_config = sed_sql_query("SELECT config_owner, config_cat, config_name, config_value FROM $db_config");

if (sed_sql_numrows($sql_config)<100)
	{
	define('SED_ADMIN',TRUE);
	require_once('system/functions.admin.php');
	unset($query);

	foreach($cfgmap as $i => $line)
		{ $query[] = "('core','".$line[0]."','".$line[1]."','".$line[2]."',".(int)$line[3].",'".$line[4]."')"; }
	$query = implode(",", $query);

	$sql = sed_sql_query("INSERT INTO $db_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ".$query);
	}

while ($row = sed_sql_fetchassoc($sql_config))
	{
	if ($row['config_owner']=='core')
		{ $cfg[$row['config_name']] = $row['config_value']; }
	else
		{ $cfg['plugin'][$row['config_cat']][$row['config_name']] = $row['config_value']; }
	}

/* ======== Extra settings (the other presets are in functions.php) ======== */

$sys['day'] = @date('Y-m-d');
$sys['now'] = time();
$sys['now_offset'] = $sys['now'] - $cfg['servertimezone']*3600;
$online_timedout = $sys['now'] - $cfg['timedout'];
$cfg['doctype'] = sed_setdoctype($cfg['doctypeid']);
$cfg['css'] = $cfg['defaultskin'];

if($cfg['clustermode'])  //Fix 175
  {
  	if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) $usr['ip'] = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
  	elseif(isset($_SERVER['HTTP_X_REAL_IP'])) $usr['ip'] = $_SERVER['HTTP_X_REAL_IP'];
  	elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $usr['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
  	else $usr['ip'] = $_SERVER['REMOTE_ADDR'];
  }
else
  {
  	$usr['ip'] = $_SERVER['REMOTE_ADDR'];
  }
if (!preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $usr['ip']))  //Fix 175
  {
  	$usr['ip'] = '0.0.0.0';
  }

$cfg['mobile_client'] = sed_mobile_detect();
$sys['unique'] = sed_unique(16); 

// ------------------ New in 175

$sys['request_uri'] = $_SERVER['REQUEST_URI'];  

$sys['url'] = base64_encode($sys['request_uri']);
$sys['url_redirect'] = 'redirect='.$sys['url'];
$redirect = sed_import('redirect','G','SLU');

$url_default = parse_url($cfg['mainurl']);
$sys['secure'] = sed_is_ssl();
$sys['scheme'] = $sys['secure'] ? 'https' : 'http';
$sys['domain'] = preg_replace('#^www\.#', '', $url_default['host']);

$sys['http_host'] = sed_set_host($sys['domain']);

if ($sys['http_host'] == $url_default['host']
  || $cfg['multihost']	
  || $sys['http_host'] != 'www.'.$sys['domain'] 
      && preg_match('`^.+\.'.preg_quote($sys['domain']).'$`i', $sys['http_host'])) 
  {          
    $sys['host'] = preg_match('#^[\w\p{L}\.\-]+$#u', $sys['http_host']) ? $sys['http_host'] : $url_default['host']; 
    $sys['domain'] = preg_replace('#^www\.#', '', $sys['host']); 
  }
else { $sys['host'] = $url_default['host']; }
  
$sys['port'] = empty($url_default['port']) ? '' : ':'.$url_default['port'];
$sys['subdir_uri'] = (mb_strlen(dirname($_SERVER['PHP_SELF'])) > 1) ? dirname($_SERVER['PHP_SELF']) : ""; 
$sys['abs_url'] = $sys['scheme'].'://'.$sys['host'].$sys['port'].$sys['subdir_uri'];
$sys['canonical_url'] = $sys['scheme'].'://'.$sys['host'].$sys['port'].$sys['request_uri']; 
if ($sys['abs_url'][mb_strlen($sys['abs_url']) - 1] != '/') { $sys['abs_url'] .= '/'; }     

// -----------------------------

$ishtml = ($cfg['textmode'] == 'html') ? 1 : 0; //New v172
$usr['user_agent'] = $_SERVER['HTTP_USER_AGENT']; //New v173
$check_defskin = "skins/".$cfg['defskin']."/header.tpl"; //New v173
$cfg['defaultskin'] = (!empty($cfg['defskin']) && @file_exists($check_defskin)) ? $cfg['defskin'] : $cfg['defaultskin']; //New v173

/* ======== Internal cache ======== */

if ($cfg['cache'])
	{
	$sql = sed_cache_getall();
	if ($sql)
		{
		while ($row = sed_sql_fetchassoc($sql))
			{ $$row['c_name'] = unserialize($row['c_value']); }
		}
	}

/* ======== Check the banlist ======== */

$userip = explode('.', $usr['ip']);
$ipmasks = "('".$userip[0].".".$userip[1].".".$userip[2].".".$userip[3]."','".$userip[0].".".$userip[1].".".$userip[2].".*','".$userip[0].".".$userip[1].".*.*','".$userip[0].".*.*.*')";

$sql = sed_sql_query("SELECT banlist_id, banlist_ip, banlist_reason, banlist_expire FROM $db_banlist WHERE banlist_ip IN ".$ipmasks, 'Common/banlist/check');

If (sed_sql_numrows($sql)>0)
	{
	$row = sed_sql_fetchassoc($sql);
	if ($sys['now']>$row['banlist_expire'] && $row['banlist_expire']>0)
		{
		$sql = sed_sql_query("DELETE FROM $db_banlist WHERE banlist_id='".$row['banlist_id']."' LIMIT 1");
		}
	else
		{
		$disp = "Your IP is banned.<br />Reason: ".$row['banlist_reason']."<br />Until: ";
		$disp .= ($row['banlist_expire']>0) ? @date($cfg['dateformat'], $row['banlist_expire'])." GMT" : "Never expire.";
		sed_diefatal($disp);
		}
	}

/* ======== Groups ======== */

if (!$sed_groups )
	{
	$sql = sed_sql_query("SELECT * FROM $db_groups WHERE grp_disabled=0 ORDER BY grp_level DESC");

	if (sed_sql_numrows($sql)>0)
		{
		while ($row = sed_sql_fetchassoc($sql))
			{
			$sed_groups[$row['grp_id']] = array (
				'id' => $row['grp_id'],
				'alias' => $row['grp_alias'],
				'level' => $row['grp_level'],
 				'disabled' => $row['grp_disabled'],
 				'hidden' => $row['grp_hidden'],
				'state' => $row['grp_state'],
				'title' => sed_cc($row['grp_title']),
				'desc' => sed_cc($row['grp_desc']),
				'icon' => $row['grp_icon'],
        'color' => $row['grp_color'],
				'pfs_maxfile' => $row['grp_pfs_maxfile'],
				'pfs_maxtotal' => $row['grp_pfs_maxtotal'],
				'ownerid' => $row['grp_ownerid']
					);
			}
		}
	else
		{ sed_diefatal('No groups found.'); }

	sed_cache_store('sed_groups',$sed_groups,3600);
	}

/* ======== User/Guest ======== */

$usr['id'] = 0;
$usr['sessionid'] = '';
$usr['name'] = '';
$usr['level'] = 0;
$usr['lastvisit'] = 30000000000;
$usr['lastlog'] = 0;
$usr['timezone'] = $cfg['defaulttimezone'];
$usr['newpm'] = 0;
$usr['messages'] = 0;

if ($cfg['authmode']==2 || $cfg['authmode']==3)
	{ session_start(); }

if (isset($_SESSION['rsedition']) && ($cfg['authmode']==2 || $cfg['authmode']==3))
	{
	$rsedition = $_SESSION['rsedition'];
	$rseditiop = $_SESSION['rseditiop'];
	$rseditios = $_SESSION['rseditios'];
	}
elseif (isset($_COOKIE['SEDITIO']) && ($cfg['authmode']==1 || $cfg['authmode']==3))
	{
	$u = base64_decode($_COOKIE['SEDITIO']);
	$u = explode(':_:',$u);
	$rsedition = sed_import($u[0],'D','INT');
	$rseditiop = sed_import($u[1],'D','H32');
	$rseditios = sed_import($u[2],'D','ALP');
	}

if ($rsedition>0 && $cfg['authmode']>0)
	{
	if (mb_strlen($rseditiop)!=32)
		{ sed_diefatal('Wrong value for the password.'); }

	if ($cfg['ipcheck'])
		{ $sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$rsedition' AND user_secret='$rseditiop' AND user_lastip='".$usr['ip']."'"); }
	else
		{ $sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$rsedition' AND user_secret='$rseditiop'"); }

	if ($row = sed_sql_fetcharray($sql))
		{
		if ($row['user_maingrp']>3)
			{
			$usr['id'] = $row['user_id'];
			$usr['sessionid'] = ($cfg['authmode']==1) ? sed_hash($row['user_lastvisit'], 2) : sed_hash($row['user_secret'], 2);
			$usr['name'] = $row['user_name'];
			$usr['maingrp'] = $row['user_maingrp'];
			$usr['lastvisit'] = $row['user_lastvisit'];
			$usr['lastlog'] = $row['user_lastlog'];
			$usr['timezone'] = $row['user_timezone'];
			$usr['skin'] = ($cfg['forcedefaultskin']) ? $cfg['defaultskin'] : $row['user_skin'];
			$usr['lang'] = ($cfg['forcedefaultlang']) ? $cfg['defaultlang'] : $row['user_lang'];
			$usr['newpm'] = $row['user_newpm'];
			$usr['auth'] = unserialize($row['user_auth']);
			$usr['level'] = $sed_groups[$usr['maingrp']]['level'];
			$usr['profile'] = $row;
		
			if ($usr['lastlog']+$cfg['timedout'] < $sys['now_offset'])
				{
				$sys['comingback']= TRUE;
				$usr['lastvisit'] = $usr['lastlog'];
				$sys['sql_update_lastvisit'] = ", user_lastvisit='".$usr['lastvisit']."'";
				}

			if (empty($row['user_auth']))
				{
				$usr['auth'] = sed_auth_build($usr['id'], $usr['maingrp']);
				$sys['sql_update_auth'] = ", user_auth='".serialize($usr['auth'])."'";
				}

			$sql = sed_sql_query("UPDATE $db_users SET user_lastlog='".$sys['now_offset']."', user_lastip='".$usr['ip']."', user_sid='".$usr['sessionid']."', user_logcount=user_logcount+1 ".$sys['sql_update_lastvisit']." ".$sys['sql_update_auth']." WHERE user_id='".$usr['id']."'");
			}
		}
	}
else
	{
	if (empty($rseditios) && ($cfg['authmode']==1 || $cfg['authmode']==3))
		{
		$u = base64_encode('0:_:0:_:'.$cfg['defaultskin']);
		setcookie('SEDITIO',$u,time()+$cfg['cookielifetime'],$cfg['cookiepath'],$cfg['cookiedomain']);
		}
	else
	  	{
	   $skin = ($cfg['forcedefaultskin']) ? $cfg['defaultskin'] : $rseditios;
	  	}
	}

if ($usr['id']==0)
	{
	$usr['auth'] = sed_auth_build(0);
	$usr['skin'] = (empty($usr['skin'])) ? $cfg['defaultskin'] : $usr['skin'];
	$usr['lang'] = $cfg['defaultlang'];
	}


/* ======== Parser ======== */

if (!$sed_parser)
	{
	$sed_parser = sed_build_parser();
	sed_cache_store('sed_parser', $sed_parser, 600);
	}

/* ======== GET imports ======== */

$z = mb_strtolower(sed_import('z','G','ALP',32));
$m = sed_import('m','G','ALP',24);
$n = sed_import('n','G','ALP',24);
$a = sed_import('a','G','ALP',24);
$b = sed_import('b','G','ALP',24);

/* ======== Plugins ======== */

if (!$sed_plugins)
	{
	$sql = sed_sql_query("SELECT * FROM $db_plugins WHERE pl_active=1 ORDER BY pl_hook ASC, pl_order ASC");
	 if (sed_sql_numrows($sql)>0)
		{
		while ($row = sed_sql_fetcharray($sql))
			{
      $sed_plugins[] = $row; 
      $sed_plugins[$row['pl_code']]['pl_title'] = $row['pl_title'];
      }
		}
	sed_cache_store('sed_plugins', $sed_plugins, 3300);
	}

/* ======== Hooks for plugins (standalone) ======== */

if (defined('SED_PLUG') && !empty($_GET['e']))
  {
  $extp = sed_getextplugins('common.plug.'.$_GET['e']);
  if (is_array($extp))
	 { foreach($extp as $k => $pl) { include('plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
  }

/* ======== Hooks for plugins (admin tools) ======== */

if (defined('SED_ADMIN') && $m=='tools' && !empty($_GET['p']))
  {
  $extp = sed_getextplugins('common.tool.'.$_GET['p']);
  if (is_array($extp))
	 { foreach($extp as $k => $pl) { include('plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
   }

/* ======== Anti-XSS protection ======== */

$xg = sed_import('x','G','ALP');
$xp = sed_import('x','P','ALP');

$xk = sed_check_xp();

$extp = sed_getextplugins('common');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include('plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }


/* ======== Gzip and output filtering ======== */

if ($cfg['gzip'])
	{ @ob_start('ob_gzhandler'); }
else
	{ ob_start(); }

ob_start('sed_outputfilters'); //fix v173

/* ======== Who's online (part 1) and shield protection ======== */

if (!$cfg['disablewhosonline'])
	{
	$sql = sed_sql_query("DELETE FROM $db_online WHERE online_lastseen<'$online_timedout'");
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_online WHERE online_name='v'");
	$sys['whosonline_vis_count'] = sed_sql_result($sql, 0, 'COUNT(*)');
	$sql = sed_sql_query("SELECT online_name, online_userid FROM $db_online WHERE online_name NOT LIKE 'v' ORDER BY online_name ASC");
	$sys['whosonline_reg_count'] = sed_sql_numrows($sql);
	$sys['whosonline_all_count'] = $sys['whosonline_reg_count'] + $sys['whosonline_vis_count'];

	$ii=0;
	while ($row = sed_sql_fetchassoc($sql))
		{
		$out['whosonline_reg_list'] .= ($ii>0) ? ', ' : '';
		$out['whosonline_reg_list'] .= sed_build_user($row['online_userid'], sed_cc($row['online_name']));
		$sed_usersonline[] = $row['online_userid'];
      	$ii++;
		}
	}
	
/* =========== Shield Protection ================= */


if (!$cfg['disablewhosonline'] || $cfg['shieldenabled'])
	{
	if ($usr['id']>0)
		{
		$sql = sed_sql_query("SELECT online_id FROM $db_online WHERE online_userid='".$usr['id']."'");

		if ($row = sed_sql_fetchassoc($sql))
			{
  		$online_count = 1;

			if ($cfg['shieldenabled'])
				{
				$sql2 = sed_sql_query("SELECT online_shield, online_action, online_hammer, online_lastseen FROM $db_online WHERE online_userid='".$usr['id']."'");
				if ($row = sed_sql_fetchassoc($sql2))
					{
					$shield_limit = $row['online_shield'];
					$shield_action = $row['online_action'];
					$shield_hammer = sed_shield_hammer($row['online_hammer'],$shield_action,$row['online_lastseen']);
					}
  			}

			}
      }
   else
      {
      $sql = sed_sql_query("SELECT COUNT(*) FROM $db_online WHERE online_ip='".$usr['ip']."'");
      $online_count = sed_sql_result($sql,0,'COUNT(*)');

      if ($online_count>0)
         {

         if ($cfg['shieldenabled'])
            {
            $sql2 = sed_sql_query("SELECT online_shield, online_action, online_hammer, online_lastseen FROM $db_online WHERE online_ip='".$usr['ip']."'");
            if ($row = sed_sql_fetchassoc($sql2))
               {
               $shield_limit = $row['online_shield'];
               $shield_action = $row['online_action'];
               $shield_hammer = sed_shield_hammer($row['online_hammer'],$shield_action,$row['online_lastseen']);
               }
            }

         }

      }
	}

/* ======== Max users ======== */

if (!$cfg['disablehitstats'])
	{
	$sql = sed_sql_query("SELECT stat_value FROM $db_stats where stat_name='maxusers' LIMIT 1");

	if ($row = sed_sql_fetcharray($sql))
    	{ $maxusers = $row[0]; }
	else
    	{ $sql = sed_sql_query("INSERT INTO $db_stats (stat_name, stat_value) VALUES ('maxusers', 1)"); }

	if ($maxusers<$sys['whosonline_all_count'])
    	{ $sql = sed_sql_query("UPDATE $db_stats SET stat_value='".$sys['whosonline_all_count']."' WHERE stat_name='maxusers'"); }
	}

/* ======== Language ======== */

$mlang = 'system/lang/'.$usr['lang'].'/main.lang.php';

if (!file_exists($mlang))
	{
	$usr['lang'] = $cfg['defaultlang'];
	$mlang = 'system/lang/'.$usr['lang'].'/main.lang.php';

	if (!file_exists($mlang))
		{ sed_diefatal('Main language file not found.'); }
	}

$lang = $usr['lang'];
require($mlang);

/* ======== Who's online part 2 ======== */

$out['whosonline'] = ($cfg['disablewhosonline']) ? '' : $sys['whosonline_reg_count'].' '.$L['com_members'].', '.$sys['whosonline_vis_count'].' '.$L['com_guests'];
$out['copyright'] = "<a href=\"http://www.seditio.org\">".$L['foo_poweredby']." Seditio</a>";

/* ======== Skin ======== */

$usr['skin_raw'] = $usr['skin'];

if (@file_exists('skins/'.$usr['skin'].'.'.$usr['lang'].'/header.tpl'))
	{ $usr['skin'] = $usr['skin'].'.'.$usr['lang']; }

$mskin = 'skins/'.$usr['skin'].'/header.tpl';

if (!file_exists($mskin))
	{
	$out['notices'] .= $L['com_skinfail'].'<br />';
	$usr['skin'] = $cfg['defaultskin'];
	$mskin = 'skins/'.$usr['skin'].'/header.tpl';

	if (!file_exists($mskin))
		{ sed_diefatal('Default skin not found.'); }
	}

$usr['skin_lang'] = 'skins/'.$usr['skin'].'/'.$usr['skin_raw'].'.'.$usr['lang'].'.lang.php';

if (@file_exists($usr['skin_lang']))
	{ require($usr['skin_lang']); }

require('skins/'.$usr['skin'].'/'.$usr['skin'].'.php');

$skin = $usr['skin'];

/* ======== Basic statistics ======== */

if (!$cfg['disablehitstats'])
	{
	sed_stat_inc('totalpages');
	$hits_today = sed_stat_get($sys['day']);

	if ($hits_today>0)
		{ sed_stat_inc($sys['day']); }
	else
		{ sed_stat_create($sys['day']); }

	$sys['referer'] = mb_substr(mb_strtolower($_SERVER['HTTP_REFERER']), 0, 255);
  $sys['httphost'] = mb_strtolower($_SERVER['HTTP_HOST']); // New Sed175

	if (!empty($sys['referer'])
		&& mb_stripos($sys['referer'], $cfg['mainurl']) === FALSE
		&& mb_stripos($sys['referer'], $cfg['hostip']) === FALSE
    && mb_stripos($sys['referer'], $sys['httphost']) === FALSE 
		&& mb_stripos($sys['referer'], str_ireplace('//www.', '//', $cfg['mainurl'])) === FALSE
		&& mb_stripos(str_ireplace('//www.', '//', $sys['referer']), $cfg['mainurl']) === FALSE)
	{
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_referers WHERE ref_url = '".sed_sql_prep($sys['referer'])."'");
		$count = sed_sql_result($sql,0,"COUNT(*)");

		if ($count>0)
			{
			$sql = sed_sql_query("UPDATE $db_referers SET ref_count=ref_count+1,
				ref_date='".$sys['now_offset']."'
				WHERE ref_url='".sed_sql_prep($sys['referer'])."'");
			}
	    else
			{
			$sql = sed_sql_query("INSERT INTO $db_referers
				(ref_url,
				ref_count,
				ref_date)
				VALUES
				('".sed_sql_prep($sys['referer'])."',
				'1',
				".(int)$sys['now_offset'].")");
			}
	}
	}

/* ======== Categories ======== */

if (!$sed_cat && !$cfg['disable_page'])
	{
	$sed_cat = sed_load_structure();
	sed_cache_store('sed_cat', $sed_cat, 3600);
	}

/* ======== Forums ======== */

if (!$sed_forums_str && !$cfg['disable_forums'])
	{
	$sed_forums_str = sed_load_forum_structure();
	sed_cache_store('sed_forums_str', $sed_forums_str, 3600);
	}

/* ======== Various ======== */

$out['img_up'] = "<img src=\"skins/".$usr['skin']."/img/system/arrow-up.gif\" alt=\"\" />";
$out['img_down'] = "<img src=\"skins/".$usr['skin']."/img/system/arrow-down.gif\" alt=\"\" />";
$out['img_left'] = "<img src=\"skins/".$usr['skin']."/img/system/arrow-left.gif\" alt=\"\" />";
$out['img_right'] = "<img src=\"skins/".$usr['skin']."/img/system/arrow-right.gif\" alt=\"\" />";
$out['img_delete'] = "<img src=\"system/img/admin/delete.png\" alt=\"\" />";
$out['img_edit'] = "<img src=\"system/img/admin/edit.png\" alt=\"\" />";
$out['img_checked'] = "<img src=\"system/img/admin/checked.png\" alt=\"\" />";
$out['img_unchecked'] = "<img src=\"system/img/admin/unchecked.png\" alt=\"\" />";
$out['img_set'] = "<img src=\"system/img/admin/set.png\" alt=\"\" />";

$sed_yesno[0] = $L['No'];
$sed_yesno[1] = $L['Yes'];
$sed_img_up = $out['img_up'];
$sed_img_down = $out['img_down'];
$sed_img_left = $out['img_left'];
$sed_img_right = $out['img_right'];

/* ======== Smilies ======== */

if (!$sed_smilies)
	{
	$sql = sed_sql_query("SELECT * FROM $db_smilies ORDER by smilie_order ASC, smilie_id ASC");
	 if (sed_sql_numrows($sql)>0)
		{
		while ($row = sed_sql_fetchassoc($sql))
			{ $sed_smilies[] = $row; }
		}
	sed_cache_store('sed_smilies',$sed_smilies,3550);
	}

/* ======== Local/GMT time ======== */

$usr['timetext'] = sed_build_timezone($usr['timezone']);
$usr['gmttime'] = @date($cfg['dateformat'],$sys['now_offset']).' GMT';


/* ======== Maintenance Mode ======== */  // New in 175

if ($cfg['maintenance'] && $usr['level'] < $cfg['maintenancelevel'] && !defined('SED_USERS'))
  {
  sed_diemaintenance();
  }

/* ======== Global hook ======== */

$extp = sed_getextplugins('global');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include('plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }

/* ======== Pre-loads ======== */

$sed_bbcodes = sed_loadbbcodes();

/* ======== 301 Redirect to SEF URL's ======== */

if ($cfg['sefurls'] && $cfg['sefurls301']) 
{      
   sed_sefurlredirect();
}


?>