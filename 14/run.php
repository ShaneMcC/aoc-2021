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

	$previous = '';
	if (isDebug()) { echo 'Step 0: ', $template, "\n"; }
	for ($i = 1; $i <= 10; $i++) {
		$previous = $template;
		$template = '';
		for ($j = 0; $j < strlen($previous) - 1; $j++) {
			$key = $previous[$j] . $previous[$j + 1];
			$template .= $previous[$j];
			if (isset($rules[$key])) {
				$template .= $rules[$key];
			}
		}
		$template .= $previous[strlen($previous) - 1];

		if (isDebug()) { echo 'Step ', $i, ': ', $template, "\n"; }
	}

	$acv = array_count_values(str_split($template));
	asort($acv);
	$keys = array_keys($acv);
	$least = $acv[array_shift($keys)];
	$most = $acv[array_pop($keys)];

	$part1 = $most - $least;
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
