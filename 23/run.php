#!/usr/bin/php
<?php
	$__CLI['long'] = ['part1', 'part2', 'history'];
	$__CLI['extrahelp'] = [];
	$__CLI['extrahelp'][] = '      --part1              run part 1';
	$__CLI['extrahelp'][] = '      --part2              run part 2';
	$__CLI['extrahelp'][] = '      --history            show history';
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputLines();

	$moveCost = ['A' => 1, 'B' => 10, 'C' => 100, 'D' => 1000];

	$validTargets = [];

	// Valid hallway spaces.
	$validTargets['hallway'] = [[1, 1],[2, 1],[4, 1],[6, 1],[8, 1],[10, 1],[11, 1]]; // Excluding doorways

	// Targets that the amphipods want to move to (front-to-back)
	$validTargets['A'] = [[3, 2], [3, 3]];
	$validTargets['B'] = [[5, 2], [5, 3]];
	$validTargets['C'] = [[7, 2], [7, 3]];
	$validTargets['D'] = [[9, 2], [9, 3]];

	// Find all places we can move to and be happy with.
	function findMoveableLocations($map, $from, $validTargets, $moveCost) {
		[$fX, $fY] = $from;

		$me = $map[$fY][$fX];

		if (!isset($validTargets[$me])) { return []; }

		// Is my room a valid destination?
		$myRoomValid = true;
		$myRoomValidTarget = false;
		foreach ($validTargets[$me] as [$pX, $pY]) {
			if ($map[$pY][$pX] == '.') {
				$myRoomValidTarget = [$pX, $pY];
			} else if ($map[$pY][$pX] != $me) {
				$myRoomValid = false;
				$myRoomValidTarget = false;
				break;
			}
		}

		// If the room is valid, and we're in it, we can't go anywhere.
		if ($myRoomValid && in_array($from, $validTargets[$me])) {
			return [];
		}

		// Are we in the hallway currently?
		$inHallway = in_array($from, $validTargets['hallway']);

		$possible = [];
		$checked = [];
		$check = [[$from, 0]];
		while (!empty($check)) {
			[[$fX, $fY], $cost] = array_pop($check);

			foreach (getAdjacentCells($map, $fX, $fY) as $cell) {
				if (in_array($cell, $checked)) { continue; }
				$checked[] = $cell;
				[$cX, $cY] = $cell;
				if ($map[$cY][$cX] == '.') {
					$next = [$cell, $cost + $moveCost[$me]];

					// We can move into our target room if it's empty or only
					// contains other instances of me, and we can only move as
					// far back as possible. (Checked above, if this is true
					// then $myRoomValidTarget will be the cell to move into)
					//
					// We can move into the hallway if we're not in the hallway
					if ($cell === $myRoomValidTarget || (!$inHallway && in_array($cell, $validTargets['hallway']))) {
						$possible[] = $next;
					}
					$check[] = $next;
				}
			}
		}

		return $possible;
	}

	function isFinalLocations($map, $validTargets, $moveCost) {
		foreach ($moveCost as $type => $cost) {
			foreach ($validTargets[$type] as [$lX, $lY]) {
				if (isset($map[$lY][$lX]) && $map[$lY][$lX] != $type) {
					return false;
				}
			}
		}

		return true;
	}

	function findAnswer($map, $validTargets, $moveCost) {
		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$map, []], 0);

		$seen = [];

		while (!$queue->isEmpty()) {
			$next = $queue->extract();
			$thisCost = abs($next['priority']);
			[$map, $history] = $next['data'];

			if (isFinalLocations($map, $validTargets, $moveCost)) {
				return [$thisCost, array_merge($history, [$map])];
			}

			if (isDebug()) {
				echo '==========', "\n";
				drawMap($map);
				echo 'Cost: ', $thisCost, "\n";
				echo '==========', "\n";
			}

			foreach ($map as $cY => $row) {
				for ($cX = 0; $cX < strlen($row); $cX++) {
					$cLoc = [$cX, $cY];
					$cName = $map[$cY][$cX];
					if (!isset($validTargets[$cName])) { continue; }

					foreach (findMoveableLocations($map, $cLoc, $validTargets, $moveCost) as [[$lX, $lY], $lCost]) {
						$newMap = $map;
						$newMap[$cY][$cX] = '.'; // Old Location
						$newMap[$lY][$lX] = $cName; // new Location
						$newCost = ($thisCost + $lCost);
						$mapHash = implode('', $newMap);

						if (!isset($seen[$mapHash]) || $seen[$mapHash] > $newCost) {
							$seen[$mapHash] = $newCost;
							$queue->insert([$newMap, array_merge($history, [$map])], -$newCost);
						}
					}
				}
			}
		}

		return $keepHistory ? [PHP_INT_MAX, []] : PHP_INT_MAX;
	}

	$runPart1 = isset($__CLIOPTS['part1']) || (!isset($__CLIOPTS['part1']) && !isset($__CLIOPTS['part2']));
	$runPart2 = isset($__CLIOPTS['part2']) || (!isset($__CLIOPTS['part1']) && !isset($__CLIOPTS['part2']));
	$showHistory = isset($__CLIOPTS['history']);

	if ($runPart1) {
		[$part1, $history] = findAnswer($map, $validTargets, $moveCost);

		if ($showHistory) {
			foreach ($history as $h) {
				drawSparseMap($h, ' ', true);
			}
		}

		echo 'Part 1: ', $part1, "\n";
	}

	if ($runPart2) {
		// Modify the map...
		$map[5] = $map[3];
		$map[6] = $map[4];
		$map[3] = '  #D#C#B#A#';
		$map[4] = '  #D#B#A#C#';

		// And the targets...
		$validTargets['A'] = [[3, 2], [3, 3], [3, 4], [3, 5]];
		$validTargets['B'] = [[5, 2], [5, 3], [5, 4], [5, 5]];
		$validTargets['C'] = [[7, 2], [7, 3], [7, 4], [7, 5]];
		$validTargets['D'] = [[9, 2], [9, 3], [9, 4], [9, 5]];

		// And go again...
		[$part2, $history] = findAnswer($map, $validTargets, $moveCost);
		if ($showHistory) {
			foreach ($history as $h) {
				drawSparseMap($h, ' ', true);
			}
		}
		echo 'Part 2: ', $part2, "\n";
	}
