{include file="header.tpl"}
{include file="pagehead.tpl"}

    <div class="modal-backdrop login-backdrop">
    <div class="container">
    
      <form action="../save/{$userObject->id}" class="form-signin" method="post">
        <h2 class="form-signin-heading">Please enter the data you want to change {$userObject->email} </h2>
        {include file="flash.tpl"}
        <input type="text" name="email" class="input-block-level" value="{$userObject->email}">
        <input type="text" name="userObjectname" class="input-block-level" value="{$userObject->username}">
        <input type="text" name="name" class="input-block-level" value="{$userObject->name}">
        <input type="text" name="lastname" class="input-block-level" value="{$userObject->lastname}">
        <input type="text" name="street" class="input-block-level" value="{$userObject->street}">
        <input type="text" name="street_number" class="input-block-level" value="{$userObject->street_number}">
        <input type="text" name="plz" class="input-block-level" value="{$userObject->plz}">
        <input type="text" name="city" class="input-block-level" value="{$userObject->city}">
        <input type="text" name="country" class="input-block-level" value="{$userObject->country}">
		<a href="{$path}" class="btn btn-large">Back</a>
        <button class="btn btn-large btn-primary" type="submit">Save</button>
      </form>

{include file="footer.tpl"}