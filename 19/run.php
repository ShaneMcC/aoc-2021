#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$scanners = [];
	foreach ($input as $group) {
		$scanner = [];
		foreach ($group as $line) {
			if (preg_match('#([-0-9]+),([-0-9]+),([-0-9]+)#', $line, $m)) {
				[$all, $x, $y, $z] = $m;
				$scanner[] = [(int)$x, (int)$y, (int)$z];
			}
		}
		$scanners[] = $scanner;
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

	function getRotations($scanner) {
		$rotations = array_fill(0, 24, []);

		foreach ($scanner as $point) {
			foreach (getPointRotations($point) as $i => $p) {
				$rotations[$i][] = $p;
			}
		}

		return $rotations;
	}

	function getOverlapOffset($scanner1, $scanner2) {
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
			if ($count >= 12) {
				return explode(',', $diff);
			}
		}

		return false;
	}


	$goodScanners = [];
	$goodScanners[0] = [[0,0,0], $scanners[0]];
	unset($scanners[0]);

	while (!empty($scanners)) {
		foreach ($scanners as $j => $s2) {
			foreach ($goodScanners as $i => [$o, $s1]) {
				foreach (getRotations($s2) as $s2r) {
					$overlapOffset = getOverlapOffset($s1, $s2r);

					if ($overlapOffset != false) {

						$overlapOffset[0] += $o[0];
						$overlapOffset[1] += $o[1];
						$overlapOffset[2] += $o[2];

						echo 'Scanner ', $j, ' overlaps with ', $i, '. And has overall offset: ', json_encode($overlapOffset), "\n";
						$goodScanners[$j] = [$overlapOffset, $s2r];
						unset($scanners[$j]);

						continue 4;
					}
				}
			}
		}

		die('Unable to find all required overlaps.'."\n");
	}

	$beacons = [];
	foreach ($goodScanners as $i => [$o, $points]) {
		foreach ($points as $p) {
			$p[0] += $o[0];
			$p[1] += $o[1];
			$p[2] += $o[2];

			$beacons[implode(',', $p)] = true;
		}
	}

	echo 'Part 1: ', count($beacons), "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
