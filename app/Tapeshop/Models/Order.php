<?php
namespace Tapeshop\Models;

use ActiveRecord\DateTime;
use ActiveRecord\Model;
use Tapeshop\Controllers\OrderStatus;

/**
 * @property array orderitems
 * @property \Tapeshop\Models\Address address
 * @property int id
 * @property String status
 * @property \Tapeshop\Models\User user
 * @property int shipping
 * @property DateTime shippingtime
 * @property int address_id
 * @property String hashlink
 * @property DateTime paymenttime
 * @property bool reminder_sent
 * @property String user_info_text
 */
class Order extends Model
{

	static $belongs_to = array(
		array('user'),
		array('address')
	);

	static $has_many = array(
		array('orderitems'),
		array('items', 'through' => 'orderitems'),
		array('nametags')
	);

	public function getSum()
	{
		$sum = 0;
		/**@var $item OrderItem*/
		foreach ($this->orderitems as $item) {
			$sum += $item->getSum();
		}
		return ($sum + $this->shipping);
	}

	public function getOrderId()
	{
		return ORDER_PREFIX . $this->id;
	}

	/**
	 * Check if the orders status is payed.
	 * @return boolean
	 */
	public function isPayed()
	{
		return $this->status == OrderStatus::PAYED;
	}

	/**
	 * Check if the orders status is shipped.
	 * @return boolean
	 */
	public function isShipped()
	{
		return $this->status == OrderStatus::SHIPPED;
	}

	/**
	 * Get the ticketcode of this order.
	 */
	public function getTicketcode()
	{
		$parts = explode("-", $this->hashlink);
		return strtoupper($parts[0]);
	}

	/**
	 * Check if the order contains items with a ticketcode.
	 */
	public function hasTicketCodes()
	{
		foreach ($this->orderitems as $orderitem) {
			if ($orderitem->item->ticketcode) {
				return true;
			}
		}
		return false;
	}
}
