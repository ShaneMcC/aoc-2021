#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$template = array_shift($input);
	$rules = [];
	foreach ($input as $line) {
		preg_match('#(.*) -> (.*)#SADi', $line, $m);
		[$all, $matching, $insert] = $m;
		$rules[$matching] = $insert;
	}

	$pairs = [];
	$letters = array_count_values(str_split($template));
	for ($i = 0; $i < strlen($template) - 1; $i++) {
		if (!isset($pairs[$template[$i] . $template[$i + 1]])) { $pairs[$template[$i] . $template[$i + 1]] = 0; }
		$pairs[$template[$i] . $template[$i + 1]]++;
	}

	for ($i = 1; $i <= 40; $i++) {
		$newPairs = $pairs;

		foreach (array_keys($pairs) as $p) {
			$before = $p[0] . $rules[$p];
			$after = $rules[$p] . $p[1];

			if (!isset($newPairs[$before])) { $newPairs[$before] = 0; }
			if (!isset($newPairs[$after])) { $newPairs[$after] = 0; }
			if (!isset($letters[$rules[$p]])) { $letters[$rules[$p]] = 0; }

			$newPairs[$p] -= $pairs[$p];
			$newPairs[$before] += $pairs[$p];
			$newPairs[$after] += $pairs[$p];
			$letters[$rules[$p]] += $pairs[$p];

			if ($newPairs[$p] == 0) { unset($newPairs[$p]); }
		}

		$pairs = $newPairs;

		if ($i == 10) {
			asort($letters);
			$keys = array_keys($letters);
			$part1 = $letters[array_pop($keys)] - $letters[array_shift($keys)];
			echo 'Part 1: ', $part1, "\n";
		}

		if ($i == 40) {
			asort($letters);
			$keys = array_keys($letters);
			$part2 = $letters[array_pop($keys)] - $letters[array_shift($keys)];
			echo 'Part 2: ', $part2, "\n";
		}
	}
