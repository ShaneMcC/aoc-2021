#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$map = [];

	foreach ($input as $in) {
		if (preg_match('/([0-9]+),([0-9]+) -> ([0-9]+),([0-9]+)/', $in, $m)) {
			[$_, $x1, $y1, $x2, $y2] = $m;

			if ($x1 == $x2) {
				$dir = $y1 > $y2 ? -1 : 1;
				$y = $y1;
				do {
					if (!isset($map[$y][$x1])) { $map[$y][$x1] = 0; }
					$map[$y][$x1]++;
					if ($y == $y2) { break; }
				} while ($y += $dir);
			} else if ($y1 == $y2) {
				$dir = $x1 > $x2 ? -1 : 1;
				$x = $x1;
				do {
					if (!isset($map[$y1][$x])) { $map[$y1][$x] = 0; }
					$map[$y1][$x]++;
					if ($x == $x2) { break; }
				} while ($x += $dir);
			}
		}
	}

	$part1 = 0;
	foreach ($map as $row) {
		foreach ($row as $point) {
			if ($point > 1) { $part1++; }
		}
	}

	echo 'Part 1: ', $part1, "\n";
