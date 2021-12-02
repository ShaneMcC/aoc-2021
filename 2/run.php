#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$x = $y = 0;

	$directions = ['forward' => [1, 0],
	               'up' => [0, -1],
	               'down' => [0, +1],
	              ];

	foreach ($input as $inst) {
		$bits = explode(' ', $inst);
		$dir = $directions[$bits[0]];
		$x += $dir[0] * $bits[1];
		$y += $dir[1] * $bits[1];
	}

	echo 'Part 1: ', $x * $y, "\n";
