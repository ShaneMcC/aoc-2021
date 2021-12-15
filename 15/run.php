#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function findBestPath($map, $start, $end) {
		$check = [];

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

		$queue->insert([$start], 0);

		$bestPath = NULL;
		$bestCost = PHP_INT_MAX;

		$bestCosts = [];

		while (!$queue->isEmpty()) {
			$next = $queue->extract();
			[$thisPath, $thisCost] = [$next['data'], abs($next['priority'])];

			// Are we higher than the best cost in general?
			if ($thisCost >= $bestCost) { continue; }

			[$x, $y] = $thisPath[count($thisPath) - 1];

			// Are we higher than the best cost to this point?
			if (isset($bestCosts[$y][$x])) {
				if ($thisCost >= $bestCosts[$y][$x]) { continue; }
			} else {
				if (!isset($bestCosts[$y])) { $bestCosts[$y] = []; }
				$bestCosts[$y][$x] = $thisCost;
			}

			foreach (getAdjacentCells($map, $x, $y) as [$ax, $ay]) {
				if (in_array([$ax, $ay], $thisPath)) { continue; }

				$newPath = $thisPath;
				$newPath[] = [$ax, $ay];
				$newCost = $thisCost + $map[$ay][$ax];

				// echo ' => ', $ax, ', ', $ay, ' = ', $newCost, "\n";

				if ([$ax, $ay] == $end) {
					// echo "!";
					if ($newCost < $bestCost) {
						$bestPath = $newPath;
						$bestCost = $newCost;
						// echo $bestCost, "\n";
					}
				} else {
					$queue->insert($newPath, -$newCost);
				}
			}
		}

		return $bestCost;
	}

	$part1 = findBestPath($map, [0, 0], [count($map) - 1, count($map) - 1]);
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
