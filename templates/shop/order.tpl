{include file="header.tpl"}
{include file="pagehead.tpl"}
<h1>Order completed</h1>

<div class="span5">
<h6>Shipping address:</h6>
{$order->getUser()->address}
</div>

<div class="span5">
<table class="table">
	<tr>
		<th>
			Ordered
		</th>
		<td>
			{$order->ordertime}
		</td>
	</tr>
	<tr>
		<th>
			Payment received
		</th>
		<td>
			{$order->paymenttime}
		</td>
	</tr>
	<tr>
		<th>
			Shipped
		</th>
		<td>
			{$order->shippingtime}
		</td>
	</tr>
</table>
</div>

<table class="table">
	<thead>
		<tr>
			<th>
				<!-- image column -->
			</th>
			<th>
				Item
			</th>
			<th>
				Size
			</th>
			<th>
				Amount
			</th>
			<th>
				Price
			</th>
			<th>
				Sum
			</th>
		</tr>
	</thead>
{foreach from=$order->getItems() item=item}
<tr>
	<td>
		<img src="{$item->getItem()->image}" class="img-square" style="background-color:#ddd; height:50px;width:50px;" />
	</td>
	<td>
		{$item->getItem()->name}
	</td>
	<td>
		{$item->size}
	</td>
	<td>
		{$item->amount}
	</td>
	<td>
		{$item->getItem()->price} €
	</td>
	<td>
		{$item->amount * ($item->getItem()->price)} €
	</td>
</tr>
{/foreach}
<tr>
	<td colspan="4"></td>
	<td><b>Total</b></td>
	<td>
		<b>{$order->getSum()} €</b>
	</td>
</tr>
</table>




