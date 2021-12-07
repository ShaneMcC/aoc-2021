#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	class Board {
		private $input = [];
		private $board = [];

		public function __construct($input) {
			$this->input = $input;
			$this->reset();
		}

		public function reset() {
			$this->board = [];
			foreach ($this->input as $line) {
				$this->board[] = preg_split('/\s+/', trim($line));
			}
		}

		public function mark($number) {
			for ($line = 0; $line < count($this->board); $line++) {
				for ($col = 0; $col < count($this->board[$line]); $col++) {
					if ($this->board[$line][$col] == $number) {
						$this->board[$line][$col] = 'X';
						break 2;
					}
				}
			}
		}

		public function check() {
			for ($line = 0; $line < count($this->board); $line++) {
				$row = $this->board[$line];
				$acv = array_count_values($row);
				if (isset($acv['X']) && $acv['X'] == count($this->board)) {
					return true;
				}
			}

			for ($col = 0; $col < count($this->board[0]); $col++) {
				$column = array_column($this->board, $col);
				$acv = array_count_values($column);
				if (isset($acv['X']) && $acv['X'] == count($this->board)) {
					return true;
				}
			}

			return false;
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

	foreach ($numbers as $num) {
		foreach ($boards as $b) {
			$b->mark($num);
			if ($b->check()) {
				$part1 = $b->value() * $num;
				break 2;
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";
