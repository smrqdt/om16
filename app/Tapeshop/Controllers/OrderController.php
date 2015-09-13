<?php
namespace Tapeshop\Controllers;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Controllers\Helpers\Billing;
use Tapeshop\Controllers\Helpers\EmailOutbound;
use Tapeshop\Models\Item;
use Tapeshop\Models\Order;
use Tapeshop\Models\Orderitem;
use Tapeshop\Models\Size;
use Tapeshop\OutOfStockException;

/**
 * Handle orders.
 */
class OrderController extends Controller
{

	/**
	 * Submit a new order. Get all items form the cart and add them as OrderItem
	 */
	public function submitOrder()
	{
		$shipping = 0;
		/** @var $order \Tapeshop\Models\Order */
		$order = null;
		$outofstock = array(
			'items' => array(),
			'sizes' => array()
		);

		$c = Order::connection();
		try {
			$c->transaction();
			$order = $this->user->create_orders(array(
				'hashlink' => $this->gen_uuid(),
				'address_id' => $this->user->currentAddress()->id
			));

			$cart = $this->getCart();

			foreach ($cart as $ci) {
				/** @var $item Item */
				$item = null;
				/** @var $size Size */
				$size = null;

				$item = Item::find_by_pk($ci["item"]->id, array());
				if (!empty($ci["size"])) {
					$size = Size::find_by_pk($ci["size"], array());
				}

				if ($item->manage_stock) {
					if ($size == null) {
						if ($item->stock >= $ci["amount"]) {
							$item->stock -= $ci["amount"];
							$item->save();
						} else {
							array_push($outofstock['items'], $item->id);
							continue;
						}
					} else {
						if ($size->stock = $ci["amount"]) {
							$size->stock -= $ci["amount"];
							$size->save();
						} else {
							array_push($outofstock['sizes'], $size->id);
							continue;
						}
					}
				}

				$orderitem = new Orderitem();
				$orderitem->order_id = $order->id;
				$orderitem->item_id = $item->id;
				$orderitem->amount = $ci["amount"];
				$orderitem->size_id = $size == null ? null : $size->id;
				$orderitem->price = $ci["item"]->price;
				$orderitem->support_price = $ci["support_price"];

				$orderitem->save();
				$orderitem->reload();

				$shipping = max($shipping, $ci["item"]->shipping);
			}

			if (!empty($outofstock['items']) || !empty($outofstock['sizes'])) {
				throw new OutOfStockException(gettext("cart.error.outofstock"));
			}

			$order->shipping = $shipping;
			$order->save();
			$c->commit();
			$order->reload();
			$mailSuccess = EmailOutbound::sendBilling($order);

			if ($mailSuccess) {
				$this->app->flash('success', 'Bestellung erfolgreich! Du erhälst zusätzlich eine Email.');
			} else {
				$this->app->flash('error', 'Konnte keine Bestätigungsmail senden!');
			}
		} catch (RecordNotFound $e) {
			$c->rollback();
			$this->app->flash('error', gettext("item.error.notfound"));
			$this->redirect('cart');
		} catch (ActiveRecordException $e) {
			$c->rollback();
			$this->app->flash('error', "Deine Bestellung konnte nicht angelegt werden! " . $e->getMessage());
			$this->redirect('cart');
		} catch (OutOfStockException $e) {
			$c->rollback();
			$this->app->flash('warn', $e->getMessage());
			$_SESSION['out_of_stock'] = $outofstock;
			$this->redirect('cart');
		} catch (\Exception $e) {
			error_log($e);
		}

		$_SESSION["cart"] = array();

		$auth_user = $this->auth->getUser();
		if (!$auth_user['logged_in']) {
			unset($_SESSION["auth_user"]);
		}

		$url = $this->app->urlFor('order', array('hash' => $order->hashlink));
		$this->app->redirect($url);
	}

	/**
	 * Delete an order.
	 * @param int $id Id of the order.
	 * @var $order \Tapeshop\Models\Order
	 */
	public function deleteOrder($id)
	{
		$this->checkAdmin();
		$order = null;

		try {
			$order = Order::find($id);
		} catch (RecordNotFound $e) {
			$this->app->flash('error', "Bestellung nicht gefunden!");
			$this->redirect('adminorders');
		}

		try {
			$order->delete();
		} catch (ActiveRecordException $e) {
			$this->app->flash('error', "Konnte Bestellung nicht löschen! " . $e->getMessage());
			$this->redirect('adminorders');
		}

		$this->redirect('adminorders');
	}

	/**
	 * Show an order and its details.
	 * @param String $hash Hash (UUID) of the order.
	 */
	public function order($hash)
	{
		$order = Order::find(
			'first',
			array(
				'hashlink' => $hash
			)
		);

		if ($order == null) {
			$this->app->flash('warn', "Bestellung nicht gefunden!" . $hash);
			$this->redirect('home');
		} else {

			$data = array(
				"orderPrefix" => ORDER_PREFIX,
				"order" => $order,
				"paypal_merchant_id" => PAYPAL_MERCHANT_ID
			);

			$this->render("order/order.html", $data);
		}
	}

	/**
	 * Generate invoice PDF.
	 * @param String $hash Hash (UUID) of the order
	 */
	function billing($hash)
	{
		$this->checkAdmin();

		$order = Order::find(
			'first',
			array(
				'hashlink' => $hash
			)
		);

		if ($order == null) {
			$this->app->flash('warn', "Bestellung nicht gefunden!");
			$this->redirect('home');
		} else {
			$billing = new Billing('P', 'mm', 'A4');
			$billing->order = $order;
			$billing->AddPage();
			$billing->Output();
			$this->app->response()->header("Content-Type", "application/pdf");
		}
	}

	public function allBillingsAsPdf()
	{
		$this->checkAdmin();

		$orders = Order::all();

		$billing = new Billing('P', 'mm', 'A4');

		foreach ($orders as $order) {
			$billing->order = $order;
			$billing->AddPage();

		}

		$billing->Output();
		$this->app->response()->header("Content-Type", "application/pdf");
	}
}
