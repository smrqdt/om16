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

{include file="shop/cartItems.tpl"}

<a href="{$path}index.php/submitOrder" class="btn">Submit Order</a>