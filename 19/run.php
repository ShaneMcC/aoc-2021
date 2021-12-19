#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$unknownScanners = [];
	foreach ($input as $group) {
		$scanner = [];
		foreach ($group as $line) {
			if (preg_match('#([-0-9]+),([-0-9]+),([-0-9]+)#', $line, $m)) {
				[$all, $x, $y, $z] = $m;
				$scanner[] = [(int)$x, (int)$y, (int)$z];
			}
		}
		$unknownScanners[] = $scanner;
	}

	// My brain can not do 3d space nicely. I hate this.
	// Borrowed from:
	// https://cdn.discordapp.com/attachments/894579870418489385/922008117628272660/unknown.png
	function getPointRotations($point) {
		[$x, $y, $z] = $point;

		$rotations = [];

		$rotations[] = [$x, $y, $z];
		$rotations[] = [-$x, -$y, $z];
		$rotations[] = [-$x, $y, -$z];
		$rotations[] = [$x, -$y, -$z];

		$rotations[] = [$y, $z, $x];
		$rotations[] = [-$y, -$z, $x];
		$rotations[] = [-$y, $z, -$x];
		$rotations[] = [$y, -$z, -$x];

		$rotations[] = [$z, $x, $y];
		$rotations[] = [-$z, -$x, $y];
		$rotations[] = [-$z, $x, -$y];
		$rotations[] = [$z, -$x, -$y];

		$rotations[] = [$x, $z, -$y];
		$rotations[] = [$x, -$z, $y];
		$rotations[] = [-$x, $z, $y];
		$rotations[] = [-$x, -$z, -$y];

		$rotations[] = [$y, $x, -$z];
		$rotations[] = [$y, -$x, $z];
		$rotations[] = [-$y, $x, $z];
		$rotations[] = [-$y, -$x, -$z];

		$rotations[] = [$z, $y, -$x];
		$rotations[] = [$z, -$y, $x];
		$rotations[] = [-$z, $y, $x];
		$rotations[] = [-$z, -$y, -$x];

		return $rotations;
	}

	function getScannerRotations($scanner) {
		$rotations = array_fill(0, 24, []);

		foreach ($scanner as $point) {
			foreach (getPointRotations($point) as $i => $p) {
				$rotations[$i][] = $p;
			}
		}

		return $rotations;
	}

	function getOverlapOffset($scanner1, $scanner2, $overlapRequired = 12) {
		$differences = [];
		foreach ($scanner1 as $point1) {
			[$x1, $y1, $z1] = $point1;

			foreach ($scanner2 as $point2) {
				[$x2, $y2, $z2] = $point2;

				$diff = ($x1 - $x2) . ',' . ($y1 - $y2) . ',' . ($z1 - $z2);

				if (!isset($differences[$diff])) { $differences[$diff] = 0; }
				$differences[$diff]++;
			}
		}

		foreach ($differences as $diff => $count) {
			if ($count >= $overlapRequired) {
				return explode(',', $diff);
			}
		}

		return false;
	}


	$goodScanners = [];
	$goodScanners[0] = [[0,0,0], $unknownScanners[0]];
	unset($unknownScanners[0]);

	while (!empty($unknownScanners)) {
		$found = false;
		foreach (array_keys($unknownScanners) as $sid) {
			$scanner = $unknownScanners[$sid];
			foreach ($goodScanners as $gid => [$goodOffset, $goodScanner]) {
				foreach (getScannerRotations($scanner) as $scannerRotation) {

					// Should be 12, but seems to be faster and still correct at 5 so...
					$overlapOffset = getOverlapOffset($goodScanner, $scannerRotation, 5);

					if ($overlapOffset != false) {

						if (isDebug()) { $origOffset = array_map(fn($x) => (int)$x, $overlapOffset); }

						$overlapOffset[0] += $goodOffset[0];
						$overlapOffset[1] += $goodOffset[1];
						$overlapOffset[2] += $goodOffset[2];

						if (isDebug()) {
							echo 'Scanner ', $sid, ' overlaps with ', $gid, ' with offset ', json_encode($origOffset), ' and has overall offset: ', json_encode($overlapOffset), "\n";
						}

						$goodScanners[$sid] = [$overlapOffset, $scannerRotation];
						unset($unknownScanners[$sid]);
						$found = true;
						break 2;
					}
				}
			}
		}

		if ($found == false) {
			die('Unable to find all required overlaps.'."\n");
		}
	}

	$beacons = [];
	$part2 = 0;
	foreach ($goodScanners as $i => [$o, $points]) {
		foreach ($points as $p) {
			$p[0] += $o[0];
			$p[1] += $o[1];
			$p[2] += $o[2];

			$beacons[implode(',', $p)] = true;
		}

		foreach ($goodScanners as $i => [$o2, $points]) {
			$part2 = max($part2, abs($o[0] - $o2[0]) + abs($o[1] - $o2[1]) + abs($o[2] - $o2[2]));
		}
	}

	echo 'Part 1: ', count($beacons), "\n";
	echo 'Part 2: ', $part2, "\n";
