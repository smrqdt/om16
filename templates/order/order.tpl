{include file="header.tpl"}
{include file="pagehead.tpl"}
<h1>Order status</h1>

<div class="span5">
<h6>Shipping address:</h6>
{$order->user->name}
{$order->user->lastname}
{$order->user->street}
{$order->user->street_number}
{$order->user->plz}
{$order->user->city}
{$order->user->country}
</div>

<div class="span5">
<table class="table">
	<tr>
		<th>
			Billing#
		</th>
		<td>
			{$order->bill}
		</td>
	</tr>
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
{foreach from=$order->orderitems item=item}
<tr>
	<td>
		<img src="{$item->item->image}" class="img-square" style="background-color:#ddd; height:50px;width:50px;" />
	</td>
	<td>
		{$item->item->name}
	</td>
	<td>
		{$item->size}
	</td>
	<td>
		{$item->amount}
	</td>
	<td>
		{$item->item->price/100} €
	</td>
	<td>
		{$item->amount * ($item->item->price)/100} €
	</td>
</tr>
{/foreach}
<tr>
	<td colspan="4"></td>
	<td><b>Total</b></td>
	<td>
		<b>{$order->getSum()/100} €</b>
	</td>
</tr>
</table>




