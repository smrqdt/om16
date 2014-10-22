<?php

namespace Tapeshop\Rest;

use ActiveRecord\ActiveRecordException;
use ActiveRecord\RecordNotFound;
use Tapeshop\Models\Item;
use Tapeshop\Models\Itemnumber;

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
				if ($key !== false) {
					/** @var ItemNumber $itemnumber */
					$itemnumber = $itemnumbers[$key];
					if ($itemnumber->valid) {
						if (empty($itemnumber->orderitem_id)) {
							$itemnumber->delete();
						} else {
							$order = $itemnumber->orderitem->order;
							if ($order->shippingtime != null || $order->status == "shipped") {
								array_push($notchanged, $itemnumber);
							} else {
								$orderitem = $itemnumber->orderitem;
								$numbers = ItemNumber::find('all', array('conditions' => array('item_id = ? AND valid = 1 AND orderitem_id IS NULL', $id), 'limit' => $orderitem->amount));
								if (count($numbers) < $orderitem->amount) {
									array_push($warnings, array(
										"message" => "Could not reassign itemnumber for item (" . $orderitem->item->id . ") " . $orderitem->item->name . " because there were no numbers left!",
										"order" => json_decode($orderitem->order->to_json()),
										"url" => $this->app->urlFor('order', array('hash' => $orderitem->order->hashlink))
									));
								} else {
									array_push($reassign, $itemnumber);
								}
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

			$reassign = array_unique($reassign, SORT_REGULAR);
			/** @var $itemnumber Itemnumber */
			foreach ($reassign as $itemnumber) {
				$orderitem = $itemnumber->orderitem;
				/** @var Itemnumber $number */
				$number = ItemNumber::find('first', array('conditions' => array('item_id = ? AND valid = 1 AND orderitem_id IS NULL', $id)));
				if ($number == null) {
					array_push($errors, array(
						"message" => "Reassign of itemnumber for item (" . $orderitem->item->id . ") " . $orderitem->item->name . " failed! Not enough numbers!",
						"order" => json_decode($orderitem->order->to_json()),
						"url" => $this->app->urlFor('order', array('hash' => $orderitem->order->hashlink))
					));
				} else {
					$number->orderitem_id = $orderitem->id;
					$number->save();
					$itemnumber->delete();
				}
			}

			foreach ($notchanged as $nc) {
				array_push($warnings, array(
					"message" => "Number " . $nc->number . " not changed, because the order was already shipped.",
					"order" => json_decode($nc->orderitem->order->to_json()),
					"url" => $this->app->urlFor('order', array('hash' => $nc->orderitem->order->hashlink))
				));
			}
		} catch (ActiveRecordException $e) {
			$c->rollback();
			$this->haltReponse(array("error" => $e->getMessage()), 500);
		}
		$c->commit();

		$this->response(array("errors" => $errors, "warnings" => $warnings));
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

	public function updateInvalidNumbers($id) {
		$this->checkAdmin();

		$string = $this->params()->numberString;
		$numbers = $this->getNumbers($string);

		$reassign = array();
		$ask = array();
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
				if ($key !== false) {
					/** @var ItemNumber $itemnumber */
					$itemnumber = $itemnumbers[$key];
					if ($itemnumber->orderitem_id == null) {
						$itemnumber->valid = !$itemnumber->valid;
						$itemnumber->save();
					} else {
						$order = $itemnumber->orderitem->order;
						if ($order->shippingtime != null || $order->status == "shipped") {
							if($itemnumber->valid){
								array_push($ask, $itemnumber);
							}else{
								$itemnumber->valid = true;
								$itemnumber->save();
							}
						} else {
							array_push($reassign, $itemnumber);
						}
					}
				} else {
					$n = new Itemnumber();
					$n->item_id = $id;
					$n->number = $number;
					$n->valid = false;
					$n->save();
				}
			}

			$reassign = array_unique($reassign, SORT_REGULAR);
			/** @var $itemnumber Itemnumber */
			foreach ($reassign as $itemnumber) {
				$orderitem = $itemnumber->orderitem;
				$number = ItemNumber::find('first', array('conditions' => array('item_id = ? AND valid = 1 AND orderitem_id IS NULL', $id)));
				if ($number == null) {
					array_push($errors, array(
						"message" => "Could not reassign itemnumber for item (" . $orderitem->item->id . ") " . $orderitem->item->name . " because there were no numbers left!",
						"order" => json_decode($orderitem->order->to_json()),
						"url" => $this->app->urlFor('order', array('hash' => $orderitem->order->hashlink))
					));
				}else{
					$itemnumber->orderitem_id = null;
					$itemnumber->valid = false;
					$itemnumber->save();
					$number->orderitem_id = $orderitem->id;
					$number->save();
				}
			}

			/** @var $nc Itemnumber */
			foreach ($ask as $nc) {
				array_push($warnings,
					array(
						"message" => "Number " . $nc->number . " not changed, because the order was already shipped.",
						"order" => json_decode($nc->orderitem->order->to_json()),
						"itemnumber" => json_decode($nc->to_json()),
						"url" => $this->app->urlFor('order', array('hash' => $nc->orderitem->order->hashlink))
					));
			}
		} catch (ActiveRecordException $e) {
			$c->rollback();
			$this->haltReponse(array("error" => $e->getMessage()), 500);
		}
		$c->commit();

		$this->response(array("errors" => $errors, "ask" => $warnings));
	}

	public function overrideWarning($id) {
		try {
			/** @var $itemnumber  Itemnumber */
			$itemnumber = Itemnumber::find_by_pk($id, array());
			$itemnumber->valid = false;
			$itemnumber->save();
		} catch (RecordNotFound $e) {
			$this->haltReponse(array("error" => "Itemnumber with id " . $id . " not found!"), 404);
		}
	}
}
