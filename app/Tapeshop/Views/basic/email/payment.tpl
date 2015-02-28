{extends file="email/base.tpl"}
{block name="message"}
We received your payment for order {$orderPrefix}{$order->id}.
We will ship your order as soon as possible and inform you as soon as it is on its way.

    {if $order->hasTicketCodes()}
You ordered some items with ticketcodes that grant you entry to venues.

        {foreach from=$order->orderitems item=orderitem}
{$orderitem->amount} x {$orderitem->item->name}                {$orderitem->ticketcode}

        {/foreach}

Please print out this email as it will funtion as your ticket to the venues.
    {/if}
{/block}
