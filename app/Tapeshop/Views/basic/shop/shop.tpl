{extends file="baseWithNav.html"}

{block name=content}
    <div class="container-fluid" ng-app="tapeshop">
        <h1>{_("shop.title")}</h1>
{literal}
        <div class="row-fluid" id="items-list" ng-controller="shopController">
            <div class="container item" ng-repeat="item in items">

                <a href="item/{{item.id}}">
                    <h4 class="muted">
                        {{item.name}}
                    </h4>
                    <img src="{if $item->image}{$item->image}{else}{$item_placeholder}{/if}" class="img-polaroid"
                         style="background-color:#ddd; "/>
                </a>
                <h5>{{item.price/100.0|number:2}} €</h5>

                <div>
                    <div class="text-error" ng-if="!inStock(item)">{/literal}{_("item.outofstock")}{literal}</div>
                    <div ng-if="inStock(item)">

                        <select name="size" class="span12" ng-if="item.sizes.length" ng-model="item.selectedSize" ng-options="size as size.size for size in sizesInStock(item)">
                        </select>
                        {{selectedSize}}
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" ng-click="addToCart(item)">{/literal}{_('item.add')}{literal}</button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="container span4" ng-if="cart.length">
                {{cart|json}}
{/literal}
                <h3>{_("cart.heading")}</h3>


                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <!-- image column -->
                            </th>
                            <th>
                                {_("cart.item")}
                            </th>
                            <th>
                                {_("item.variation")}
                            </th>
                            <th>
                                {_("cart.amount")}
                            </th>
                            <th>
                                {_("item.price")}
                            </th>
                            <th>
                                {_("order.total")}
                            </th>
                            <th>
                                <!-- remove item -->
                            </th>
                        </tr>
                    </thead>
{literal}
                    <tbody>
                        <tr ng-repeat="cartItem in cart">
                            <td>
                                <img src="{if $item['item']->image}{$item['item']->image}{else}{$item_placeholder}{/if}" class="img-square" style="background-color:#ddd; height:50px;width:50px;" />
                            </td>
                            <td>
                                {{cartItem.item.name}}
                            </td>
                            <td>
                                <span ng-if="cartItem.item.sizes.length">
                                {{findById(cartItem.item.sizes, cartItem.size).size}}
                                </span>
                            </td>
                            <td>
                                {{cartItem.amount}}
                                <!--
                                <button type="submit" class="btn btn-mini">
                                    <i class="icon-plus"></i>
                                </button>

                                <button type="submit" class="btn btn-mini">
                                    <i class="icon-minus"></i>
                                </button>
                                -->
                            </td>
                            <td>
                                {{cartItem.item.price/100|number:2}} €
                            </td>
                            <td>
                                {{cartItem.item.price/100*cartItem.amount|number:2}} €
                            </td>
                            <td>
                                <button type="submit" class="btn btn-mini btn-link" ng-click="removeFromCart(cartItem)">
                                    <i class="icon-remove"></i>
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4" style="border-top:none;"></td>
                            <td><b>{/literal}{_("order.total")}{literal}</b></td>
                            <td>
                                <b>{{getSum(cart)/100|number:2}} €</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
{/literal}
                <button type="submit" class="btn"><i class="icon-remove"></i> {_("cart.clear")}</button>
                <a href="{$path}checkout" class="btn"><i class="icon-play"></i> {_("cart.checkout")}</a>
            </div>

        </div>

        <!--/row-->
    </div>
{/block}

{block name=moreScripts}
    <script src="assets/js/shop.js"></script>
{/block}