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

			if ($axis == 'y') {
				for ($y = ($point + 1); $y <= $maxY; $y++) {
					$newY = $point - ($y - $point);
					if (!isset($map[$y])) { continue; }

					foreach ($map[$y] as $x => $cell) {
						$map[$newY][$x] = $cell;
					}
					unset($map[$y]);
				}
				$maxY = $point;
			}

			if ($axis == 'x') {
				for ($x = ($point + 1); $x <= $maxX; $x++) {
					$newX = $point - ($x - $point);

					foreach (array_keys($map) as $y) {
						if (isset($map[$y][$x])) {
							$map[$y][$newX] = $map[$y][$x];
							unset($map[$y][$x]);
						}
					}
				}
				$maxX = $point;
			}

			if ($part1 == 0) {
				foreach (cells($map) as [$x, $y, $cell]) {
					$part1++;
				}
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";

	// Get a decodable map.
	$newMap = [];
	for ($y = 0; $y < $maxY; $y++) {
		$newMap[$y] = [];
		for ($x = 0; $x < $maxX; $x++) {
			$newMap[$y][$x] = isset($map[$y][$x]) ? $map[$y][$x] : ' ';
		}
	}
	$part2 = decodeText($newMap);
	echo 'Part 2: ', $part2, "\n";

	drawMap($newMap, true);
