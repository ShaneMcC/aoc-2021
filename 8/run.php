#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match('#(.*)\|(.*)#SADi', $line, $m);
		[$all, $tests, $number] = $m;
		$entries[] = [explode(' ', trim($tests)), explode(' ', trim($number))];
	}

	$part1 = 0;
	foreach ($entries as $e) {
		[$tests, $number] = $e;

		foreach ($number as $b) {
			if (strlen($b) == 3 || strlen($b) == 7 || strlen($b) == 4 || strlen($b) == 2) {
				$part1++;
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";
