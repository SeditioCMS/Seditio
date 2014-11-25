<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome & Seditio Team
http://www.neocrome.net
http://www.seditio.org
[BEGIN_SED]
File=forums.php
Version=175
Updated=2012-dec-31
Type=Core
Author=Neocrome
Description=Forums
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$id = sed_import('id','G','INT');
$s = sed_import('s','G','INT');
$q = sed_import('q','G','INT');
$p = sed_import('p','G','INT');
$d = sed_import('d','G','INT');
$o = sed_import('o','G','ALP');
$w = sed_import('w','G','ALP',4);
$quote = sed_import('quote','G','INT');
$poll = sed_import('poll','G','INT');
$vote = sed_import('vote','G','INT');

sed_blockguests();
sed_die(empty($s));

/* === Hook === */
$extp = sed_getextplugins('forums.newtopic.first');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include('plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$sql = sed_sql_query("SELECT * FROM $db_forum_sections WHERE fs_id='$s'");

if ($row = sed_sql_fetchassoc($sql))
	{
	$fs_state = $row['fs_state'];
	$fs_title = $row['fs_title'];
	$fs_category = $row['fs_category'];
	$fs_desc = $row['fs_desc'];
	$fs_autoprune = $row['fs_autoprune'];
	$fs_allowusertext = $row['fs_allowusertext'];
	$fs_allowbbcodes = $row['fs_allowbbcodes'];
	$fs_allowsmilies = $row['fs_allowsmilies'];
	$fs_allowprvtopics = $row['fs_allowprvtopics'];
	$fs_countposts = $row['fs_countposts'];

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', $s);
	sed_block($usr['auth_write']);
	}
else
	{ sed_die(); }

if ($fs_state)
	{
	sed_redirect(sed_url("message", "msg=602", "", true));
	}

if ($a=='newtopic')
	{
	sed_shield_protect();

	/* === Hook === */
	$extp = sed_getextplugins('forums.newtopic.newtopic.first');
	if (is_array($extp))
		{ foreach($extp as $k => $pl) { include('plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$newtopictitle = sed_import('newtopictitle','P','TXT', 128);
	$newtopicdesc = sed_import('newtopicdesc','P','TXT', 255);	
	$newprvtopic = sed_import('newprvtopic','P','BOL');
	$newmsg = sed_import('newmsg','P','HTM');
	$newprvtopic = (!$fs_allowprvtopics) ? 0 : $newprvtopic;

	$error_string .= (mb_strlen($newtopictitle)<2) ? $L['for_titletooshort']."<br />" : '';
	$error_string .= (mb_strlen($newmsg)<2) ? $L['for_msgtooshort']."<br />" : '';

	if (empty($error_string))
		{	
		if (strip_tags(mb_strlen($newtopictitle))>0 && mb_strlen($newmsg)>0)
			{
			if (mb_substr($newtopictitle, 0 ,1)=="#")
				{ $newtopictitle = str_replace('#', '', $newtopictitle); }

			$sql = sed_sql_query("INSERT into $db_forum_topics
				(ft_state,
				ft_mode,
				ft_sticky,
				ft_sectionid,
				ft_title,
				ft_desc,			
				ft_creationdate,
				ft_updated,
				ft_postcount,
				ft_viewcount,
				ft_firstposterid,
				ft_firstpostername,
				ft_lastposterid,
				ft_lastpostername )
				VALUES
				(0,
				".(int)$newprvtopic.",
				0,
				".(int)$s.",
				'".sed_sql_prep($newtopictitle)."',
				'".sed_sql_prep($newtopicdesc)."',			
				".(int)$sys['now_offset'].",
				".(int)$sys['now_offset'].",
				1,
				0,
				".(int)$usr['id'].",
				'".sed_sql_prep($usr['name'])."',
				".(int)$usr['id'].",
				'".sed_sql_prep($usr['name'])."')");

			$sql = sed_sql_query("SELECT ft_id FROM $db_forum_topics WHERE 1 ORDER BY ft_id DESC LIMIT 1");
			$row = sed_sql_fetchassoc($sql);
			$q = $row['ft_id'];

			$sql = sed_sql_query("INSERT into $db_forum_posts
				(fp_topicid,
				fp_sectionid,
				fp_posterid,
				fp_postername,
				fp_creation,
				fp_updated,
				fp_text,
				fp_text_ishtml,
				fp_posterip)
				VALUES
				(".(int)$q.",
				".(int)$s.",
				".(int)$usr['id'].",
				'".sed_sql_prep($usr['name'])."',
				".(int)$sys['now_offset'].",
				".(int)$sys['now_offset'].",
				'".sed_sql_prep($newmsg)."',".(int)$ishtml.",
				'".$usr['ip']."')");

			$sql = sed_sql_query("UPDATE $db_forum_sections SET
				fs_postcount=fs_postcount+1,
				fs_topiccount=fs_topiccount+1
				WHERE fs_id='$s'");

			if ($fs_autoprune>0)
				{ sed_forum_prunetopics('updated', $s, $fs_autoprune); }

			if ($fs_countposts)
				{ $sql = sed_sql_query("UPDATE $db_users SET
					user_postcount=user_postcount+1
					WHERE user_id='".$usr['id']."'"); }

			if (!$newprvtopic)
				{ sed_forum_sectionsetlast($s); }

			/* === Hook === */
			$extp = sed_getextplugins('forums.newtopic.newtopic.done');
			if (is_array($extp))
				{ foreach($extp as $k => $pl) { include('plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
			/* ===== */

			sed_shield_update(45, "New topic");
			sed_redirect(sed_url("forums", "m=posts&q=".$q."&n=last", "#bottom", true));
			}
		}	
	}

// -----------
if ($cfg['textmode']=='bbcode')
    {
    $smilies = ($cfg['parsesmiliesforums'] && $fs_allowsmilies) ? " &nbsp; ".sed_build_smilies('newtopic', 'newmsg', $L['Smilies'])." &nbsp; " : '';
    $bbcodes = ($cfg['parsesmiliesforums'] && $fs_allowsmilies) ? sed_build_bbcodes('newtopic', 'newmsg', $L['BBcodes']): '';
    } 
else { $bbcodes = ''; $smilies = ''; } 
// -----------


$pfs = sed_build_pfs($usr['id'], 'newtopic', 'newmsg', $L['Mypfs']);
$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, 'newtopic', 'newmsg', $L['SFS']) : '';
$morejavascript .= sed_build_addtxt('newtopic', 'newmsg');
$post_main = "<div id=\"nt\"><textarea name=\"newmsg\" rows=\"".$cfg['textarea_default_height']."\" cols=\"".$cfg['textarea_default_width']."\">".sed_cc($newmsg, ENT_QUOTES)."</textarea></div>";

$toptitle = "<a href=\"".sed_url("forums")."\">".$L['Forums']."</a> ".$cfg['separator']." ".sed_build_forums($s, $fs_title, $fs_category)." ".$cfg['separator']." <a href=\"".sed_url("forums", "m=newtopic&s=".$s)."\">".$L['for_newtopic']."</a>";
$toptitle .= ($usr['isadmin']) ? " *" : '';

$sys['sublocation'] = $fs_title;
$out['subtitle'] = $L['Forums'];

/**/
$title_tags[] = array('{MAINTITLE}', '{SUBTITLE}', '{TITLE}');
$title_tags[] = array('%1$s', '%2$s', '%3$s');
$title_data = array($cfg['maintitle'], $cfg['subtitle'], $out['subtitle']);
$out['subtitle'] = sed_title('forumstitle', $title_tags, $title_data);
/**/

/* === Hook === */
$extp = sed_getextplugins('forums.newtopic.main');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include('plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require("system/header.php");

$mskin = sed_skinfile(array('forums', 'newtopic', $fs_category, $s));
$t = new XTemplate($mskin);

if (!empty($error_string))
	{
	$t->assign("FORUMS_NEWTOPIC_ERROR_BODY",$error_string);
	$t->parse("MAIN.FORUMS_NEWTOPIC_ERROR");
	}

$t->assign(array(

	"FORUMS_NEWTOPIC_PAGETITLE" => $toptitle ,
	"FORUMS_NEWTOPIC_SUBTITLE" => sed_bbcode($fs_desc),
	"FORUMS_NEWTOPIC_SEND" => sed_url("forums", "m=newtopic&a=newtopic&s=".$s),
	"FORUMS_NEWTOPIC_TITLE" => "<input type=\"text\" class=\"text\" name=\"newtopictitle\" value=\"".sed_cc($newtopictitle)."\" size=\"56\" maxlength=\"64\" />",
	"FORUMS_NEWTOPIC_DESC" => "<input type=\"text\" class=\"text\" name=\"newtopicdesc\" value=\"".sed_cc($newtopicdesc)."\" size=\"56\" maxlength=\"64\" />",
	"FORUMS_NEWTOPIC_TEXT" => $post_main.$bbcodes." ".$smilies." ".$pfs.$poll_form,
	"FORUMS_NEWTOPIC_TEXTONLY" => $post_main,
	"FORUMS_NEWTOPIC_TEXTBOXER" => $post_main.$bbcodes." ".$smilies." ".$pfs.$poll_form,
	"FORUMS_NEWTOPIC_SMILIES" => $smilies,
	"FORUMS_NEWTOPIC_BBCODES" => $bbcodes,
	"FORUMS_NEWTOPIC_MYPFS" => $pfs,
	"FORUMS_NEWTOPIC_POLLFORM" => $poll_form
		));

if ($fs_allowprvtopics)
	{
	$checked = ($newprvtopic) ? "checked=\"checked\"" : '';
	$prvtopic = "<input type=\"checkbox\" class=\"checkbox\" name=\"newprvtopic\" $checked />";

	$t->assign(array(
		"FORUMS_NEWTOPIC_ISPRIVATE" => $prvtopic
			));
	$t->parse("MAIN.PRIVATE");
	}

/* === Hook === */
$extp = sed_getextplugins('forums.newtopic.tags');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include('plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require("system/footer.php");

?>
