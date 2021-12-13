#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$map = [];
	$maxX = $maxY = 0;
	$part1 = 0;
	foreach ($input as $line) {
		if (preg_match('#(.*),(.*)#SADi', $line, $m)) {
			[$all, $x, $y] = $m;

			if (!isset($map[$y])) { $map[$y] = []; }
			$map[$y][$x] = '#';
			$maxX = max($maxX, $x);
			$maxY = max($maxY, $y);
		}

		if (preg_match('#fold along ([xy])=(.*)#SADi', $line, $m)) {
			[$all, $axis, $point] = $m;

			$newMap = $map;
			if ($axis == 'y') {
				for ($y = ($point + 1); $y <= $maxY; $y++) {
					if (!isset($map[$y])) { continue; }
					$newY = $point - ($y - $point);

					foreach ($map[$y] as $x => $cell) {
						$newMap[$newY][$x] = $cell;
					}
					unset($newMap[$y]);
				}
			}

			if ($axis == 'x') {
				for ($x = ($point + 1); $x <= $maxX; $x++) {
					$newX = $point - ($x - $point);

					for ($y = 0; $y <= $maxY; $y++) {
						if (isset($newMap[$y][$x])) {
							$newMap[$y][$newX] = $newMap[$y][$x];
							unset($newMap[$y][$x]);
						}
					}
				}
			}

			$map = $newMap;

			if ($part1 == 0) {
				foreach (cells($map) as [$x, $y, $cell]) {
					if ($cell == '#') { $part1++; }
				}
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";

	drawSparseMap($map, true);
	// EFLFJGRF
	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
