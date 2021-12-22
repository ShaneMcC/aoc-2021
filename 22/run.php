#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		if (preg_match('#(on|off) x=(.*)\.\.(.*),y=(.*)\.\.(.*),z=(.*)\.\.(.*)#SADi', $line, $m)) {
			[$all, $type, $startX, $endX, $startY, $endY, $startZ, $endZ] = $m;
			$entries[] = [min($startX, $endX), max($startX, $endX), min($startY, $endY), max($startY, $endY), min($startZ, $endZ), max($startZ, $endZ), $type];
		}
	}

	$map = [];

	foreach ($entries as [$startX, $endX, $startY, $endY, $startZ, $endZ, $type]) {
		if ($startZ < -50 || $startY < -50 || $startX < -50 || $endZ > 50 || $endY > 50 || $endX > 50) { continue; }

		for ($z = $startZ; $z <= $endZ; $z++) {
			if (!isset($map[$z])) { $map[$z] = []; }
			for ($y = $startY; $y <= $endY; $y++) {
				if (!isset($map[$z][$y])) { $map[$z][$y] = []; }
				for ($x = $startX; $x <= $endX; $x++) {
					if ($type == 'on') {
						$map[$z][$y][$x] = true;
					} else {
						unset($map[$z][$y][$x]);
					}
				}
			}
		}
	}


	$count = 0;

	foreach ($map as $z => $ext) {
		foreach ($ext as $y => $row) {
			$count += count($row);
		}
	}

	echo 'Part 1: ', $count, "\n";

	$cubes = [];
	foreach ($entries as [$startX, $endX, $startY, $endY, $startZ, $endZ, $type]) {
		$newCubes = [];
		if ($type == 'on') { $newCubes[] = [$startX, $endX, $startY, $endY, $startZ, $endZ, $type]; }

		foreach ($cubes as $testCube) {
			$noOverlap = max($startX, $testCube[0]) > min($endX, $testCube[1])
			             || max($startY, $testCube[2]) > min($endY, $testCube[3])
			             || max($startZ, $testCube[4]) > min($endZ, $testCube[5]);

			if ($noOverlap) {
				$newCubes[] = $testCube;
			} else {
				// Slice the left part of this cube and treat it as a new cube.
				if ($testCube[0] < $startX) {
					$new = $testCube;
					$new[1] = $startX - 1;
					$newCubes[] = $new;

					// Remove it from our test cube for future.
					$testCube[0] = $startX;
				}

				// Now the Right...
				if ($testCube[1] > $endX) {
					$new = $testCube;
					$new[0] = $endX + 1;
					$newCubes[] = $new;
					$testCube[1] = $endX;
				}

				// Now the Top...
				if ($testCube[2] < $startY) {
					$new = $testCube;
					$new[3] = $startY - 1;
					$newCubes[] = $new;
					$testCube[2] = $startY;
				}

				// Now the Bottom...
				if ($testCube[3] > $endY) {
					$new = $testCube;
					$new[2] = $endY + 1;
					$newCubes[] = $new;
					$testCube[3] = $endY;
				}

				// Now the Front...
				if ($testCube[4] < $startZ) {
					$new = $testCube;
					$new[5] = $startZ - 1;
					$newCubes[] = $new;
					$testCube[4] = $startZ;
				}

				// Now the Back...
				if ($testCube[5] > $endZ) {
					$new = $testCube;
					$new[4] = $endZ + 1;
					$newCubes[] = $new;
					$testCube[5] = $endZ;
				}
			}
		}

		$cubes = [];
		foreach ($newCubes as $new) {
			$cubes[] = $new;
		}
	}

	$total = 0;
	foreach ($cubes as $cube) {
		[$startX, $endX, $startY, $endY, $startZ, $endZ, $type] = $cube;
		$total += (abs($startX - $endX - 1)) * (abs($startY - $endY- 1)) * (abs($startZ - $endZ - 1));
	}

	echo 'Part 2: ', $total, "\n";
