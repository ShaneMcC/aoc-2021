#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$x1 = $y1 = $x2 = $y2 = $aim = 0;

	foreach ($input as $inst) {
		@[$dir, $amt] = explode(' ', $inst);

		if ($dir == 'up') {
			$y1 -= $amt;
			$aim -= $amt;
		} else if ($dir == 'down') {
			$y1 += $amt;
			$aim += $amt;
		} else if ($dir == 'forward') {
			$x1 += $amt;
			$x2 += $amt;
			$y2 += $aim * $amt;
		}
	}

	echo 'Part 1: ', $x1 * $y1, "\n";
	echo 'Part 2: ', $x2 * $y2, "\n";
