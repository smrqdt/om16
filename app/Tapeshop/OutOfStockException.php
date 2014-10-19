<?php

namespace Tapeshop;

class OutOfStockException extends \Exception {
	var $item = null;
	var $size = null;

	public function __construct($message, $item=null, $size=null) {
		parent::__construct($message, 0);
		$this->item = $item;
		$this->size = $size;
	}

	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
