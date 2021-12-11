#!/usr/bin/php
<?php
    $__CLI['long'] = ['draw', 'delay:'];
    $__CLI['extrahelp'] = [];
    $__CLI['extrahelp'][] = '      --draw               Draw the map.';
    $__CLI['extrahelp'][] = '      --delay <num>        Delay between frames (Default: 0.1)';

	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function step($map) {
		$wantsToFlash = [];

		// First, the energy level of each octopus increases by 1.
		foreach (cells($map) as [$x, $y, $cell]) {
			$map[$y][$x]++;

			// Any octopus with an energy level greater than 9 wants to flash.
			if ($map[$y][$x] == 10) {
				$wantsToFlash[] = [$x, $y];
			}
		}

		// Then, any octopus with an energy level greater than 9 flashes.
		// This increases the energy level of all adjacent octopuses by 1,
		// including octopuses that are diagonally adjacent. If this causes
		// an octopus to have an energy level greater than 9, it also flashes.
		// This process continues as long as new octopuses keep having their
		// energy level increased beyond 9. (An octopus can only flash at
		// most once per step.)
		$flashers = [];

		while ([$x, $y] = array_pop($wantsToFlash)) {
			$flashers[] = [$x, $y];
			foreach (getAdjacentCells($map, $x, $y, true) as [$ax, $ay]) {
				$map[$ay][$ax]++;
				if ($map[$ay][$ax] == 10) {
					$wantsToFlash[] = [$ax, $ay];
				}
			}
		}

		// Finally, any octopus that flashed during this step has its energy
		// level set to 0, as it used all of its energy to flash.
		foreach ($flashers as [$x, $y]) {
			$map[$y][$x] = 0;
		}

		return [$map, $flashers];
	}


	if (isset($__CLIOPTS['draw'])) {
		drawMap($map, true, 'Step 0');
		$drawDelay = isset($__CLIOPTS['delay']) ? (is_array($__CLIOPTS['delay']) ? $__CLIOPTS['delay'][count($__CLIOPTS['delay']) - 1] : $__CLIOPTS['delay']) : 0.1;
		$drawDelay *= 1000000;
	}

	$mapSize = count($map) * count($map[0]);
	$part1 = $part2 = 0;
	for ($steps = 1; true; $steps++) {
		[$map, $flashers] = step($map);
		$flashCount = count($flashers);

		if (isset($__CLIOPTS['draw'])) {
			echo "\033[" . (count($map) + 7) . "A";
			$drawMap = $map;
			foreach ($flashers as [$fx, $fy]) {
				$drawMap[$fy][$fx] = "\033[1;31m" . $drawMap[$fy][$fx] . "\033[0m";
			}
			drawMap($drawMap, true, 'Step ' . $steps);
			usleep($drawDelay);
		}

		if ($steps <= 100) { $part1 += $flashCount; }
		if ($part2 == 0 && $flashCount == $mapSize) { $part2 = $steps; }
		if ($steps >= 100 && $part2 > 0) { break; }
		if ($steps > 10000) { break; } // Bail if it looks like we're never succeeding...
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
