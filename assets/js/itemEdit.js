angular.module("tapeshop", []);

angular.module("tapeshop").value("baseUrl", jQuery("base").attr("href"));
angular.module("tapeshop").value("itemId", jQuery("#itemId").data("itemId"));

angular.module("tapeshop").controller("stockController", function ($scope, itemsAPI, variationsAPI, itemId) {

    $scope.reloadItem = function () {
        itemsAPI.get(itemId).success(function (item) {
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

angular.module("tapeshop").controller("numbersController", function($scope, itemsAPI, numbersAPI, itemId){

    $scope.reloadItem = function () {
        itemsAPI.get(itemId).success(function (item) {
            $scope.item = item;
            $scope.item.numbered = !!$scope.item.numbered;
            $scope.item.itemnumbers.sort(function(a,b){return a.number - b.number;});
            $scope.freeNumberBlocks = $scope.getFreeNumberBlocks(item);
            $scope.freeNumbers = $scope.getFreeNumbers(item);
            $scope.invalidNumberBlocks = $scope.getInvalidBlocks(item);
            $scope.invalidNumbers = $scope.getInvalidNumbers(item);
        });
    };

    $scope.getFreeNumbers= function(item){
        var i = 0;
        angular.forEach(item.itemnumbers, function(value, key){
            if(value.valid) {
                i++;
            }
        });
        return i;
    };

    $scope.getInvalidNumbers= function(item){
        var i = 0;
        angular.forEach(item.itemnumbers, function(value, key){
            if(!value.valid) {
                i++;
            }
        });
        return i;
    };

    $scope.getFreeNumberBlocks = function(item){
        var blocks = [];
        var block = null;
        angular.forEach(item.itemnumbers, function(value, key){
            if(value.valid) {
                if (block == null) {
                    block = {
                        numbers: [key],
                        low: value.number,
                        high: value.number
                    };
                } else {
                    if (value.number == block.high + 1) {
                        block.numbers.push(key);
                        block.high = value.number;
                    } else {
                        blocks.push(block);
                        block = {
                            numbers: [key],
                            low: value.number,
                            high: value.number
                        };
                    }
                }
            }
        });
        blocks.push(block);
        return blocks;
    };

    $scope.getInvalidBlocks= function(item){
        var blocks = [];
        var block = null;
        angular.forEach(item.itemnumbers, function(value, key){
            if(!value.valid) {
                if (block == null) {
                    block = {
                        numbers: [key],
                        low: value.number,
                        high: value.number
                    };
                } else {
                    if (value.number == block.high + 1) {
                        block.numbers.push(key);
                        block.high = value.number;
                    } else {
                        blocks.push(block);
                        block = {
                            numbers: [key],
                            low: value.number,
                            high: value.number
                        };
                    }
                }
            }
        });
        blocks.push(block);
        return blocks;
    };


    $scope.updateManageNumbers = function(item){
      numbersAPI.updateManageNumbers(item).success($scope.reloadItem.bind($scope));
    };

    $scope.reloadItem();
});

angular.module("tapeshop").factory("numbersAPI", function($http, baseUrl){
    var numbersAPI = {
        updateManageNumbers: function (item) {
        return $http({
            url: baseUrl + "items/" + item.id + "/numbered",
            method: "PUT",
            data: {
                numbered: item.numbered
            }
        });
    }
    };
    return numbersAPI;
});