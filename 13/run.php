#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/../common/decodeText.php');
	$input = getInputLines();

	$map = [];
	$maxX = $maxY = 0;
	$part1 = 0;
	foreach ($input as $line) {
		if (preg_match('#(.*),(.*)#SADi', $line, $m)) {
			[$all, $x, $y] = $m;

			if (!isset($map[$y])) { $map[$y] = []; }
			$map[$y][$x] = '█';
			$maxX = max($maxX, $x);
			$maxY = max($maxY, $y);
		}

		if (preg_match('#fold along ([xy])=(.*)#SADi', $line, $m)) {
			if (isDebug()) { drawSparseMap($map, ' ', true); }
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
				$maxY = $point;
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
				$maxX = $point;
			}

			$map = $newMap;

			if ($part1 == 0) {
				foreach (cells($map) as [$x, $y, $cell]) {
					$part1++;
				}
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";

	// Get a decodeable map.
	$newMap = [];
	for ($y = 0; $y < $maxY; $y++) {
		$newMap[$y] = [];
		for ($x = 0; $x < $maxX; $x++) {
			$newMap[$y][$x] = isset($map[$y][$x]) ? 1 : 0;
		}
	}
	$part2 = decodeText($newMap);
	echo 'Part 2: ', $part2, "\n";

	drawSparseMap($map, ' ', true);
