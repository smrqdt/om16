{extends file="email/base.tpl"}
{block name="message"}
Thank you for your order!

You ordered:

{foreach from=$order->orderitems item=item}
{$item->amount} x {$item->item->name} - {(($item->price + $item->support_price)/ 100)|number_format:2:",":"."}€
{/foreach}
------------------------------------------------------------------------------------------
Shipping:   {(($order->shipping) / 100)|number_format:2:",":"."}€
Sum:        {(($order->getSum()) / 100)|number_format:2:",":"."}€

###########################
---------------------------
INCLUDE PAYMENT INFORMATION
---------------------------
###########################
Billing number: {$orderPrefix}{$order->id}
{/block}
