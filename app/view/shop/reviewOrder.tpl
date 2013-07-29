{include file="header.tpl"}
{include file="pagehead.tpl"}
<h1>Review Order</h1>

{$user->email}<br />
{$user->currentAddress()->name} 
{$user->currentAddress()->lastname}<br />
{$user->currentAddress()->street} 
{$user->currentAddress()->building_number}<br />
{$user->currentAddress()->postcode} 
{$user->currentAddress()->city}<br />
{$user->currentAddress()->country}

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
{foreach from=$cart item=item}
<tr>
	<td>
		<img src="{if $item['item']->image}$item['item']->image{else}{$path}assets/img/molumen_audio_cassette.svg{/if}" class="img-square" style="background-color:#ddd; height:50px;width:50px;" />
	</td>
	<td>
		{$item["item"]->name}
	</td>
	<td>
		{$item["size"]}
	</td>
	<td>
		{$item["amount"]} 
	</td>
	<td>
		{$item["item"]->price/100} €
	</td>
	<td>
		{$item["amount"] * ($item["item"]->price/100)} €
	</td>
</tr>
{/foreach}
</table>
Sum: {$sum} €

<form method="post" action="{$path}index.php/order" style="display:inline">
	<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
	<button type="submit" class="btn">Submit Order</button>
</form>
