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
 * @property String payment_id
 * @property int payment_fee
 * @property int payment_method_id
 * @property DateTime paymenttime
 */
class Order extends Model
{

	static $belongs_to = array(
		array('user'),
		array('address')
	);

	static $has_many = array(
		array('orderitems'),
		array('items', 'through' => 'orderitems')
	);

	public function getSum()
	{
		$sum = 0;
		foreach ($this->orderitems as $item) {
			$sum += $item->amount * $item->price;
		}
		return ($sum + $this->shipping + $this->payment_fee);
	}

	public function getOrderId()
	{
		return ORDER_PREFIX . $this->id;
	}

	public function getFeeFor($method)
	{
		switch ($method) {
			case 'sofort':
				$pm = PaymentMethod::find('first', array("conditions" => array("name = 'sofort'")));
				return $pm->fix + $this->getSum() * $pm->fee;
			case 'paypal':
				$pm = PaymentMethod::find('first', array("conditions" => array("name = 'paypal'")));
				return $pm->fix + $this->getSum() * $pm->fee;
			default:
				return 0;
		}
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
		return strtoupper(explode("-", $this->hashlink)[0]);
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
