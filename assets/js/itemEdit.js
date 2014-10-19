angular.module("tapeshop", []);

angular.module("tapeshop").value("baseUrl", jQuery("base").attr("href"));
angular.module("tapeshop").value("itemId", jQuery("#itemId").data("itemId"));

angular.module("tapeshop").controller("stockController", function ($scope, itemsAPI, stockAPI, variationsAPI, itemId) {
    console.log("item "+itemId);

    $scope.reloadItem = function () {
        console.log("reload "+itemId);
        itemsAPI.get(itemId).success(function (item) {
            console.log(item);
            $scope.item = item;
            $scope.item.manage_stock = !!$scope.item.manage_stock;
        });
    };


    $scope.updateManageStock = function (item) {
        itemsAPI.updateManageStock(item).success($scope.reloadItem.bind($scope))
    };

    $scope.addStock = function(item, amount){
        itemsAPI.addStock(item, amount).success($scope.reloadItem.bind($scope));
    };

    $scope.addVariationStock = function(variation, amount){
        variationsAPI.addStock(variation, amount).success($scope.reloadItem.bind($scope));
    };

    $scope.addVariation = function(item, variationName){
        variationsAPI.addVariation(item, variationName).success($scope.reloadItem.bind($scope));
    };

    $scope.deleteVariation = function(variation){
      variationsAPI.deleteVariation(variation).success($scope.reloadItem.bind($scope));
    };

    $scope.reloadItem();
});

angular.module("tapeshop").factory("itemsAPI", function ($http, baseUrl) {
    var itemsAPI = {
        get: function (id) {
            return $http.get(baseUrl + "items/" + id);
        },
        updateManageStock: function (item) {
            return $http({
                url: baseUrl + "items/" + item.id + "/manage",
                method: "PUT",
                data: {
                    manage_stock: item.manage_stock
                }
            });
        },
        addStock: function (item, amount) {
            return $http({
                url: baseUrl + "stocks/item/" + item.id,
                method: "PUT",
                data: {
                    amount: amount
                }
            });
        }
    };
    return itemsAPI;
});

angular.module("tapeshop").factory("variationsAPI", function ($http, baseUrl) {
    var variationsAPI = {
        addVariation: function (item, variationName, stock) {
            if(!stock || stock < 0){
                stock = 0;
            }

            return $http({
                url: baseUrl + "variations/",
                method: "POST",
                data: {
                    itemId: item.id,
                    name: variationName,
                    stock: stock
                }
            });
        },
        deleteVariation: function (variation) {
            return $http.delete(baseUrl + "variations/" + variation.id);
        },
        addStock: function (variation, amount) {
            return $http({
                url: baseUrl + "stocks/variation/" + variation.id,
                method: "PUT",
                data: {
                    amount: amount
                }
            });
        }
    };
    return variationsAPI;
});

angular.module("tapeshop").factory("stockAPI", function ($http, baseUrl) {
    var stockAPI = {

    };
    return stockAPI;
});