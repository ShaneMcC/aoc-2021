#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function checkIncrements($input, $size = 3) {
		$inc = 0;
		for ($i = $size; $i < count($input); $i++) {
			// if (array_sum(array_slice($input, $i - 1 - $size, $size)) < array_sum(array_slice($input, $i - $size, $size))) {
			if ($input[$i - $size] < $input[$i]) {
				$inc++;
			}
		}
		return $inc;
	}


	$part1 = checkIncrements($input, 1);
	echo 'Part 1: ', $part1, "\n";

	$part2 = checkIncrements($input, 3);
	echo 'Part 2: ', $part2, "\n";
