{include file="header.tpl"}
{include file="pagehead.tpl"}
<h1>Review Order</h1>

{$user->email}<br />
{$user->currentAddress()->name} 
{$user->currentAddress()->lastname}<br />
{$user->currentAddress()->street} 
{$user->currentAddress()->building_number}<br />
{$user->currentAddress()->postcode} 
{$user->currentAddress()->city}<br />
{$user->currentAddress()->country}

{include file="cart/cartItems.tpl"}

<form method="post" action="{$path}index.php/order" style="display:inline">
	<button type="submit" class="btn">Submit Order</button>
</form>
