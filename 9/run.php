#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$map = getInputMap();

	function getBasinCells($map, $x, $y, $known = []) {
		$known[] = [$x, $y];
		foreach (getAdjacentCells($map, $x, $y) as [$ax, $ay]) {
			if ($map[$ay][$ax] == 9) { continue; }
			if (!in_array([$ax, $ay], $known)) {
				$known = getBasinCells($map, $ax, $ay, $known);
			}
		}

		return $known;
	}

	$part1 = 0;
	$basins = [];
	foreach (cells($map) as [$x, $y, $cell]) {
		$lowPoint = true;
		foreach (getAdjacentCells($map, $x, $y) as [$ax, $ay]) {
			if ($map[$ay][$ax] <= $cell) {
				$lowPoint = false;
			}
		}

		if ($lowPoint) {
			$part1 += (1 + $cell);
			$basins[] = count(getBasinCells($map, $x, $y));
		}
	}
	rsort($basins);
	$part2 = array_product(array_slice($basins, 0, 3));

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
