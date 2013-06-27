{include file="header.tpl"}
{include file="pagehead.tpl"}
<h1>Review Order</h1>

{$userObj->email}
{$userObj->name}
{$userObj->lastname}
{$userObj->street}
{$userObj->street_number}
{$userObj->plz}
{$userObj->city}
{$userObj->country}

{include file="cart/cartItems.tpl"}

<form method="post" action="{$path}index.php/order" style="display:inline">
	<button type="submit" class="btn">Submit Order</button>
</form>
