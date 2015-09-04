<?php
namespace Tapeshop\Rest;


use Tapeshop\Models\Nametag;

class NametagAPI extends RestController
{
	public function create()
	{
		$order_id = $this->params()->order_id;
		$name = $this->params()->name;
		$nickname = $this->params()->nickname;
		$pronoun = $this->params()->pronoun;

		$nametag = new Nametag();
		$nametag->order_id = $order_id;
		$nametag->name = $name;
		$nametag->nickname = $nickname;
		$nametag->pronoun = $pronoun;

		$nametag->save();
		$nametag->reload();
		$this->response($nametag->to_json());
	}

	public function delete($id)
	{
		$nametag = Nametag::find_by_pk($id, array());

		$nametag->delete();
	}

	public function get($order_id)
	{
		$nametags = Nametag::all(array("order_id" => $order_id));
		$json = "[";
		foreach ($nametags as $tag) {
			$json .= $tag->to_json();
			if ($tag !== end($nametags)) {
				$json .= ", ";
			}
		}
		$json .= "]";
		$this->response($json);
	}

}
