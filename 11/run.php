#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function step($map) {
		// First, the energy level of each octopus increases by 1.
		foreach (cells($map) as [$x, $y, $cell]) {
			$map[$y][$x]++;
		}

		// Then, any octopus with an energy level greater than 9 flashes.
		// This increases the energy level of all adjacent octopuses by 1,
		// including octopuses that are diagonally adjacent. If this causes
		// an octopus to have an energy level greater than 9, it also flashes.
		// This process continues as long as new octopuses keep having their
		// energy level increased beyond 9. (An octopus can only flash at
		// most once per step.)
		$flashers = [];

		do {
			$flashed = false;

			for ($y = 0; $y < count($map); $y++) {
				for ($x = 0; $x < count($map[$y]); $x++) {
					// Check we haven't flashed, and we're larger than 9.
					if ($map[$y][$x] > 9 && !in_array([$x, $y], $flashers)) {
						$flashed = true;
						$flashers[] = [$x, $y];

						// Bump all neighbours
						foreach (getAdjacentCells($map, $x, $y, true) as [$ax, $ay]) {
							$map[$ay][$ax]++;
						}
					}
				}
			}
		} while ($flashed);

		// Finally, any octopus that flashed during this step has its energy
		// level set to 0, as it used all of its energy to flash.
		foreach ($flashers as [$x, $y]) {
			$map[$y][$x] = 0;
		}

		return [$map, count($flashers)];
	}

	$mapSize = count($map) * count($map[0]);
	$part1 = $part2 = 0;
	for ($steps = 1; true; $steps++) {
		[$map, $flashCount] = step($map);
		if ($steps <= 100) { $part1 += $flashCount; }
		if ($part2 == 0 && $flashCount == $mapSize) { $part2 = $steps; }
		if ($steps >= 100 && $part2 > 0) { break; }
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
