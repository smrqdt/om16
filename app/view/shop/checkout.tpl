{include file="header.tpl"}
{include file="pagehead.tpl"}
<h1>Checkout</h1>
<div class="span5 well">
	<h2>Login</h2>
	<form class="form-horizontal" method="post" action="{$path}index.php/login">
	  <div class="control-group">
	    <label class="control-label" for="email">Email</label>
	    <div class="controls">
	      <input type="text" id="email" name="email" placeholder="Email">
	    </div>
	  </div>
	  <div class="control-group">
	    <label class="control-label" for="password">Password</label>
	    <div class="controls">
	      <input type="password" id="password" name="password" placeholder="Password">
	    </div>
	  </div>
	  <div class="control-group">
	    <div class="controls">
	      <label class="checkbox">
	        <input type="checkbox"> Remember me
	      </label>
	      <button type="submit" class="btn">Sign in</button>
	    </div>
	  </div>
	</form>
</div>
<div class="span5 well">
<h2>Continue without login</h2>
<form class="form-horizontal" method="post" action="{$path}index.php/noSignup">
  <div class="control-group">
    <label class="control-label" for="email">Email</label>
    <div class="controls">
      <input type="text" id="email" name="email" placeholder="Email" value="{if isset($checkoutform)}{$checkoutform['email']}{/if}">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="name">Name</label>
    <label class="control-label" for="lastname">Lastname</label>
    <label class="control-label" for="street">Street</label>
    <label class="control-label" for="building_number">Building Number</label>
    <label class="control-label" for="postcode">Postcode</label>
    <label class="control-label" for="city">City</label>
    <label class="control-label" for="country">Country</label>
    <div class="controls">
    	<input type="text" id="name" name="name" placeholder="Name" value="{if isset($checkoutform)}{$checkoutform['name']}{/if}">
    	<input type="text" id="lastname" name="lastname" placeholder="Lastname" value="{if isset($checkoutform)}{$checkoutform['lastname']}{/if}">
    	<input type="text" id="street" name="street" placeholder="Street" value="{if isset($checkoutform)}{$checkoutform['street']}{/if}">
    	<input type="text" id="bulding_number" name="building_number" placeholder="Building Number" value="{if isset($checkoutform)}{$checkoutform['building_number']}{/if}">
    	<input type="text" id="postcode" name="postcode" placeholder="Postcode" value="{if isset($checkoutform)}{$checkoutform['postcode']}{/if}">
    	<input type="text" id="city" name="city" placeholder="City" value="{if isset($checkoutform)}{$checkoutform['city']}{/if}">
    	<input type="text" id="country" name="country" placeholder="Country" value="{if isset($checkoutform)}{$checkoutform['country']}{/if}">
    </div>
  </div>
  <div class="controls">
	<button type="submit" class="btn">Continue</button>
  </div>	
</form>
</div>