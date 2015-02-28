{extends file="baseWithNav.html"}

{block name=content}
    <table class="table" id="ticketcodesTable">
        <thead>
        <tr>
            <th>
                Ticketcode
            </th>
            <th>
                Name
            </th>
            <th>
                Anzahl
            </th>
            <th>
                Bestellnummer
            </th>
        </tr>
        </thead>
        <tbody>
        {foreach $item->orderitems as $orderitem}
            <tr>
                <td>
                    {if $orderitem->ticketcode_valid}
                        {$orderitem->ticketcode}
                    {else}
                        <del>{$orderitem->ticketcode}</del>
                    {/if}
                </td>
                <td>
                    {$orderitem->order->user->currentAddress()->name} {$orderitem->order->user->currentAddress()->lastname}
                </td>
                <td>
                    {$orderitem->amount}
                </td>
                <td>
                    {$orderitem->order->id}
                </td>
                <td>
                    {if $orderitem->ticketcode_valid}
                    <form method="post"
                          action="{$path}ticketcode/{$orderitem->id}">
                        <input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
                        <button type="submit" class="btn"><i class="icon-ok"></i></button>
                    </form>
                    {else}
                        <form method="post"
                              action="{$path}ticketcode/{$orderitem->id}/reactivate">
                            <input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
                            <button type="submit" class="btn"><i class="icon-edit"></i></button>
                        </form>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/block}

{block name=moreStyles}
    <link href="{$path}assets/css/DT_bootstrap.css" rel="stylesheet" media="all">
{/block}
{block name=moreScripts}
    <script src="{$path}node_modules/drmonty-datatables/js/jquery.dataTables.min.js"></script>
    <script src="{$path}assets/js/DT_bootstrap.js"></script>
{literal}
    <script type="text/javascript">

        /* orders table initialisation */
        $(document).ready(function() {
            $('#ticketcodesTable').dataTable({
                "aaSorting": [[ 0, "asc" ]],
                "aoColumns": [
                    null,
                    null,
                    null,
                    null,
                    { "bSortable": false, "bSearchable" : false}
                ]
            });
        });
    </script>
{/literal}
{/block}