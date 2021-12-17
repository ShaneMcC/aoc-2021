#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	preg_match('#target area: x=([-0-9]+)\.\.([-0-9]+), y=([-0-9]+)\.\.([-0-9]+)#SADi', $input, $m);
	[$all, $startX, $endX, $startY, $endY] = $m;

	$target = ['x' => ['start' => $startX, 'end' => $endX], 'y' => ['start' => $startY, 'end' => $endY]];

	function testProbe($target, $vx, $vy) {
		$probe = ['x' => 0, 'y' => 0, 'vx' => $vx, 'vy' => $vy];

		while (true) {
			$probe['x'] += $probe['vx'];
			$probe['y'] += $probe['vy'];
			if ($probe['vx'] != 0) { $probe['vx'] += $probe['vx'] > 0 ? -1 : 1; }
			$probe['vy'] += -1;

			$inX = $probe['x'] >= $target['x']['start'] && $probe['x'] <= $target['x']['end'];
			$inY = $probe['y'] >= $target['y']['start'] && $probe['y'] <= $target['y']['end'];

			if ($inX && $inY) {
				return true;
			}

			if ($probe['y'] < min($target['y']['start'], $target['y']['end'])) {
				return false;
			}

			if ($probe['vx'] == 0 && !$inX) {
				return false;
			}
		}
	}

	$highestY = $target['y']['start'] * ($target['y']['start'] + 1) / 2;
	echo 'Part 1: ', $highestY, "\n";

	$valid = 0;
	for ($vx = 0; $vx <= $target['x']['end']; $vx++) {
		for ($vy = $target['y']['start']; $vy <= abs($target['y']['start']); $vy++) {
			if (testProbe($target, $vx, $vy)) {
				if (isDebug()) { echo $vx, ',', $vy, "\n"; }
				$valid++;
			}
		}
	}

	echo 'Part 2: ', $valid, "\n";
