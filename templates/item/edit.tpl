{include file="header.tpl"} {include file="pagehead.tpl"}

<div class="row-fluid">

	<div class="span6">
		<form method="post" action="{$path}index.php/item/edit/{$item->id}"
			class="form-horizontal">{include file="item/itemform.tpl"}</form>
	</div>

	<div class="span4 well">
		<a href="#" class="btn"><i class="icon-pencil"></i> Edit</a> <a
			href="#" class="btn btn-error"><i class="icon-trash"></i> Delete</a>
	</div>

	<div class="span4 well">
		<h5>Sizes</h5>
		<ul>
			{if $item->sizes} {foreach from=$item->sizes item=size}
			<li>{$size->size}
				<form method="post"
					action="{$path}index.php/item/deletesize/{$size->id}"
					style="display: inline">
					<button type="submit" class="btn btn-mini">
						<i class="icon-trash"></i>
					</button>
				</form>
			</li> {/foreach} {/if}
		</ul>
		<form method="post" action="{$path}index.php/item/{$item->id}/addsize">
			<input type="text" placeholder="size" name="size" /> <input
				type="submit" value="+" class="btn" />
		</form>
	</div>
</div>
