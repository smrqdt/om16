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
		<img src="{$item["item"]->image}" class="img-square" style="background-color:#ddd; height:50px;width:50px;" />
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