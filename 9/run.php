#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/../common/pathfinder.php');
	$map = getInputMap();

	function getBasinCells($map, $x, $y, $known = []) {
		$adjacent = [];

		if (isset($map[$y - 1][$x])) { $adjacent[] = [$y - 1, $x]; }
		if (isset($map[$y + 1][$x])) { $adjacent[] = [$y + 1, $x]; }
		if (isset($map[$y][$x - 1])) { $adjacent[] = [$y, $x - 1]; }
		if (isset($map[$y][$x + 1])) { $adjacent[] = [$y, $x + 1]; }


		$known[] = [$x, $y];
		foreach ($adjacent as $a) {
			[$ay, $ax] = $a;
			if ($map[$ay][$ax] == 9) { continue; }
			if (!in_array([$ax, $ay], $known)) {
				$known = getBasinCells($map, $ax, $ay, $known);
			}
		}

		return $known;
	}

	$part1 = 0;
	$basins = [];
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

			$cells = getBasinCells($map, $x, $y);
			$basins[] = count($cells);
		}
	}

	echo 'Part 1: ', $part1, "\n";

	rsort($basins);
	$part2 = $basins[0] * $basins[1] * $basins[2];
	echo 'Part 2: ', $part2, "\n";
