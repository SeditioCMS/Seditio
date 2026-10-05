<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/revisions/revisions.admin.plug.php
Version=186
Updated=2026-oct-05
Type=Plugin
Author=Seditio Team
Description=Admin controller for revisions plugin
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=revisions
Part=admin.plug
Hooks=admin.plug
File=revisions.admin.plug
Order=15
Lock=0
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) {
	die('Wrong URL.');
}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('plug', 'revisions');
sed_block($usr['isadmin']);

global $db_revisions, $cfg, $L;

$types = sed_revisions_load_types();

// ---------- Breadcrumbs
$urlpaths = array();
$urlpaths[sed_url("admin", "m=manage")] = $L['adm_manage'];
$urlpaths[sed_url("admin", "m=revisions")] = $L['Revisions'];

$admintitle = $L['Revisions'];

$s = sed_import('s', 'G', 'ALP', 16);
$s = empty($s) ? 'list' : $s;

$a = sed_import('a', 'G', 'ALP', 24);
$id = sed_import('id', 'G', 'INT');
$type = sed_import('type', 'G', 'ALP', 32);
$type = (empty($type) || $type === 'all') ? 'all' : $type;
$itemid = sed_import('itemid', 'G', 'TXT', 64);
$q = sed_import('q', 'G', 'TXT', 128);
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int)$d;

$num = sed_import('num', 'G', 'TXT', 8);
$perpage_values = array(
	'5'   => '5',
	'10'  => '10',
	'15'  => '15',
	'20'  => '20',
	'30'  => '30',
	'40'  => '40',
	'50'  => '50',
	'60'  => '60',
	'70'  => '70',
	'80'  => '80',
	'100' => '100',
	'all' => $L['All']
);
if (empty($num) || !isset($perpage_values[$num])) {
	$num = '20';
}
$maxrowsperpage = ($num === 'all') ? 0 : (int)$num;

// -------------------------------------------------------------
// Action Handlers
// -------------------------------------------------------------

if ($a == 'restore') {
	sed_check_xg();
	$rev = sed_revision_get($id);
	if ($rev) {
		if (sed_revision_restore($id)) {
			if ($rev['rev_type'] === 'page') {
				sed_redirect(sed_url('admin', 'm=page&s=edit&id=' . (int)$rev['rev_itemid'], '', true), false, array('msg' => '917'));
				exit;
			} elseif ($rev['rev_type'] === 'structure') {
				global $db_structure;
				$sql_chk = sed_sql_query("SELECT structure_id FROM $db_structure WHERE structure_code = '" . sed_sql_prep($rev['rev_itemid']) . "' OR structure_id = '" . (int)$rev['rev_itemid'] . "' LIMIT 1");
				if ($row_chk = sed_sql_fetchassoc($sql_chk)) {
					sed_redirect(sed_url('admin', 'm=page&mn=structure&n=options&id=' . (int)$row_chk['structure_id'], '', true), false, array('msg' => '917'));
					exit;
				}
			}
			sed_redirect(sed_url("admin", "m=revisions&type=" . $rev['rev_type'] . "&itemid=" . $rev['rev_itemid'], "", true), false, array('msg' => '917'));
			exit;
		}
	}
	sed_redirect(sed_url("admin", "m=revisions", "", true), false, array('msg' => '900'));
	exit;
} elseif ($a == 'delete') {
	sed_check_xg();
	$rev = sed_revision_get($id);
	if ($rev) {
		sed_revision_delete($id);
		sed_redirect(sed_url("admin", "m=revisions&type=" . $rev['rev_type'] . "&itemid=" . $rev['rev_itemid'], "", true), false, array('msg' => '302'));
		exit;
	}
	sed_redirect(sed_url("admin", "m=revisions", "", true), false, array('msg' => '302'));
	exit;
} elseif ($a == 'prune') {
	sed_check_xg();
	$days = sed_import('days', 'P', 'INT');
	if (empty($days)) {
		$days = sed_import('days', 'G', 'INT');
	}
	if ($days > 0) {
		$deleted = sed_revision_prune($days);
	}
	sed_redirect(sed_url("admin", "m=revisions&s=cleanup", "", true), false, array('msg' => '302'));
	exit;
} elseif ($a == 'wipeall') {
	sed_check_xg();
	sed_sql_query("TRUNCATE TABLE $db_revisions");
	sed_redirect(sed_url("admin", "m=revisions&s=cleanup", "", true), false, array('msg' => '302'));
	exit;
} elseif ($a == 'optimize') {
	sed_check_xg();
	sed_sql_query("OPTIMIZE TABLE $db_revisions");
	sed_redirect(sed_url("admin", "m=revisions&s=cleanup", "", true), false, array('msg' => '917'));
	exit;
}

// -------------------------------------------------------------
// Sub-views
// -------------------------------------------------------------

if ($s === 'diff') {
	// ================= Diff View ==============================
	$urlpaths[sed_url("admin", "m=revisions&s=diff&id=" . $id)] = $L['rev_diff'];

	$rev = sed_revision_get($id);
	if (!$rev) {
		sed_die($L['rev_no_revisions']);
	}

	$t = new XTemplate(sed_skinfile(array('revisions', 'diff'), true));

	$diff_type = $rev['rev_type'];
	$diff_itemid = $rev['rev_itemid'];
	$old_data = $rev['rev_datas'];
	$return = sed_import('return', 'G', 'TXT');
	$return_param = !empty($return) ? '&return=' . urlencode($return) : '';

	// Load current live data from DB to compare against
	$current_live_data = array();
	$edit_url = '';

	if ($diff_type === 'page') {
		global $db_pages;
		$sql_curr = sed_sql_query("SELECT * FROM $db_pages WHERE page_id = '" . (int)$diff_itemid . "' LIMIT 1");
		$current_live_data = sed_sql_fetchassoc($sql_curr);
		$edit_url = sed_url('admin', 'm=page&s=edit&id=' . (int)$diff_itemid);
		$back_url = sed_url('admin', 'm=revisions&type=' . $rev['rev_type'] . '&itemid=' . $rev['rev_itemid']);
	} elseif ($diff_type === 'structure') {
		global $db_structure;
		$sql_curr = sed_sql_query("SELECT * FROM $db_structure WHERE structure_code = '" . sed_sql_prep($diff_itemid) . "' OR structure_id = '" . (int)$diff_itemid . "' LIMIT 1");
		$current_live_data = sed_sql_fetchassoc($sql_curr);
		$struct_id = isset($current_live_data['structure_id']) ? (int)$current_live_data['structure_id'] : (int)$diff_itemid;
		$edit_url = !empty($struct_id) ? sed_url('admin', 'm=page&mn=structure&n=options&id=' . $struct_id) : '';
		$back_url = sed_url('admin', 'm=revisions&type=' . $rev['rev_type'] . '&itemid=' . $rev['rev_itemid']);
	} else {
		$back_url = sed_url('admin', 'm=revisions&type=' . $rev['rev_type'] . '&itemid=' . $rev['rev_itemid']);
	}

	$type_config = isset($types[$diff_type]) ? $types[$diff_type] : array();
	$diff_fields = isset($type_config['diff_fields']) ? $type_config['diff_fields'] : array();

	// If no custom fields registered, compare all non-ignored scalar keys
	if (empty($diff_fields) && is_array($old_data)) {
		foreach ($old_data as $k => $v) {
			if (is_scalar($v)) {
				$diff_fields[$k] = $k;
			}
		}
	}

	$has_changes = false;

	foreach ($diff_fields as $field_key => $field_title) {
		$val_old = isset($old_data[$field_key]) ? (string)$old_data[$field_key] : '';
		$val_new = isset($current_live_data[$field_key]) ? (string)$current_live_data[$field_key] : '';

		if ($val_old !== $val_new) {
			$has_changes = true;
			$line_diff = sed_revisions_compute_line_diff($val_old, $val_new);

			$t->assign(array(
				'REVISIONS_DIFF_FIELD_CODE' => $field_key,
				'REVISIONS_DIFF_FIELD_TITLE' => $field_title
			));

			foreach ($line_diff as $line) {
				$line_text = htmlspecialchars($line['val']);
				if ($line['type'] === 'same') {
					$t->assign('REVISIONS_DIFF_LINE_VAL', $line_text);
					$t->parse('ADMIN_REVISIONS_DIFF.REVISIONS_DIFF_FIELDS.REVISIONS_DIFF_ROW.REVISIONS_DIFF_LINES.LINE_SAME');
				} elseif ($line['type'] === 'del') {
					$t->assign('REVISIONS_DIFF_LINE_VAL', $line_text);
					$t->parse('ADMIN_REVISIONS_DIFF.REVISIONS_DIFF_FIELDS.REVISIONS_DIFF_ROW.REVISIONS_DIFF_LINES.LINE_DEL');
				} elseif ($line['type'] === 'add') {
					$t->assign('REVISIONS_DIFF_LINE_VAL', $line_text);
					$t->parse('ADMIN_REVISIONS_DIFF.REVISIONS_DIFF_FIELDS.REVISIONS_DIFF_ROW.REVISIONS_DIFF_LINES.LINE_ADD');
				}
				$t->parse('ADMIN_REVISIONS_DIFF.REVISIONS_DIFF_FIELDS.REVISIONS_DIFF_ROW.REVISIONS_DIFF_LINES');
			}

			$t->parse('ADMIN_REVISIONS_DIFF.REVISIONS_DIFF_FIELDS.REVISIONS_DIFF_ROW');
		}
	}

	$t->assign(array(
		'REVISIONS_DIFF_TITLE' => sprintf($L['rev_diff_title'], $rev['rev_version']),
		'REVISIONS_DIFF_OLD_VERSION' => $rev['rev_version'],
		'REVISIONS_DIFF_OLD_DATE' => sed_build_date($cfg['dateformat'], $rev['rev_date']),
		'REVISIONS_DIFF_OLD_AUTHOR' => sed_cc($rev['rev_user_name']),
		'REVISIONS_DIFF_OLD_COMMENT' => sed_cc($rev['rev_comment']),
		'REVISIONS_DIFF_RESTORE_URL' => sed_url('admin', 'm=revisions&a=restore&id=' . $rev['rev_id'] . '&' . sed_xg()),
		'REVISIONS_DIFF_BACK_URL' => $back_url,
		'REVISIONS_DIFF_EDIT_URL' => $edit_url
	));

	$t->parse('ADMIN_REVISIONS_DIFF.REVISIONS_DIFF_INFO');

	if ($has_changes) {
		$t->parse('ADMIN_REVISIONS_DIFF.REVISIONS_DIFF_FIELDS');
	} else {
		$t->parse('ADMIN_REVISIONS_DIFF.REVISIONS_DIFF_NO_CHANGES');
	}

	$t->parse('ADMIN_REVISIONS_DIFF');
	$adminmain = $t->text('ADMIN_REVISIONS_DIFF');

} else {
	// ================= Main Revisions List / Cleanup ==============================
	$t = new XTemplate(sed_skinfile(array('revisions', 'admin'), true));

	$t->assign(array(
		'ADMIN_REVISIONS_TITLE' => $L['Revisions'],
		'REVISIONS_SUBNAV_ALL_URL' => sed_url('admin', 'm=revisions'),
		'REVISIONS_SUBNAV_ALL_SELECTED' => ($s === 'list' && $type === 'all') ? 'selected' : '',
		'REVISIONS_SUBNAV_MAINTENANCE_URL' => sed_url('admin', 'm=revisions&s=cleanup'),
		'REVISIONS_SUBNAV_MAINTENANCE_SELECTED' => ($s === 'cleanup') ? 'selected' : '',
		'REVISIONS_CONFIG_URL' => sed_url('admin', 'm=config&n=edit&o=plug&p=revisions')
	));

	if ($s === 'cleanup') {
		$urlpaths[sed_url("admin", "m=revisions&s=cleanup")] = $L['rev_maintenance'];

		$sql_count = sed_sql_query("SELECT COUNT(*) AS total, SUM(LENGTH(rev_datas)) AS total_size FROM $db_revisions");
		$row_stat = sed_sql_fetchassoc($sql_count);
		$total_revs = (int)$row_stat['total'];
		$total_size = sed_format_size((int)$row_stat['total_size']);

		$days_input = sed_textbox('days', '30', 5, 5, 'form-control', false, 'number', array('min' => '1', 'max' => '3650', 'style' => 'width:80px; display:inline-block; margin:0 8px;'));

		$t->assign(array(
			'ADMIN_REVISIONS_TOTALITEMS' => $total_revs,
			'ADMIN_REVISIONS_TOTALSIZE' => $total_size,
			'REVISIONS_PRUNE_DAYS_INPUT' => $days_input,
			'REVISIONS_PRUNE_ACTION_URL' => sed_url('admin', 'm=revisions&a=prune&' . sed_xg()),
			'REVISIONS_PRUNE_30_URL' => sed_url('admin', 'm=revisions&a=prune&days=30&' . sed_xg()),
			'REVISIONS_PRUNE_60_URL' => sed_url('admin', 'm=revisions&a=prune&days=60&' . sed_xg()),
			'REVISIONS_PRUNE_90_URL' => sed_url('admin', 'm=revisions&a=prune&days=90&' . sed_xg()),
			'REVISIONS_OPTIMIZE_URL' => sed_url('admin', 'm=revisions&a=optimize&' . sed_xg()),
			'REVISIONS_WIPEALL_URL' => sed_url('admin', 'm=revisions&a=wipeall&' . sed_xg())
		));

		$t->parse('ADMIN_REVISIONS.REVISIONS_MAINTENANCE');
	} else {
		// List view with filters
		$where = array();
		if ($type !== 'all' && !empty($type)) {
			$where[] = "rev_type = '" . sed_sql_prep($type) . "'";
		}
		if (!empty($itemid)) {
			$where[] = "rev_itemid = '" . sed_sql_prep($itemid) . "'";
		}
		if (!empty($q)) {
			$where[] = "(rev_title LIKE '%" . sed_sql_prep($q) . "%' OR rev_comment LIKE '%" . sed_sql_prep($q) . "%' OR rev_itemid = '" . sed_sql_prep($q) . "')";
		}

		$where_str = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

		$sql_total = sed_sql_query("SELECT COUNT(*) AS total FROM $db_revisions $where_str");
		$totalitems = (int)sed_sql_result($sql_total, 0, 'total');

		// Type dropdown options for header right
		$type_options = array('all' => $L['All']);
		foreach ($types as $type_code => $type_info) {
			$type_options[$type_code] = isset($type_info['title']) ? $type_info['title'] : $type_code;
		}

		$type_base_url = sed_url("admin", "m=revisions" . (!empty($q) ? "&q=" . urlencode($q) : "") . ($num !== '20' ? "&num=" . $num : ""));
		$type_delim = (strpos($type_base_url, '?') !== false) ? '&' : '?';
		$type_select = sed_selectbox($type, 'type', $type_options, false, true, false, array('onchange' => 'location.href=\'' . $type_base_url . $type_delim . 'type=\' + this.value;'));

		$search_input = sed_textbox('q', $q, 30, 128, 'form-control', false, 'text', array('placeholder' => $L['Search'] . '...'));
		$perpage_select_top = sed_selectbox($num, 'num', $perpage_values, false, true, false, array('onchange' => 'this.form.submit();'));

		$search_action_url = sed_url('admin', 'm=revisions');
		$search_reset_url = sed_url('admin', 'm=revisions' . ($type !== 'all' ? '&type=' . $type : ''));

		$t->assign(array(
			'ADMIN_REVISIONS_TOTALITEMS' => $totalitems,
			'ADMIN_REVISIONS_TYPE_SELECT' => $type_select,
			'ADMIN_REVISIONS_SEARCH_INPUT' => $search_input,
			'ADMIN_REVISIONS_PERPAGE_TOP' => $perpage_select_top,
			'ADMIN_REVISIONS_SEARCH_ACTION_URL' => $search_action_url,
			'ADMIN_REVISIONS_SEARCH_RESET_URL' => $search_reset_url,
			'ADMIN_REVISIONS_SEARCH_QUERY' => $q,
			'ADMIN_REVISIONS_CURRENT_TYPE' => $type,
			'ADMIN_REVISIONS_HAS_FILTER' => (!empty($q) || $type !== 'all')
		));

		if ($totalitems > 0) {
			$limit_str = ($maxrowsperpage > 0) ? " LIMIT $d, $maxrowsperpage" : "";
			$sql = sed_sql_query("SELECT *, LENGTH(rev_datas) as data_size FROM $db_revisions $where_str ORDER BY rev_date DESC, rev_id DESC" . $limit_str);

			while ($row = sed_sql_fetchassoc($sql)) {
				$type_code = $row['rev_type'];
				$type_name = isset($types[$type_code]['title']) ? $types[$type_code]['title'] : $type_code;

				$edit_url = '';
				if ($type_code === 'page') {
					$edit_url = sed_url('admin', 'm=page&s=edit&id=' . (int)$row['rev_itemid']);
				} elseif ($type_code === 'structure') {
					global $db_structure;
					$sql_st = sed_sql_query("SELECT structure_id FROM $db_structure WHERE structure_code = '" . sed_sql_prep($row['rev_itemid']) . "' OR structure_id = '" . (int)$row['rev_itemid'] . "' LIMIT 1");
					if ($st_row = sed_sql_fetchassoc($sql_st)) {
						$edit_url = sed_url('admin', 'm=page&mn=structure&n=options&id=' . (int)$st_row['structure_id']);
					}
				}

				$user_link = !empty($row['rev_userid']) 
					? "<a href=\"" . sed_url('users', 'm=details&id=' . $row['rev_userid']) . "\">" . sed_cc($row['rev_user_name']) . "</a>" 
					: sed_cc($row['rev_user_name']);

				$t->assign(array(
					'REVISIONS_LIST_ID' => $row['rev_id'],
					'REVISIONS_LIST_TYPE' => $type_code,
					'REVISIONS_LIST_TYPE_TITLE' => $type_name,
					'REVISIONS_LIST_ITEMID' => $row['rev_itemid'],
					'REVISIONS_LIST_TITLE' => sed_cc($row['rev_title']),
					'REVISIONS_LIST_VERSION' => $row['rev_version'],
					'REVISIONS_LIST_DATE' => sed_build_date($cfg['dateformat'], $row['rev_date']),
					'REVISIONS_LIST_AUTHOR' => $user_link,
					'REVISIONS_LIST_COMMENT' => sed_cc($row['rev_comment']),
					'REVISIONS_LIST_SIZE' => sed_format_size($row['data_size']),
					'REVISIONS_LIST_EDIT_URL' => $edit_url,
					'REVISIONS_LIST_DIFF_URL' => sed_url('admin', 'm=revisions&s=diff&id=' . $row['rev_id']),
					'REVISIONS_LIST_RESTORE_URL' => sed_url('admin', 'm=revisions&a=restore&id=' . $row['rev_id'] . '&' . sed_xg()),
					'REVISIONS_LIST_DELETE_URL' => sed_url('admin', 'm=revisions&a=delete&id=' . $row['rev_id'] . '&' . sed_xg())
				));

				$t->parse('ADMIN_REVISIONS.REVISIONS_LIST.REVISIONS_ITEMS.REVISIONS_ROW');
			}

			// Pagination
			if ($maxrowsperpage > 0) {
				$page_url_params = "m=revisions" . ($type !== 'all' ? "&type=" . $type : "") . (!empty($itemid) ? "&itemid=" . $itemid : "") . (!empty($q) ? "&q=" . urlencode($q) : "") . ($num !== '20' ? "&num=" . $num : "");
				$pagination = sed_pagination(sed_url('admin', $page_url_params), $d, $totalitems, $maxrowsperpage);
				list($pageprev, $pagenext) = sed_pagination_pn(sed_url('admin', $page_url_params), $d, $totalitems, $maxrowsperpage, TRUE);

				$t->assign(array(
					'REVISIONS_PAGINATION' => $pagination,
					'REVISIONS_PAGEPREV' => $pageprev,
					'REVISIONS_PAGENEXT' => $pagenext
				));

				if (!empty($pagination)) {
					$t->parse('ADMIN_REVISIONS.REVISIONS_LIST.REVISIONS_ITEMS.REVISIONS_PAGINATION_BM');
				}
			}

			$t->parse('ADMIN_REVISIONS.REVISIONS_LIST.REVISIONS_ITEMS');
		} else {
			$t->parse('ADMIN_REVISIONS.REVISIONS_LIST.REVISIONS_NO_ITEMS');
		}

		$t->parse('ADMIN_REVISIONS.REVISIONS_LIST');
	}

	$t->parse('ADMIN_REVISIONS');
	$adminmain = $t->text('ADMIN_REVISIONS');
}
