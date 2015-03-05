<?php

namespace Tapeshop\Rest;

use ActiveRecord\RecordNotFound;
use Tapeshop\Models\Item;

class Items extends RestController
{

    public function getAll()
    {
        $items = Item::all(array("conditions" => array("deleted = false")));
        $json = "[";

        foreach ($items as $item) {
            $json .= $item->to_json(array('include' => array('sizes')));
            if ($item !== end($items)) {
                $json .= ", ";
            }
        }

        $json .= "]";

        $this->response($json);
    }


    public function get($id)
    {
        $item = null;
        try {
            $item = Item::find_by_pk($id, array("conditions" => array("deleted = false")));
            $this->response($item->to_json(array('include' => array('sizes', 'itemnumbers'))));
        } catch (RecordNotFound $e) {
            $this->response(array("error" => "Item with id " . $id . " not found!"), 404);
        }
    }

    public function updateManageStock($id)
    {
        $this->checkAdmin();

        $item = null;
        $manage_stock = $this->params()->manage_stock;
        try {
            /** @var $item Item */
            $item = Item::find_by_pk($id, array());
            if ($manage_stock == true) {
                $item->manage_stock = 1;
            } else {
                $item->manage_stock = 0;
            }
            $item->save();
            $this->response($item->to_json());
        } catch (RecordNotFound $e) {
            $this->response(array("error" => "Item with id " . $id . " not found!"), 404);
        }
    }
}
