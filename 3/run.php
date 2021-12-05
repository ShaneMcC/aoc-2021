#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function getBitCounts($input) {
		$bitCount = [];
		foreach ($input as $in) {
			for ($i = 0; $i < strlen($in); $i++) {
				$bit = $in[$i];
				if (!isset($bitCount[$i][$bit])) { $bitCount[$i][$bit] = 0; }
				$bitCount[$i][$bit]++;
			}
		}

		return $bitCount;
	}

	$bitCount = getBitCounts($input);

	$gamma = $epsilon = '';
	foreach ($bitCount as $bit => $counts) {
		$maxBit = array_search(max($counts), $counts);
		$minBit = array_search(min($counts), $counts);
		$gamma .= $maxBit;
		$epsilon .= $minBit;
	}
	$consumption = bindec($gamma) * bindec($epsilon);

	$oxygen = $input;
	$scrubber = $input;

	for ($i = 0; $i < strlen($gamma); $i++) {
		if (count($oxygen) > 1) {
			$bitCount = getBitCounts($oxygen);
			$oxygen = array_filter($oxygen, function ($val) use ($bitCount, $i) {
				return $bitCount[$i]['0'] > $bitCount[$i]['1'] ? $val[$i] == 0 : $val[$i] == 1;
			});
		}

		if (count($scrubber) > 1) {
			$bitCount = getBitCounts($scrubber);
			$scrubber = array_filter($scrubber, function ($val) use ($bitCount, $i) {
				return $bitCount[$i]['1'] < $bitCount[$i]['0'] ? $val[$i] == 1 : $val[$i] == 0;
			});
		}
	}

	$lifeSupport = bindec(array_values($oxygen)[0]) * bindec(array_values($scrubber)[0]);

	$part1 = $consumption;
	echo 'Part 1: ', $part1, "\n";

	$part2 = $lifeSupport;
	echo 'Part 2: ', $part2, "\n";
