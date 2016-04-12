{extends file="email/base.tpl"}
{block name="message"}
Thank you for your order!

You ordered:

{foreach from=$order->orderitems item=item}
{$item->amount} x {$item->item->name} - {(($item->price + $item->support_price)/ 100)|number_format:2:",":"."}€
{/foreach}
------------------------------------------------------------------------------------------
Sum:        {(($order->getSum()) / 100)|number_format:2:",":"."}€


Please transfer the amount within 14 days.

Account holder: Junge Piraten e.V.
IBAN: DE76 4306 0967 6016 5069 00
BIC: GENODEM1GLS
Amount: {(($order->getSum()) / 100)|number_format:2:",":"."}€
Reason for payment/transfer: openmind {$orderPrefix}{$order->id}

{/block}
