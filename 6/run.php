#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(',', getInputLine());

	$fish = $input;

	for ($day = 1; $day <= 80; $day++) {
		for ($i = 0; $i < count($fish); $i++) {
			$fish[$i]--;
			if ($fish[$i] < 0) {
				$fish[$i] = 6;
				$fish[] = 9;
			}
		}
	}

	$part1 = count($fish);
	echo 'Part 1: ', $part1, "\n";
