<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome & Seditio Team
http://www.neocrome.net
http://www.seditio.org
[BEGIN_SED]
File=admin.forums.inc.php
Version=175
Updated=2012-dec-31
Type=Core.admin
Author=Neocrome
Description=Forums & categories
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$id = sed_import('id','G','INT');

$adminpath[] = array (sed_url("admin", "m=forums"), $L['Forums']);
$adminmain = "<h2><img src=\"system/img/admin/forums.png\" alt=\"\" /> ".$L['Forums']."</h2>"; 

$adminmain .= "<ul class=\"arrow_list\"><li><a href=\"".sed_url("admin", "m=config&n=edit&o=core&p=forums")."\">".$L['Configuration']."</a></li><li>";
$adminmain .= sed_linkif(sed_url("admin", "m=forums&s=structure"), $L['adm_forum_structure'], sed_auth('admin', 'a', 'A'));
$adminmain .= "</li></ul>";

if ($n=='edit')
	{
	if ($a=='update')
		{
		sed_check_xg();
		$rstate = sed_import('rstate', 'P', 'BOL');
		$rtitle = sed_import('rtitle', 'P', 'TXT');
		$rdesc = sed_import('rdesc', 'P', 'TXT');
		$ricon = sed_import('ricon', 'P', 'TXT');
		$rautoprune = sed_import('rautoprune', 'P', 'INT');
		$rcat = sed_import('rcat', 'P', 'TXT');
		$rallowusertext = sed_import('rallowusertext', 'P', 'BOL');
		$rallowbbcodes = sed_import('rallowbbcodes', 'P', 'BOL');
		$rallowsmilies = sed_import('rallowsmilies', 'P', 'BOL');
		$rallowprvtopics = sed_import('rallowprvtopics', 'P', 'BOL');
		$rcountposts = sed_import('rcountposts', 'P', 'BOL');
		$rtitle = sed_sql_prep($rtitle);
		$rdesc = sed_sql_prep($rdesc);
		$rcat = sed_sql_prep($rcat);
		$rparentcat = sed_import('rparentcat', 'P', 'INT'); // New in sed 172

		$sql = sed_sql_query("SELECT fs_id, fs_order, fs_category FROM $db_forum_sections WHERE fs_id='".$id."'");
		sed_die(sed_sql_numrows($sql)==0);
		$row_cur = sed_sql_fetchassoc($sql);

		if ($row_cur['fs_category']!=$rcat)
			{
		   	$sql = sed_sql_query("SELECT fs_order FROM $db_forum_sections WHERE fs_category='".$rcat."' ORDER BY fs_order DESC LIMIT 1");

   			if (sed_sql_numrows($sql)>0)
				{
				$row_oth = sed_sql_fetchassoc($sql);
				$rorder = $row_oth['fs_order']+1;
				}
			else
				{ $rorder = 100; }

			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_order=fs_order-1 WHERE fs_category='".$row_cur['fs_category']."' AND fs_order>".$row_cur['fs_order']);
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_order='$rorder' WHERE fs_id='$id'");
			}

		$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_state='$rstate', fs_title='$rtitle', fs_desc='$rdesc', fs_category='$rcat', fs_parentcat='$rparentcat', fs_icon='$ricon', fs_autoprune='$rautoprune', fs_allowusertext='$rallowusertext', fs_allowbbcodes='$rallowbbcodes', fs_allowsmilies='$rallowsmilies', fs_allowprvtopics='$rallowprvtopics', fs_countposts='$rcountposts' WHERE fs_id='$id'");

		sed_redirect(sed_url("admin", "m=forums", "", true));
		exit;
		}
	elseif ($a=='delete')
		{
		sed_check_xg();
		sed_auth_clear('all');
		$num = sed_forum_deletesection($id);
		sed_redirect(sed_url("message", "msg=916&rc=103&num=".$num, "", true));
		exit;
		}
	elseif ($a=='resync')
		{
		sed_check_xg();
		sed_forum_resync($id);
		}

  $sql = sed_sql_query("SELECT * FROM $db_forum_sections WHERE fs_id='$id'");
	sed_die(sed_sql_numrows($sql)==0);
	$row = sed_sql_fetchassoc($sql);

	$fs_id = $row['fs_id'];
	$fs_parentcat = $row['fs_parentcat'];  //New Sed 172
	$fs_state = $row['fs_state'];
	$fs_order = $row['fs_order'];
	$fs_title = $row['fs_title'];
	$fs_desc = $row['fs_desc'];
	$fs_category = $row['fs_category'];
	$fs_icon = $row['fs_icon'];
	$fs_autoprune = $row['fs_autoprune'];
	$fs_allowusertext = $row['fs_allowusertext'];
	$fs_allowbbcodes = $row['fs_allowbbcodes'];
	$fs_allowsmilies = $row['fs_allowsmilies'];
	$fs_allowprvtopics = $row['fs_allowprvtopics'];
	$fs_countposts = $row['fs_countposts'];

	$form_state = ($fs_state) ? "<input type=\"radio\" class=\"radio\" name=\"rstate\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rstate\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rstate\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rstate\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowusertext = ($fs_allowusertext) ? "<input type=\"radio\" class=\"radio\" name=\"rallowusertext\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowusertext\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowusertext\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowusertext\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowbbcodes = ($fs_allowbbcodes) ? "<input type=\"radio\" class=\"radio\" name=\"rallowbbcodes\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowbbcodes\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowbbcodes\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowbbcodes\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowsmilies = ($fs_allowsmilies) ? "<input type=\"radio\" class=\"radio\" name=\"rallowsmilies\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowsmilies\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowsmilies\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowsmilies\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowprvtopics = ($fs_allowprvtopics) ? "<input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_countposts = ($fs_countposts) ? "<input type=\"radio\" class=\"radio\" name=\"rcountposts\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rcountposts\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rcountposts\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rcountposts\" value=\"0\" checked=\"checked\" />".$L['No'];

	$adminpath[] = array(sed_url("admin", "m=forums&n=edit&id=".$id), sed_cc($fs_title));

	$adminmain .= "<h4>".$L['editdeleteentries']." : ".sed_cc($fs_title)."</h4>";

	$adminmain .= "<form id=\"updatesection\" action=\"".sed_url("admin", "m=forums&n=edit&a=update&id=".$fs_id."&".sed_xg())."\" method=\"post\">";
  
	$adminmain .= "<table class=\"cells striped\">";
	$adminmain .= "<tr><td>".$L['Section']." :</td><td>".$fs_id."</td></tr>";
	
  /* ======== New Sed 171 Subforums ========== */
	
	$adminmain .= "<tr><td>".$L['adm_parentcat']." :</td><td><select name=\"rparentcat\"><option value=\"0\">--</option>";	
	$sql = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections 
                        AS s LEFT JOIN sed_forum_structure AS n ON n.fn_code = s.fs_category 
                        WHERE fs_id <> '$id' AND fs_parentcat < 1 AND fs_category = '".$fs_category."' 
                        ORDER by fn_path ASC, fs_order ASC");
                        
	while ($row = sed_sql_fetchassoc($sql))
		{
			$parent_name = sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'], FALSE);		
      $selected = ($fs_parentcat == $row['fs_id']) ? " selected=\"selected\"" : "";
			$adminmain .= "<option value=\"".$row['fs_id']."\"".$selected.">".$parent_name."</option>";		
		}
	$adminmain .= "</select></td></tr>";

	/* ====================== */
		
	$adminmain .= "<tr><td>".$L['Category']." :</td><td>".sed_selectbox_forumcat($fs_category, 'rcat')."</td></tr>";
	$adminmain .= "<tr><td>".$L['Title']." :</td><td><input type=\"text\" class=\"text\" name=\"rtitle\" value=\"".sed_cc($fs_title)."\" size=\"56\" maxlength=\"128\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Description']." :</td><td><input type=\"text\" class=\"text\" name=\"rdesc\" value=\"".sed_cc($fs_desc)."\" size=\"56\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Icon']." :</td><td> <input type=\"text\" class=\"text\" name=\"ricon\" value=\"".sed_cc($fs_icon)."\" size=\"40\" maxlength=\"255\" />";
	if (file_exists($fs_icon))
    { $adminmain .= " <img src=\"".$fs_icon."\" alt=\"\" />"; }
	$adminmain .= "</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_diplaysignatures']." :</td><td>".$form_allowusertext."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_enablebbcodes']." :</td><td>".$form_allowbbcodes."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_enablesmilies']." :</td><td>".$form_allowsmilies."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_enableprvtopics']." :</td><td>".$form_allowprvtopics."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_countposts']." :</td><td>".$form_countposts."</td></tr>";
	$adminmain .= "<tr><td>".$L['Locked']." :</td><td>".$form_state."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_autoprune']." :</td><td><input type=\"text\" class=\"text\" name=\"rautoprune\" value=\"".$fs_autoprune."\" size=\"3\" maxlength=\"7\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['adm_postcounters']." :</td><td><a href=\"".sed_url("admin", "m=forums&n=edit&a=resync&id=".$fs_id."&".sed_xg())."\">".$L['Resync']."</a></td></tr>";
	$adminmain .= ($usr['isadmin']) ? "<tr><td>".$L['Delete']." :</td><td><a href=\"".sed_url("admin", "m=forums&n=edit&a=delete&id=".$fs_id."&".sed_xg())."\">".$out['img_delete']."</a></td></tr>" : '';
	$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit btn\" value=\"".$L['Update']."\" /></td></tr>";
	$adminmain .= "</table>";
	$adminmain .= "</form>";
	}

else
	{
	if ($a=='order')
		{
		$w = sed_import('w', 'G', 'ALP', 4);

		$sql = sed_sql_query("SELECT fs_order, fs_category FROM $db_forum_sections WHERE fs_id='".$id."'");
		sed_die(sed_sql_numrows($sql)==0);
		$row_cur = sed_sql_fetchassoc($sql);

		if ($w=='up')
			{
			$sql = sed_sql_query("SELECT fs_id, fs_order FROM $db_forum_sections WHERE fs_category='".$row_cur['fs_category']."' AND fs_order<'".$row_cur['fs_order']."' ORDER BY fs_order DESC LIMIT 1");
			}
		else
			{
			$sql = sed_sql_query("SELECT fs_id, fs_order FROM $db_forum_sections WHERE fs_category='".$row_cur['fs_category']."' AND fs_order>'".$row_cur['fs_order']."' ORDER BY fs_order ASC LIMIT 1");
			}

		if (sed_sql_numrows($sql)>0)
			{
			$row_oth = sed_sql_fetchassoc($sql);
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_order='".$row_oth['fs_order']."' WHERE fs_id='".$id."'");
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_order='".$row_cur['fs_order']."' WHERE fs_id='".$row_oth['fs_id']."'");
			}

		sed_redirect(sed_url("admin", "m=forums", "", true));
		exit;
		}
	elseif ($a=='add')
		{
		$g = array ('ntitle', 'ndesc', 'ncat');
		foreach($g as $k => $x) $$x = $_POST[$x];

		if (!empty($ntitle))
			{
			$sql1 = sed_sql_query("SELECT fs_order FROM $db_forum_sections WHERE fs_category='".sed_sql_prep($ncat)."' ORDER BY fs_order DESC LIMIT 1");
			if ($row1 = sed_sql_fetchassoc($sql1))
				{ $nextorder = $row1['fs_order']+1; }
			else
				{ $nextorder = 100; }

			$sql = sed_sql_query("INSERT INTO $db_forum_sections (fs_order, fs_title, fs_desc, fs_category, fs_icon, fs_autoprune, fs_allowusertext, fs_allowbbcodes, fs_allowsmilies, fs_allowprvtopics, fs_countposts) VALUES ('".(int)$nextorder."', '".sed_sql_prep($ntitle)."', '".sed_sql_prep($ndesc)."', '".sed_sql_prep($ncat)."', 'system/img/admin/forums.png', 0, 1, 1, 1, 0, 1)");

			$forumid = sed_sql_insertid();

			foreach($sed_groups as $k => $v)
				{
				if ($k==1 || $k==2)
					{
					$ins_auth = 1;
					$ins_lock = 254;
					}
				elseif ($k==3)
					{
					$ins_auth = 0;
					$ins_lock = 255;
					}
				elseif ($k==5)
					{
					$ins_auth = 255;
					$ins_lock = 255;
					}
				else
					{
					$ins_auth = 3;
					$ins_lock = ($k==4) ? 128 : 0;
					}

				$sql = sed_sql_query("INSERT into $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid) VALUES (".(int)$v['id'].", 'forums', ".(int)$forumid.", ".(int)$ins_auth.", ".(int)$ins_lock.", ".(int)$usr['id'].")");
				}
			sed_auth_reorder();
			sed_auth_clear('all');
			sed_redirect(sed_url("admin", "m=forums", "", true));
			}
		}

    $sql = sed_sql_query("SELECT s.*, n.* FROM $db_forum_sections AS s LEFT JOIN
    $db_forum_structure AS n ON n.fn_code=s.fs_category
    ORDER by fn_path ASC, fs_order ASC, fs_title ASC");
    
	$adminmain .= "<div class=\"sedtabs\">	
		<ul class=\"tabs\">
      <li><a href=\"".$sys['request_uri']."#tab1\" class=\"selected\">".$L['editdeleteentries']."</a></li>
      <li><a href=\"".$sys['request_uri']."#tab2\">".$L['addnewentry']."</a></li>
    </ul>    
    <div class=\"tab-box\">";

	$adminmain .= "<div id=\"tab1\" class=\"tabs\">";

	$adminmain .= "<h4>".$L['editdeleteentries']."</h4>";    

	$adminmain .= "<form id=\"updateorder\" action=\"".sed_url("admin", "m=forums&a=update")."\" method=\"post\">";

	$adminmain .= "<table class=\"cells striped\"><tr>";
	$adminmain .= "<td class=\"coltop\">".$L['Section']." ".$L['adm_clicktoedit']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Order']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['adm_enableprvtopics']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Topics']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Posts']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Views']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:80px;\">".$L['Rights']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:64px;\">".$L['Open']."</td>";
	$adminmain .= "</tr>";

	$prev_cat = '';
	$line = 1;

	while ($row = sed_sql_fetchassoc($sql))
		{
		$fs_id = $row['fs_id'];
		$fs_state = $row['fs_state'];
		$fs_order = $row['fs_order'];
		$fs_title = sed_cc($row['fs_title']);
		$fs_desc = sed_cc($row['fs_desc']);
		$fs_category = $row['fs_category'];

		if ($fs_category!=$prev_cat)
			{
			$adminmain .= "<tr><td colspan=\"8\"><strong><a href=\"".sed_url("admin", "m=forums&s=structure&n=options&id=".$row['fn_id'])."\">".sed_cc($row['fn_title']);
 			$adminmain .= " (".$row['fn_path'].")</a></strong></td></tr>";
			$prev_cat = $fs_category;
			$line = 1;
			}

		$adminmain .= "<tr>";
		$adminmain .= "<td><a href=\"".sed_url("admin", "m=forums&n=edit&id=".$fs_id)."\">".$fs_title."</a></td>";
		$adminmain .= "<td style=\"text-align:center;\">";
		$adminmain .= "<a href=\"".sed_url("admin", "m=forums&id=".$fs_id."&a=order&w=up")."\">$sed_img_up</a> ";
		$adminmain .= "<a href=\"".sed_url("admin", "m=forums&id=".$fs_id."&a=order&w=down")."\">$sed_img_down</a></td>";

		$adminmain .= "<td style=\"text-align:center;\">".$sed_yesno[$row['fs_allowprvtopics']]."</td>";
		$adminmain .= "<td style=\"text-align:right;\">".$row['fs_topiccount']."</td>";
		$adminmain .= "<td style=\"text-align:right;\">".$row['fs_postcount']."</td>";
		$adminmain .= "<td style=\"text-align:right;\">".$row['fs_viewcount']."</td>";
		$adminmain .= "<td style=\"text-align:center;\"><a href=\"".sed_url("admin", "m=rightsbyitem&ic=forums&io=".$row['fs_id'])."\"><img src=\"system/img/admin/rights2.png\" alt=\"\"></a></td>";
		$adminmain .= "<td style=\"text-align:center;\"><a href=\"".sed_url("forums", "m=topics&s=".$fs_id)."\"><img src=\"system/img/admin/jumpto.png\" alt=\"\"></a></a></td>";
 		$adminmain .= "</tr>";
 		$line++;
 		}

	$adminmain .= "<tr><td colspan=\"9\"><input type=\"submit\" class=\"submit btn\" value=\"".$L['Update']."\" /></td></tr>";
	$adminmain .= "</table>";
	$adminmain .= "</form>";
	
	$adminmain .= "</div>";
	
	$adminmain .= "<div id=\"tab2\" class=\"tabs\">";	
  
	$adminmain .= "<h4>".$L['addnewentry']."</h4>";  
	$adminmain .= "<form id=\"addsection\" action=\"".sed_url("admin", "m=forums&a=add")."\" method=\"post\">";
	$adminmain .= "<table class=\"cells striped\">";
	$adminmain .= "<tr><td>".$L['Category']." :</td><td>".sed_selectbox_forumcat('', 'ncat')."</td></tr>";
	$adminmain .= "<tr><td>".$L['Title']." :</td><td><input type=\"text\" class=\"text\" name=\"ntitle\" value=\"\" size=\"64\" maxlength=\"128\" /> ".$L['adm_required']."</td></tr>";
	$adminmain .= "<tr><td>".$L['Description']." :</td><td><input type=\"text\" class=\"text\" name=\"ndesc\" value=\"\" size=\"64\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit btn\" value=\"".$L['Add']."\" /></td></tr>";
	$adminmain .= "</table>";
	$adminmain .= "</form>"; 
	
	$adminmain .= "</div></div></div>"; 
	}

?>