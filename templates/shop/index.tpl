{include file="header.tpl"}
{include file="pagehead.tpl"}

<h1>Shop</h1>
<div class="row-fluid">
	{foreach from=$items item=item}
	<div class="span4">
		<h4 class="muted">
			{$item->name}
		</h4>
		<img src="{if $item->image}$item->image{else}{$path}img/molumen_audio_cassette.svg{/if}" class="img-polaroid" style="background-color:#ddd; height:240px; width:240px;"/>
		<p>{$item->description}</p>
		<h5>{$item->price/100.0} €</h5>
		<form method="post" action="{$path}index.php/cart/addItem/{$item->id}">
		{if $item->sizes}
			<select id="size" name="size" class="btn span6">
			{foreach from=$item->sizes item=size}
				<option>{$size->size}</option>
			{/foreach}
			<select>
		{/if}

		{if $item->numbered && count($item->getFreeNumbers()) == 0}
			<div class="text-error">Out of stock.</div>
		{else}
			<div class="btn-group">
					<input type="submit" value="Add Item" class="btn btn-primary" />
			</div>
		{/if}
		</form>
	</div>
	{/foreach}

</div><!--/row-->
   
{include file="footer.tpl"}