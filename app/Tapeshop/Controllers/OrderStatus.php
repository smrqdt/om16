<?php

namespace Tapeshop\Controllers;

abstract class OrderStatus {
	const NEW_ORDER = "new";
	const SUBMITTED = "submitted";
	const PAYED = "payed";
	const SHIPPED = "shipped";
	const OVERDUE = "overdue";
}
