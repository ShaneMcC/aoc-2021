#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$part1 = 0;
	foreach (cells($map) as $c) {
		[$x, $y, $cell] = $c;

		$adjacent = [];

		if (isset($map[$y - 1][$x])) { $adjacent[] = $map[$y - 1][$x]; }
		if (isset($map[$y + 1][$x])) { $adjacent[] = $map[$y + 1][$x]; }
		if (isset($map[$y][$x - 1])) { $adjacent[] = $map[$y][$x - 1]; }
		if (isset($map[$y][$x + 1])) { $adjacent[] = $map[$y][$x + 1]; }

		$lowPoint = true;
		foreach ($adjacent as $a) {
			if ($a <= $cell) {
				$lowPoint = false;
			}
		}

		if ($lowPoint) {
			$part1 += 1 + $cell;
		}
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
