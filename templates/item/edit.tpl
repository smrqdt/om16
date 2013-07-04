{include file="header.tpl"} {include file="pagehead.tpl"}

<div class="row-fluid">

	<div class="span6">
		<form method="post" action="{$path}index.php/item/edit/{$item->id}"
			class="form-horizontal" enctype="multipart/form-data" >
			{include file="item/itemform.tpl"}
		</form>
	</div>

	<div class="span4 well">
		<form method="post" action="{$path}index.php/item/{$item->id}/removeimage">
			 <button type="submit" value="+" class="btn"><i class="icon-remove"></i> Remove Image</button>
		</form>
		<form method="post"
				action="{$path}index.php/item/delete/{$item->id}">
			<button type="submit" class="btn btn-danger"><i class="icon-trash"></i> Delete</button>
		</form>
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
	
	<div class="span4 well">
		<h5>Numberd</h5>
		{if $item->numbered}
		<table>
			<tr>
				<th>Total</th>
				<td>{count($item->itemnumbers)}</td>
			</tr>
			<tr>
				<th>Free</th>
				<td>{count($item->getFreeNumbers())}</td>
			</tr>
			<tr>
				<th>Invalid</th>
				<td>{count($item->getInvalidNumbers())}</td>
			</tr>
		</table>
		<form method="post" action="{$path}index.php/item/{$item->id}/addnumbers">
			<div class="input-append">
				<input type="number" placeholder="amount" name="amount" />
				<button type="submit" class="btn"><i class="icon-plus"></i></button>
			</div>
		</form>
		<form method="post" action="{$path}index.php/item/{$item->id}/takenumbers">
			<div class="input-append">
				<input type="text" placeholder="1,2,3,5-7" name="numbers" />
				<button type="submit" class="btn"><i class="icon-ok"></i></button>
			</div>
		</form>
		<form method="post" action="{$path}index.php/item/{$item->id}/invalidatenumbers">
			<div class="input-append">
				<input type="text" placeholder="1,2,3,5-7" name="numbers" />
				<button type="submit" class="btn"><i class="icon-remove"></i></button>
			</div>
		</form>
		
		{else}
		<form method="post" action="{$path}index.php/item/{$item->id}/numbered">
			<button type="submit" class="btn"><i class="icon-tags"></i> Add item numbers</button>
		</form>
		{/if}
	</div>
</div>

{include file="footer.tpl"}
