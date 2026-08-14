<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=system/core/admin/admin.hits.inc.php
Version=186
Updated=2026-aug-14
Type=Core.admin
Author=Seditio Team
Description=Hits statistics module with SedChart, year, month and week filters
[END_SED]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) {
	die('Wrong URL.');
}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['auth_read']);

// Register SedChart library from system assets
sed_add_javascript('system/assets/js/sedchart.js', true);

// ---------- Breadcrumbs
$urlpaths = array();
$urlpaths[sed_url("admin", "m=manage")] = $L['adm_manage'];
$urlpaths[sed_url("admin", "m=hits")] = $L['Hits'];

$admintitle = $L['Hits'];

$t = new XTemplate(sed_skinfile('admin.hits', false, true));

// Query all statistics from DB
$sql = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name ASC");
$sqlmax = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_value DESC LIMIT 1");
$rowmax = sed_sql_fetchassoc($sqlmax);
$max_date = isset($rowmax['stat_name']) ? $rowmax['stat_name'] : '';
$max_hits = isset($rowmax['stat_value']) ? (int)$rowmax['stat_value'] : 0;

$L['adm_maxhits'] = (empty($L['adm_maxhits'])) ? "Maximum hitcount was reached %1\$s, %2\$s pages displayed this day." : $L['adm_maxhits'];

$all_stats = array();
$years_list = array();
$months_map = array(
	'01' => $L['January'],
	'02' => $L['February'],
	'03' => $L['March'],
	'04' => $L['April'],
	'05' => $L['May'],
	'06' => $L['June'],
	'07' => $L['July'],
	'08' => $L['August'],
	'09' => $L['September'],
	'10' => $L['October'],
	'11' => $L['November'],
	'12' => $L['December']
);

while ($row = sed_sql_fetchassoc($sql)) {
	$stat_name = $row['stat_name']; // YYYY-MM-DD
	$val = (int)$row['stat_value'];
	$all_stats[$stat_name] = $val;

	$y = mb_substr($stat_name, 0, 4);
	if (!in_array($y, $years_list)) {
		$years_list[] = $y;
	}
}

rsort($years_list); // Descending years list for select dropdown

// Handle GET parameters
$is_submitted = isset($_GET['y']) || isset($_GET['m_code']) || isset($_GET['w_code']);
$req_year = sed_import('y', 'G', 'INT');
$req_month = sed_import('m_code', 'G', 'TXT', 2);
$req_week = sed_import('w_code', 'G', 'TXT', 3);

// Default behavior when page is opened without parameters:
// Set default year & month to the latest available in DB
if (!$is_submitted) {
	$latest_stat = !empty($all_stats) ? array_key_last($all_stats) : date('Y-m-d');
	$req_year = (int)mb_substr($latest_stat, 0, 4);
	$req_month = mb_substr($latest_stat, 5, 2);
	$req_week = '0';
}

// Build Year Select Options
foreach ($years_list as $yr) {
	$t->assign(array(
		"YEAR_VAL" => $yr,
		"YEAR_SELECTED" => ($req_year == $yr) ? 'selected="selected"' : ''
	));
	$t->parse("ADMIN_HITS.HITS_YEAR_OPTION");
}

// Build Month Select Options
foreach ($months_map as $m_code => $m_title) {
	$t->assign(array(
		"MONTH_VAL" => $m_code,
		"MONTH_TITLE" => $m_title,
		"MONTH_SELECTED" => ($req_month === $m_code) ? 'selected="selected"' : ''
	));
	$t->parse("ADMIN_HITS.HITS_MONTH_OPTION");
}

// Collect available weeks for the selected year (or all stats)
$weeks_list = array();
foreach ($all_stats as $date_key => $hits_val) {
	$y = mb_substr($date_key, 0, 4);
	if ($req_year == 0 || $y == $req_year) {
		$m = (int)mb_substr($date_key, 5, 2);
		$d = (int)mb_substr($date_key, 8, 2);
		$w_num = sprintf("%02d", (int)@date('W', mktime(0, 0, 0, $m, $d, (int)$y)));
		if (!in_array($w_num, $weeks_list)) {
			$weeks_list[] = $w_num;
		}
	}
}
sort($weeks_list);

// Build Week Select Options
foreach ($weeks_list as $w_num) {
	$t->assign(array(
		"WEEK_VAL" => $w_num,
		"WEEK_SELECTED" => ($req_week === $w_num) ? 'selected="selected"' : ''
	));
	$t->parse("ADMIN_HITS.HITS_WEEK_OPTION");
}

$chart_labels = array();
$chart_values = array();
$table_rows = array();

if ($req_year > 0 && !empty($req_week) && $req_week !== '0') {
	// Mode 1: Year AND Week selected -> Filter by Days in that Week
	$chart_title = $L['Hits'] . ': ' . $L['adm_byweek'] . ' ' . $req_week . ' (' . $req_year . ')';

	foreach ($all_stats as $date_key => $hits_val) {
		$y = (int)mb_substr($date_key, 0, 4);
		$m = (int)mb_substr($date_key, 5, 2);
		$d = (int)mb_substr($date_key, 8, 2);
		$w_num = sprintf("%02d", (int)@date('W', mktime(0, 0, 0, $m, $d, $y)));

		if ($y == $req_year && $w_num === $req_week) {
			$chart_labels[] = sprintf("%02d.%02d", $d, $m);
			$chart_values[] = $hits_val;
			$table_rows[$date_key] = $hits_val;
		}
	}

} elseif ($req_year > 0 && !empty($req_month) && $req_month !== '0') {
	// Mode 2: Year AND Month selected -> Filter by Days in that Month
	$prefix = sprintf("%04d-%02d", $req_year, (int)$req_month);
	$m_title_str = isset($months_map[$req_month]) ? $months_map[$req_month] : $req_month;
	$chart_title = $L['Hits'] . ': ' . $m_title_str . ' ' . $req_year;

	foreach ($all_stats as $date_key => $hits_val) {
		if (mb_strpos($date_key, $prefix) === 0) {
			$day_num = mb_substr($date_key, 8, 2);
			$chart_labels[] = $day_num . '.' . $req_month;
			$chart_values[] = $hits_val;
			$table_rows[$date_key] = $hits_val;
		}
	}

} elseif ($req_year > 0) {
	// Mode 3: ONLY Year selected -> Filter by Months in that Year
	$prefix = sprintf("%04d-", $req_year);
	$chart_title = $L['Hits'] . ': ' . $req_year;

	$grouped_months = array();
	foreach ($all_stats as $date_key => $hits_val) {
		if (mb_strpos($date_key, $prefix) === 0) {
			$ym_key = mb_substr($date_key, 0, 7); // YYYY-MM
			$grouped_months[$ym_key] = isset($grouped_months[$ym_key]) ? $grouped_months[$ym_key] + $hits_val : $hits_val;
		}
	}

	foreach ($grouped_months as $ym_key => $hits_val) {
		$m_code = mb_substr($ym_key, 5, 2);
		$m_label = isset($months_map[$m_code]) ? $months_map[$m_code] : $ym_key;
		$chart_labels[] = $m_label;
		$chart_values[] = $hits_val;
		$table_rows[$ym_key] = $hits_val;
	}

} else {
	// Mode 4: Neither Year, Month, nor Week selected -> Filter by Years
	$chart_title = $L['Hits'] . ': ' . $L['adm_byyear'];

	$grouped_years = array();
	foreach ($all_stats as $date_key => $hits_val) {
		$y_key = mb_substr($date_key, 0, 4);
		$grouped_years[$y_key] = isset($grouped_years[$y_key]) ? $grouped_years[$y_key] + $hits_val : $hits_val;
	}

	foreach ($grouped_years as $y_key => $hits_val) {
		$chart_labels[] = $y_key;
		$chart_values[] = $hits_val;
		$table_rows[$y_key] = $hits_val;
	}
}

// Table Data Rendering (Newest First)
$table_max = !empty($table_rows) ? max($table_rows) : 1;
if ($table_max == 0) $table_max = 1;
$table_rows_rev = array_reverse($table_rows, true);

foreach ($table_rows_rev as $row_key => $hits_val) {
	$percentbar = floor(($hits_val / $table_max) * 100);

	// Create drill-down links if clicking a year or month in table
	$row_url = '';
	if (mb_strlen($row_key) == 4) {
		// Year row -> Drill down to Year
		$row_url = sed_url('admin', 'm=hits&y=' . $row_key . '&m_code=0&w_code=0');
	} elseif (mb_strlen($row_key) == 7) {
		// Month row YYYY-MM -> Drill down to Month
		$r_y = mb_substr($row_key, 0, 4);
		$r_m = mb_substr($row_key, 5, 2);
		$row_url = sed_url('admin', 'm=hits&y=' . $r_y . '&m_code=' . $r_m . '&w_code=0');
	}

	$t->assign(array(
		"HITS_ROW_KEY" => $row_key,
		"HITS_ROW_URL" => $row_url,
		"HITS_ROW_HITS" => number_format($hits_val, 0, '', ' '),
		"HITS_ROW_PERCENTBAR" => $percentbar
	));

	$t->parse("ADMIN_HITS.HITS_TABLE_ROW");
}

// Pass inline JS for SedChart and auto-submit form listeners
$js_chart_labels = json_encode($chart_labels);
$js_chart_values = json_encode($chart_values);
$js_hits_label = json_encode($L['Hits']);

sed_add_javascript("
document.addEventListener('DOMContentLoaded', function () {
	var container = document.getElementById('adminHitsChart');
	if (container && typeof SedChart !== 'undefined') {
		new SedChart(container, {
			labels: {$js_chart_labels},
			values: {$js_chart_values},
			seriesLabel: {$js_hits_label}
		});
	}

	var yearSelect = document.getElementById('hitsYearSelect');
	var monthSelect = document.getElementById('hitsMonthSelect');
	var weekSelect = document.getElementById('hitsWeekSelect');
	var filterForm = document.getElementById('hitsFilterForm');

	if (yearSelect && filterForm) {
		yearSelect.addEventListener('change', function() {
			if (monthSelect) monthSelect.value = '0';
			if (weekSelect) weekSelect.value = '0';
			filterForm.submit();
		});
	}
	if (monthSelect && filterForm) {
		monthSelect.addEventListener('change', function() {
			if (weekSelect) weekSelect.value = '0';
			filterForm.submit();
		});
	}
	if (weekSelect && filterForm) {
		weekSelect.addEventListener('change', function() {
			if (monthSelect) monthSelect.value = '0';
			filterForm.submit();
		});
	}
});
");

$t->assign(array(
	"ADMIN_HITS_TITLE" => $admintitle,
	"HITS_CHART_TITLE" => $chart_title,
	"HITS_FILTER_ACTION" => sed_url('admin', 'm=hits'),
	"HITS_MAXHITS" => sprintf($L['adm_maxhits'], $max_date, number_format($max_hits, 0, '', ' '))
));

$t->parse("ADMIN_HITS");

$adminmain .= $t->text("ADMIN_HITS");
