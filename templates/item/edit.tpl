{include file="header.tpl"} {include file="pagehead.tpl"}

<div class="row-fluid">
	<div>
		<div class="span8">
			<form method="post" action="{$path}index.php/item/edit/{$item->id}"
				class="form-horizontal">
				<div class="control-group">
					<label class="control-label" for="name">Name</label>
					<div class="controls">
						<input type="text" name="name" placeholder="Name"
							value="{$item->name}">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="image">Image</label>
					<div class="controls">
						<img src="{$item->image}" class="img-polaroid"
							style="background-color: #ddd; height: 240px; width: 240px;" /> <br />
						<input type="file" id="image" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="description">Description</label>
					<div class="controls">
						<textarea rows="4" type="text" name="description"
							placeholder="Description">{$item->description}</textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="price">Price</label>
					<div class="controls">
						<input type="text" name="price" placeholder="17.42"
							value="{$item->price/100}"> €
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<input type="submit" value="Save" class="btn" />
					</div>
				</div>
			</form>
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
			<form method="post"
				action="{$path}index.php/item/{$item->id}/addsize">
				<input type="text" placeholder="size" name="size" /> <input
					type="submit" value="+" class="btn" />
			</form>
		</div>