angular.module("tapeshop", []);

angular.module("tapeshop").value("baseUrl", jQuery("base").attr("href"));
angular.module("tapeshop").value("orderId", jQuery("#orderId").data("itemId"));

angular.module("tapeshop").controller("orderController", function ($scope, ordersAPI, orderId) {

    $scope.reloadOrder = function () {
        ordersAPI.get(orderId).success(function (order) {
            $scope.order= order;
        });
    };

    $scope.updateStatus = function(order){
        ordersAPI.updateStatus(order).success($scope.reloadOrder.bind($scope));
    };

    $scope.reloadOrder();
});

angular.module("tapeshop").factory("ordersAPI", function ($http, baseUrl) {
    var ordersAPI = {
        get: function(id){
            return $http.get(baseUrl+"orders/"+id);
        },
        updateStatus: function(order){
            return $http({
                url: baseUrl+"orders/"+id,
                method:"PUT",
                data:{
                    status: order.status
                }
            });
        }
    };
    return ordersAPI;
});
