{extends file="baseWithNav.html"}

{block name=content}
    <div class="container" ng-app="tapeshop">
        <h1>{_("shop.title")}</h1>
{literal}
        <div class="row" id="items-list" ng-controller="shopController">
            <div class="span6">
                <div class="container item" ng-if="inStock(findById(items, EARLY_BIRD_ID))">
                    <h4>#om15 Early Brid Ticket</h4>
                    <p>Das Konferenzticket für Frühentschlossene.</p>
                    <button type="button" class="btn" ng-click="addEarlyBird()">Bestellen</button>
                </div>

                <div class="container item" ng-if="inStock(findById(items, TICKET_ID))">
                    <h4>#om15 Konferenzticket</h4>
                    <p>Das Konferenzticket.</p>
                    <button type="button" class="btn" ng-click="addToCart(findById(items, TICKET_ID))">Bestellen</button>
                </div>

                <div class="container item" ng-if="inStock(findById(items, TICKET_ID)) && inStock(findById(items, FIRST_TIME_ID))">
                    <h4>#om15 First Time Ticket</h4>
                    <p>Für Menschen die das erste mal auf der OpenMind sind.</p>
                    <button type="button" class="btn" ng-click="addFirstTime()">Bestellen</button>
                </div>

                <div class="container item" ng-if="inStock(findById(items, UEBERNACHTUNG_ID)) && uebernachtungAvailable(cart)">
                    <h4>#om15 Übernachtung</h4>
                    <p>Übernachtung in der Jugendherberge, inklusive Frühstück und Abendessen.</p>
                    <button type="button" class="btn" ng-click="addToCart(findById(items, UEBERNACHTUNG_ID))">Bestellen</button>
                </div>

                <div class="container item" ng-init="item = findById(items, SHIRT_ID)" ng-if="items">
                    <h4>#om15 Shirt</h4>
                    <p>Schickes #om15 Shirt</p>
                    <select name="size" ng-if="item.sizes.length" ng-model="item.selectedSize" ng-init="item.selectedSize = sizesInStock(item)[0]" ng-options="size as size.size for size in sizesInStock(item)" required>
                    </select>
                    <button type="button" class="btn" ng-click="addToCart(item)">Bestellen</button>
                </div>




                <div class="container item" ng-repeat="item in items">
                    <a href="item/{{item.id}}">
                        <h4 class="muted">
                            {{item.id}} {{item.name}}
                        </h4>
                    </a>
                    <h5>{{item.price/100.0|number:2}} €</h5>

                    <div>
                        <div class="text-error" ng-if="!inStock(item)">{/literal}{_("item.outofstock")}{literal}</div>
                        <div ng-if="inStock(item)">
                            <select name="size" ng-if="item.sizes.length" ng-model="item.selectedSize" ng-options="size as size.size for size in sizesInStock(item)">
                            </select>
                            {{selectedSize}}
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary" ng-click="addToCart(item)">{/literal}{_('item.add')}{literal}</button>
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
                            <td colspan="3" style="border-top:none;"></td>
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