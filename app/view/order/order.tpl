{include file="header.tpl"}
{include file="pagehead.tpl"}
<h1>Order status</h1>

<div class="span5">
<h6>Shipping address:</h6>
{if $order->address}
	{$order->address->name} 
	{$order->address->lastname}<br/>
	{$order->address->street} 
	{$order->address->building_number}<br/>
	{$order->address->postcode} 
	{$order->address->city}<br/>
	{$order->address->country}
{else}
	{$order->user->currentAddress()->name} 
	{$order->user->currentAddress()->lastname}<br/>
	{$order->user->currentAddress()->street} 
	{$order->user->currentAddress()->building_number}<br/>
	{$order->user->currentAddress()->postcode} 
	{$order->user->currentAddress()->city}<br/>
	{$order->user->currentAddress()->country}
{/if}
</div>

<div class="span5">
<table class="table">
	<tr>
		<th>
			Billing#
		</th>
		<td>
			TS-{$order->id}
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
			{if $order->paymenttime}
				{$order->paymenttime}
			{else}
				{if isset($smarty.session['auth_user']) && $user->admin}
					<form method="post" action="{$path}order/{$order->id}/payed" style="display:inline">
						<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
						<button type="submit" class="btn"><i class="icon-barcode"></i> Mark as payed</button>
					</form>
				{/if}
			{/if}
		</td>
	</tr>
	<tr>
		<th>
			Shipped
		</th>
		<td>
			{if $order->shippingtime}
				{$order->shippingtime}
			{else}
				{if isset($smarty.session['auth_user']) && $user->admin}
					<form method="post" action="{$path}order/{$order->id}/shipped" style="display:inline">
						<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
						<button type="submit" class="btn"><i class="icon-gift"></i> Mark as shipped</button>
					</form>
				{/if}
			{/if}
		</td>
	</tr>
	<tr>
		<th>
			Billing .PDF
		</th>
		<td>
			{if $order->paymenttime}
				{if isset($smarty.session['auth_user']) && $user->admin}
					<form method="post" action="{$path}order/{$order->id}/billing" style="display:inline">
						<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
						<button type="submit" class="btn"><i class="icon-barcode"></i> Create Billing .PDF</button>
					</form>
				{/if}
				{else}
					Mark as Payed First.
			{/if}
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
		<img src="{if $item->item->image}{$item->item->image}{else}{$item_placeholder}{/if}" class="img-square" style="background-color:#ddd; height:50px;width:50px;" />
	</td>
	<td>
		{$item->item->name}
		{if $item->item->numbered}
			<br />Numbers: 
			{foreach $item->itemnumbers as $in}
				{$in->number}{if not $in@last}, {/if}
			{/foreach}
		{/if}
	</td>
	<td>
		{$item->size}
	</td>
	<td>
		{$item->amount}
	</td>
	<td>
		{$item->price/100} €
	</td>
	<td>
		{$item->amount * ($item->price)/100} €
	</td>
</tr>
{/foreach}

<tr>
	<td colspan="4"></td>
	<td><b>Shipping</b></td>
	<td>
		<b>{$order->shipping/100} €</b>
	</td>

</tr>
<tr>
	<td colspan="4"></td>
	<td><b>Total</b></td>
	<td>
		<b>{$order->getSum() / 100} €</b>
	</td>
</tr>
</table>

{include file="footer.tpl"}