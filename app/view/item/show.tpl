{include file="header.tpl"}
{include file="pagehead.tpl"}

<div class="container container-fluid">

	<div class="span8">
			<h4 class="muted">
				{$item->name}
			</h4>
			<img src="{if $item->image}{$item->image}{else}{$path}assets/img/molumen_audio_cassette.svg{/if}" class="img-polaroid" style="background-color:#ddd;"/>
			<p>{$item->description}</p>
			{if $item->sizes}
				<b class="muted">Available sizes:</b>
				{foreach $item->sizes as $size}
					{$size->size}{if ! $size@last}, {/if}
				{/foreach}
			{/if}
			<h5>{$item->price/100.0} €</h5>
	</div>
	
	{if isset($smarty.session['auth_user']) && $user->admin}
	<div class="span4 well">
		<a href="{$path}index.php/item/edit/{$item->id}" class="btn"><i class="icon-pencil"></i> Edit</a>
		<form method="post" action="{$path}index.php/item/delete/{$item->id}" style="display:inline;">
			<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			<button type="submit" class="btn btn-danger"><i class="icon-trash"></i> Delete</button>
		</form>
	</div>
	{/if}
	
	<div class="span4 well">
	<h5>{$item->price/100.0} €</h5>
			<form method="post" action="{$path}index.php/cart/addItem/{$item->id}">
				<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			{if $item->sizes}
				<select id="size" name="size" class="btn span6">
				{foreach from=$item->sizes item=size}
					<option>{$size->size}</option>
				{/foreach}
				<select>
			{/if}
			{if $item->numbered && $item->getUnrequestedNumberCount() <= 0}
				<p class="text-error">Out of stock.</p>
			{else}
				<div class="btn-group">
						<input type="submit" value="Add Item" class="btn btn-primary" />
				</div>
			{/if}
			{if $item->ticketscript}
				<a href="{$path}index.php/ticketscript" class="btn btn-success">Buy Onlineticket</a>
			{/if}
			</form>
	</div>
<div>
{include file="footer.tpl"}