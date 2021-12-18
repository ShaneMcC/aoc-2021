#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	preg_match('#target area: x=([-0-9]+)\.\.([-0-9]+), y=([-0-9]+)\.\.([-0-9]+)#SADi', $input, $m);
	[$all, $startX, $endX, $startY, $endY] = $m;

	$target = ['x' => ['start' => $startX, 'end' => $endX], 'y' => ['start' => $startY, 'end' => $endY]];

	function testProbe($target, $vx, $vy) {
		$probe = ['x' => 0, 'y' => 0, 'vx' => $vx, 'vy' => $vy];

		$highestY = 0;
		while (true) {
			$probe['x'] += $probe['vx'];
			$probe['y'] += $probe['vy'];
			$probe['vx'] = max($probe['vx'] - 1, 0);
			$probe['vy'] += -1;
			$highestY = max($highestY, $probe['y']);

			$inX = $probe['x'] >= $target['x']['start'] && $probe['x'] <= $target['x']['end'];
			$inY = $probe['y'] >= $target['y']['start'] && $probe['y'] <= $target['y']['end'];

			// Hit.
			if ($inX && $inY) {
				return [true, $highestY];
			}

			// Overshot X
			if ($probe['x'] > max($target['x']['start'], $target['x']['end'])) {
				return [false, 0];
			}

			// Overshot Y
			if ($probe['y'] < min($target['y']['start'], $target['y']['end'])) {
				return [false, 0];
			}

			// Stopped moving towards X.
			if ($probe['vx'] == 0 && !$inX) {
				return [false, 0];
			}
		}
	}

	$lowY = min(array_values($target['y']));

	// Doesn't work for
	//   target area: x=352..377, y=-49..-30
	//   See: https://www.reddit.com/r/adventofcode/comments/rid0g3/2021_day_17_part_1_an_input_that_might_break_your/
	//
	// $highestY = abs($lowY) * (abs($lowY) - 1) / 2;
	// echo 'Part 1: ', $highestY, "\n";

	$highestY = 0;
	$valid = 0;
	for ($vx = 0; $vx <= $target['x']['end']; $vx++) {
		for ($vy = $lowY; $vy <= abs($lowY); $vy++) {
			[$result, $testHighestY] = testProbe($target, $vx, $vy);
			if ($result) {
				if (isDebug()) { echo $vx, ',', $vy, "\n"; }
				$highestY = max($highestY, $testHighestY);
				$valid++;
			}
		}
	}

	echo 'Part 1: ', $highestY, "\n";
	echo 'Part 2: ', $valid, "\n";
