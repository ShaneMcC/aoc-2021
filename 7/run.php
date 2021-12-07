#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(',', getInputLine());
	sort($input);

	function checkPosition($input, $pos, $max = PHP_INT_MAX, $expensive = false) {
		$cost = 0;
		foreach ($input as $in) {
			$diff = abs($in - $pos);
			$cost += $expensive ? ($diff * (($diff + 1) / 2)) : $diff;
			if ($cost > $max) { return $max; }
		}
		return $cost;
	}

	$median = $input[count($input) / 2];
	$bestPos1 = $median;
	$bestCost1 = checkPosition($input, $median);

	$mean = floor(array_sum($input) / count($input));
	$bestCost2 = PHP_INT_MAX;
	foreach ([$mean, $mean + 1] as $i) {
		$cost2 = checkPosition($input, $i, $bestCost2, true);

		if ($cost2 < $bestCost2) {
			$bestCost2 = $cost2;
			$bestPos2 = $i;
		}
	}

	echo 'Part 1: Align at ', $bestPos1, ' with cost: ', $bestCost1, "\n";
	echo 'Part 2: Align at ', $bestPos2, ' with cost: ', $bestCost2, "\n";
