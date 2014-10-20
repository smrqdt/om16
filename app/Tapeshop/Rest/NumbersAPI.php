<?php
/**
 * Created by IntelliJ IDEA.
 * User: robert
 * Date: 20.10.14
 * Time: 19:43
 */

namespace Tapeshop\Rest;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Models\Item;
use Tapeshop\Models\Itemnumber;
use Tapeshop\Models\Orderitem;

class NumbersAPI extends RestController {

	public function updateManageNumbers($id) {
		$this->checkAdmin();
		$numbered = $this->params()->numbered;
		try {
			/** @var Item $item */
			$item = Item::find_by_pk($id, array());
			$item->numbered = $numbered;
			$item->save();
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Could not find item with id " . $id), 404);
		}
	}

	public function updateNumbers($id) {
		$this->checkAdmin();

		$string = $this->params()->numberString;
		$numbers = $this->getNumbers($string);

		$reassign = array();
		$notchanged = array();
		$errors = array();
		$warnings = array();

		$c = Item::connection();
		$c->transaction();
		try {
			/** @var $item ItemNumber */
			$itemnumbers = Itemnumber::find('all', array('conditions' => array('item_id = ?', $id)));
			$in = array_map(function ($a) { return $a->number; }, $itemnumbers);
			foreach ($numbers as $number) {
				$key = array_search($number, $in);
				if ($key) {
					/** @var ItemNumber $itemnumber */
					$itemnumber = $itemnumbers[$key];
					if($itemnumber->valid){
						if (empty($itemnumber->orderitem_id) ) {
							$itemnumber->delete();
						} else {
							$order = $itemnumber->orderitem->order;
							if ($order->shippingtime == null && $order->status == "shipped") {
								array_push($notchanged, $itemnumber);
							} else {
								array_push($reassign, $itemnumber->orderitem);
								$itemnumber->orderitem_id = null;
								$itemnumber->save();
							}
						}
					}
				} else {
					$n = new Itemnumber();
					$n->item_id = $id;
					$n->number = $number;
					$n->save();
				}
			}

			/** @var $orderitem OrderItem */
			foreach ($reassign as $orderitem) {
				$numbers = ItemNumber::find('all', array('conditions' => array('item_id = ? AND valid = 1 AND orderitem_id IS NULL', $id), 'limit' => $orderitem->amount));
				if (count($numbers) < $orderitem->amount) {
					array_push($errors, "Could not reassign itemnumber for item (" . $orderitem->item->id . ") " . $orderitem->item->name . " because there were no numbers left!");
				}
				foreach ($numbers as $n) {
					$n->orderitem_id = $orderitem->id;
					$n->save();
				}
			}

			foreach ($notchanged as $nc) {
				array_push($warnings, "Number " . $nc->number . " not changed, because the order was already shipped.");
			}
		} catch (ActiveRecordException $e) {
			$c->rollback();
			$this->haltReponse(array("error" => $e->getMessage()), 500);
		}
		$c->commit();

		$this->response(array($errors, $warnings));
	}

	/**
	 * Get the numbers property from a POST request and split it into single numbers that can be processed.
	 * @return array
	 */
	private function getNumbers($string) {
		$numbers = array();

		// remove whitespace
		$params = preg_replace('/\s+/', '', $string);
		// spilt numbers and ranges at delimiter
		$parts = preg_split('/,/', $params);

		foreach ($parts as $number) {
			// check for range
			if (preg_match('/[\d]+-[\d]+/', $number)) {
				$range = preg_split('[\-]', $number);
				for ($i = min($range); $i < max($range) + 1; $i++) {
					array_push($numbers, intval($i));
				}
				// check for single number
			} elseif (preg_match('/[\d]+/', $number)) {
				array_push($numbers, intval($number));
			}
		}
		return $numbers;
	}
} 