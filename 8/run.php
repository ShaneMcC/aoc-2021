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

	function getSegmentMap($tests) {
		/**
 		 *  0000
		 * 1    2
		 * 1    2
 		 *  3333
		 * 4    5
		 * 4    5
 		 *  6666
 		*/
		$segmentMap = array_fill(0, 7, []);

		// Get known possibilities.
		$num069 = $num235 = [];
		foreach ($tests as $t) {
			$len = strlen($t);
			$split = str_split($t);

			if ($len == 2) { $num1 = $split; }
			else if ($len == 3) { $num7 = $split; }
			else if ($len == 4) { $num4 = $split; }
			else if ($len == 5) { $num235[] = $split; } // Unused.
			else if ($len == 6) { $num069[] = $split; }
			else if ($len == 7) { $num8 = $split; } // Unused.
		}

		// segment-0 is the letter in num7 but not num1
		$segmentMap[0] = array_values(array_diff($num7, $num1))[0];

		// segment-1 is a letter in num4, but not num1 that is also present in
		// all of (0,6,9)
		// segment-3 is the other letter in num4, that is only present in 6,9
		// but not 0)
		$options = array_values(array_diff($num4, $num1));

		if (in_array($options[0], $num069[0]) &&
			in_array($options[0], $num069[1]) &&
			in_array($options[0], $num069[2])) {
			$segmentMap[1] = $options[0];
			$segmentMap[3] = $options[1];
		} else {
			$segmentMap[1] = $options[1];
			$segmentMap[3] = $options[0];
		}

		// Of the remaining letters:
		// segment-5 appears in 9 of the tests
		// segment-2 appears in 8 of the tests
		// segment-6 appears in 7 of the tests
		// segment-4 appears in 4 of the tests
		foreach (str_split('abcdefg') as $option) {
			if (in_array($option, array_values($segmentMap))) { continue; }

			$count = 0;
			foreach ($tests as $t) {
				if (strpos($t, $option) !== false) { $count++; }
			}

			if ($count == 9) { $segmentMap[5] = $option; }
			else if ($count == 8) { $segmentMap[2] = $option; }
			else if ($count == 7) { $segmentMap[6] = $option; }
			else if ($count == 4) { $segmentMap[4] = $option; }
		}

		return $segmentMap;
	}

	function getNumberMap($segmentMap) {
		$numberMap = [];

		// What segments do we want on per number
		$numbers = [];
		$numbers[0] = [0, 1, 2, 4, 5, 6];
		$numbers[1] = [2, 5];
		$numbers[2] = [0, 2, 3, 4, 6];
		$numbers[3] = [0, 2, 3, 5, 6];
		$numbers[4] = [1, 2, 3, 5];
		$numbers[5] = [0, 1, 3, 5, 6];
		$numbers[6] = [0, 1, 3, 4, 5, 6];
		$numbers[7] = [0, 2, 5];
		$numbers[8] = [0, 1, 2, 3, 4, 5, 6];
		$numbers[9] = [0, 1, 2, 3, 5, 6];

		// Convert the above into letters.
		foreach ($numbers as $num => $segments) {
			$str = [];
			foreach ($segments as $s) {
				$str[] = $segmentMap[$s];
			}
			sort($str);
			$str = implode('', $str);

			$numberMap[$str] = $num;
		}

		return $numberMap;
	}

	function getNumber($numberMap, $numberLetters) {
		$number = '';
		foreach ($numberLetters as $nl) {
			$nl = str_split($nl);
			sort($nl);
			$number .= $numberMap[implode('', $nl)];
		}

		return intval($number);
	}

	$part1 = $part2 = 0;
	foreach ($entries as $e) {
		[$tests, $number] = $e;

		foreach ($number as $b) {
			if (strlen($b) == 3 || strlen($b) == 7 || strlen($b) == 4 || strlen($b) == 2) {
				$part1++;
			}
		}

		$part2 += getNumber(getNumberMap(getSegmentMap($tests)), $number);
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
