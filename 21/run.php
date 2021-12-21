#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	[$_, $p1Start] = preg_match_return('#Player .* starting position: (.*)#SADi', $input[0]);
	[$_, $p2Start] = preg_match_return('#Player .* starting position: (.*)#SADi', $input[1]);

	$players = [1 => [$p1Start, 0],
	            2 => [$p2Start, 0],
	           ];
	$die = 0;
	$rollCount = 0;
	while (true) {
		foreach (array_keys($players) as $p) {
			[$position, $score] = $players[$p];

			$rollCount += 3;
			$roll = (($die++ % 100) + 1) + (($die++ % 100) + 1) + (($die++ % 100) + 1);

			$position = (($position + $roll - 1) % 10) + 1;
			$score += $position;

			$players[$p] = [$position, $score];

			if (isDebug()) {
				echo 'Player ', $p, ' move to space ', $position, ' for a total: ', $score, "\n";
			}

			if ($score >= 1000) {
				$part1 = $rollCount * $players[($p == 1 ? 2 : 1)][1];
				echo 'Part 1: ', $part1, "\n";
				break 2;
			}
		}
	}

	$rollOptions = [];
	$die = [1,2,3];
	foreach ($die as $d1) {
		foreach ($die as $d2) {
			foreach ($die as $d3) {
				$score = $d1 + $d2 + $d3;
				if (!isset($rollOptions[$score])) { $rollOptions[$score] = 0; }
				$rollOptions[$score]++;
			}
		}
	}

	function getWinCount($rollOptions, $p1pos, $p2pos, $p1score = 0, $p2score = 0, &$winStates = []) {
		if ($p1score >= 21) { return [1, 0]; }
		if ($p2score >= 21) { return [0, 1]; }

		$thisState = sprintf('%d,%d,%d,%d', $p1pos, $p2pos, $p1score, $p2score);

		if (!isset($winStates[$thisState])) {
			$p1wins = 0;
			$p2wins = 0;

			foreach ($rollOptions as $score => $times) {
				$newPos = (($p1pos + $score - 1) % 10) + 1;
				$newScore = $p1score + $newPos;

				[$winCount2, $winCount1] = getWinCount($rollOptions, $p2pos, $newPos, $p2score, $newScore, $winStates);
				$p1wins += $winCount1 * $times;
				$p2wins += $winCount2 * $times;
			}

			$winStates[$thisState] = [$p1wins, $p2wins];
			if (isDebug()) {
				echo 'Setting new winState: ', $thisState, ' = [', $p1w, ', ', $p2w, ']', "\n";
			}
		} else {
			if (isDebug()) {
				echo 'Known winState: ', $thisState, ' = [', $winStates[$thisState][0], ', ', $winStates[$thisState][1], ']', "\n";
			}
		}

		return $winStates[$thisState];
	}

	$part2 = getWinCount($rollOptions, $p1Start, $p2Start);

	if ($part2[0] > $part2[1]) {
		echo 'Part 2: Player 1 wins with ', $part2[0], ' (vs ', $part2[1], ')', "\n";
	} else {
		echo 'Part 2: Player 2 wins with ', $part2[1], ' (vs ', $part2[0], ')', "\n";
	}
