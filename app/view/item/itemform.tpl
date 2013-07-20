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
		<input type="file" name="image"  id="image"/>
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
