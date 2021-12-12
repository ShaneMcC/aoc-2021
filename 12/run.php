#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$caves = [];
	foreach ($input as $line) {
		preg_match('#(.*)-(.*)#SADi', $line, $m);
		[$all, $first, $second] = $m;
		if (!isset($caves[$first])) { $caves[$first] = []; }
		if (!isset($caves[$second])) { $caves[$second] = []; }

		$caves[$first][] = $second;
		$caves[$second][] = $first;
	}

	function findPaths($caves, $start, $end, $allowTwice = false) {
		$paths = [];

		$pending = [[false, [$start]]];

		while (!empty($pending)) {
			[$hasVisitedTwice, $current] = array_pop($pending);
			$last = $current[count($current) - 1];

			foreach ($caves[$last] as $possible) {
				$next = $current;
				$next[] = $possible;
				$isSmall = strtolower($possible) == $possible;

				if ($possible == $start) { continue; }

				if ($possible == $end) {
					$paths[implode(',', $next)] = true;
					continue;
				}

				$inArray = in_array($possible, $current);
				if (!$isSmall || !$inArray) {
					$pending[] = [$hasVisitedTwice, $next];
				}

				if ($inArray && $isSmall && $allowTwice && $hasVisitedTwice === FALSE) {
					$pending[] = [true, $next];
				}

			}
		}

		return $paths;
	}

	$paths = findPaths($caves, 'start', 'end');

	$part1 = count($paths);
	echo 'Part 1: ', $part1, "\n";

	$paths = findPaths($caves, 'start', 'end', true);
	$part2 = count($paths);

	echo 'Part 2: ', $part2, "\n";
