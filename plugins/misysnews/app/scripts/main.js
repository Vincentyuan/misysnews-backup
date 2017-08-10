(function() {
'use strict';

  angular.module('misysnews', ['ngSanitize', 'FBAngular']);

  angular.module('misysnews')
    .controller('MainCtrl', MainController);

  MainController.$inject = ['$scope'];

  function MainController($scope) {
    $scope.title = "Misys News";

    
  };

})();

