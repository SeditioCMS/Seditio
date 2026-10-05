<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/revisions/inc/revisions.diff.php
Version=186
Updated=2026-oct-01
Type=Plugin
Author=Seditio Team
Description=Lightweight LCS (Longest Common Subsequence) line diff engine for revisions
[END_SED]
==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}

/**
 * Computes difference between two strings on line-by-line basis
 * 
 * @param string $old Old text
 * @param string $new New text
 * @return array Array of diff rows: ['type' => 'same'|'add'|'del', 'val' => string]
 */
function sed_revisions_compute_line_diff($old, $new)
{
	$old_lines = preg_split("/\r\n|\n|\r/", (string)$old);
	$new_lines = preg_split("/\r\n|\n|\r/", (string)$new);

	$matrix = array();
	$old_count = count($old_lines);
	$new_count = count($new_lines);

	for ($i = 0; $i <= $old_count; $i++) {
		$matrix[$i] = array_fill(0, $new_count + 1, 0);
	}

	for ($i = 1; $i <= $old_count; $i++) {
		for ($j = 1; $j <= $new_count; $j++) {
			if ($old_lines[$i - 1] === $new_lines[$j - 1]) {
				$matrix[$i][$j] = $matrix[$i - 1][$j - 1] + 1;
			} else {
				$matrix[$i][$j] = max($matrix[$i - 1][$j], $matrix[$i][$j - 1]);
			}
		}
	}

	$diff = array();
	$i = $old_count;
	$j = $new_count;

	while ($i > 0 || $j > 0) {
		if ($i > 0 && $j > 0 && $old_lines[$i - 1] === $new_lines[$j - 1]) {
			$diff[] = array('type' => 'same', 'val' => $old_lines[$i - 1]);
			$i--;
			$j--;
		} elseif ($j > 0 && ($i === 0 || $matrix[$i][$j - 1] >= $matrix[$i - 1][$j])) {
			$diff[] = array('type' => 'add', 'val' => $new_lines[$j - 1]);
			$j--;
		} elseif ($i > 0 && ($j === 0 || $matrix[$i][$j - 1] < $matrix[$i - 1][$j])) {
			$diff[] = array('type' => 'del', 'val' => $old_lines[$i - 1]);
			$i--;
		}
	}

	return array_reverse($diff);
}
