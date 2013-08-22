{include file="header.tpl"}
{include file="pagehead.tpl"}
<a href="{$path}items/create" class="btn"><i class="icon-plus"></i> New Item</a>
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
		{if $item-> numbered && $item->getUnrequestedNumberCount() <= 0}
			<tr class="error">
		{else}
			<tr>
		{/if}
			<td>
				{$item->id}
			</td>
			<td>
				{$item->name}
			</td>
			<td>
				{$item->description} 
				{if $item-> numbered}
					{if $item->getUnrequestedNumberCount() <= 0}
						<p class="text-error">
					{else}
						<p class="text-info">
					{/if}
					<em>{$item->getUnrequestedNumberCount()} left</em>
				</p>
				{/if}
			</td>
			<td>
				{$item->price/100} €
			</td>
			<td>
				{count($item->sizes)}
			</td>
			<td>
				<img src="{if $item->image}{$item->image}{else}{$item_placeholder}{/if}" class="img-square" style="background-color:#ddd; height:50px;width:50px;" />
			</td>
			<td>
				<a href="{$path}item/{$item->id}" class="btn"><i class="icon-share"></i></a>
			</td>
			<td>
				<a href="{$path}item/edit/{$item->id}" class="btn"><i class="icon-pencil"></i></a>
			</td>
			<td>
			<form method="post"
				action="{$path}item/delete/{$item->id}">
				<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
				<button type="submit" class="btn"><i class="icon-trash"></i></button>
			</form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{include file="footer.tpl"}