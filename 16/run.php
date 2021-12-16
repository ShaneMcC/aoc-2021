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

// var_dump(getPackets(getBinary('D2FE28')));
// var_dump(getPackets(getBinary('38006F45291200')));
// var_dump(getPackets(getBinary('EE00D40C823060')));
// var_dump(getVersionSum(getPackets(getBinary('8A004A801A8002F478'))));
// var_dump(getVersionSum(getPackets(getBinary('620080001611562C8802118E34'))));
// var_dump(getVersionSum(getPackets(getBinary('C0015000016115A2E0802F182340'))));
// var_dump(getVersionSum(getPackets(getBinary('A0016C880162017C3686B18A3D4780'))));

	[$packets, ] = getPackets(getBinary($input));

	$part1 = getVersionSum($packets);
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
