#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	class Board {
		private $input = [];
		private $board = [];
		private $numbers = [];
		private $isWinner = false;

		public function __construct($input) {
			$this->input = $input;
			$this->reset();
		}

		public function reset() {
			$this->board = [];
			$this->isWinner = false;
			foreach ($this->input as $line) {
				$this->board[] = preg_split('/\s+/', trim($line));
			}

			$this->numbers = [];
			for ($line = 0; $line < count($this->board); $line++) {
				for ($col = 0; $col < count($this->board[$line]); $col++) {
					$number = $this->board[$line][$col];
					$this->numbers[$number] = [$line, $col];
				}
			}
		}

		public function mark($number) {
			if (isset($this->numbers[$number])) {
				[$line, $col] = $this->numbers[$number];
				$this->board[$line][$col] = 'X';
				$this->isWinner = $this->check($line, $col);
			}
		}

		private function check($line, $col) {
			$row = $this->board[$line];
			$acv = array_count_values($row);
			if (isset($acv['X']) && $acv['X'] == count($this->board)) {
				return true;
			}

			$column = array_column($this->board, $col);
			$acv = array_count_values($column);
			if (isset($acv['X']) && $acv['X'] == count($this->board)) {
				return true;
			}

			return false;
		}

		public function isWinner() {
			return $this->isWinner;
		}

		public function value() {
			$val = 0;
			foreach ($this->board as $line) {
				foreach ($line as $col) {
					if ($col != 'X') {
						$val += $col;
					}
				}
			}
			return $val;
		}

		public function __toString() {
			$s = '';
			foreach ($this->board as $line) {
				foreach ($line as $col) {
					$s .= sprintf('%2s ', $col);
				}
				$s .= "\n";
			}
			return $s;
		}
	}

	$numbers = explode(',', $input[0][0]);
	$boards = [];
	for ($i = 1; $i < count($input); $i++) {
		$boards[] = new Board($input[$i]);
	}

	$part1 = $part2 = null;
	$winners = 0;
	foreach ($numbers as $num) {
		foreach ($boards as $b) {
			if ($b->isWinner()) { continue; }

			$b->mark($num);
			if ($b->isWinner()) {
				if ($part1 == null) {
					$part1 = $b->value() * $num;
				}
				if ($winners == count($boards) - 1) {
					$part2 = $b->value() * $num;
				}
				$winners++;
			}
		}
		if ($winners == count($boards)) { break; }
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
