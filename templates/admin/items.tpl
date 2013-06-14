{include file="header.tpl"}
{include file="pagehead.tpl"}

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
				{$item->price/100} €
			</td>
			<td>
				{count($item->sizes)}
			</td>
			<td>
				<img src="{$item->image}" class="img-square" style="background-color:#ddd; height:50px;width:50px;" />
			</td>
			<td>
				<a href="{$path}index.php/item/{$item->id}" class="btn"><i class="icon-share"></i></a>
			</td>
			<td>
				<a href="{$path}index.php/item/edit/{$item->id}" class="btn"><i class="icon-pencil"></i></a>
			</td>
			<td>
			<form method="post"
				action="{$path}index.php/item/delete/{$item->id}">
				<button type="submit" class="btn"><i class="icon-trash"></i></button>
			</form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{include file="footer.tpl"}