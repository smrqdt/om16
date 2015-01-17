{extends file="email/base.tpl"}
{block name="message"}
We received your payment for order {$orderPrefix}{$order->id}.
We will ship your order as soon as possible and inform you as soon as it is on its way.
{/block}
