(function () {
    'use strict';
	var userlogin = angular.module('theme.demos.signup_page', ['theme.core.services'])
	
    userlogin.controller('UserloginController', UserloginController);
 
    UserloginController.$inject = ['$location', '$scope', '$theme', '$window', '$rootScope', 'AuthenticationServiceUser', 'FlashService'];
    function UserloginController($location, $scope, $theme, $window, $rootScope, AuthenticationServiceUser, FlashService) {

		$rootScope.bodylayout = 'black';
		$rootScope.interface = 'login';	

		$theme.set('fullscreen', true);

		$scope.$on('$destroy', function() {
		  $theme.set('fullscreen', false);
		});
		var logged = '';
		var userid = '';
		logged = $rootScope.globals.currentUser
		userid = ( logged )?logged.userid:'';
		if(userid){
			window.location = '#/my-profile';
		}
	
        var vm = $scope;
 
        vm.userlogin = userlogin;
 
        (function initController() {
            // reset login status
            //AuthenticationServiceUser.ClearCredentials();
        })();
 
        function userlogin() { 
            vm.dataLoading = true;
            AuthenticationServiceUser.Loginuser(vm.email, vm.password, function (response) {
                if (response.success) {
                    AuthenticationServiceUser.SetCredentials(vm.email, vm.password, response.userstatus, response.userid);
					if(response.success) { 
						$location.path('/my-profile');
					}
					else {
						FlashService.Error(response.message);
						vm.dataLoading = false;
					}
                } else {
                    FlashService.Error(response.message);
                    vm.dataLoading = false;
                }
            });
        };
    }
 
})(); 