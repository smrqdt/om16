{extends file="email/base.tpl"}
{block name="message"}
Your order {$orderPrefix}{$order->id} has been shipped!
{/block}
