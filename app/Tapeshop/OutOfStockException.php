<?php

namespace Tapeshop;

class OutOfStockException extends \Exception {
	var $item = null;
	var $size = null;

	public function __construct($message) {
		parent::__construct($message, 0);
	}

	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
