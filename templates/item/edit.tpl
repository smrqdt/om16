{include file="header.tpl"} {include file="pagehead.tpl"}

<div class="row-fluid">
	<div>
		<div class="span8">
			<h4 class="muted">{$item->name}</h4>
			<img src="{$item->image}" class="img-polaroid"
				style="background-color: #ddd; height: 240px; width: 240px;" />
			<p>{$item->description}</p>
			{if $item->sizes} <b class="muted">Available sizes:</b> {foreach
			$item->sizes as $size} {$size->size}{if ! $size@last}, {/if}
			{/foreach} {/if}
			<h5>{$item->price/100.0} €</h5>
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
				<input type="text" placeholder="size" name="size"/>
				<input type="submit" value="+" class="btn" />
			</form>
		</div>
