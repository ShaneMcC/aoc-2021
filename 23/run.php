#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$moveCost = ['A' => 1, 'B' => 10, 'C' => 100, 'D' => 1000];

	$namedLocations = [];

	// Rooms, front then rear spaces.
	$namedLocations['r1'] = [[3, 2], [3, 3]];
	$namedLocations['r2'] = [[5, 2], [5, 3]];
	$namedLocations['r3'] = [[7, 2], [7, 3]];
	$namedLocations['r4'] = [[9, 2], [9, 3]];
	$namedLocations['hallway'] = [[1, 1],[2, 1],[4, 1],[6, 1],[8, 1],[10, 1],[11, 1]]; // Excluding doorways
	$namedLocations['rooms'] = array_merge($namedLocations['r1'], $namedLocations['r2'], $namedLocations['r3'], $namedLocations['r4']);

	// A can also move to r1
	$validTargets['A'] = $namedLocations['r1'];
	// B can also move to r2
	$validTargets['B'] = $namedLocations['r2'];
	// C can also move to r3
	$validTargets['C'] = $namedLocations['r3'];
	// D can also move to r4
	$validTargets['D'] = $namedLocations['r4'];

	// Find all places we can move to and be happy with.
	function findMoveableLocations($map, $from) {
		global $namedLocations, $validTargets, $moveCost;

		[$fX, $fY] = $from;

		$me = $map[$fY][$fX];

		if (!isset($validTargets[$me])) { return []; }

		// Are we in the hallway currently?
		$inHallway = in_array($from, $namedLocations['hallway']);
		// What does my room look like?
		$myRoom = '';
		foreach ($validTargets[$me] as [$pX, $pY]) {
			$myRoom .= $map[$pY][$pX];
		}

		$possible = [];
		$checked = [];
		$check = [[$from, 0]];
		while (!empty($check)) {
			[[$fX, $fY], $cost] = array_pop($check);

			foreach (getAdjacentCells($map, $fX, $fY) as $cell) {
				[$cX, $cY] = $cell;
				if (in_array($cell, $checked)) { continue; }

				$checked[] = $cell;
				if ($map[$cY][$cX] == '.') {
					$validLocation = false;

					// We can move into the hallway if we're not in the hallway.
					$validLocation = $validLocation || (!$inHallway && in_array($cell, $namedLocations['hallway']));

					// We can move into our target room, if it is empty, or contains 1 of me.
					// If it's empty, only to the back location.
					//
					// This is a bit horrible to look at. Sorry.
					$validLocation = $validLocation || (in_array($cell, $validTargets[$me]) && (($myRoom == '..' && $cell == $validTargets[$me][1]) || $myRoom == '.' . $me));

					if ($validLocation) {
						$possible[] = [$cell, $cost + 1];
					}
					$check[] = [$cell, $cost + 1];
				}
			}
		}

		// Update costs based on our character type.
		foreach (array_keys($possible) as $p) {
			$possible[$p][1] = $possible[$p][1] * $moveCost[$me];
		}

		// Sort by highest cost.
		usort($possible, function ($a, $b) { return ($a[1] <=> $b[1]); });
		rsort($possible);
		return $possible;
	}

	function isFinalLocations($map) {
		global $validTargets;
		foreach ($validTargets as $type => $locations) {
			foreach ($locations as [$lX, $lY]) {
				if (isset($map[$lY][$lX]) && $map[$lY][$lX] != $type) {
					return false;
				}
			}
		}

		return true;
	}

	$queue = new SPLPriorityQueue();
	$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
	$queue->insert($map, 0);

	$seen = [];

	$bestMap = NULL;
	$bestCost = PHP_INT_MAX;
	while (!$queue->isEmpty()) {
		$next = $queue->extract();
		$thisCost = abs($next['priority']);
		$map = $next['data'];

		if ($thisCost >= $bestCost) { continue; }

		/* echo '==========', "\n";
		drawMap($map);
		echo 'Cost: ', $thisCost, "\n";
		echo '==========', "\n"; */

		// Foreach character, add all their possible moves into the queue.
		$characters = [];
		foreach (cells($map) as [$cX, $cY, $cell]) {
			if (!isset($validTargets[$cell])) { continue; }

			$cLoc = [$cX, $cY];
			$cName = $map[$cY][$cX];

			$locations = findMoveableLocations($map, $cLoc);
			// echo 'Found ', count($locations), ' locations for character ', $c, ' (', $cName, ') at [', implode(', ', $cLoc), '] to move to: ', "\n";
			foreach ($locations as $l) {
				$newMap = $map;
				[[$lX, $lY], $lCost] = $l;
				$newMap[$cY][$cX] = '.'; // Old Location
				$newMap[$lY][$lX] = $cName; // new Location
				$newCost = ($thisCost + $lCost);
				$mapHash = json_encode($newMap);

				if ($newCost >= $bestCost) { continue; }

				if (isFinalLocations($newMap)) {
					$bestCost = $newCost;
					$bestMap = $newMap;
				} else {
					if (!isset($seen[$mapHash]) || $seen[$mapHash] > $newCost) {
						$seen[$mapHash] = $newCost;
						$queue->insert($newMap, -$newCost);
					}
				}
			}
		}
	}

	echo 'Part 1: ', $bestCost, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
