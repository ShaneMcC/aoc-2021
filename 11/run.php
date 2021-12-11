#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function step($map) {
		$newMap = $map;

		$flashers = [];

		foreach (cells($newMap) as [$x, $y, $cell]) {
			$newMap[$y][$x]++;
		}

		$flashed = false;
		do {
			$flashed = false;
			foreach (cells($newMap) as [$x, $y, $cell]) {
				if ($cell > 9) {
					if (!in_array([$x, $y], $flashers)) {
						$flashed = true;
						$flashers[] = [$x, $y];

						foreach (getAdjacentCells($newMap, $x, $y, true) as [$ax, $ay]) {
							$newMap[$ay][$ax]++;
						}
					}
				}
			}
		} while ($flashed);

		foreach ($flashers as [$x, $y]) {
			$newMap[$y][$x] = 0;
		}

		return [$newMap, count($flashers)];
	}

	$mapSize = count($map) * count($map[0]);
	$part1 = $part2 = 0;
	$steps = 0;
	while (true) {
		$steps++;
		[$map, $flashCount] = step($map);
		if ($steps <= 100) {
			$part1 += $flashCount;
		}
		if ($steps == 100) {
			echo 'Part 1: ', $part1, "\n";
		}

		if ($flashCount == $mapSize) {
			$part2 = $steps;
		}

		if ($steps >= 100 && $part2 > 0) { break; }
	}

	echo 'Part 2: ', $part2, "\n";
