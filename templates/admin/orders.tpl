{include file="header.tpl"}
{include file="pagehead.tpl"}

<table class="table">
	<thead>
		<tr>
			<th>
				Status
			</th>
			<th>
				id
			</th>
			<th>
				number
			</th>
			<th>
				bill
			</th>
			<th>
				user
			</th>
			<th>
				items
			</th>
			<th>
				hash
			</th>
			<th>
				show
			</th>
			<th>
				edit
			</th>
			<th>
				delete
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach $orders as $order}
		{if $order->status=='payed'}
		<tr class="error">
		{elseif $order->status=='shipped'}
		<tr class="success">
		{elseif $order->status=='new'}
		<tr class="warning">
		{else}
		<tr>
		{/if}
			<td>
				{$order->status}
			</td>
			<td>
				{$order->id}
			</td>
			<td>
				{$order->number}
			</td>
			<td>
				{$order->bill}
			</td>
			<td>
				{$order->user->email}
			</td>
			<td>
				{count($order->orderitems)}
			</td>
			<td>
				{$order->hashlink}
			</td>
			<td>
				<a href="{$path}index.php/order/{$order->hashlink}" class="btn"><i class="icon-share"></i></a>
			</td>
			<td>
				<a href="{$path}index.php/order/edit/{$order->id}" class="btn"><i class="icon-pencil"></i></a>
			</td>
			<td>
				<form method="post" action="{$path}index.php/order/delete/{$order->id}" style="display:inline">
					<button type="submit" class="btn"><i class="icon-trash"></i></button>
				</form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{include file="footer.tpl"}