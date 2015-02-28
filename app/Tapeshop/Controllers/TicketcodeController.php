<?php
namespace Tapeshop\Controllers;


use ActiveRecord\RecordNotFound;
use Tapeshop\Controller;
use Tapeshop\Models\Item;
use Tapeshop\Models\Orderitem;

class TicketcodeController extends Controller{


    public function show($item_id){
        $this->checkAdmin();

        $item = null;

        try {
            $item = Item::find($item_id);
        } catch (RecordNotFound $e) {
            $this->app->flash('error', 'Item not found');
            $this->redirect('home');
        }

        $data = array(
            "item"=>$item
        );

        $this->render('admin/ticketcodes.tpl', $data);
    }

    public function invalidate($orderitem_id){
        $this->checkAdmin();

        /**@var Orderitem $orderItem*/
        $orderItem = null;

        try {
            $orderItem = Orderitem::find($orderitem_id);
            $orderItem->ticketcode_valid = false;
            $orderItem->save()
            ;        } catch (RecordNotFound $e) {
            $this->app->flash('error', 'Orderitem not found');
            $this->redirect('home');
        }

        $this->redirect($this->app->urlFor('ticketcodes', array("item_id" => $orderItem->item_id)), false);
    }

    public function reactivate($orderitem_id){
        $this->checkAdmin();

        /**@var Orderitem $orderItem*/
        $orderItem = null;

        try {
            $orderItem = Orderitem::find($orderitem_id);
            $orderItem->ticketcode_valid = true;
            $orderItem->save()
            ;        } catch (RecordNotFound $e) {
            $this->app->flash('error', 'Orderitem not found');
            $this->redirect('home');
        }

        $this->redirect($this->app->urlFor('ticketcodes', array("item_id" => $orderItem->item_id)), false);
    }
}
