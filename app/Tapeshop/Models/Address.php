<?php

namespace Tapeshop\Models;

use ActiveRecord\Model;

/**
 * @property int user_id
 * @property String name
 * @property String lastname
 * @property String street
 * @property String building_number
 * @property String postcode
 * @property String city
 * @property String country
 * @property array orders
 * @property boolean current
 * @property String user_info_text
 */
class Address extends Model {

	static $table_name = "addresses";

	static $belongs_to = array(
		array('user')
	);

	static $has_many = array(
		array('orders')
	);
}
