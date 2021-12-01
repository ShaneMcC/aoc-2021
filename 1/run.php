#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function part1($input) {
		$inc = 0;
		for ($i = 1; $i < count($input); $i++) {
			if ($input[$i - 1] < $input[$i]) {
				$inc++;
			}
		}
		return $inc;
	}

	function part2($input, $size = 3) {
		$inc = 0;
		for ($i = $size; $i <= count($input); $i++) {
			if (array_sum(array_slice($input, $i - 1 - $size, $size)) < array_sum(array_slice($input, $i - $size, $size))) {
				$inc++;
			}
		}
		return $inc;
	}


	$part1 = part1($input);
	echo 'Part 1: ', $part1, "\n";

	$part2 = part2($input);
	echo 'Part 2: ', $part2, "\n";
