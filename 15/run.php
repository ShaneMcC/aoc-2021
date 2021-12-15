#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function findBestPathCost($map, $scale = 1) {
		$check = [];

		$end = [(count($map) * $scale) - 1, (count($map) * $scale) - 1];

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

		$queue->insert(['seen' => [0 => [0 => true]], 'last' => [0, 0]], 0);

		$bestCost = PHP_INT_MAX;

		$bestCosts = [];

		$width = count($map[0]);
		$height = count($map);

		while (!$queue->isEmpty()) {
			$next = $queue->extract();
			$thisCost = abs($next['priority']);
			$thisSeen = $next['data']['seen'];
			[$x, $y] = $next['data']['last'];

			// Are we higher than the best cost in general?
			if ($thisCost >= $bestCost) { continue; }

			// Are we higher than the best cost to this point?
			if (isset($bestCosts[$y][$x])) {
				if ($thisCost >= $bestCosts[$y][$x]) { continue; }
			} else {
				if (!isset($bestCosts[$y])) { $bestCosts[$y] = []; }
				$bestCosts[$y][$x] = $thisCost;
			}

			$adjacent = [];
			if (isset($map[($y - 1) % $height][$x % $width])) { $adjacent[] = [$x, $y - 1]; }
			if (isset($map[$y % $height][($x - 1) % $width])) { $adjacent[] = [$x - 1, $y]; }
			if (isset($map[$y % $height][($x + 1) % $width])) { $adjacent[] = [$x + 1, $y]; }
			if (isset($map[($y + 1) % $height][$x % $width])) { $adjacent[] = [$x, $y + 1]; }

			foreach ($adjacent as [$ax, $ay]) {
				if ($ax > $end[0] || $ay > $end[1]) { continue; }
				if (isset($thisSeen[$ay][$ax])) { continue; }

				$newSeen = $thisSeen;
				if (!isset($newSeen[$ay])) { $newSeen[$ay] = []; }
				$newSeen[$ay][$ax] = true;
				$nextCost = $map[$ay % $height][$ax % $width] + floor($ay / $height) + floor($ax / $width);
				$newCost = $thisCost + ((($nextCost - 1) % 9) + 1);

				if ([$ax, $ay] == $end) {
					if ($newCost < $bestCost) {
						$bestCost = $newCost;
					}
				} else {
					$queue->insert(['seen' => $thisSeen, 'last' => [$ax, $ay]], -$newCost);
				}
			}
		}

		return $bestCost;
	}

	$part1 = findBestPathCost($map);
	echo 'Part 1: ', $part1, "\n";

	$part2 = findBestPathCost($map, 5);
	echo 'Part 2: ', $part2, "\n";
