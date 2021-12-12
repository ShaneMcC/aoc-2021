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

	function findPaths($caves, $allowTwice = false) {
		$paths = [];

		$pending = [[false, ['start']]];

		while (!empty($pending)) {
			[$hasVisitedTwice, $currentPath] = array_pop($pending);
			$last = $currentPath[count($currentPath) - 1];

			foreach ($caves[$last] as $nextCave) {
				$nextPath = $currentPath;
				$nextPath[] = $nextCave;
				$isSmallCave = strtolower($nextCave) == $nextCave;

				if ($nextCave == 'start') { continue; }

				if ($nextCave == 'end') {
					$paths[implode(',', $nextPath)] = true;
					continue;
				}

				$hasVisited = in_array($nextCave, $currentPath);
				if (!$hasVisited || !$isSmallCave) {
					$pending[] = [$hasVisitedTwice, $nextPath];
				}

				if ($hasVisited && $isSmallCave && $allowTwice && $hasVisitedTwice === false) {
					$pending[] = [true, $nextPath];
				}

			}
		}

		return $paths;
	}

	$paths = findPaths($caves);

	$part1 = count($paths);
	echo 'Part 1: ', $part1, "\n";

	$paths = findPaths($caves, true);
	$part2 = count($paths);

	echo 'Part 2: ', $part2, "\n";
