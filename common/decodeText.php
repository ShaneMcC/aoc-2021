<?php

	// This looks like it should be considered to be 4x6, but some letters use
	// all 5 columns (Y), whereas the rest do not.
	$encodedChars[5][6] = ['011001001010010111101001010010' => 'A',
	                       '111001001011100100101001011100' => 'B',
	                       '011001001010000100001001001100' => 'C',
	                       '111001001010010100101001011100' => 'D',
	                       '111101000011100100001000011110' => 'E',
	                       '111101000011100100001000010000' => 'F',
	                       '011001001010000101101001001110' => 'G',
	                       '100101001011110100101001010010' => 'H',
	                       '' => 'I',
	                       '001100001000010000101001001100' => 'J',
	                       '100101010011000101001010010010' => 'K',
	                       '100001000010000100001000011110' => 'L',
	                       '' => 'M',
	                       '' => 'N',
	                       '011001001010010100101001001100' => 'O',
	                       '111001001010010111001000010000' => 'P',
	                       '' => 'Q',
	                       '111001001010010111001010010010' => 'R',
	                       '' => 'S',
	                       '' => 'T',
	                       '100101001010010100101001001100' => 'U',
	                       '' => 'V',
	                       '' => 'W',
	                       '' => 'X',
	                       '100011000101010001000010000100' => 'Y',
	                       '111100001000100010001000011110' => 'Z',
	                       '000000000000000000000000000000' => ' ',
	                      ];

	// 4x6 version. Remove Y, and the last column of empty spaces.
	$encodedChars[4][6] = [];
	foreach ($encodedChars[5][6] as $code => $char) {
		if ($char == 'Y') { continue; } // Not compatible.
		$encodedChars[4][6][preg_replace('/(.{4})./', '$1', $code)] = $char;
	}

	function decodeText($image, $width = 5, $height = 6, $gap = 0) {
		global $encodedChars;

		$text = '';
		$charCount = ceil((is_array($image[0]) ? count($image[0]) : strlen($image[0])) / ($width + $gap));
		$chars = [];

		if (!isset($encodedChars[$width][$height])) { return str_repeat('?', $charCount);  }
		$encChars = $encodedChars[$width][$height];

		foreach ($image as $row) {
			for ($i = 0; $i < $charCount; $i++) {
				$c = is_array($row) ? implode('', array_slice($row, ($i * ($width + $gap)), $width)) : substr($row, ($i * ($width + $gap)), $width);
				$c = str_pad(preg_replace(['/(█|[^.\s0])/', '/[.\s0]/'], [1, 0], $c), $width, '0');
				$chars[$i][] = $c;
			}
		}

		foreach ($chars as $c) {
			$id = implode('', $c);
			if (isDebug() && !isset($encChars[$id])) { echo 'Unknown Letter: ', $id, "\n"; }
			$text .= isset($encChars[$id]) ? $encChars[$id] : '?';
		}

		return $text;
	}
