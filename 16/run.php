#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function getBinary($hex) {
		$binary = '';
		foreach (str_split($hex) as $c) {
			$binary .= str_pad(base_convert($c, 16, 2), 4, '0', STR_PAD_LEFT);
		}
		return $binary;
	}

	function getPackets($binary, $packetCount = 1) {
		$packets = [];

		$binlen = strlen($binary);
		$ptr = 0;

		 while ($packetCount-- != 0 && $ptr < $binlen) {
			$packet = [];
			$packet['version'] = base_convert((substr($binary, $ptr, 3)), 2, 10); $ptr += 3;
			$packet['type'] = base_convert((substr($binary, $ptr, 3)), 2, 10); $ptr += 3;

			if ($packet['type'] == 4) { // Number
				$number = '';
				while (true) {
					$bit = substr($binary, $ptr, 1); $ptr += 1;
					$number .= substr($binary, $ptr, 4); $ptr += 4;

					if ($bit == 0) { break; }
				}
				$packet['number'] = base_convert($number, 2, 10);
			} else { // Operator
				$packet['opLengthType'] = substr($binary, $ptr, 1); $ptr += 1;

				if ($packet['opLengthType'] == 0) {
					$packet['opLength'] = base_convert(substr($binary, $ptr, 15), 2, 10); $ptr += 15;
					[$packet['packets'], $count] = getPackets(substr($binary, $ptr, $packet['opLength']), -1);
					$ptr += $packet['opLength'];
				} else if ($packet['opLengthType'] == 1) {
					$packet['opCount'] = base_convert(substr($binary, $ptr, 11), 2, 10); $ptr += 11;
					[$packet['packets'], $count] = getPackets(substr($binary, $ptr), $packet['opCount']);
					$ptr += $count;
				}
			}

			$packets[] = $packet;
		}

		return [$packets, $ptr];
	}

	function processPacket($packet) {
		$values = [];
		if (isset($packet['packets'])) {
			foreach ($packet['packets'] as $p) {
				$values[] = processPacket($p);
			}
		}

		if ($packet['type'] == '0') {
			return array_sum($values);
		} else if ($packet['type'] == '1') {
			return array_product($values);
		} else if ($packet['type'] == '2') {
			return min($values);
		} else if ($packet['type'] == '3') {
			return max($values);
		} else if ($packet['type'] == '4') {
			return intval($packet['number']);
		} else if ($packet['type'] == '5' && count($values) == 2) {
			return intval($values[0] > $values[1]);
		} else if ($packet['type'] == '6' && count($values) == 2) {
			return intval($values[0] < $values[1]);
		} else if ($packet['type'] == '7' && count($values) == 2) {
			return intval($values[0] == $values[1]);
		} else {
			throw new Exception('Bad packet.');
		}
	}

	function getVersionSum($packets) {
		$sum = 0;
		foreach ($packets as $packet) {
			$sum += $packet['version'];
			if (isset($packet['packets'])) {
				$sum += getVersionSum($packet['packets']);
			}
		}

		return $sum;
	}

	[$packets, ] = getPackets(getBinary($input));

	$part1 = getVersionSum($packets);
	echo 'Part 1: ', $part1, "\n";

	$part2 = processPacket($packets[0]);
	echo 'Part 2: ', $part2, "\n";
