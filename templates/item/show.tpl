{include file="header.tpl"}
{include file="pagehead.tpl"}

<div class="row-fluid">

<div class="span8">
		<h4 class="muted">
			{$item->name}
		</h4>
		<img src="{$item->image}" class="img-polaroid" style="background-color:#ddd; height:240px; width:240px;"/>
		<p>{$item->description}</p>
		{if $item->sizes}
			<b class="muted">Available sizes:</b>
			{foreach $item->sizes as $size}
				{$size->size}{if ! $size@last}, {/if}
			{/foreach}
		{/if}
		<h5>{$item->price/100.0} €</h5>
</div>

{if isset($smarty.session['auth_user']) && $smarty.session['auth_user']['admin']}
<div class="span4 well">
	<a href="{$path}index.php/item/edit/{$item->id}" class="btn"><i class="icon-pencil"></i> Edit</a>
	<br/><br/>
	<form method="post" action="{$path}index.php/item/delete/{$item->id}">
		<button type="submit" class="btn btn-danger"><i class="icon-trash"></i> Delete</button>
	</form>
</div>
{/if}

<div class="span4 well">
<h5>{$item->price/100.0} €</h5>
		<form method="post" action="{$path}index.php/cart/addItem/{$item->id}">
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
<div>
{include file="footer.tpl"}