{include file="header.tpl"}
{include file="pagehead.tpl"}    
    <div class="span6 offset3">
      <h2 class="form-signin-heading">Please enter your data</h2>
      <form class="form-horizontal" method="post">
        {include file="flash.tpl"}
        <input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
        <div class="control-group">

            <label class="control-label" for="email">E-Mail Adress</label>
            
            <div class="controls">
                <input type="text" name="email" class="input-block-level" value="{if isset($signupform)}{$signupform['email']}{/if}"">
            </div>
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="username">Username</label>
            
            <div class="controls">
                <input type="text" name="username" class="input-block-level" value="{if isset($signupform)}{$signupform['username']}{/if}">
            </div>
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="password">Password</label>
            
            <div class="controls">
                <input type="password" name="password" class="input-block-level" value="{if isset($signupform)}{$signupform['password']}{/if}">
            </div>
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="password_verify">Reenter Password</label>

            <div class="controls">
                <input type="password" name="password_verify" class="input-block-level" value="{if isset($signupform)}{$signupform['password_verify']}{/if}">
            </div> 
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="name">Name</label>
                
            <div class="controls">
                <input type="text" name="name" class="input-block-level" value="{if isset($signupform)}{$signupform['name']}{/if}">
            </div>

        </div>

        <div class="control-group">

            <label class="control-label" for="lastname">Lastname</label>
        
            <div class="controls">
                <input type="text" name="lastname" class="input-block-level" value="{if isset($signupform)}{$signupform['lastname']}{/if}">
            </div>
    
        </div>
    
        <div class="control-group">

            <label class="control-label" for="street">Street</label>
            
            <div class="controls">
                <input type="text" name="street" class="input-block-level" value="{if isset($signupform)}{$signupform['street']}{/if}">
            </div>
        
        </div>
        
        <div class="control-group">

            <label class="control-label" for="building_number">Street Number</label>
        
            <div class="controls">
                <input type="text" name="building_number" class="input-block-level" value="{if isset($signupform)}{$signupform['building_number']}{/if}">
            </div>
    
        </div>
    
        <div class="control-group">

            <label class="control-label" for="plz">Postcode</label>
            
            <div class="controls">
                <input type="text" name="postcode" class="input-block-level" value="{if isset($signupform)}{$signupform['postcode']}{/if}">
            </div>
                
        </div>

        <div class="control-group">

            <label class="control-label" for="city">City</label>
        
            <div class="controls">
                <input type="text" name="city" class="input-block-level" value="{if isset($signupform)}{$signupform['city']}{/if}">
            </div>
        </div>

        <div class="control-group">

            <label class="control-label" for="country">Country</label>
            
            <div class="controls">
                <input type="text" name="country" class="input-block-level" value="{if isset($signupform)}{$signupform['country']}{/if}">
            </div>
    
        </div>
    	<div class="control-group">
		    <div class="controls">
		        <label class="checkbox">    
		          <input type="checkbox" name="remember" value="remember-me"> Remember me
		        </label>
				<a href="{$path}" class="btn">Back</a>
		        <button class="btn btn-primary" type="submit">Register</button>
		    </div>
	    </div>
      </form>
	</div>

{include file="footer.tpl"}