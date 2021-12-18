#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$input = [];
	foreach (getInputLines() as $in) {
		if (preg_match('/([0-9]+),([0-9]+) -> ([0-9]+),([0-9]+)/', $in, $m)) {
			[$_, $startX, $startY, $endX, $endY] = $m;
			$input[] = [$startX, $startY, $endX, $endY];
		}
	}

	function countCrossingPoints($map) {
		$count = 0;
		foreach ($map as $row) {
			foreach ($row as $point) {
				if ($point > 1) {
					$count++;
				}
			}
		}
		return $count;
	}

	$map = [];

	foreach ($input as $in) {
		[$startX, $startY, $endX, $endY] = $in;

		if ($startX == $endX) {
			for ($y = min($startY, $endY); $y <= max($startY, $endY); $y++) {
				if (!isset($map[$y][$startX])) { $map[$y][$startX] = 0; }
				$map[$y][$startX]++;
			}
		} else if ($startY == $endY) {
			for ($x = min($startX, $endX); $x <= max($startX, $endX); $x++) {
				if (!isset($map[$startY][$x])) { $map[$startY][$x] = 0; }
				$map[$startY][$x]++;
			}
		}
	}

	$part1 = countCrossingPoints($map);
	echo 'Part 1: ', $part1, "\n";

	foreach ($input as $in) {
		[$startX, $startY, $endX, $endY] = $in;
		if ($startX != $endX && $startY != $endY) {
			$xdir = $startX > $endX ? -1 : 1;
			$x = $startX;
			$ydir = $startY > $endY ? -1 : 1;
			$y = $startY;
			while (true) {
				if (!isset($map[$y][$x])) { $map[$y][$x] = 0; }
				$map[$y][$x]++;
				if ($x == $endX && $y == $endY) { break; }
				$x += $xdir;
				$y += $ydir;
			};
		}
	}

	$part2 = countCrossingPoints($map);
	echo 'Part 2: ', $part2, "\n";
