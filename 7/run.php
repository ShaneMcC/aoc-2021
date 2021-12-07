#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(',', getInputLine());

	function checkPosition($input, $pos) {
		$cost = 0;
		foreach ($input as $in) {
			$cost += abs($in - $pos);
		}
		return $cost;
	}

	$bestCost = $bestPos = PHP_INT_MAX;
	for ($i = 0; $i < max($input); $i++) {
		$cost = checkPosition($input, $i);
		if ($cost < $bestCost) {
			$bestCost = $cost;
			$bestPos = $i;
		}
	}

	echo 'Part 1: Align at ', $bestPos, ' with cost: ', $bestCost, "\n";
