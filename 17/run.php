#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	preg_match('#target area: x=([-0-9]+)\.\.([-0-9]+), y=([-0-9]+)\.\.([-0-9]+)#SADi', $input, $m);
	[$all, $startX, $endX, $startY, $endY] = $m;

	$target = ['x' => ['start' => $startX, 'end' => $endX], 'y' => ['start' => $startY, 'end' => $endY]];


	function step($probe) {
		$probe['x'] += $probe['vx'];
		$probe['y'] += $probe['vy'];

		$probe['vx'] += $probe['vx'] > 0 ? -1 : 1;
		$probe['vy'] += -1;

		return $probe;
	}

	function testProbe($target, $vx, $vy) {
		$probe = ['x' => 0, 'y' => 0, 'vx' => $vx, 'vy' => $vy];

		$highestY = 0;
		while (true) {
			$probe = step($probe);
			$highestY = max($highestY, $probe['y']);

			$inX = $probe['x'] >= $target['x']['start'] && $probe['x'] <= $target['x']['end'];
			$inY = $probe['y'] >= $target['y']['start'] && $probe['y'] <= $target['y']['end'];

			if ($inX && $inY) {
				return [true, $highestY];
			}

			if ($probe['y'] <= min($target['y']['start'], $target['y']['end'])) {
				return [false, $highestY];
			}
		}
	}


	$highestY = 0;
	for ($vx = 0; $vx <= $target['x']['end']; $vx++) {
		for ($vy = 0; $vy <= 200; $vy++) {
			[$result, $testHighestY] = testProbe($target, $vx, $vy);
			if ($result) {
				echo json_encode([$vx, $vy, $testHighestY, $highestY]), "\n";
				$highestY = max($highestY, $testHighestY);
			}
		}
	}

	echo 'Part 1: ', $highestY, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
