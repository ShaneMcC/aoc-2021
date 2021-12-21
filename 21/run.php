#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$players = [];
	foreach ($input as $line) {
		preg_match('#Player (.*) starting position: (.*)#SADi', $line, $m);
		[$all, $player, $position] = $m;
		$players[] = ['position' => $position, 'score' => 0];
	}

	$die = 0;

	$rollCount = 0;

	while (true) {
		foreach (array_keys($players) as $p) {
			['position' => $position, 'score' => $score] = $players[$p];

			$roll = 0;
			$roll += ($die++ % 100) + 1;
			$roll += ($die++ % 100) + 1;
			$roll += ($die++ % 100) + 1;

			$rollCount += 3;

			$position += $roll;
			while ($position > 10) { $position -= 10; } // TODO: Mod.

			$score += $position;

			$players[$p] = ['position' => $position, 'score' => $score];

			// echo 'Player ', $p, ' move to space ', $position, ' for a total: ', $score, "\n";

			if ($score >= 1000) {
				$part1 = $rollCount * $players[($p == 0 ? 1 : 0)]['score'];
				echo 'Part 1: ', $part1, "\n";
				die();
			}
		}
	}
