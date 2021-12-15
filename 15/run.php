#!/usr/bin/php
<?php
	$__CLI['long'] = ['part1', 'part2', 'scale:'];
	$__CLI['extrahelp'] = [];
	$__CLI['extrahelp'][] = '      --part1              run part 1';
	$__CLI['extrahelp'][] = '      --part2              run part 2';
	$__CLI['extrahelp'][] = '      --scale <#>          Part 2 scale';

	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function findBestPathCost($map, $scale = 1) {
		$check = [];

		$end = [(count($map[0]) * $scale) - 1, (count($map) * $scale) - 1];

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

			if (isDebug()) { echo 'Looking at: ', $x, ', ', $y, ' at cost: ', $thisCost, "\n"; }

			$adjacent = [];
			if (isset($map[($y - 1) % $height][$x % $width])) { $adjacent[] = [$x, $y - 1]; }
			if (isset($map[$y % $height][($x - 1) % $width])) { $adjacent[] = [$x - 1, $y]; }
			if (isset($map[$y % $height][($x + 1) % $width])) { $adjacent[] = [$x + 1, $y]; }
			if (isset($map[($y + 1) % $height][$x % $width])) { $adjacent[] = [$x, $y + 1]; }

			foreach ($adjacent as [$ax, $ay]) {
				if ($ax > $end[0] || $ay > $end[1]) { continue; }
				if (isset($seen[$ay][$ax])) {
					if (isDebug()) { echo "\t", 'Ignoring already visited: ', $ax, ', ', $ay, "\n"; }
					continue;
				}

				if (!isset($seen[$ay])) { $seen[$ay] = []; }
				$seen[$ay][$ax] = true;

				$nextCost = $map[$ay % $height][$ax % $width] + floor($ay / $height) + floor($ax / $width);
				$newCost = $thisCost + ((($nextCost - 1) % 9) + 1);

				if ([$ax, $ay] == $end) {
					if (isDebug()) { echo "\t", 'Found answer: ', $ax, ', ', $ay, ' with cost: ', $newCost, "\n"; }
					return $newCost;
				} else {
					if (isDebug()) { echo "\t", 'Inserting: ', $ax, ', ', $ay, ' with cost: ', $newCost, "\n"; }
					$queue->insert([$ax, $ay], -$newCost);
				}
			}
		}

		return PHP_INT_MAX;
	}

	$runPart1 = isset($__CLIOPTS['part1']) || (!isset($__CLIOPTS['part1']) && !isset($__CLIOPTS['part2']));
	$runPart2 = isset($__CLIOPTS['part2']) || (!isset($__CLIOPTS['part1']) && !isset($__CLIOPTS['part2']));

	if ($runPart1) {
		$part1 = findBestPathCost($map);
		echo 'Part 1: ', $part1, "\n";
	}

	if ($runPart2) {
		$scale = isset($__CLIOPTS['scale']) ? (is_array($__CLIOPTS['scale']) ? $__CLIOPTS['scale'][count($__CLIOPTS['scale']) - 1] : $__CLIOPTS['scale']) : 5;

		$part2 = findBestPathCost($map, $scale);
		echo 'Part 2: ', $part2, "\n";
	}
