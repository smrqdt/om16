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
		{foreach from=$orders item=order}
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
				<a href="{$path}index.php/admin/order/edit/{$order->id}" class="btn"><i class="icon-pencil"></i></a>
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

<table class="table">
	<thead>
		<tr>
			<th>
				id
			</th>
			<th>
				username
			</th>
			<th>
				email
			</th>
			<th>
				name
			</th>
			<th>
				lastname
			</th>
			<th>
				street
			</th>
			<th>
				street_number
			</th>
			<th>
				plz
			</th>
			<th>
				city
			</th>
			<th>
				country
			</th>
			<th>
				admin
			</th>
			<th>
				orders
			</th>
			<th>
				edit
			</th>
			<th>
				delete
			</th>
		</tr>
	</thead>
	{foreach from=$users item=user}
	<tr>
		<td>
			{$user->id}
		</td>
		<td>
			{$user->username}
		</td>
		<td>
			{$user->email}
		</td>
		<td>
			{$user->name}
		</td>
		<td>
			{$user->lastname}
		</td>
		<td>
			{$user->street}
		</td>
		<td>
			{$user->street_number}
		</td>
		<td>
			{$user->plz}
		</td>
		<td>
			{$user->city}
		</td>
		<td>
			{$user->country}
		</td>
		<td>
			{$user->admin}
		</td>
		<td>
			{count($user->orders)}
		</td>
		<td>
			<a href="#" class="btn"><i class="icon-pencil"></i></a>
		</td>
		<td>
			<form method="post" action="{$path}index.php/user/delete/{$user->id}" style="display:inline">
				<button type="submit" class="btn"><i class="icon-trash"></i></button>
			</form>
		</td>
	</tr>
	{/foreach}
</table>

{include file="footer.tpl"}