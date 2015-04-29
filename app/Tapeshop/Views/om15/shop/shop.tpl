{extends file="baseWithNav.html"}

{block name=content}
    <div class="container" ng-app="tapeshop" ng-cloak>
        <h1>{_("shop.title")}</h1>
        {include file="shop/shop_text.tpl"}
{literal}
        <div class="row" id="items-list" ng-controller="shopController">
            <div class="span6">

                <div class="item" ng-repeat="item in items">
                    <a href="item/{{item.id}}">
                        <h4 class="muted">
                            {{item.name}}
                        </h4>
                    </a>
                    <h5>{{ item.price | euro }}</h5>
                    <p ng-bind-html="item.description"></p>
                    <div>
                        <div class="text-error" ng-if="!inStock(item)">{/literal}{_("item.outofstock")}{literal}</div>
                        <div ng-if="inStock(item)">
                            <select name="size" ng-if="item.sizes.length" ng-model="item.selectedSize" ng-options="size as size.size for size in sizesInStock(item)">
                            </select>
                            {{selectedSize}}
                            <div class="btn-group" ng-hide="item.support_ticket">
                                <button type="button" class="btn btn-default" ng-click="addToCart(item)">{/literal}{_('item.add')}{literal}</button>
                            </div>
                            <div ng-show="item.support_ticket" ng-init="item.support_price = 500">
                                {/literal}{_('item.choose_your_price')}{literal}
                                <input ng-model="item.support_price" type="range" min="500" max="10000" step="100" />
                                {{ item.support_price | euro }}
                                <button ng-click="addToCart(item)" class="btn btn-default">{{ addPrices(item) | euro }} {/literal}{_('item.add')}{literal}</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="container span6" ng-if="cart.length">
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
                            <td><b>{/literal}{_("order.total")}{literal}</b></td>
                            <td>
                                <b>{{getSum(cart)/100|number:2}} €</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
{/literal}
                <button type="submit" class="btn" ng-click="clearCart()"><i class="icon-remove"></i> {_("cart.clear")}</button>
                <a href="{$path}checkout" class="btn"><i class="icon-play"></i> {_("cart.checkout")}</a>
            </div>

        </div>

        <!--/row-->
    </div>
{/block}

{block name=moreScripts}
    <script src="{$path}/assets/js/shop.js"></script>
{/block}
