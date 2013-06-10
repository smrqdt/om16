{include file="header.tpl"}
    <div class="modal-backdrop login-backdrop">
    <div class="container">
    
      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Please enter your data</h2>
        {include file="flash.tpl"}
        <input type="text" name="email" class="input-block-level" placeholder="Email address">
        <input type="text" name="username" class="input-block-level" placeholder="Username">
        <input type="password" name="password" class="input-block-level" placeholder="Password">
        <input type="password" name="password_verify" class="input-block-level" placeholder="Reenter Password">
        <input type="text" name="name" class="input-block-level" placeholder="Name">
        <input type="text" name="lastname" class="input-block-level" placeholder="Lastname">
        <input type="text" name="street" class="input-block-level" placeholder="Street">
        <input type="text" name="street_number" class="input-block-level" placeholder="Street Number">
        <input type="text" name="plz" class="input-block-level" placeholder="PLZ">
        <input type="text" name="city" class="input-block-level" placeholder="City">
        <input type="text" name="country" class="input-block-level" placeholder="Country">
        <label class="checkbox">
          <input type="checkbox" name="remember" value="remember-me"> Remember me
        </label>
		<a href="{$path}" class="btn btn-large">Back</a>
        <button class="btn btn-large btn-primary" type="submit">Register</button>
      </form>

{include file="footer.tpl"}