angular.module("tapeshop", ['ngSanitize']);

angular.module("tapeshop").value("baseUrl", jQuery("base").attr("href"));

angular.module("tapeshop").controller("nametagController", function ($scope, nametagAPI, orderAPI) {

    $scope.submitTicketcode = function (ticketcode) {
        orderAPI.get(ticketcode).success(function (order) {
            $scope.order_id = order.id;
            $scope.loadNametags(order.id);
            $scope.error = false;
        }).error(function () {
            $scope.error = true;
        })
    };

    $scope.loadNametags = function (order_id) {
        nametagAPI.get(order_id).success(function (nametags) {
            $scope.nametags = nametags;
        })
    };

    $scope.addNametag = function (nametag) {
        if(!nametag){
            return;
        }

        nametagAPI.create($scope.order_id, nametag).success(function () {
            $scope.loadNametags($scope.order_id);
        })
    };

    $scope.removeNametag = function (nametag) {
        nametagAPI.delete(nametag.id).success(function () {
            $scope.loadNametags($scope.order_id);
        })
    }

});

angular.module("tapeshop").factory("nametagAPI", function ($http, baseUrl) {
    return {
        get: function (order_id) {
            return $http.get(baseUrl + "nametags/" + order_id);
        },
        create: function (order_id, nametag) {
            return $http({
                url: baseUrl + "nametags",
                method: "POST",
                data: {
                    order_id: order_id,
                    name: nametag.name,
                    nickname: nametag.nickname,
                    pronoun: nametag.pronoun
                }
            });
        },
        delete: function (id) {
            return $http({
                url: baseUrl + "nametags/" + id,
                method: "DELETE"
            })
        }

    };
});

angular.module("tapeshop").factory("orderAPI", function ($http, baseUrl) {
    return {
        get: function (ticketcode) {
            return $http.get(baseUrl + "orders/ticketcode/" + ticketcode);
        }
    };
});
