#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$template = array_shift($input);
	$rules = [];
	foreach ($input as $line) {
		preg_match('#(.*) -> (.*)#SADi', $line, $m);
		[$all, $pair, $insert] = $m;
		$rules[$pair] = $insert;
	}

	$pairCounts = [];
	for ($i = 0; $i < strlen($template) - 1; $i++) {
		$pair = $template[$i] . $template[$i + 1];
		if (!isset($pairCounts[$pair])) { $pairCounts[$pair] = 0; }
		$pairCounts[$pair]++;
	}
	$letters = array_count_values(str_split($template));

	for ($i = 1; $i <= 40; $i++) {
		$oldPairCounts = $pairCounts;

		foreach ($oldPairCounts as $p => $count) {
			$insert = $rules[$p];
			$before = $p[0] . $insert;
			$after = $insert . $p[1];

			if (!isset($pairCounts[$before])) { $pairCounts[$before] = 0; }
			if (!isset($pairCounts[$after])) { $pairCounts[$after] = 0; }
			if (!isset($letters[$insert])) { $letters[$insert] = 0; }

			$pairCounts[$p] -= $count;
			$pairCounts[$before] += $count;
			$pairCounts[$after] += $count;
			$letters[$insert] += $count;

			if ($pairCounts[$p] == 0) { unset($pairCounts[$p]); }
		}

		if ($i == 10) {
			echo 'Part 1: ', (max($letters) - min($letters)), "\n";
		} else if ($i == 40) {
			echo 'Part 2: ', (max($letters) - min($letters)), "\n";
		}
	}
