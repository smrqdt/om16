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
			<th>
				<!-- remove item -->
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
		<form method="post" action="{$path}cart/increase" class="pull-right" style="display:inline;">
			<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			<input type="hidden" name="id" value="{$item["item"]->id}">
			<input type="hidden" name="size" value="{$item["size"]}">
			<button type="submit" class="btn btn-mini">
				<i class="icon-plus"></i>
			</button>
		</form>
		<form method="post" class="pull-right" action="{$path}cart/decrease" style="display:inline;">
			<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			<input type="hidden" name="id" value="{$item["item"]->id}">
			<input type="hidden" name="size" value="{$item["size"]}">
			<button type="submit" class="btn btn-mini">
				<i class="icon-minus"></i>
			</button>
		</form>
	</td>
	<td>
		{$item["item"]->price/100} €
	</td>
	<td>
		{$item["amount"] * ($item["item"]->price/100)} €
	</td>
	<td>
		<form method="post" class="pull-right" action="{$path}cart/remove" style="display:inline;">
		<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			<input type="hidden" name="id" value="{$item["item"]->id}">
			<input type="hidden" name="size" value="{$item["size"]}">
			<button type="submit" class="btn">
				<i class="icon-trash"></i>
			</button>
		</form>
	</td>
</tr>
{/foreach}
<tr>
	<td colspan="4" style="border-top:none;"></td>
	<td><b>Shipping</b></td>
	<td>
		<b>{$shipping/100} €</b>
	</td>

</tr>
<tr>
	<td colspan="4" style="border-top:none;"></td>
	<td><b>Total</b></td>
	<td>
		<b>{$sum} €</b>
	</td>
</tr>
</table>