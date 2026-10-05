<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=modules/page/page.revisions.php
Version=186
Updated=2026-oct-01
Type=Module
Author=Seditio Team
Description=Registers Page and Structure entities with Revisions plugin
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=page
Part=revisions
File=page.revisions
Hooks=revisions.api
Order=10
Lock=0
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}

global $sed_revisions_types, $L;

// 1. Register Page entity
$sed_revisions_types['page'] = array(
	'title'         => isset($L['Page']) ? $L['Page'] : 'Page',
	'icon'          => 'page.png',
	'edit_url'      => 'admin/page?m=edit&id={ID}',
	'restore'       => 'sed_revisions_page_restore',
	'ignore_fields' => array('page_count', 'page_filecount', 'page_rating', 'page_comcount'),
	'diff_fields'   => array(
		'page_title'        => isset($L['pageedit_title']) ? $L['pageedit_title'] : 'Title',
		'page_desc'         => isset($L['pageedit_description']) ? $L['pageedit_description'] : 'Description',
		'page_text'         => isset($L['pageedit_bodyofthepage']) ? $L['pageedit_bodyofthepage'] : 'Body',
		'page_text2'        => (isset($L['pageedit_bodyofthepage']) ? $L['pageedit_bodyofthepage'] : 'Body') . ' 2',
		'page_alias'        => isset($L['pageedit_alias']) ? $L['pageedit_alias'] : 'Alias',
		'page_cat'          => isset($L['pageedit_category']) ? $L['pageedit_category'] : 'Category',
		'page_author'       => isset($L['pageedit_author']) ? $L['pageedit_author'] : 'Author',
		'page_seo_title'    => 'SEO Title',
		'page_seo_desc'     => 'SEO Description',
		'page_seo_keywords' => 'SEO Keywords',
		'page_seo_h1'       => 'SEO H1'
	)
);

// 2. Register Structure entity
$sed_revisions_types['structure'] = array(
	'title'         => isset($L['Structure']) ? $L['Structure'] : 'Structure',
	'icon'          => 'folders.png',
	'edit_url'      => 'admin/page?mn=structure&n=options&id={ID}',
	'restore'       => 'sed_revisions_structure_restore',
	'ignore_fields' => array('structure_order'),
	'diff_fields'   => array(
		'structure_title'        => isset($L['Title']) ? $L['Title'] : 'Title',
		'structure_desc'         => isset($L['Description']) ? $L['Description'] : 'Description',
		'structure_text'         => isset($L['Text']) ? $L['Text'] : 'Text',
		'structure_path'         => isset($L['Path']) ? $L['Path'] : 'Path',
		'structure_tpl'          => isset($L['adm_tpl_mode']) ? $L['adm_tpl_mode'] : 'Template',
		'structure_icon'         => isset($L['Icon']) ? $L['Icon'] : 'Icon',
		'structure_thumb'        => isset($L['Thumbnail']) ? $L['Thumbnail'] : 'Thumbnail',
		'structure_seo_title'    => 'SEO Title',
		'structure_seo_desc'     => 'SEO Description',
		'structure_seo_keywords' => 'SEO Keywords',
		'structure_seo_h1'       => 'SEO H1'
	)
);

/**
 * Restore page from revision snapshot
 *
 * @param array $snapshot_data Raw snapshot data
 * @param mixed $itemid Page ID
 * @param array $res Full revision record
 * @return bool
 */
function sed_revisions_page_restore($snapshot_data, $itemid, $res = array())
{
	global $db_pages, $sed_revisions_types, $L;

	$page_id = (int)$itemid;
	if ($page_id <= 0 || !is_array($snapshot_data)) {
		return false;
	}

	// 1. Snapshot current state before rolling back
	$sql_curr = sed_sql_query("SELECT * FROM $db_pages WHERE page_id = '$page_id' LIMIT 1");
	if ($curr = sed_sql_fetchassoc($sql_curr)) {
		$version_label = !empty($res['rev_version']) ? $res['rev_version'] : '';
		$restore_comment = sprintf($L['rev_auto_restore_comment'], $version_label);
		sed_revision_add('page', $page_id, $curr['page_title'], $curr, $restore_comment);
	}

	// 2. Build update query ignoring primary key and counter fields
	$ignore = isset($sed_revisions_types['page']['ignore_fields']) 
		? $sed_revisions_types['page']['ignore_fields'] 
		: array('page_count', 'page_filecount', 'page_rating', 'page_comcount');

	$update_fields = array();
	foreach ($snapshot_data as $column => $value) {
		if ($column === 'page_id' || in_array($column, $ignore)) {
			continue;
		}
		$update_fields[] = $column . " = '" . sed_sql_prep($value) . "'";
	}

	if (!empty($update_fields)) {
		sed_sql_query("UPDATE $db_pages SET " . implode(', ', $update_fields) . " WHERE page_id = '$page_id'");
		sed_log("Page #" . $page_id . " restored from revision #" . (!empty($res['rev_id']) ? $res['rev_id'] : ''), 'adm');
		sed_page_clear_menu_cache();
		return true;
	}

	return false;
}

/**
 * Restore structure category from revision snapshot
 *
 * @param array $snapshot_data Raw snapshot data
 * @param mixed $itemid Structure ID or Code
 * @param array $res Full revision record
 * @return bool
 */
function sed_revisions_structure_restore($snapshot_data, $itemid, $res = array())
{
	global $db_structure, $sed_revisions_types, $L;

	if (!is_array($snapshot_data)) {
		return false;
	}

	$where_clause = is_numeric($itemid) 
		? "structure_id = '" . (int)$itemid . "'" 
		: "structure_code = '" . sed_sql_prep($itemid) . "'";

	// 1. Snapshot current state before rolling back
	$sql_curr = sed_sql_query("SELECT * FROM $db_structure WHERE $where_clause LIMIT 1");
	if ($curr = sed_sql_fetchassoc($sql_curr)) {
		$target_id = (string)$curr['structure_id'];
		$version_label = !empty($res['rev_version']) ? $res['rev_version'] : '';
		$restore_comment = sprintf($L['rev_auto_restore_comment'], $version_label);
		sed_revision_add('structure', $target_id, $curr['structure_title'], $curr, $restore_comment);
	}

	// 2. Build update query ignoring primary key and order fields
	$ignore = isset($sed_revisions_types['structure']['ignore_fields']) 
		? $sed_revisions_types['structure']['ignore_fields'] 
		: array('structure_order');

	$update_fields = array();
	foreach ($snapshot_data as $column => $value) {
		if ($column === 'structure_id' || in_array($column, $ignore)) {
			continue;
		}
		$update_fields[] = $column . " = '" . sed_sql_prep($value) . "'";
	}

	if (!empty($update_fields)) {
		sed_sql_query("UPDATE $db_structure SET " . implode(', ', $update_fields) . " WHERE $where_clause");
		sed_auth_clear('all');
		sed_cache_clear('sed_cat');
		sed_page_clear_menu_cache();
		return true;
	}

	return false;
}
