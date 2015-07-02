{extends file="email/base.tpl"}
{block name="message"}
This is a reminder that you ordered something. We do not seem to have received a payment yet.
If you already payed, you do not need to take further action and can ignore this email.

You ordered:

{foreach from=$order->orderitems item=item}
    {$item->amount} x {$item->item->name} - {(($item->price + $item->support_price)/ 100)|number_format:2:",":"."}€
{/foreach}
------------------------------------------------------------------------------------------
Sum:        {(($order->getSum()) / 100)|number_format:2:",":"."}€

Account holder: Junge Piraten e.V.
IBAN: DE76 4306 0967 6016 5069 00
BIC: GENODEM1GLS
Amount: {(($order->getSum()) / 100)|number_format:2:",":"."}€
Reason for payment/transfer: openmind {$orderPrefix}{$order->id}

{/block}
