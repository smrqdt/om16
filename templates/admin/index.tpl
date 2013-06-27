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
				{$order->getUser()->email}
			</td>
			<td>
				{count($order->getItems())}
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
				<a href="{$path}index.php/admin/order/delete/{$order->id}" class="btn"><i class="icon-trash"></i></a>
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
			{count($user->getOrders())}
		</td>
		<td>
			<a href="{$path}index.php/admin/user/edit/{$user->id}" class="btn"><i class="icon-pencil"></i></a>
		</td>
		<td>
			<a href="{$path}index.php/admin/user/delete/{$user->id}" class="btn btn-error"><i class="icon-trash"></i></a>
		</td>
	</tr>
	{/foreach}
</table>

<table class="table">
	<thead>
		<tr>
			<th>
				id
			</th>
			<th>
				name
			</th>
			<th>
				description
			</th>
			<th>
				price
			</th>
			<th>
				sizes
			</th>
			<th>
				image
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
		{foreach from=$items item=item}
		<tr>
			<td>
				{$item->id}
			</td>
			<td>
				{$item->name}
			</td>
			<td>
				{$item->description}
			</td>
			<td>
				{$item->price} €
			</td>
			<td>
				{count($item->getSizes())}
			</td>
			<td>
				<img src="{$item->image}" class="img-square" style="background-color:#ddd; height:50px;width:50px;" />
			</td>
			<td>
				<a href="#" class="btn"><i class="icon-share"></i></a>
			</td>
			<td>
				<a href="#" class="btn"><i class="icon-pencil"></i></a>
			</td>
			<td>
				<a href="#" class="btn btn-error"><i class="icon-trash"></i></a>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{include file="footer.tpl"}