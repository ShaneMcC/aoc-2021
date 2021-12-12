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

	function findPaths($caves, $start, $end, $allowTwice = '') {
		$paths = [];

		$pending = [[$start]];

		while (!empty($pending)) {
			$current = array_pop($pending);
			$last = $current[count($current) - 1];

			foreach ($caves[$last] as $possible) {
				$next = $current;
				$next[] = $possible;
				$isSmall = strtolower($possible) == $possible;

				if ($possible == $start) { continue; }

				if ($possible == $end) {
					$paths[] = $next;
					continue;
				}

				$inArray = in_array($possible, $current);
				if (!$isSmall || !$inArray) {
					$pending[] = $next;
				}

				if ($inArray && $possible == $allowTwice) {
					$count = count(array_filter($current, function($a) use ($possible) {return $a == $possible;}));
					if ($count == 1) {
						$pending[] = $next;
					}
				}

			}
		}

		return $paths;
	}

	$paths = findPaths($caves, 'start', 'end');

	$part1 = count($paths);
	echo 'Part 1: ', $part1, "\n";


	foreach (array_keys($caves) as $c) {
		if (strtolower($c) == $c && $c != 'start' && $c != 'end') {
			$paths = array_merge($paths, findPaths($caves, 'start', 'end', $c));
		}
	}
	$paths = array_unique($paths, SORT_REGULAR);
	$part2 = count($paths);

	echo 'Part 2: ', $part2, "\n";
