#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$corruptedPoints = [')' => 3, ']' => 57, '}' => 1197, '>' => 25137];
	$incompletePoints = [')' => 1, ']' => 2, '}' => 3, '>' => 4];

	$part1 = 0;
	$part2 = [];

	$corruptedLines = [];
	$incompleteLines = [];
	foreach ($input as $line) {
		$bits = str_split($line);
		$expected = [];
		foreach ($bits as $bit) {
			switch ($bit) {
				case '(':
					$expected[] = ')';
					break;
				case '[':
					$expected[] = ']';
					break;
				case '{':
					$expected[] = '}';
					break;
				case '<':
					$expected[] = '>';
					break;
				case ')':
				case ']':
				case '}':
				case '>':
					$wanted = empty($expected) ? '' : array_pop($expected);
					if ($wanted != $bit) {
						// echo 'Expected ', $wanted, ', but found ', $bit, ' instead.', "\n";
						$part1 += $corruptedPoints[$bit];
						continue 3;
					}
			}
		}

		$points = 0;
		foreach (array_reverse($expected) as $bit) {
			$points *= 5;
			$points += $incompletePoints[$bit];
		}
		// echo 'Complete by adding: ', implode(array_reverse($expected)), ' for ', $points, ' points.', "\n";
		$part2[] = $points;
	}

	sort($part2);
	$part2 = $part2[count($part2) / 2];

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
