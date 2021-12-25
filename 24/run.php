#!/usr/bin/php
<?php

	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/../common/VM.php');
	$input = VM::parseInstrLines(getInputLines());

	// This is an implementation of
	//
	// https://www.reddit.com/r/adventofcode/comments/rnejv5/2021_day_24_solutions/hpv7g7j
	//
	// I don't fully understand it at the moment, but it does the job.

	$digits = [];
	$stack = [];
	$dig = 0;
	foreach ($input as $i => $line) {
		if ($i % 18 == 4) {
			$push = $line[1][1] == '1';
		}
		if ($i % 18 == 5) {
			$sub = (int)$line[1][1];
		}
		if ($i % 18 == 15) {
			if ($push) {
				$stack[] = [$dig, (int)$line[1][1]];
			} else {
				[$sibling, $add] = array_pop($stack);
				$diff = $add + $sub;
				if ($diff < 0) {
					$digits[$sibling] = [-$diff + 1, 9];
					$digits[$dig] = [1, 9 + $diff];
				} else {
					$digits[$sibling] = [1, 9 - $diff];
					$digits[$dig] = [1 + $diff, 9];
				}
			}
			$dig += 1;
		}
	}

	$part1 = '';
	$part2 = '';
	for ($i = 0; $i < 14; $i++) {
		$part1 .= $digits[$i][1];
		$part2 .= $digits[$i][0];
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
