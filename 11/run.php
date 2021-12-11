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


	$part1 = 0;
	for ($i = 0; $i < 100; $i++) {
		[$map, $flashCount] = step($map);
		$part1 += $flashCount;
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
