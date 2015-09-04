{extends file="baseWithNav.html"}

{block name=content}
    <div class="row">
        <div class="span7 index-text" ng-app="tapeshop" ng-cloak ng-controller="nametagController">
            <h1>Namensschilder</h1>

            <p>
                Wie jedes Jahr wird es f&uuml;r jede_n ein Namensschild geben. Auf dieser Seite kannst du angeben, was
                auf
                deinem
                Namensschild stehen soll. Zun&auml;chst musst du jedoch den Code eingeben, den wir dir nach Eingang
                deiner
                Zahlung zugeschickt haben, um dich als Teilnehmer_in zu identifizieren. Wenn du mehrere Tickets bestellt
                hast, kannst du hier auch mehrere
                Namensschilder
                eintragen. Du kannst dich aber auch bei der Akkredetierung an uns wenden.
            </p>

            <br>

            <div ng-hide="order_id">
                <form action="#" class="form-search">
                    <label for="ticketcode">Ticketcode</label>
                    <input type="text" name="ticketcode" id="ticketcode" ng-model="ticketcode"
                           class="input-medium search-query"/>

                    <div class="control-group error" ng-show="error">
                    <span class="help-inline">
                        Ticketcode nicht gefunden!
                    </span>
                    </div>

                    <button type="button" ng-click="submitTicketcode(ticketcode)" class="btn" name="submit-ticketcode"
                            id="submit-ticketcode">Abschicken
                    </button>
                </form>
            </div>

            <div ng-show="order_id">
                <form class="form-horizontal" action="#">
                    <div class="control-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" ng-model="nametag.name" placeholder="Name"/>
                    </div>
                    <div class="control-group">
                        <label for="nickname">Nick</label>
                        <input type="text" name="nickname" id="nickname" ng-model="nametag.nickname"
                               placeholder="Nick"/>
                    </div>
                    <div class="control-group">
                        <label for="pronoun">Anrede</label>
                        <select name="pronoun" id="pronoun" ng-model="nametag.pronoun">
                            <option value="" disabled selected>Anrede</option>
                            <option>mit meinem Namen</option>
                            <option>sie (weiblich)</option>
                            <option>er (männlich)</option>
                            <option>frag mich einfach</option>
                        </select>
                    </div>
                    <button type="button" ng-click="addNametag(nametag)" class="btn" name="submit-nametag"
                            id="submit-nametag">
                        Hinzuf&uuml;gen
                    </button>
                </form>
            </div>

            <div class="row">
                <div ng-repeat="nametag in nametags" class="span3 well">
                    {literal}
                        <div>
                            <strong>
                                Name:
                            </strong>
                            {{nametag.name}}
                        </div>
                        <div>
                            <strong>
                                Nick:
                            </strong>
                            {{nametag.nickname}}
                        </div>
                        <div>
                            <strong>
                                Anrede:
                            </strong>
                            {{nametag.pronoun}}
                        </div>
                        <br>
                        <button ng-click="removeNametag(nametag)" type="button" class="btn">Entfernen</button>
                    {/literal}
                </div>
            </div>
        </div>
        {include file="static/sidebar.tpl"}
    </div>
{/block}

{block name=moreScripts}
    <script src="{$path}/assets/js/nametags.js"></script>
{/block}
