{extends file="baseWithNav.html"}

{block name=content}
    <div class="container" ng-app="tapeshop" ng-cloak>
        <h1>Tickets</h1>
        {include file="shop/shop_text.tpl"}
{literal}
        <div class="row" id="items-list" ng-controller="shopController">
            <div class="span6">

                <div class="row item span6" ng-repeat="item in items">

                    <div class="span4">
                        <h4>
                            {{item.name}}
                        </h4>
                        <img src="{{item.image}}" ng-if="item.image"/>

                        <p ng-bind-html="item.description"></p>
                    </div>

                    <div class="span2">
                        <h5 ng-hide="item.support_ticket">{{ item.price | euro }}</h5>
                        <h5 ng-show="item.support_ticket">{{ addPrices(item) | euro }}</h5>
                        <div class="text-error" ng-if="!inStock(item)">{/literal}{_("item.outofstock")}{literal}</div>
                        <div ng-if="inStock(item)">
                            <form class="form-search" action="#">
                                <select name="size" class="span2" ng-init="item.selectedSize = sizesInStock(item)[0]" ng-if="item.sizes.length" ng-model="item.selectedSize" ng-options="size as size.size for size in sizesInStock(item)">
                                </select>
                                {{selectedSize}}
                                <div class="btn-group span2" ng-hide="item.support_ticket">
                                    <button type="button" class="btn btn-white span2" ng-click="addToCart(item)">{/literal}{_('item.add')}{literal}</button>
                                </div>
                                <div ng-show="item.support_ticket" ng-init="item.support_price = 500">
                                    {/literal}{_('item.choose_your_price')}{literal}<br/>
                                    <input class="span2" ng-model="item.support_price" type="range" min="500" max="20000" step="100" />
                                    <!--{{ item.support_price | euro }}-->
                                    <button type="button" ng-click="addToCart(item)" class="btn btn-white span2">{{ addPrices(item) | euro }} {/literal}{_('item.add')}{literal}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="cart" class="container span6" ng-if="cart.length">
{/literal}
                <h3>{_("cart.heading")}</h3>


                <table class="table">
                    <thead>
                        <tr>
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
                                {{cartItem.item.name}}
                            </td>
                            <td>
                                <span ng-if="cartItem.item.sizes.length">
                                {{findById(cartItem.item.sizes, cartItem.size).size}}
                                </span>
                            </td>
                            <td>
                                {{cartItem.amount}}
                            </td>
                            <td>
                                {{cartItem.item.price + cartItem.support_price | euro }}
                            </td>
                            <td>
                                {{ addPrices(cartItem) * cartItem.amount | euro }}
                            </td>
                            <td>
                                <button type="submit" class="btn btn-mini btn-link" ng-click="removeFromCart(cartItem)">
                                    <i class="icon-remove"></i>
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3" style="border-top:none;"></td>
                            <td class="cart-total"><b>{/literal}{_("order.total")}{literal}</b></td>
                            <td class="cart-total">
                                <b>{{getSum(cart)/100|number:2}} €</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
{/literal}
                <div class="container">
                    <div class="row">
                        <div class="span2">
                            <button type="submit" class="btn btn-white span2" ng-click="clearCart()"><i class="icon-remove"></i> {_("cart.clear")}</button>
                        </div>
                        <br class="hidden-desktop">
                        <div class="span2">
                            <a href="{$path}checkout" class="btn btn-white span2"><i class="icon-play"></i> {_("cart.checkout")}</a>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <br>
            </div>

        </div>

        <!--/row-->
    </div>
{/block}

{block name=moreScripts}
    <script src="{$path}/assets/js/shop.js"></script>
{/block}
