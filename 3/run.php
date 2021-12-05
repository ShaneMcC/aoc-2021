#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$bitCount = [];
	foreach ($input as $in) {
		for ($i = 0; $i < strlen($in); $i++) {
			$bit = $in[$i];
			if (!isset($bitCount[$i][$bit])) { $bitCount[$i][$bit] = 0; }
			$bitCount[$i][$bit]++;
		}
	}

	$gamma = $epsilon = '';
	foreach ($bitCount as $bit => $counts) {
		$maxBit = array_search(max($counts), $counts);
		$minBit = array_search(min($counts), $counts);
		$gamma .= $maxBit;
		$epsilon .= $minBit;
	}

	$consumption = bindec($gamma) * bindec($epsilon);

	$part1 = $consumption;
	echo 'Part 1: ', $part1, "\n";

	$part2 = 0;
	echo 'Part 2: ', $part2, "\n";
