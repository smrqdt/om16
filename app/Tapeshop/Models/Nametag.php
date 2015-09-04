<?php

namespace Tapeshop\Models;


use ActiveRecord\Model;

/**
 * Class Nametag
 * @package Tapeshop\Models
 * @property int id
 * @property int order_id
 * @property string name
 * @property string nickname
 * @property string pronoun
 */
class Nametag extends Model
{

	static $belongs_to = array(
		array('order')
	);
}
