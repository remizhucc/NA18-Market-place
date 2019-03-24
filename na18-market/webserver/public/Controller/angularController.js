var app = angular.module('myApp', ['ngCookies', 'ngRoute']);

app.config(['$routeProvider', function ($routeProvider) {
    $routeProvider
        .when('/list/:categorie', {templateUrl: 'list.html', controller: 'list'})
        .when('/login', {templateUrl: 'login.html', controller: 'login'})
        .when('/panier', {templateUrl: 'panier.html', controller: 'panier'})
        .when('/signup', {templateUrl: 'signup.html', controller: 'register'})
        .when('/commande/:id', {templateUrl: 'commande.html', controller: 'commande'})
        .when('/commandeList', {templateUrl: 'commandeList.html', controller: 'commandeList'})
        .otherwise({redirectTo: '/list/Promotion'});
}]).controller('register', ['$scope', '$http', function ($scope, $http) {
        $scope.submitted = false;
        $scope.signupForm = function () {
            if ($scope.registerForm.$valid) {
                $http({
                    method: 'POST',
                    url: './Php/signUp.php',
                    data: {
                        'email': $scope.user.email,
                        'motdepass': $scope.user.motDePass,
                        'nom': $scope.user.nom,
                        'prenom': $scope.user.prenom
                    }
                }).then(function successCallback(response) {
                    if (!response.data.error) {
                        $scope.message = "Compte a crée";
                    } else {
                        console.log(response.data);
                        $scope.message = "Compte exists";
                    }
                }, function errorCallback(response) {
                    console.log(response.data);
                    $scope.message = "Connection erruer s'est passé";
                });
            } else {
                $scope.registerForm.submitted = true;
            }
        }
    }]
).controller('login', ['$scope', '$http', '$cookies', '$rootScope', function ($scope, $http, $cookies, $rootScope) {
        $scope.submitted = false;
        $rootScope.connected = false;
        $scope.message = "Veuillez vous connecter";
        if ($cookies.get('id')) {
            $rootScope.identification = {};
            $rootScope.identification.id = $cookies.get('id');
            $rootScope.identification.nom = $cookies.get('nom');
            $rootScope.identification.prenom = $cookies.get('prenom');
            $rootScope.identification.email = $cookies.get('email');
            $rootScope.connected = true;
        }

        $scope.submitLoginForm = function () {
            if ($scope.loginForm.$valid) {
                $http({
                    method: 'POST',
                    url: './Php/login.php',
                    data: {
                        'email': $scope.user.email,
                        'motdepass': $scope.user.motDePass
                    }
                }).then(function successCallback(response) {
                    if (!response.data.error) {
                        var date = new Date();
                        //add 20 minutes to date
                        date.setTime(date.getTime() + (3600 * 12 * 21));
                        $scope.message = "C'est bon";
                        $cookies.put('path', '/');
                        $cookies.put('id', response.data.id);
                        $cookies.put('nom', response.data.nom);
                        $cookies.put('prenom', response.data.prenom);
                        $cookies.put('email', response.data.email);
                        $rootScope.connected = true;
                        $rootScope.identification = {};
                        $rootScope.identification.id = response.data.id;
                        $rootScope.identification.nom = response.data.nom;
                        $rootScope.identification.prenom = response.data.prenom;
                        $rootScope.identification.email = response.data.email;
                    } else {
                        console.log(response.data);
                        $scope.message = "Verification error";
                    }
                }, function errorCallback(response) {
                    console.log(response.data);
                    $scope.message = "Connection erruer s'est passé";
                });
            } else {
                $scope.loginForm.submitted = true;
            }
        }
        $scope.logOut = function () {
            $rootScope.connected = false;
            $cookies.remove('id');
            $cookies.remove('nom');
            $cookies.remove('prenom');
            $cookies.remove('email');
        }

    }]
).controller('list', function ($location, $scope, $http, $routeParams, $rootScope) {
    $http({
        method: 'POST',
        url: './Php/list.php',
        data: {
            'categorie': $routeParams.categorie
        }
    }).then(function successCallback(response) {
        if (!response.data.error) {
            console.log("success");
            console.log(response.data);
            $scope.products = response.data;
        } else {
            $scope.products = {};
            console.log("error");
            console.log(response.data);
        }
    }, function errorCallback(response) {
        $scope.products = {};
        console.log("error");
        console.log("error" + response.data);
    });

    $scope.ajoutePanier = function (id) {
        if ($rootScope.connected != true) {
            $location.path('/login');
        } else {
            $http({
                method: 'POST',
                url: './Php/ajoutePanier.php',
                data: {
                    'acheteur': $rootScope.identification.id,
                    'produit': id
                }
            }).then(function successCallback(response) {
                if (!response.data.error) {
                    console.log("success");
                    $('#SuccessModal').modal('show')
                } else {
                    console.log("Ajoute error");
                    console.log(response.data);
                    $('#ErrorModal').modal('show')
                }
            }, function errorCallback(response) {
                console.log("Connection error");
                console.log("error" + response.data);
            });
        }
    }
}).controller('panier', function ($location, $scope, $http, $rootScope) {
        $scope.noinformaiton = false;
        $scope.modelivraison = "none";
        $scope.livraison = "none";
        $http({
            method: 'POST',
            url: './Php/getPanier.php',
            data: {
                'acheteur': $rootScope.identification.id
            }
        }).then(function successCallback(response) {
            if (!response.data.error) {
                console.log("success");
                console.log(response.data);
                if (response.data != "null") {
                    $scope.products = response.data;
                } else {
                    $scope.products = [];
                }
                $scope.calcule();
            } else {
                $scope.products = {};
                console.log("error");
                console.log(response.data);
            }
        }, function errorCallback(response) {
            $scope.products = {};
            console.log("error");
            console.log("error" + response.data);
        });

        $http({
            method: 'POST',
            url: './Php/getAdresse.php',
            data: {
                'acheteur': $rootScope.identification.id
            }
        }).then(function successCallback(response) {
            if (!response.data.error) {
                console.log("success");
                console.log(response.data);
                if (response.data != null) {
                    $scope.adresses = response.data;
                } else {
                    $scope.adresses = [];
                }
            } else {
                $scope.adresses = [];
                console.log("error");
                console.log(response.data);
            }
        }, function errorCallback(response) {
            $scope.products = {};
            console.log("error");
            console.log("error" + response.data);
        });
        $scope.calcule = function () {
            $scope.price = 0;
            for (var x = 0; x < $scope.products.length; x++) {
                if ($scope.products[x].prixpromotion) {
                    $scope.price += parseFloat($scope.products[x].quantite) * parseFloat($scope.products[x].prixpromotion);
                } else {
                    $scope.price += parseFloat($scope.products[x].quantite) * parseFloat($scope.products[x].prix);
                }
            }
        }
        $scope.deleteProduit = function (x) {
            x.quantite = 0;
            console.log($rootScope.identification.id);
            console.log(x.id);
            $scope.calcule($rootScope.identification.id);
            $scope.calcule(x.id);

            $("[name='produit" + x.id + "']").remove();
            $http({
                method: 'POST',
                url: './Php/deletePanier.php',
                data: {
                    'acheteur': $rootScope.identification.id,
                    'produit': x.id
                }
            }).then(function successCallback(response) {
                if (!response.data.error) {
                    console.log("success");
                } else {
                    console.log("error");
                    console.log(response.data);
                }
            }, function errorCallback(response) {
                console.log("error");
                console.log("error" + response.data);
            });
        }


        $scope.genererCommande = function () {
            if ($scope.modelivraison == "none" || $scope.livraison == "none") {
                $scope.noinformaiton = true;
            } else {
                $http({
                    method: 'POST',
                    url: './Php/genererCommande.php',
                    data: {
                        'acheteur': $rootScope.identification.id,
                        'modelivraison': $scope.modelivraison,
                        'livraison': $scope.livraison,
                        'products': $scope.products
                    }
                }).then(function successCallback(response) {
                    if (!response.data.error) {
                        console.log("success");
                        console.log(response.data);
                        $location.path('/commande/' + response.data.toString());
                    } else {
                        console.log("error");
                        console.log(response.data);
                    }
                }, function errorCallback(response) {
                    console.log("error");
                    console.log("error" + response.data);
                });
            }
        }


        $scope.ajouteAdresse = function () {
            $http({
                method: 'POST',
                url: './Php/ajouteAdresse.php',
                data: {
                    'acheteur': $rootScope.identification.id,
                    'nom': $scope.newAdresse.nom,
                    'prenom': $scope.newAdresse.prenom,
                    'postal': $scope.newAdresse.postal,
                    'adresse': $scope.newAdresse.adresse
                }
            }).then(function successCallback(response) {
                if (!response.data.error) {
                    $scope.adresses[$scope.adresses.length] = {
                        'id': response.data,
                        'nom': $scope.newAdresse.nom,
                        'prenom': $scope.newAdresse.prenom,
                        'postal': $scope.newAdresse.postal,
                        'adresse': $scope.newAdresse.adresse
                    };
                    $('#ajouteAdresse').modal('toggle');
                    $scope.newAdresse = {};
                } else {
                    console.log("error");
                    console.log(response.data);
                }
            }, function errorCallback(response) {
                console.log("error");
                console.log("error" + response.data);
            });
        }
    }
).controller('commande', function ($location, $scope, $http, $routeParams, $rootScope) {
    $http({
        method: 'POST',
        url: './Php/getCommande.php',
        data: {
            'idcommande': $routeParams.id
        }
    }).then(function successCallback(response) {
        if (!response.data.error) {
            console.log("success");
            console.log(response.data);
            $scope.commande = response.data;
        } else {
            $scope.products = {};
            console.log("error");
            console.log(response.data);
        }
    }, function errorCallback(response) {
        $scope.products = {};
        console.log("error");
        console.log("error" + response.data);
    });

}).controller('commandeList', function ($location, $scope, $http, $routeParams, $rootScope) {
    $http({
        method: 'POST',
        url: './Php/getCommandeList.php',
        data: {
            'acheteur': $rootScope.identification.id
        }
    }).then(function successCallback(response) {
        if (!response.data.error) {
            console.log("success");
            console.log(response.data);
            $scope.commandeList = response.data;
        } else {
            $scope.products = {};
            console.log("error");
            console.log(response.data);
        }
    }, function errorCallback(response) {
        $scope.products = {};
        console.log("error");
        console.log("error" + response.data);
    });

});


app.controller('layout', ['$scope', '$http', '$cookies', '$rootScope', function ($scope, $http, $cookies, $rootScope) {
        $rootScope.connected = false;
        $rootScope.identification = {};
        if ($cookies.get('id')) {
            $rootScope.identification.id = $cookies.get('id');
            $rootScope.identification.nom = $cookies.get('nom');
            $rootScope.identification.prenom = $cookies.get('prenom');
            $rootScope.identification.email = $cookies.get('email');
        }
        if ($rootScope.identification.id) {
            $rootScope.connected = true;
        }
        $(".navbar-nav .nav-item").on("click", function () {
            $(".navbar-nav .nav-item.active").removeClass("active");
            $(this).addClass("active");
        });
    }]
);





