#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$x = $y = 0;
	$x2 = $y2 = $aim = 0;

	$directions = ['forward' => [1, 0],
	               'up' => [0, -1],
	               'down' => [0, +1],
	              ];

	foreach ($input as $inst) {
		$bits = explode(' ', $inst);

		// Part 1
		$dir = $directions[$bits[0]];
		$x += $dir[0] * $bits[1];
		$y += $dir[1] * $bits[1];

		// Part 2
		if ($bits[0] == 'down') {
			$aim += $bits[1];
		} else if ($bits[0] == 'up') {
			$aim -= $bits[1];
		} else if ($bits[0] == 'forward') {
			$x2 += $bits[1];
			$y2 += $aim * $bits[1];
		}
	}

	echo 'Part 1: ', $x * $y, "\n";
	echo 'Part 2: ', $x2 * $y2, "\n";
