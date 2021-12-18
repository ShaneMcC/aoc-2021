#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	class SnailNumber {
		public $parent = null;
		public $left = null;
		public $right = null;

		public function __construct($line, $parent = null) {
			$this->parent = $parent;
			$json = json_decode($line);

			if (is_array($json)) {
				if (is_array($json[0])) {
					$this->left = new SnailNumber(json_encode($json[0]), $this);
				} else {
					$this->left = $json[0];
				}

				if (is_array($json[1])) {
					$this->right = new SnailNumber(json_encode($json[1]), $this);
				} else {
					$this->right = $json[1];
				}
			}
		}

		private function doExplosions($nesting) {
			// Need to explode
			if ($nesting >= 4 && is_numeric($this->left) && is_numeric($this->right)) {

				// Find my nearest regular left number...
				$p = $this;
				while ($p->parent != null) {
					if (is_numeric($p->parent->left)) {
						$p->parent->left += $this->left;
						break;
					} else if ($p->parent->left !== $p) {
						// Recurse into this left number instead...
						$p2 = $p->parent->left;
						while (true) {
							if (is_numeric($p2->right)) {
								$p2->right += $this->left;
								break 2;
							}
							$p2 = $p2->right;
						}
					}
					$p = $p->parent;
				}

				// Find my nearest regular right number...
				$p = $this;
				while ($p->parent != null) {
					if (is_numeric($p->parent->right)) {
						$p->parent->right += $this->right;
						break;
					} else if ($p->parent->right !== $p) {
						// Recurse into this right number instead...
						$p2 = $p->parent->right;
						while (true) {
							if (is_numeric($p2->left)) {
								$p2->left += $this->right;
								break 2;
							}
							$p2 = $p2->left;
						}
					}
					$p = $p->parent;
				}


				if ($this->parent->left === $this) {
					$this->parent->left = 0;
				} else if ($this->parent->right === $this) {
					$this->parent->right = 0;
				}

				// We did something.
				return true;
			}

			foreach ([$this->left, $this->right] as $n) {
				if ($n instanceof SnailNumber) {
					if ($n->doExplosions($nesting + 1)) {
						return true;
					}
				}
			}

			return false;
		}

		private function doSplits($nesting) {

			if (is_numeric($this->left) && $this->left >= 10) {
				$newLeft = floor($this->left / 2);
				$newRight = ceil($this->left / 2);
				$this->left = new SnailNumber('[' . $newLeft . ',' . $newRight . ']', $this);
				return true;
			} else if (!is_numeric($this->left) && $this->left->doSplits($nesting + 1)) {
				return true;
			}

			if (is_numeric($this->right) && $this->right >= 10) {
				$newLeft = floor($this->right / 2);
				$newRight = ceil($this->right / 2);
				$this->right = new SnailNumber('[' . $newLeft . ',' . $newRight . ']', $this);
				return true;
			} else if (!is_numeric($this->right) && $this->right->doSplits($nesting + 1)) {
				return true;
			}

			return false;
		}

		public function __toString() {
			return '[' . $this->left . ',' . $this->right . ']';
		}

		public function reduce() {
			if (isDebug()) { echo 'Start:         ', $this, "\n"; }
			while (true) {
				if ($this->doExplosions(0)) {
					if (isDebug()) { echo 'After explode: ', $this, "\n"; }
					continue;
				}

				if ($this->doSplits(0)) {
					if (isDebug()) { echo 'After Split:   ', $this, "\n"; }

					continue;
				}

				break;
			}
			if (isDebug()) { echo 'End:           ', $this, "\n"; }
		}

		public function getMagnitude() {
			$left = is_numeric($this->left) ? $this->left : $this->left->getMagnitude();
			$right = is_numeric($this->right) ? $this->right : $this->right->getMagnitude();

			return (3 * $left) + (2 * $right);
		}

		public static function add($first, $second) {
			$new = new SnailNumber('');
			$new->left = new SnailNumber($first, $new);
			$new->right = new SnailNumber($second, $new);

			$new->reduce();

			return $new;
		}
	}

	$numbers = [];

	$maxMagnitude = 0;
	$final = null;
	for ($i = 0; $i < count($input); $i++) {
		$number = new SnailNumber($input[$i]);

		// Part 1
		$final = ($final == null) ? $number : SnailNumber::add($final, $number);

		// Part 2
		for ($j = 0; $j < count($input); $j++) {
			if ($i != $j) {
				$maxMagnitude = max($maxMagnitude, SnailNumber::add($number, $input[$j])->getMagnitude());
			}
		}
	}

	echo 'Part 1: ', $final->getMagnitude(), "\n";
	echo 'Part 2: ', $maxMagnitude, "\n";
