{include file="header.tpl"}
    <div class="modal-backdrop login-backdrop">
    <div class="container">
    
      <form class="form-horizontal" method="post">
        <h2 class="form-signin-heading">Please enter your data</h2>
        {include file="flash.tpl"}
        <div class="control-group">

            <label class="control-label" for="email">E-Mail Adress</label>
            
            <div class="controls">
                <input type="text" name="email" class="input-block-level">
            </div>
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="username">Username</label>
            
            <div class="controls">
                <input type="text" name="username" class="input-block-level">
            </div>
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="password">Password</label>
            
            <div class="controls">
                <input type="password" name="password" class="input-block-level">
            </div>
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="password_verify">Reenter Password</label>

            <div class="controls">
                <input type="password" name="password_verify" class="input-block-level">
            </div> 
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="name">Name</label>
                
            <div class="controls">
                <input type="text" name="name" class="input-block-level">
            </div>

        </div>

        <div class="control-group">

            <label class="control-label" for="lastname">Lastname</label>
        
            <div class="controls">
                <input type="text" name="lastname" class="input-block-level">
            </div>
    
        </div>
    
        <div class="control-group">

            <label class="control-label" for="street">Street</label>
            
            <div class="controls">
                <input type="text" name="street" class="input-block-level">
            </div>
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="street_number">Street Number</label>
        
            <div class="controls">
                <input type="text" name="street_number" class="input-block-level">
            </div>
    
        </div>
    
        <div class="control-group">

            <label class="control-label" for="plz">PLZ</label>
            
            <div class="controls">
                <input type="text" name="plz" class="input-block-level">
            </div>
                
        </div>

        <div class="control-group">

            <label class="control-label" for="city">City</label>
        
            <div class="controls">
                <input type="text" name="city" class="input-block-level">
            </div>
        </div>

        <div class="control-group">

            <label class="control-label" for="country">Country</label>
            
            <div class="controls">
                <input type="text" name="country" class="input-block-level">
            </div>
    
        </div>
    
        <label class="checkbox">    
          <input type="checkbox" name="remember" value="remember-me"> Remember me
        </label>
		<a href="{$path}" class="btn btn-large">Back</a>
        <button class="btn btn-large btn-primary" type="submit">Register</button>
      </form>

{include file="footer.tpl"}