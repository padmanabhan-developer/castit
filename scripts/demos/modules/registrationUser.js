angular
  .module('theme.demos.registration_user', [])
  .controller('RegistrationUserController', ['$scope', '$timeout', function($scope, $timeout) {
    'use strict';
    $scope.checking = false;
    $scope.checked = false;
    $scope.checkAvailability = function() {
      if ($scope.reg_user_form.email.$dirty === false) {
        return;
      }
      $scope.checking = true;
      $timeout(function() {
        $scope.checking = false;
        $scope.checked = true;
      }, 500);
    };
  }]);