<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/revisions/lang/revisions.en.lang.php
Version=186
Updated=2026-oct-01
Type=Plugin
Author=Seditio Team
Description=English language file for revisions plugin
[END_SED]
==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}

$L['Revisions'] = 'Revisions';
$L['rev_title'] = 'Revisions & Checkpoints';
$L['rev_revision'] = 'Revision';
$L['rev_version'] = 'Version';
$L['rev_date'] = 'Date & Time';
$L['rev_author'] = 'Author';
$L['rev_comment'] = 'Comment';
$L['rev_actions'] = 'Actions';
$L['rev_diff'] = 'Compare';
$L['rev_restore'] = 'Rollback';
$L['rev_restore_confirm'] = 'Are you sure you want to restore this version? The current state will be automatically saved as a new checkpoint.';
$L['rev_restored_success'] = 'Item successfully restored to version #%1$s';
$L['rev_auto_edit_comment'] = 'Automatic checkpoint before save';
$L['rev_auto_restore_comment'] = 'Snapshot before rollback to version #%1$s';
$L['rev_clean_older_than'] = 'Clean revisions older than %1$s days';
$L['rev_total_count'] = 'Total checkpoints in database';
$L['rev_total_size'] = 'Total data size';
$L['rev_no_revisions'] = 'No saved revisions';
$L['rev_all_revisions'] = 'Revisions';
$L['rev_maintenance'] = 'Cleanup';
$L['rev_prune_custom_title'] = 'Prune old checkpoints';
$L['rev_prune_custom_desc'] = 'Delete saved versions older than specified number of days';
$L['rev_prune_older_than_days'] = 'Older than (days)';
$L['rev_prune_btn'] = 'Prune';
$L['rev_prune_quick_title'] = 'Quick interval prune';
$L['rev_prune_quick_desc'] = 'Delete checkpoints older than 30, 60 or 90 days';
$L['rev_optimize_title'] = 'Optimize and compact database table';
$L['rev_optimize_desc'] = 'Defragment table and reclaim physical disk space after record deletions';
$L['rev_optimize_btn'] = 'Optimize';
$L['rev_wipeall_title'] = 'Purge all revisions';
$L['rev_wipeall_desc'] = 'Permanently delete all checkpoints for all objects';
$L['rev_wipeall_confirm'] = 'WARNING: All version history for all objects will be permanently deleted. Continue?';
$L['rev_diff_title'] = 'Comparing version #%1$s with current state';
$L['rev_diff_old_version'] = 'Version #%1$s (%2$s)';
$L['rev_diff_current_version'] = 'Current state';
$L['rev_diff_no_changes'] = 'No differences found in selected fields.';
$L['rev_diff_legend_del'] = 'Removed text';
$L['rev_diff_legend_ins'] = 'Added text';
$L['rev_item_id'] = 'Item ID';
$L['rev_entity_type'] = 'Entity type';
$L['rev_filter_type'] = 'Filter by type';
$L['rev_filter_author'] = 'Filter by author';
$L['rev_delete_confirm'] = 'Delete this checkpoint?';
$L['rev_prune_success'] = 'Pruned obsolete checkpoints: %1$s';
$L['rev_deleted_success'] = 'Checkpoint #%1$s successfully deleted';
$L['rev_back_to_edit'] = 'Back to edit';
$L['rev_back_to_list'] = 'Back to revisions list';
$L['rev_view_details'] = 'Details';
$L['rev_current_label'] = 'current';
$L['rev_size'] = 'Data size';
