#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match('#(on|off) x=(.*)\.\.(.*),y=(.*)\.\.(.*),z=(.*)\.\.(.*)#SADi', $line, $m);
		[$all, $type, $startX, $endX, $startY, $endY, $startZ, $endZ] = $m;
		$entries[] = [$type, $startX, $endX, $startY, $endY, $startZ, $endZ];
	}

	$map = [];

	foreach ($entries as [$type, $startX, $endX, $startY, $endY, $startZ, $endZ]) {

		for ($z = max(-50, $startZ); $z <= min(50, $endZ); $z++) {
			if (!isset($map[$z])) { $map[$z] = []; }
			for ($y = max(-50, $startY); $y <= min(50, $endY); $y++) {
				if (!isset($map[$z][$y])) { $map[$z][$y] = []; }
				for ($x = max(-50, $startX); $x <= min(50, $endX); $x++) {
					$map[$z][$y][$x] = ($type == 'on' ? '#' : ' ');
				}
			}
		}
	}


	$count = 0;

	foreach ($map as $z => $ext) {
		foreach ($ext as $y => $row) {
			foreach ($row as $x => $cell) {
				if ($cell == '#') {
					$count++;
				}
			}
		}
	}

	echo 'Part 1: ', $count, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
