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
      <input type="text" id="email" name="email" placeholder="Email">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="name">Name</label>
    <label class="control-label" for="lastname">Lastname</label>
    <label class="control-label" for="street">Street</label>
    <label class="control-label" for="street_number">Street Number</label>
    <label class="control-label" for="plz">PLZ</label>
    <label class="control-label" for="city">City</label>
    <label class="control-label" for="country">Country</label>
    <div class="controls">
    	<input type="text" id="name" name="name" placeholder="Name">
    	<input type="text" id="lastname" name="lastname" placeholder="Lastname">
    	<input type="text" id="street" name="street" placeholder="Street">
    	<input type="text" id="street_number" name="street_number" placeholder="Street Number">
    	<input type="text" id="plz" name="plz" placeholder="PLZ">
    	<input type="text" id="city" name="city" placeholder="City">
    	<input type="text" id="country" name="country" placeholder="Country">
    </div>
  </div>
  <div class="controls">
	<button type="submit" class="btn">Continue</button>
  </div>	
</form>
</div>