{include file="header.tpl"}
{include file="pagehead.tpl"}
<h1>Review Order</h1>

{$userObj->email}<br />
{$userObj->currentAddress()->name} 
{$userObj->currentAddress()->lastname}<br />
{$userObj->currentAddress()->street} 
{$userObj->currentAddress()->building_number}<br />
{$userObj->currentAddress()->postcode} 
{$userObj->currentAddress()->city}<br />
{$userObj->currentAddress()->country}

{include file="cart/cartItems.tpl"}

<form method="post" action="{$path}index.php/order" style="display:inline">
	<button type="submit" class="btn">Submit Order</button>
</form>
