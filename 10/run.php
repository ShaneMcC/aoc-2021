#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$corruptedPoints = [')' => 3, ']' => 57, '}' => 1197, '>' => 25137];
	$incompletePoints = [')' => 1, ']' => 2, '}' => 3, '>' => 4];

	$opposite = ['(' => ')', '[' => ']', '{' => '}', '<' => '>'];

	$part1 = 0;
	$part2 = [];

	foreach ($input as $line) {
		$expected = [];
		foreach (str_split($line) as $bit) {
			if (isset($opposite[$bit])) {
				$expected[] = $opposite[$bit];
			} else {
				$wanted = array_pop($expected);
				if ($wanted != $bit) {
					$part1 += $corruptedPoints[$bit];
					continue 2; // Go to next Line, ignore this one.
				}
			}
		}

		// Line is incomplete rather than corrupted...
		$linePoints = 0;
		foreach (array_reverse($expected) as $bit) {
			$linePoints *= 5;
			$linePoints += $incompletePoints[$bit];
		}
		$part2[] = $linePoints;
	}

	sort($part2);
	$part2 = $part2[count($part2) / 2];

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
