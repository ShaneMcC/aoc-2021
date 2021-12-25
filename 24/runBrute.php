#!/usr/bin/php
<?php

	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/../common/VM.php');
	$input = VM::parseInstrLines(getInputLines());

	// Collect the bits that are different per input
	$addXs = [];
	$divZs = [];
	$addYs = [];

	foreach ($input as $num => $i) {
		if ($i[0] == 'add' && $i[1][0] == 'x' && $i[1][1] != 'z') { $addXs[] = $i[1][1]; }
		if ($i[0] == 'div' && $i[1][0] == 'z') { $divZs[] = $i[1][1]; }
		if ($i[0] == 'add' && $i[1][0] == 'y' && $num % 18 == 15) { $addYs[] = $i[1][1]; }
	}

	// This will actually run the code, but isn't fast.
	class Day24VM extends VM {
		public $vars = ['w' => 0, 'x' => 0, 'y' => 0, 'z' => 0];
		public $input = [];

		private function getVal($v) {
			return isset($this->vars[$v]) ? $this->vars[$v] : $v;
		}

		/**
		 * Init the opcodes.
		 */
		protected function init() {
			$this->instrs['inp'] = function($vm, $args) {
				$this->vars[$args[0]] = array_shift($this->input);
			};

			$this->instrs['add'] = function($vm, $args) {
				$this->vars[$args[0]] += $this->getVal($args[1]);
			};

			$this->instrs['mul'] = function($vm, $args) {
				$this->vars[$args[0]] *= $this->getVal($args[1]);
			};

			$this->instrs['div'] = function($vm, $args) {
				$this->vars[$args[0]] = floor($this->vars[$args[0]] / $this->getVal($args[1]));
			};

			$this->instrs['mod'] = function($vm, $args) {
				$this->vars[$args[0]] = $this->vars[$args[0]] % $this->getVal($args[1]);
			};

			$this->instrs['eql'] = function($vm, $args) {
				$this->vars[$args[0]] = ($this->vars[$args[0]] == $this->getVal($args[1]) ? 1 : 0);
			};

		}
	}

	// Test a code via the VM
	function testCodeVM($code) {
		global $input;
		$vm = new Day24VM($input);
		$vm->input = $code;
		while (true) { if (!$vm->step()) { break; } }
		return $vm->vars;
	}

/*
	echo "\n";
	$test = '91897399498995';
	echo $test, ' => ', "\n";
	echo json_encode(testCode(str_split($test))), "\n";
	echo $test, ' => ', json_encode(testCodeVM(str_split($test))), "\n";

	echo "\n";
	$test = '51121176121391';
	echo $test, ' => ', "\n";
	echo json_encode(testCode(str_split($test))), "\n";
	echo $test, ' => ', json_encode(testCodeVM(str_split($test))), "\n";
/* */

	// Do what we need to do for each position
	function doStage($input, $pos, $w, $x, $y, $z) {
		global 	$addXs, $divZs, $addYs;

		$w = $input[$pos];
		$x = ((($z % 26) + $addXs[$pos]) != $w) ? 1 : 0;
		$z = floor($z / $divZs[$pos]);
		$y = (25 * $x) + 1;
		$z = $z * $y;
		$y = ($w + $addYs[$pos]) * $x;
		$z = $z + $y;

		return [$w, $x, $y, $z];
	}

	// Re-implementation of the ALU code
	function testCode($input) {
		$pos = 0;

		$w = $x = $y = $z = 0;

		for ($pos = 0; $pos < count($input); $pos++) {
			[$w, $x, $y, $z] = doStage($input, $pos, $w, $x, $y, $z);
			// echo "\t", $w, ' => ', json_encode(['w' => $w, 'x' => $x, 'y' => $y, 'z' => $z]), "\n";
		}

		return ['w' => $w, 'x' => $x, 'y' => $y, 'z' => $z];
	}

	// Poorly attempt to brute force. This will never be sane....
	for ($i = 99999999999999; $i >= 11111111111111; $i--) {
		if (strstr($i, '0')) { continue; }
		// echo $i, "\n";
		$res = testCode(str_split($i), 1);
		if ($res !== false && $res['z'] == 0) {
			echo 'Part 1: ', $i, "\n";
			break;
		}
	}

	for ($i = 11111111111111; $i <= 99999999999999; $i++) {
		if (strstr($i, '0')) { continue; }
		// echo $i, "\n";
		$res = testCode(str_split($i), 1);
		if ($res !== false && $res['z'] == 0) {
			echo 'Part 2: ', $i, "\n";
			break;
		}
	}



