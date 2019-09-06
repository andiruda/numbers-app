(function(angular) {
  'use strict';

  var numbersApp = angular.module('numbersApp', ['ngMaterial', 'ngMessages']);
    angular.module('numbersApp').config(function($mdDateLocaleProvider) {
        $mdDateLocaleProvider.formatDate = function(date) {
            return moment(date).format('YYYY-MM-DD');
        };
    });

numbersApp.controller('MainController', function($scope, $rootScope, $timeout) {
    $rootScope.$on('fire',function(event, data){
        console.log(data);
        $scope.$broadcast('returnFire','S BROADCAST RETURNED FIRE From the Main Controller');
    });
    var today = new Date();
    $scope.start = '2015-08-07';
    $scope.end = today;
    $scope.limit = '69';
    $scope.isOpen = false;

    $scope.fire = function(){
        //$scope.$emit('fire','Shots fired from Forms Controller!');
        $rootScope.$broadcast('reloadSection');
    };
    $timeout(function(){
        $scope.$broadcast('reloadSection');
    }, 1000);

});

numbersApp.controller('ListDrawsController', function ($scope,$rootScope,$http) {

    $scope.$on('reloadSection',function(event){
        $http.get('php/controller.php?page=listDraws&start='+moment($scope.start).format("YYYY-MM-DD")+'&end='+moment($scope.end).format("YYYY-MM-DD")+'&limit='+$scope.limit)
        .then(function(response) {
            $scope.draws = response.data;
            console.log(response.data);
        });
    });

});

numbersApp.controller('ListTopNumbers', function ($scope, $http){
    $scope.$on('reloadSection',function(event){
        $http.get('php/controller.php?page=topNumbers&start='+moment($scope.start).format("YYYY-MM-DD")+'&end='+moment($scope.end).format("YYYY-MM-DD")+'&limit='+$scope.limit+'&pb=false')
        .then(function(response) {
            $scope.tops = response.data;
            console.log(response.data);
        })
    });
});


numbersApp.controller('ListTopPbs', function ($scope, $http){
    $scope.$on('reloadSection',function(event){
        $http.get('php/controller.php?page=topNumbers&start='+moment($scope.start).format("YYYY-MM-DD")+'&end='+moment($scope.end).format("YYYY-MM-DD")+'&limit='+$scope.limit+'&pb=true')
        .then(function(response) {
            $scope.pbs = response.data;
            console.log(response.data);
        })
    });
});

numbersApp.controller('luckyNumbers', function ($scope, $http){
    $scope.$on('reloadSection',function(event){
        $http.get('php/controller.php?page=luckyNumbers&start='+moment($scope.start).format("YYYY-MM-DD")+'&end='+moment($scope.end).format("YYYY-MM-DD")+'&limit='+$scope.limit+'&pb=true')
        .then(function(response) {
            $scope.luckyNumbers = response.data;
            console.log(response.data);
        })
    });
});

})(window.angular);
