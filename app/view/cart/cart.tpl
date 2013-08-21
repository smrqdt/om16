{include file="header.tpl"}
{include file="pagehead.tpl"}
<h1>Cart</h1>
{if $noCartItems > 0}
{include file="cart/cartItems.tpl"}

<form method="post" action="{$path}index.php/cart/clear" style="display:inline">
	<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
	<button type="submit" class="btn"><i class="icon-remove"></i> Clear cart</button>
</form>
<a href="{$path}index.php/checkout" class="btn"><i class="icon-play"></i> Checkout</a>
{else}
No items in cart.
{/if}

{include file="footer.tpl"}