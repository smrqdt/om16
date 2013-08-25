{include file="header.tpl"}
{include file="pagehead.tpl"}

<div class="row-fluid">

	<div class="span6">
		<form method="post" action="{$path}item/edit/{$item->id}"
			class="form-horizontal" enctype="multipart/form-data" >
			<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			{include file="item/itemform.tpl"}
		</form>
	</div>

	<div class="span4 well">
		<form method="post" action="{$path}item/{$item->id}/removeimage" style="display:inline;">
			 <input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			 <button type="submit" value="+" class="btn"><i class="icon-remove"></i> Remove Image</button>
		</form>
		<form method="post"
				action="{$path}item/delete/{$item->id}" style="display:inline;">
			<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			<button type="submit" class="btn btn-danger"><i class="icon-trash"></i> Delete Item</button>
		</form>
	</div>

	<div class="span4 well">
		<h5>Variations</h5>
		<ul>
			{if $item->sizes} {foreach from=$item->sizes item=size}
			<li>{$size->size}
				<form method="post"
					action="{$path}item/deletesize/{$size->id}"
					style="display: inline">
					<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
					<button type="submit" class="btn btn-mini">
						<i class="icon-trash"></i>
					</button>
				</form>
			</li> {/foreach} {/if}
		</ul>
		<form method="post" action="{$path}item/{$item->id}/addsize">
			<div class="input-append">
				<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
				<input type="text" placeholder="size" name="size" />
				<button type="submit" class="btn"><i class="icon-plus"></i></button>
			</div>
		</form>
	</div>
	
	<div class="span4 well">
		<h5>Item numbers</h5>
		{if $item->numbered}
		<table class="table">
			<tr>
				<th>Total</th>
				<th>Free</th>
				<th>Invalid</th>
				<th>Unrequested</th>
			</tr>
			<tr>
				<td>{count($item->itemnumbers)}</td>
				<td>{count($item->getFreeNumbers())}</td>
				<td>{count($item->getInvalidNumbers())}</td>
				{if $item->getUnrequestedNumberCount() <= 0}
					<td class="text-error">
				{else}
					<td>
				{/if}
				{$item->getUnrequestedNumberCount()}</td>
			</tr>
		</table>
		<form method="post" action="{$path}item/{$item->id}/addnumbers">
			<div class="input-append">
				<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
				<input type="number" placeholder="amount" name="amount" />
				<button type="submit" class="btn"><i class="icon-plus"></i></button>
			</div>
		</form>
		<form method="post" action="{$path}item/{$item->id}/takenumbers">
			<div class="input-append">
				<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
				<input type="text" placeholder="1,2,3,5-7" name="numbers" />
				<button type="submit" class="btn"><i class="icon-ok"></i></button>
			</div>
		</form>
		<form method="post" action="{$path}item/{$item->id}/invalidatenumbers">
			<div class="input-append">
				<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
				<input type="text" placeholder="1,2,3,5-7" name="numbers" />
				<button type="submit" class="btn"><i class="icon-remove"></i></button>
			</div>
		</form>
		
		{else}
		<form method="post" action="{$path}item/{$item->id}/makenumbered">
			<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			<button type="submit" class="btn"><i class="icon-tags"></i> Add item numbers</button>
		</form>
		{/if}
	</div>
</div>

<script type="text/javascript" src="assets/js/snapeditor.js"></script>
<script type="text/javascript">
  // "editor" is the id of the textarea.
  var formEditor = new SnapEditor.Form("editor", {
    imageServer: {
      uploadUrl: "http://images.snapeditor.com/snapimage_api",
      publicUrl: "http://images.snapeditor.com/images",
      directory: "my-directory"
    }
  });
</script>

{include file="footer.tpl"}