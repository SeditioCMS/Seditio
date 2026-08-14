<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/adminqv/adminqv.php
Version=186
Updated=2026-aug-13
Type=Plugin
Author=Seditio Team
Description=Administration Quick View Dashboard
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=adminqv
Part=main
File=adminqv
Hooks=admin.home
Tags=
Order=10
Lock=0
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}

if ($f = sed_langfile('adminqv', 'plugin')) {
	require_once($f);
}

// ---------- Optimize DB Action ----------
if ($a == 'optimizedb') {
	sed_check_xg();
	$sql_tables = sed_sql_query("SHOW TABLES LIKE '" . $cfg['sqldbprefix'] . "%'");
	while ($row = sed_sql_fetchrow($sql_tables)) {
		sed_sql_query("OPTIMIZE TABLE `" . $row[0] . "`");
	}
	sed_redirect(sed_url("admin", "m=home", "", true), false, array('msg' => '923'));
	exit;
}

if (!function_exists('sed_calc_kpi_trend')) {
	function sed_calc_kpi_trend($cur, $prev) {
		if ($prev == 0) {
			if ($cur > 0) {
				return array('dir' => 'up', 'text' => '↗ +100%');
			} else {
				return array('dir' => 'neutral', 'text' => '0%');
			}
		}
		$diff = $cur - $prev;
		$pct = round(($diff / $prev) * 100);
		if ($pct > 0) {
			return array('dir' => 'up', 'text' => '↗ +' . $pct . '%');
		} elseif ($pct < 0) {
			return array('dir' => 'down', 'text' => '↘ ' . $pct . '%');
		} else {
			return array('dir' => 'neutral', 'text' => '0%');
		}
	}
}

$timeback = $sys['now_offset'] - (7 * 86400); // Current 7 days
$timeback_prev = $sys['now_offset'] - (14 * 86400); // Previous 7 days
$timeback_stats = 14;

// 1. Users
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_regdate > '$timeback'");
$newusers = (int)sed_sql_result($sql, 0, "COUNT(*)");

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_regdate >= '$timeback_prev' AND user_regdate <= '$timeback'");
$prevusers = (int)sed_sql_result($sql, 0, "COUNT(*)");
$trend_users = sed_calc_kpi_trend($newusers, $prevusers);

// 2. Pages
$newpages = 0;
$prevpages = 0;
if (sed_module_active('page')) {
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_date > '$timeback'");
	$newpages = (int)sed_sql_result($sql, 0, "COUNT(*)");

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_date >= '$timeback_prev' AND page_date <= '$timeback'");
	$prevpages = (int)sed_sql_result($sql, 0, "COUNT(*)");
}
$trend_pages = sed_calc_kpi_trend($newpages, $prevpages);

// 3. Forums
$newtopics = 0;
$newposts = 0;
$prevposts = 0;
if (sed_module_active('forums')) {
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_creationdate > '$timeback'");
	$newtopics = (int)sed_sql_result($sql, 0, "COUNT(*)");

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_updated > '$timeback'");
	$newposts = (int)sed_sql_result($sql, 0, "COUNT(*)");

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_updated >= '$timeback_prev' AND fp_updated <= '$timeback'");
	$prevposts = (int)sed_sql_result($sql, 0, "COUNT(*)");
}
$total_posts = $newtopics + $newposts;
$trend_posts = sed_calc_kpi_trend($total_posts, $prevposts);

// 4. Comments
$newcomments = 0;
$prevcomments = 0;
if (sed_plug_active('comments')) {
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_com WHERE com_date > '$timeback'");
	$newcomments = (int)sed_sql_result($sql, 0, "COUNT(*)");

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_com WHERE com_date >= '$timeback_prev' AND com_date <= '$timeback'");
	$prevcomments = (int)sed_sql_result($sql, 0, "COUNT(*)");
}
$trend_comments = sed_calc_kpi_trend($newcomments, $prevcomments);

$newpms = 0;
if (sed_module_active('pm')) {
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_date > '$timeback'");
	$newpms = (int)sed_sql_result($sql, 0, "COUNT(*)");
}

// Multi-period Hits statistics for Chart.js
$all_hits = array();
$sql = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name ASC");
while ($row = sed_sql_fetchassoc($sql)) {
	$all_hits[$row['stat_name']] = (int)$row['stat_value'];
}

// 1. By Days (last 14 days)
$days_labels = array();
$days_values = array();
for ($i = 13; $i >= 0; $i--) {
	$ts = mktime(0, 0, 0, date('n', $sys['now_offset']), date('j', $sys['now_offset']) - $i, date('Y', $sys['now_offset']));
	$d_key = date('Y-m-d', $ts);
	$days_labels[] = @date('d M', $ts);
	$days_values[] = isset($all_hits[$d_key]) ? $all_hits[$d_key] : 0;
}

// 2. By Weeks (last 8 weeks)
$weeks_labels = array();
$weeks_values = array();
for ($w = 7; $w >= 0; $w--) {
	$w_start = mktime(0, 0, 0, date('n', $sys['now_offset']), date('j', $sys['now_offset']) - (date('N', $sys['now_offset']) - 1) - ($w * 7), date('Y', $sys['now_offset']));
	$w_end = $w_start + (6 * 86400);
	$w_sum = 0;
	for ($d = 0; $d < 7; $d++) {
		$cur_ts = $w_start + ($d * 86400);
		$cur_key = date('Y-m-d', $cur_ts);
		if (isset($all_hits[$cur_key])) {
			$w_sum += $all_hits[$cur_key];
		}
	}
	$weeks_labels[] = @date('d M', $w_start) . ' - ' . @date('d M', $w_end);
	$weeks_values[] = $w_sum;
}

// 3. By Months (last 12 months)
$months_labels = array();
$months_values = array();
for ($m = 11; $m >= 0; $m--) {
	$m_ts = mktime(0, 0, 0, date('n', $sys['now_offset']) - $m, 1, date('Y', $sys['now_offset']));
	$m_prefix = date('Y-m', $m_ts);
	$m_sum = 0;
	foreach ($all_hits as $date_str => $val) {
		if (strpos($date_str, $m_prefix) === 0) {
			$m_sum += $val;
		}
	}
	$months_labels[] = @date('M Y', $m_ts);
	$months_values[] = $m_sum;
}

// 4. By Years (last 5 years)
$years_labels = array();
$years_values = array();
$cur_year = (int)date('Y', $sys['now_offset']);
for ($y = $cur_year - 4; $y <= $cur_year; $y++) {
	$y_prefix = (string)$y;
	$y_sum = 0;
	foreach ($all_hits as $date_str => $val) {
		if (strpos($date_str, $y_prefix) === 0) {
			$y_sum += $val;
		}
	}
	$years_labels[] = (string)$y;
	$years_values[] = $y_sum;
}

// Legacy hits_d array for backwards compatibility
$hits_d = array();
foreach ($days_labels as $idx => $label) {
	$hits_d[$label] = $days_values[$idx];
}
$hits_d_max = (count($days_values) > 0) ? max($days_values) : 1;

// 7-day daily activity breakdown for Chart.js
$act_labels = array();
$act_users = array();
$act_pages = array();
$act_posts = array();
$act_comments = array();

for ($i = 6; $i >= 0; $i--) {
	$day_start = mktime(0, 0, 0, date('n', $sys['now_offset']), date('j', $sys['now_offset']) - $i, date('Y', $sys['now_offset']));
	$day_end = $day_start + 86400;
	$act_labels[] = @date('d M', $day_start);

	$act_users[] = (int)sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_regdate >= $day_start AND user_regdate < $day_end"), 0, "COUNT(*)");
	$act_pages[] = sed_module_active('page') ? (int)sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_date >= $day_start AND page_date < $day_end"), 0, "COUNT(*)") : 0;
	$act_posts[] = sed_module_active('forums') ? (int)sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_updated >= $day_start AND fp_updated < $day_end"), 0, "COUNT(*)") : 0;
	$act_comments[] = sed_plug_active('comments') ? (int)sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_com WHERE com_date >= $day_start AND com_date < $day_end"), 0, "COUNT(*)") : 0;
}

// Database stats calculation
$tables = array();
$sql = sed_sql_query("SHOW TABLES");
while ($row = sed_sql_fetchrow($sql)) {
	$table_name = $row[0];
	$status = sed_sql_query("SHOW TABLE STATUS LIKE '$table_name'");
	$status1 = sed_sql_fetcharray($status);
	$tables[] = $status1;
}

$total_length = 0;
$total_rows = 0;
$total_index_length = 0;
$total_fragmented = 0;
$total_data_length = 0;

foreach ($tables as $i => $dat) {
	$table_length = $dat['Index_length'] + $dat['Data_length'];
	$total_length += $table_length;
	$total_rows += $dat['Rows'];
	$total_index_length += $dat['Index_length'];
	$total_fragmented += $dat['Data_free'];
	$total_data_length += $dat['Data_length'];
}

$mskin = sed_skinfile('adminqv', true, false);
if ($mskin === '') {
	$mskin = SED_ROOT . '/plugins/adminqv/tpl/adminqv.tpl';
}

$qv = new XTemplate($mskin);

$qv_size_opts = array('precision' => 1);
$qv->assign(array(
	"QV_NEWUSERS" => $newusers,
	"QV_NEWUSERS_URL" => sed_url("users", "f=all&s=regdate&w=desc"),
	"QV_NEWUSERS_TREND_DIR" => $trend_users['dir'],
	"QV_NEWUSERS_TREND_TEXT" => $trend_users['text'],
	"QV_NEWPAGES" => $newpages,
	"QV_NEWPAGES_URL" => sed_url("admin", "m=page"),
	"QV_NEWPAGES_TREND_DIR" => $trend_pages['dir'],
	"QV_NEWPAGES_TREND_TEXT" => $trend_pages['text'],
	"QV_NEWTOPICS" => $newtopics,
	"QV_NEWPOSTS" => ($newtopics + $newposts),
	"QV_NEWFORUMS_URL" => sed_url("forums"),
	"QV_NEWPOSTS_TREND_DIR" => $trend_posts['dir'],
	"QV_NEWPOSTS_TREND_TEXT" => $trend_posts['text'],
	"QV_NEWCOMMENTS" => $newcomments,
	"QV_NEWCOMMENTS_URL" => sed_url("admin", "m=comments"),
	"QV_NEWCOMMENTS_TREND_DIR" => $trend_comments['dir'],
	"QV_NEWCOMMENTS_TREND_TEXT" => $trend_comments['text'],
	"QV_NEWPMS" => $newpms,
	"QV_DB_ROWS" => $total_rows,
	"QV_DB_INDEXSIZE" => sed_format_size($total_index_length, $qv_size_opts),
	"QV_DB_DATASSIZE" => sed_format_size($total_data_length, $qv_size_opts),
	"QV_DB_TOTALSIZE" => sed_format_size($total_length, $qv_size_opts),
	"QV_DB_TOTALFRAGMENTED" => sed_format_size($total_fragmented, $qv_size_opts),
	"QV_OPTIMIZE_DB_URL" => sed_url("admin", "m=home&a=optimizedb&" . sed_xg()),
	"QV_QUICK_PAGEADD_URL" => sed_url("admin", "m=page&s=add"),
	"QV_QUICK_PAGEMGMT_URL" => sed_url("admin", "m=page"),
	"QV_QUICK_MENU_URL" => sed_url("admin", "m=menu"),
	"QV_QUICK_DIC_URL" => sed_url("admin", "m=dic"),
	"QV_QUICK_SKINEDEDIT_URL" => sed_url("admin", "m=manage&p=skineditor"),
	"QV_QUICK_BANLIST_URL" => sed_url("admin", "m=banlist"),
	"QV_QUICK_CACHE_URL" => sed_url("admin", "m=cache"),
	"QV_HITS_DAYS_JSON" => json_encode(array('labels' => $days_labels, 'values' => $days_values)),
	"QV_HITS_WEEKS_JSON" => json_encode(array('labels' => $weeks_labels, 'values' => $weeks_values)),
	"QV_HITS_MONTHS_JSON" => json_encode(array('labels' => $months_labels, 'values' => $months_values)),
	"QV_HITS_YEARS_JSON" => json_encode(array('labels' => $years_labels, 'values' => $years_values)),
	"QV_HITS_LABELS_JSON" => json_encode($days_labels),
	"QV_HITS_VALUES_JSON" => json_encode($days_values),
	"QV_ACTIVITY_LABELS_JSON" => json_encode($act_labels),
	"QV_ACTIVITY_USERS_JSON" => json_encode($act_users),
	"QV_ACTIVITY_PAGES_JSON" => json_encode($act_pages),
	"QV_ACTIVITY_POSTS_JSON" => json_encode($act_posts),
	"QV_ACTIVITY_COMMENTS_JSON" => json_encode($act_comments)
));

if (!$cfg['disablereg']) {
	$qv->parse("ADMIN_QV.ADMIN_QV_NEWUSERS");
}

if (sed_module_active('page')) {
	$qv->parse("ADMIN_QV.ADMIN_QV_NEWPAGES");
}

if (sed_module_active('forums')) {
	$qv->parse("ADMIN_QV.ADMIN_QV_NEWONFORUMS");
}

if (sed_plug_active('comments')) {
	$qv->parse("ADMIN_QV.ADMIN_QV_NEWCOMMENTS");
}

if (sed_module_active('pm')) {
	$qv->parse("ADMIN_QV.ADMIN_QV_NEWPMS");
}

$qv->parse("ADMIN_QV.ADMIN_QV_DB");
$t->assign("ADMIN_QV_DB", $qv->text("ADMIN_QV.ADMIN_QV_DB"));

if (!$cfg['disablehitstats']) {
	$qv->assign(array(
		"QV_HITS_URL" => sed_url("admin", "m=hits")
	));
	foreach ($hits_d as $day => $hits) {
		$qv->assign(array(
			"QV_HITS_PERCENTBAR" => floor(($hits / $hits_d_max) * 100),
			"QV_HITS_DAY" => $day,
			"QV_HITS_COUNT" => $hits
		));
		$qv->parse("ADMIN_QV.ADMIN_QV_HITS.ADMIN_QV_HITS_DAYLIST");
	}
	$qv->parse("ADMIN_QV.ADMIN_QV_HITS");
}

$qv->parse("ADMIN_QV");
$t->assign("ADMIN_QV", $qv->text("ADMIN_QV"));

// Register lightweight SedChart library from system assets folder
sed_add_javascript('system/assets/js/sedchart.js', true);
sed_add_css('plugins/adminqv/css/adminqv.css', true);

// Register inline JS for Hits chart
$js_hits_datasets = json_encode(array(
	'days' => array('labels' => $days_labels, 'values' => $days_values),
	'weeks' => array('labels' => $weeks_labels, 'values' => $weeks_values),
	'months' => array('labels' => $months_labels, 'values' => $months_values),
	'years' => array('labels' => $years_labels, 'values' => $years_values)
));

$js_period_titles = json_encode(array(
	'days' => $L['plu_hits_days'],
	'weeks' => $L['plu_hits_weeks'],
	'months' => $L['plu_hits_months'],
	'years' => $L['plu_hits_years']
));

$js_hits_label = json_encode($L['Hits']);

sed_add_javascript("
document.addEventListener('DOMContentLoaded', function () {
	var chartContainer = document.getElementById('seditioHitsChart');
	if (chartContainer && typeof SedChart !== 'undefined') {
		var hitsDatasets = {$js_hits_datasets};
		var periodTitles = {$js_period_titles};

		var hitsChart = new SedChart(chartContainer, {
			labels: hitsDatasets.days.labels,
			values: hitsDatasets.days.values,
			seriesLabel: {$js_hits_label}
		});

		var periodSelect = document.getElementById('hitsPeriodSelect');
		if (periodSelect) {
			periodSelect.addEventListener('change', function() {
				var period = this.value;
				if (hitsDatasets[period]) {
					hitsChart.setData(hitsDatasets[period].labels, hitsDatasets[period].values);

					var titleEl = document.getElementById('hitsChartTitle');
					if (titleEl && periodTitles[period]) {
						titleEl.textContent = periodTitles[period];
					}
				}
			});
		}
	}
});
");
