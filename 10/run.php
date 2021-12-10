#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$points = [')' => 3, ']' => 57, '}' => 1197, '>' => 25137];

	$part1 = 0;

	$validLines = [];
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
						$corruptedLines[] = $line;
						$part1 += $points[$bit];
						break 2;
					}
			}
		}
		if (empty($expected)) {
			$validLines[] = $line;
		} else {
			$incompleteLines[] = $line;
		}
	}

	echo 'Part 1: ', $part1, "\n";
