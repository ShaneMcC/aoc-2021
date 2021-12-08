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

		// Lump things together by length.
		$lengthMap = [];
		foreach ($tests as $t) {
			$len = strlen($t);
			if (!isset($lengthMap[$len])) { $lengthMap[$len] = []; }
			$lengthMap[$len][] = str_split($t);
		}

		// We know what these are as they are the only options.
		$num1 = $lengthMap[2][0];
		$num4 = $lengthMap[4][0];
		$num7 = $lengthMap[3][0];
		$num8 = $lengthMap[7][0];

		// segment-0 is the letter in num7 but not num1
		$segmentMap[0] = array_values(array_diff($num7, $num1))[0];

		// segment-1 is a letter in num4, but not num1
		// And that is also present in all 3 of the "6-length" options (0,6,9)
		// This also then tells us what segment-3 is (it is the other one, that
		// is thus only present in 6,9 but not 0)
		$options = array_values(array_diff($num4, $num1));

		if (in_array($options[0], $lengthMap[6][0]) &&
			in_array($options[0], $lengthMap[6][1]) &&
			in_array($options[0], $lengthMap[6][2])) {
			$segmentMap[1] = $options[0];
			$segmentMap[3] = $options[1];
		} else {
			$segmentMap[1] = $options[1];
			$segmentMap[3] = $options[0];
		}

		// We can also work out the segment-2 and segment-5 from the 1, by
		// looking at (0,6,9) segment-5 appears in all 3, segment-2 does not.
		$options = $num1;
		if (in_array($options[0], $lengthMap[6][0]) &&
			in_array($options[0], $lengthMap[6][1]) &&
			in_array($options[0], $lengthMap[6][2])) {
			$segmentMap[5] = $options[0];
			$segmentMap[2] = $options[1];
		} else {
			$segmentMap[5] = $options[1];
			$segmentMap[2] = $options[0];
		}

		// segment-6 appears in exactly 7 of the tests and isn't segment-3.
		foreach (str_split('abcdefg') as $option) {
			// Ignore options we already know of
			if (in_array($option, array_values($segmentMap))) { continue; }

			$count = 0;
			foreach ($tests as $t) {
				if (strpos($t, $option) !== false) { $count++; }
			}
			if ($count == 7) {
				$segmentMap[6] = $option;
			}
		}

		// segment-4 is the remaining one.
		foreach (str_split('abcdefg') as $option) {
			if (in_array($option, array_values($segmentMap))) { continue; }
			$segmentMap[4] = $option;
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

		// Get the number map
		$numberMap = getNumberMap(getSegmentMap($tests));
		$part2 += getNumber($numberMap, $number);
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
