{include file="header.tpl"}
{include file="pagehead.tpl"}

<h1>Shop</h1>
<div class="row-fluid">
	{foreach from=$items item=item}
	<div class="span4">
		<h4 class="muted">
			{$item->name}
		</h4>
		<img src="{$item->image}" class="img-polaroid" style="background-color:#ddd; height:240px; width:240px;"/>
		<p>{$item->description}</p>
		<h5>{$item->price/100.0} €</h5>
		<form method="post" action="{$path}index.php/addItem/{$item->id}">
		{if $item->sizes}
			<select id="size" name="size" class="btn span6">
			{foreach from=$item->sizes item=size}
				<option>{$size->size}</option>
			{/foreach}
			<select>
		{/if}
		<div class="btn-group">
			<input type="submit" value="Add Item" class="btn btn-primary" />
		</div>
		</form>
	</div>
	{/foreach}

</div><!--/row-->
   
{include file="footer.tpl"}