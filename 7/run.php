#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(',', getInputLine());

	function checkPosition($input, $pos) {
		$cost = 0;
		$expensiveCost = 0;
		foreach ($input as $in) {
			$diff = abs($in - $pos);
			$cost += $diff;
			$expensiveCost += array_sum(range(0, $diff));
		}
		return [$cost, $expensiveCost];
	}

	$bestCost1 = $bestPos1 = $bestCost2 = $bestPos2 = PHP_INT_MAX;
	for ($i = 0; $i < max($input); $i++) {
		[$cost1, $cost2] = checkPosition($input, $i);

		if ($cost1 < $bestCost1) {
			$bestCost1 = $cost1;
			$bestPos1 = $i;
		}
		if ($cost2 < $bestCost2) {
			$bestCost2 = $cost2;
			$bestPos2 = $i;
		}
	}

	echo 'Part 1: Align at ', $bestPos1, ' with cost: ', $bestCost1, "\n";
	echo 'Part 2: Align at ', $bestPos2, ' with cost: ', $bestCost2, "\n";
