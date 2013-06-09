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
    <label class="control-label" for="address">Address</label>
    <div class="controls">
      <textarea id="address" name="address" rows="4" placeholder="Address"></textarea>
      <span class="helpline">Firstname, Lastname, Street Building, Postcode, City, Country</span>
    </div>
  </div>
  <div class="controls">
	<button type="submit" class="btn">Continue</button>
  </div>	
</form>
</div>