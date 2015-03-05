angular.module("tapeshop", []);

angular.module("tapeshop").value("baseUrl", jQuery("base").attr("href"));

angular.module("tapeshop").controller("shopController", function ($scope, itemsAPI, cartAPI) {

    $scope.selectedSize = "";

    $scope.reloadItems = function () {
        itemsAPI.getAll().success(function (items) {
            $scope.items = items;
        });
    };

    $scope.reloadCart = function () {
        cartAPI.get().success(function (cart) {
            $scope.cart = cart;
        });
    };

    $scope.inStock = function (item) {
        if (!item.manage_stock) {
            return true;
        }

        if (item.sizes.length) {
            var instock = false;

            $.each(item.sizes, function (index, size) {
                if (size.stock > 0) {
                    instock = true;
                }
            });

            return instock;
        } else {
            return item.stock;
        }
    };

    $scope.sizesInStock = function (item) {
        if (!item.manage_stock) {
            return item.sizes;
        }

        var sizes = [];
        $.each(item.sizes, function (index, size) {
            if (size.stock) {
                sizes.push(size);
            }
        });
        return sizes;
    };

    $scope.addToCart = function (item) {
        cartAPI.addToCart(item).then(function (response) {
            $scope.cart = response.data;
        });
    };

    $scope.removeFromCart= function(cartItem){
        cartAPI.removeFromCart(cartItem).then(function(response){
            $scope.cart = response.data;
        });
    };

    $scope.findById = function (array, id) {
        return array.filter(function (object) {
            return object.id == id;
        })[0];
    };

    $scope.getSum = function (cart) {
        var sum = 0;
        $.each(cart, function (index, cartItem) {
            sum += cartItem.item.price * cartItem.amount;
        });
        return sum;
    };

    $scope.reloadItems();
    $scope.reloadCart();
});

angular.module("tapeshop").factory("itemsAPI", function ($http, baseUrl) {
    var itemsAPI = {
        get: function (id) {
            return $http.get(baseUrl + "items/" + id);
        },
        getAll: function () {
            return $http.get(baseUrl + "items");
        }
    };
    return itemsAPI;
});

angular.module("tapeshop").factory("cartAPI", function ($http, baseUrl) {
    var cartAPI = {
        get: function () {
            return $http.get(baseUrl + "cartapi");
        },
        addToCart: function (item) {
            size = item.selectedSize;
            if (size == null) {
                size = {id: null}
            }
            return $http({
                url: baseUrl + "cartapi",
                method: "POST",
                data: {
                    item: item.id,
                    size: size.id
                }
            });
        },
        removeFromCart: function(cartItem){
            return $http({
                url: baseUrl + "cartapi",
                method: "DELETE",
                data: {
                    item: cartItem.item.id,
                    size: cartItem.size
                }
            })
        }
    };
    return cartAPI;
});
