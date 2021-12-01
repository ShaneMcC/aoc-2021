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

	$part1 = part1($input);
	echo 'Part 1: ', $part1, "\n";
