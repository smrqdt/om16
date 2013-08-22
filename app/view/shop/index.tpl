{include file="header.tpl"}
{include file="pagehead.tpl"}
{include file="flash.tpl"}
<h1>Shop</h1>
<div class="row-fluid">
	{foreach from=$items item=item}
	<div class="container-fluid item">
		<a href="{$path}item/{$item->id}">
			<h4 class="muted">
				{$item->name}
			</h4>
			<img src="{if $item->image}{$item->image}{else}{$item_placeholder}{/if}" class="img-polaroid" style="background-color:#ddd; "/>
		</a>
		<h5>{$item->price/100.0} €</h5>
		<form method="post" action="{$path}cart/addItem/{$item->id}">
			<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
		{if $item->sizes}
			<select id="size" name="size" class="btn span5">
			{foreach from=$item->sizes item=size}
				<option>{$size->size}</option>
			{/foreach}
			<select>
		{/if}

		{if $item->numbered && $item->getUnrequestedNumberCount() <= 0}
			<div class="text-error">Out of stock.</div>
		{else}
			<div class="btn-group">
					<input type="submit" value="Add Item" class="btn btn-primary" />
			</div>
		{/if}
		{if $item->ticketscript}
			<a href="{$path}ticketscript" class="btn btn-success">Onlineticket</a>
		{/if}
		</form>
	</div>
	{/foreach}

</div><!--/row-->
   
{include file="footer.tpl"}