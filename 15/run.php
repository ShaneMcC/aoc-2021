#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function findBestPathCost($map, $scale = 1) {
		$check = [];

		$end = [(count($map) * $scale) - 1, (count($map) * $scale) - 1];

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

		$queue->insert([0, 0], 0);

		$width = count($map[0]);
		$height = count($map);

		$seen = [0 => [0 => true]];

		while (!$queue->isEmpty()) {
			$next = $queue->extract();
			$thisCost = abs($next['priority']);
			[$x, $y] = $next['data'];

			$adjacent = [];
			if (isset($map[($y - 1) % $height][$x % $width])) { $adjacent[] = [$x, $y - 1]; }
			if (isset($map[$y % $height][($x - 1) % $width])) { $adjacent[] = [$x - 1, $y]; }
			if (isset($map[$y % $height][($x + 1) % $width])) { $adjacent[] = [$x + 1, $y]; }
			if (isset($map[($y + 1) % $height][$x % $width])) { $adjacent[] = [$x, $y + 1]; }

			foreach ($adjacent as [$ax, $ay]) {
				if ($ax > $end[0] || $ay > $end[1]) { continue; }
				if (isset($seen[$ay][$ax])) { continue; }

				if (!isset($seen[$ay])) { $seen[$ay] = []; }
				$seen[$ay][$ax] = true;

				$nextCost = $map[$ay % $height][$ax % $width] + floor($ay / $height) + floor($ax / $width);
				$newCost = $thisCost + ((($nextCost - 1) % 9) + 1);

				if ([$ax, $ay] == $end) {
					return $newCost;
				} else {
					$queue->insert([$ax, $ay], -$newCost);
				}
			}
		}

		return PHP_INT_MAX;
	}

	$part1 = findBestPathCost($map);
	echo 'Part 1: ', $part1, "\n";

	$part2 = findBestPathCost($map, 5);
	echo 'Part 2: ', $part2, "\n";
