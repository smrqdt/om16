{include file="header.tpl"}
{include file="pagehead.tpl"}

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
				building_number
			</th>
			<th>
				postcode
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
			{$user->currentAddress()->name}
		</td>
		<td>
			{$user->currentAddress()->lastname}
		</td>
		<td>
			{$user->currentAddress()->street}
		</td>
		<td>
			{$user->currentAddress()->building_number}
		</td>
		<td>
			{$user->currentAddress()->postcode}
		</td>
		<td>
			{$user->currentAddress()->city}
		</td>
		<td>
			{$user->currentAddress()->country}
		</td>
		<td>
			{$user->admin}
		</td>
		<td>
			{count($user->orders)}
		</td>
		<td>
			<a href="{$path}admin/user/edit/{$user->id}" class="btn"><i class="icon-pencil"></i></a>
		</td>
		<td>
			<form method="post" action="{$path}user/delete/{$user->id}" style="display:inline">
				<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
				<button type="submit" class="btn"><i class="icon-trash"></i></button>
			</form>
		</td>
	</tr>
	{/foreach}
</table>

{include file="footer.tpl"}