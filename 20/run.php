#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$enhancement = $input[0];

	$image = [];
	for ($i = 1; $i < count($input); $i++) {
		$image[] = str_split($input[$i]);
	}

	function getEnhancedCell($image, $enhancement, $x, $y) {
		$checkCells = [];
		$checkCells[] = [$x - 1, $y - 1];
		$checkCells[] = [$x, $y - 1];
		$checkCells[] = [$x + 1, $y - 1];
		$checkCells[] = [$x - 1, $y];
		$checkCells[] = [$x, $y];
		$checkCells[] = [$x + 1, $y];
		$checkCells[] = [$x - 1, $y + 1];
		$checkCells[] = [$x, $y + 1];
		$checkCells[] = [$x + 1, $y + 1];

		$binary = '';
		foreach ($checkCells as [$cX, $cY]) {
			$binary .= isset($image[$cY][$cX]) && $image[$cY][$cX] == '#' ? 1 : 0;
		}

		$index = base_convert($binary, 2, 10);

		return $enhancement[$index];
	}

	$padding = 10;
	for ($count = 0; $count < 2; $count++) {
		drawMap($image, true);
		[$minX, $minY, $maxX, $maxY] = getBoundingBox($image);

		$newImage = [];

		for ($y = $minY - $padding; $y <= $maxY + $padding; $y++) {
			for ($x = $minX - $padding; $x <= $maxX + $padding; $x++) {
				if (!isset($newImage[$y])) { $newImage[$y] = []; }
				$newImage[$y][$x] = getEnhancedCell($image, $enhancement, $x, $y);
			}
		}

		$image = $newImage;
	}

	drawMap($image, true);

	$part1 = 0;

	[$minX, $minY, $maxX, $maxY] = getBoundingBox($image);
	for ($y = $minY + $padding + 1; $y <= $maxY - $padding - 1; $y++) {
		for ($x = $minX + $padding + 1; $x <= $maxX - $padding - 1; $x++) {
			if ($image[$y][$x] == '#') {
				$part1++;
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";
