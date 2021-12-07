#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(',', getInputLine());

	$fish = array_fill(0, 10, 0);
	foreach ($input as $f) {
		$fish[$f]++;
	}

	$day = 1;
	do {
		$newFish = [];

		// Reset our counter.
		$fish[7] += $fish[0];

		// Add new Fish
		$fish[9] += $fish[0];

		// Decrease all fish values.
		for ($i = 0; $i < count($fish); $i++) {
			$fish[$i] = isset($fish[$i + 1]) ? $fish[$i + 1] : 0;
		}

		if ($day == 80) {
			$part1 = array_sum(array_values($fish));
			echo 'Part 1: ', $part1, "\n";
		}

		if ($day == 256) {
			$part2 = array_sum(array_values($fish));
			echo 'Part 2: ', $part2, "\n";
			break;
		}
	} while ($day++);
