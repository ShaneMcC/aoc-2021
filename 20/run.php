#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$enhancement = $input[0];

	$image = [];
	for ($i = 1; $i < count($input); $i++) {
		$image[] = str_split($input[$i]);
	}

	function getEnhancedCell($image, $enhancement, $x, $y, $default = '.') {
		$binary = '';
		for ($cY = $y - 1; $cY <= $y + 1; $cY++) {
			for ($cX = $x - 1; $cX <= $x + 1; $cX++) {
				$binary .= (isset($image[$cY][$cX]) ? $image[$cY][$cX] : $default) == '#' ? 1 : 0;
			}
		}

		$index = base_convert($binary, 2, 10);

		return $enhancement[$index];
	}

	$default = '.';
	if (isDebug()) { drawMap($image, true, '0 - ' . $default); }
	for ($count = 1; $count <= 50; $count++) {
		[$minX, $minY, $maxX, $maxY] = getBoundingBox($image, 1);

		$newImage = [];

		for ($y = $minY; $y <= $maxY; $y++) {
			if (!isset($newImage[$y])) { $newImage[$y] = []; }
			for ($x = $minX; $x <= $maxX; $x++) {
				$newImage[$y][$x] = getEnhancedCell($image, $enhancement, $x, $y, $default);
			}
		}

		$image = $newImage;

		// After the first run, all the infinite pixels will now be
		// $enhancement[0]
		//
		// On subsequent runs, they will change depending on what they
		// are currently.
		$default = ($default == '.') ? $enhancement[0] : $enhancement[511];

		if (isDebug()) { drawMap($image, true, $count . ' - ' . $default); }

		if ($count == 2) {
			echo 'Part 1: ', countCells($image, '#'), "\n";
		}

		if ($count == 50) {
			echo 'Part 2: ', countCells($image, '#'), "\n";
		}
	}
