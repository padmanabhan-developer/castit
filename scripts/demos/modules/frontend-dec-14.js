var frontend = angular.module('theme.demos.dashboard', [
    'angular-skycons',
    'theme.demos.forms'
  ])
  frontend.controller('FrontendController', ['$scope', '$rootScope', '$http', '$timeout', '$window', '$filter', function($scope,$rootScope, $http, $timeout, $window, $filter) {
    'use strict';
	//$('#headerlogo').hide();
	//alert('ddd');
	
	$rootScope.bodylayout = '';	
	
	$rootScope.interface = 'home';
    $scope.currentPage = 0;
    $scope.pageSize = 12;
    $scope.profiles = [];
    $scope.q = '';
	$scope.loading = 'Indlaeser';
	$http.get('api/v1/getprofiles', {params: {view: 'home'}}).success(function(homedata) {
		if(homedata.success){
			$scope.profiles = homedata.profiles;
		}else{
			$scope.profiles ='';
		}
	 });


    $scope.getDataProfile = function () {
      return $filter('filter')($scope.profiles, $scope.q)
    }
    $scope.numberOfPages=function(){
        return Math.ceil($scope.getDataProfile().length/$scope.pageSize);                
    }
    //$scope.data = $scope.profiles;
    //alert($scope.profiles.length);

	var logged = '';
	var usertype = '';
	logged = $rootScope.globals.currentUser
	usertype = ( logged )?logged.usertype:'';
	if(usertype == 1) {
		$scope.usertype = true;	
	}
	else {
		$scope.usertype = false;	
	}
	$scope.search_text ='';
	$scope.age_from ='';
	$scope.age_to ='';
	$scope.genderval ='';
	$scope.set_gender = set_gender;
	function set_gender(genderval){
		
		$scope.genderval =genderval;
		if(genderval==1){
			angular.element(document.querySelector("#gender1")).addClass("active");
			angular.element(document.querySelector("#gender2")).removeClass("active");
		}else{
			angular.element(document.querySelector("#gender2")).addClass("active");
			angular.element(document.querySelector("#gender1")).removeClass("active");
		}
	}
	
	$scope.filterProfiles = filterProfiles;
	function filterProfiles() {
    $scope.currentPage = 0;
    $scope.pageSize = 12;
    $scope.profiles = [];
    $scope.q = '';
	$scope.loading = 'soger';

		 //$scope.dataLoading = true;  
		var formData = {search_text: $scope.search_text,age_from: $scope.age_from,age_to: $scope.age_to,genderval: $scope.genderval,purchase_name: $scope.purchase_name,submittype: $scope.submittype}
		$http.get('api/v1/getfilterprofiles', {params:formData}).success(function(homedata) {
			//alert(response.success);
			if(homedata.success){
				$scope.profiles = homedata.profiles;
			}else{
				$scope.profiles ='';
				$scope.loading = 'Ingen data fundet';
			}
		});				
	  }
	
	$scope.getSingleProfile = function (profileid){
		$http.get('api/v1/getfilterprofiles', {params:{profileid:profileid}}).success(function(homedata) {
			//alert(response.success);
			if(homedata.success){
				$scope.profiles = homedata.profiles;
			}else{
				$scope.profiles ='';
				$scope.loading = 'Ingen data fundet';
			}
		});	
		
	}
	
  }]);
   frontend.controller('AboutusController', ['$scope', '$rootScope', '$http', '$timeout', '$window', function($scope,$rootScope, $http, $timeout, $window) {
    'use strict';
	
	var logged = '';
	var usertype = '';
	logged = $rootScope.globals.currentUser
	usertype = ( logged )?logged.usertype:'';
	if(usertype == 1) {
		$scope.usertype = true;	
	}
	else {
		$scope.usertype = false;	
	}
	
    $scope.currentPage = 1;
    $scope.itemsPerPage = 7;
	
	$http.get('api/v1/aboutus').success(function(totalrec) {
		$scope.title = totalrec.usertotal
		$scope.subtitle = totalrec.foundpetscount
		$scope.maincontent = totalrec.lostpetscount
     });

    $scope.uaHandleSelected = function() {
      this.customer = _.filter(this.customer, function(item) {
        return (item.rem === false || item.rem === undefined);
      });
    };
	
  }]);
  
  frontend.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});
