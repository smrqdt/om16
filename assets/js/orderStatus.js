angular.module("tapeshop", []);

angular.module("tapeshop").value("baseUrl", jQuery("base").attr("href"));
angular.module("tapeshop").value("orderId", jQuery("#orderId").data("itemId"));

angular.module("tapeshop").controller("orderController", function ($scope, ordersAPI, orderId) {

    $scope.reloadOrder = function () {
        ordersAPI.get(orderId).success(function (order) {
            $scope.order= order;
        });
    };

    $scope.markAsPayed = function(order){
        ordersAPI.markAsPayed(order).success($scope.reloadOrder.bind($scope));
    };

    $scope.markAsNotPayed = function(order){
        ordersAPI.markAsNotPayed(order).success($scope.reloadOrder.bind($scope));
    };

    $scope.markAsShipped = function(order){
        ordersAPI.markAsShipped(order).success($scope.reloadOrder.bind($scope));
    };

    $scope.markAsNotShipped = function(order){
        ordersAPI.markAsNotShipped(order).success($scope.reloadOrder.bind($scope));
    };

    $scope.reloadOrder();
});

angular.module("tapeshop").factory("ordersAPI", function ($http, baseUrl) {
    var ordersAPI = {
        get: function(id){
            return $http.get(baseUrl+"orders/"+id);
        },
        markAsPayed: function(order){
            return $http.put(baseUrl+"orders/"+order.id+"/payed");
        },
        markAsNotPayed: function(order){
            return $http.put(baseUrl+"orders/"+order.id+"/notpayed");
        },
        markAsShipped: function(order){
            return $http.put(baseUrl+"orders/"+order.id+"/shipped");
        },
        markAsNotShipped: function(order){
            return $http.put(baseUrl+"orders/"+order.id+"/notshipped");
        }
    };
    return ordersAPI;
});
