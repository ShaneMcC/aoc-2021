#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$count = 0;
	while (true) {
		$moved = false;

		$newMap = $map;
		$checkDown = [];
		foreach (cells($map) as [$x, $y, $cell]) {
			if ($cell == '>') {
				if ($map[$y][($x + 1) % count($map[$y])] == '.') {
					$newMap[$y][$x] = '.';
					$newMap[$y][($x + 1) % count($map[$y])] = $cell;
					$moved = true;
				}
			} else if ($cell == 'v') {
				$checkDown[] = [$x, $y, $cell];
			}
		}

		$map = $newMap;
		foreach ($checkDown as [$x, $y, $cell]) {
			if ($cell == 'v') {
				if ($map[($y + 1) % count($map)][$x] == '.') {
					$newMap[$y][$x] = '.';
					$newMap[($y + 1) % count($map)][$x] = $cell;
					$moved = true;
				}
			}
		}
		$map = $newMap;
		$count++;
		if (!$moved) {
			break;
		}
	}

	echo 'Part 1: ', $count, "\n";
