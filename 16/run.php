#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	class PacketStream {
		private $stream = '';
		private $ptr = 0;

		public function __construct($stream, $isBinary = false) {
			if ($isBinary) {
				$this->stream = $stream;
			} else {
				foreach (str_split($stream) as $c) {
					$this->stream .= str_pad(base_convert($c, 16, 2), 4, '0', STR_PAD_LEFT);
				}
			}
		}

		private function consume($bits) {
			$this->ptr += $bits;
			return substr($this->stream, $this->ptr - $bits, $bits);
		}

		private function hasMore($maxPtr = null) {
			return $this->ptr < ($maxPtr == null ? strlen($this->stream) : $maxPtr);
		}

		private function processPackets($maxPacketCount = 1, $maxPtr = null) {
			$packets = [];

			 while ($maxPacketCount-- != 0 && $this->hasMore($maxPtr)) {
				$packet = [];
				$packet['version'] = base_convert($this->consume(3), 2, 10);
				$packet['type'] = base_convert($this->consume(3), 2, 10);

				if ($packet['type'] == 4) { // Number
					$number = '';
					while (true) {
						$bit = $this->consume(1);
						$number .= $this->consume(4);

						if ($bit == 0) { break; }
					}
					$packet['number'] = base_convert($number, 2, 10);
				} else { // Operator
					$packet['opLengthType'] = $this->consume(1);

					if ($packet['opLengthType'] == 0) {
						$packet['opLength'] = base_convert($this->consume(15), 2, 10);
						$packet['packets'] = $this->processPackets(-1, $this->ptr + $packet['opLength']);
					} else if ($packet['opLengthType'] == 1) {
						$packet['opCount'] = base_convert($this->consume(11), 2, 10);
						$packet['packets'] = $this->processPackets($packet['opCount']);
					}
				}

				$packets[] = $packet;
			}

			return $packets;
		}

		public function getPackets() {
			return $this->processPackets(1, null);
		}
	}

	function processPacket($packet) {
		$values = [];
		if (isset($packet['packets'])) {
			foreach ($packet['packets'] as $p) {
				$values[] = processPacket($p);
			}
		}

		switch ($packet['type']) {
			case 0:
				return array_sum($values);
			case 1:
				return array_product($values);
			case 2:
				return min($values);
			case 3:
				return max($values);
			case 4:
				return intval($packet['number']);
			case 5:
				return intval($values[0] > $values[1]);
			case 6:
				return intval($values[0] < $values[1]);
			case 7:
				return intval($values[0] == $values[1]);
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

	$packets = (new PacketStream($input))->getPackets();
	if (isDebug()) { echo json_encode($packets, JSON_PRETTY_PRINT), "\n"; }

	$part1 = getVersionSum($packets);
	echo 'Part 1: ', $part1, "\n";

	$part2 = processPacket($packets[0]);
	echo 'Part 2: ', $part2, "\n";
