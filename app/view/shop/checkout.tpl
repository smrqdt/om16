{include file="header.tpl"} {include file="pagehead.tpl"}
<h1>Checkout</h1>

<div class="span5 well">
	<h2>Continue without login</h2>
	<form class="form-horizontal" method="post"
		action="{$path}index.php/noSignup">
		<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
		<div class="control-group">
			<label class="control-label" for="email">Email</label>
			<div class="controls">
				<input type="text" id="email" name="email" placeholder="Email"
					value="{if isset($checkoutform)}{$checkoutform['email']}{/if}">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="name">Name</label>
			<div class="controls">
				<input type="text" id="name" name="name" placeholder="Name"
					value="{if isset($checkoutform)}{$checkoutform['name']}{/if}">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="lastname">Lastname</label>
			<div class="controls">
				<input type="text" id="lastname" name="lastname"
					placeholder="Lastname"
					value="{if isset($checkoutform)}{$checkoutform['lastname']}{/if}">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="street">Street</label>
			<div class="controls">
				<input type="text" id="street" name="street" placeholder="Street"
					value="{if isset($checkoutform)}{$checkoutform['street']}{/if}">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="building_number">Building Number</label>
			<div class="controls">
				<input type="text" id="bulding_number" name="building_number"
					placeholder="Building Number"
					value="{if isset($checkoutform)}{$checkoutform['building_number']}{/if}">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="postcode">Postcode</label>
			<div class="controls">
				<input type="text" id="postcode" name="postcode"
					placeholder="Postcode"
					value="{if isset($checkoutform)}{$checkoutform['postcode']}{/if}">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="city">City</label>
			<div class="controls">
				<input type="text" id="city" name="city" placeholder="City"
					value="{if isset($checkoutform)}{$checkoutform['city']}{/if}">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="country">Country</label>
			<div class="controls">
				<input type="text" id="country" name="country" placeholder="Country"
					value="{if isset($checkoutform)}{$checkoutform['country']}{/if}">
			</div>
		</div>
		<div class="controls">
			<button type="submit" class="btn">Continue</button>
		</div>
	</form>
</div>
