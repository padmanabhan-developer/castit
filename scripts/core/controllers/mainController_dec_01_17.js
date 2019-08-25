main = angular.module('theme.core.main_controller', ['theme.core.services'])
main.controller('MainController', ['$scope', '$theme', '$timeout', 'progressLoader', '$location', 'AuthenticationServiceUser',
    function($scope, $theme, $timeout, progressLoader, $location, AuthenticationServiceUser) {
    'use strict';
    // $scope.layoutIsSmallScreen = false;
    $scope.layoutFixedHeader = $theme.get('fixedHeader');
    $scope.layoutPageTransitionStyle = $theme.get('pageTransitionStyle');
    $scope.layoutDropdownTransitionStyle = $theme.get('dropdownTransitionStyle');
    $scope.layoutPageTransitionStyleList = ['bounce',
      'flash',
      'pulse',
      'bounceIn',
      'bounceInDown',
      'bounceInLeft',
      'bounceInRight',
      'bounceInUp',
      'fadeIn',
      'fadeInDown',
      'fadeInDownBig',
      'fadeInLeft',
      'fadeInLeftBig',
      'fadeInRight',
      'fadeInRightBig',
      'fadeInUp',
      'fadeInUpBig',
      'flipInX',
      'flipInY',
      'lightSpeedIn',
      'rotateIn',
      'rotateInDownLeft',
      'rotateInDownRight',
      'rotateInUpLeft',
      'rotateInUpRight',
      'rollIn',
      'zoomIn',
      'zoomInDown',
      'zoomInLeft',
      'zoomInRight',
      'zoomInUp'
    ];
    $scope.layoutLoading = true;
    $scope.getLayoutOption = function(key) {
      return $theme.get(key);
    };
    $scope.setNavbarClass = function(classname, $event) {
      $event.preventDefault();
      $event.stopPropagation();
      $theme.set('topNavThemeClass', classname);
    };
    $scope.setSidebarClass = function(classname, $event) {
      $event.preventDefault();
      $event.stopPropagation();
      $theme.set('sidebarThemeClass', classname);
    };
    $scope.$watch('layoutFixedHeader', function(newVal, oldval) {
      if (newVal === undefined || newVal === oldval) {
        return;
      }
      $theme.set('fixedHeader', newVal);
    });
    $scope.$watch('layoutLayoutBoxed', function(newVal, oldval) {
      if (newVal === undefined || newVal === oldval) {
        return;
      }
      $theme.set('layoutBoxed', newVal);
    });
    $scope.$watch('layoutLayoutHorizontal', function(newVal, oldval) {
      if (newVal === undefined || newVal === oldval) {
        return;
      }
      $theme.set('layoutHorizontal', newVal);
    });
    $scope.$watch('layoutPageTransitionStyle', function(newVal) {
      $theme.set('pageTransitionStyle', newVal);
    });
    $scope.$watch('layoutDropdownTransitionStyle', function(newVal) {
      $theme.set('dropdownTransitionStyle', newVal);
    });
    $scope.hideHeaderBar = function() {
      $theme.set('headerBarHidden', true);
    };
    $scope.showHeaderBar = function($event) {
      $event.stopPropagation();
      $theme.set('headerBarHidden', false);
    };
    $scope.toggleLeftBar = function() {
      if ($scope.layoutIsSmallScreen) {
        return $theme.set('leftbarShown', !$theme.get('leftbarShown'));
      }
      $theme.set('leftbarCollapsed', !$theme.get('leftbarCollapsed'));
    };
    $scope.toggleRightBar = function() {
      $theme.set('rightbarCollapsed', !$theme.get('rightbarCollapsed'));
    };
    $scope.toggleSearchBar = function($event) {
      $event.stopPropagation();
      $event.preventDefault();
      $theme.set('showSmallSearchBar', !$theme.get('showSmallSearchBar'));
    };
    $scope.chatters = [{
      id: 0,
      status: 'online',
      name: 'Jeremy Potter'
    }, {
      id: 1,
      status: 'online',
      name: 'David Tennant'
    }, {
      id: 2,
      status: 'online',
      name: 'Anna Johansson'
    }, {
      id: 3,
      status: 'busy',
      name: 'Eric Jackson'
    }, {
      id: 4,
      status: 'online',
      name: 'Howard Jobs'
    }, {
      id: 5,
      status: 'online',
      name: 'Jeremy Potter'
    }, {
      id: 6,
      status: 'away',
      name: 'David Tennant'
    }, {
      id: 7,
      status: 'away',
      name: 'Anna Johansson'
    }, {
      id: 8,
      status: 'online',
      name: 'Eric Jackson'
    }, {
      id: 9,
      status: 'online',
      name: 'Howard Jobs'
    }];
    $scope.currentChatterId = null;
    $scope.hideChatBox = function() {
      $theme.set('showChatBox', false);
    };
    $scope.toggleChatBox = function(chatter, $event) {
      $event.preventDefault();
      if ($scope.currentChatterId === chatter.id) {
        $theme.set('showChatBox', !$theme.get('showChatBox'));
      } else {
        $theme.set('showChatBox', true);
      }
      $scope.currentChatterId = chatter.id;
    };
    $scope.hideChatBox = function() {
      $theme.set('showChatBox', false);
    };
    $scope.$on('themeEvent:maxWidth767', function(event, newVal) {
      $timeout(function() {
        $scope.layoutIsSmallScreen = newVal;
        if (!newVal) {
          $theme.set('leftbarShown', false);
        } else {
          $theme.set('leftbarCollapsed', false);
        }
      });
    });
    $scope.$on('themeEvent:changed:fixedHeader', function(event, newVal) {
      $scope.layoutFixedHeader = newVal;
    });
    $scope.$on('themeEvent:changed:layoutHorizontal', function(event, newVal) {
      $scope.layoutLayoutHorizontal = newVal;
    });
    $scope.$on('themeEvent:changed:layoutBoxed', function(event, newVal) {
      $scope.layoutLayoutBoxed = newVal;
    });
    $scope.isLoggedIn = false;
    $scope.logOut = function() {
       AuthenticationServiceUser.ClearCredentials();
	    $scope.isLoggedIn = false;
	   $location.path('/login');
    };
    $scope.logIn = function() {
      $scope.isLoggedIn = true;
    };
    $scope.$on('logInUser', function(event, newVal) {
       $scope.isLoggedIn = true;
    });
	if(AuthenticationServiceUser.GetCredentials()){
		$scope.isLoggedIn = true;
	}
	//$scope.baseUrl = $browser.baseHref();
    $scope.rightbarAccordionsShowOne = false;
    $scope.rightbarAccordions = [{
      open: true
    }, {
      open: true
    }, {
      open: true
    }, {
      open: true
    }, {
      open: true
    }, {
      open: true
    }, {
      open: true
    }];
    $scope.$on('$routeChangeStart', function() {
      if ($location.path() === '') {
        return $location.path('/');
      }
     // progressLoader.start();
    //  progressLoader.set(50);
    });
    $scope.$on('$routeChangeSuccess', function() {
     // progressLoader.end();
      if ($scope.layoutLoading) {
        $scope.layoutLoading = false;
      }
    });
  }]);






// Regsiter Step 1
main.controller('RegisterStep1Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 
 	$http.get('api/v1/step1', {params: {limit: limit}}).success(function(lostepets) {
     });
	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown
	});
	$scope.countriesListDropdown = function() {
		return this.countriesdropdown;
	}; 
	
	$scope.step1Create = step1Create;
	function step1Create() {  
		var formData = {first_name: $scope.first_name,
						last_name: $scope.last_name,
						password: $scope.password,
						zipcode: $scope.zipcode,
						city: $scope.city,
						country_id: $scope.country_id
						}
		$http.post('api/v1/step1Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/ansog-step2';
			}
		});				
	  }

	  
}]);

// Regsiter Step 2
main.controller('RegisterStep2Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 
 	$http.get('api/v1/step2', {params: {limit: limit}}).success(function(lostepets) {
     });
	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown
	});
	$scope.countriesListDropdown = function() {
		return this.countriesdropdown;
	}; 
	
	$scope.step2Create = step2Create;
	function step2Create() {  
		var formData = {first_name: $scope.first_name,
						last_name: $scope.last_name,
						password: $scope.password,
						zipcode: $scope.zipcode,
						city: $scope.city,
						country_id: $scope.country_id
						}
		$http.post('api/v1/step2Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/ansog-step3';
			}
		});				
	  }

	  
}]);

// Regsiter Step 3
main.controller('RegisterStep3Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 
 	$http.get('api/v1/step3', {params: {limit: limit}}).success(function(lostepets) {
     });
	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown
	});
	$scope.countriesListDropdown = function() {
		return this.countriesdropdown;
	}; 
	
	$scope.step3Create = step3Create;
	function step3Create() {  
		var formData = {first_name: $scope.first_name,
						last_name: $scope.last_name,
						password: $scope.password,
						zipcode: $scope.zipcode,
						city: $scope.city,
						country_id: $scope.country_id
						}
		$http.post('api/v1/step3Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/ansog-step4';
			}
		});				
	  }

	  
}]);

// Regsiter Step 4
main.controller('RegisterStep4Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 
 	$http.get('api/v1/step4', {params: {limit: limit}}).success(function(lostepets) {
     });
	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown
	});
	$scope.countriesListDropdown = function() {
		return this.countriesdropdown;
	}; 
	
	$scope.step4Create = step4Create;
	function step4Create() {  
		var formData = {first_name: $scope.first_name,
						last_name: $scope.last_name,
						password: $scope.password,
						zipcode: $scope.zipcode,
						city: $scope.city,
						country_id: $scope.country_id
						}
		$http.post('api/v1/step4Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/ansog-step5';
			}
		});				
	  }

	  
}]);

// Regsiter Step 5
main.controller('RegisterStep5Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 
 	$http.get('api/v1/step5', {params: {limit: limit}}).success(function(lostepets) {
     });
	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown
	});
	$scope.countriesListDropdown = function() {
		return this.countriesdropdown;
	}; 
	
	$scope.step5Create = step5Create;
	function step5Create() {  
		var formData = {first_name: $scope.first_name,
						last_name: $scope.last_name,
						password: $scope.password,
						zipcode: $scope.zipcode,
						city: $scope.city,
						country_id: $scope.country_id
						}
		$http.post('api/v1/step5Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/ansog-step6';
			}
		});				
	  }

	  
}]);

// Regsiter Step 6
main.controller('RegisterStep6Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 
 	$http.get('api/v1/step6', {params: {limit: limit}}).success(function(lostepets) {
     });
	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown
	});
	$scope.countriesListDropdown = function() {
		return this.countriesdropdown;
	}; 
	
	$scope.step6Create = step6Create;
	function step6Create() {  
		var formData = {first_name: $scope.first_name,
						last_name: $scope.last_name,
						password: $scope.password,
						zipcode: $scope.zipcode,
						city: $scope.city,
						country_id: $scope.country_id
						}
		$http.post('api/v1/step6Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/ansog-step7';
			}
		});				
	  }

	  
}]);

// Regsiter Step 7
main.controller('RegisterStep7Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 
 	$http.get('api/v1/step7', {params: {limit: limit}}).success(function(lostepets) {
     });
	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown
	});
	$scope.countriesListDropdown = function() {
		return this.countriesdropdown;
	}; 
	
	$scope.step7Create = step7Create;
	function step7Create() {  
		var formData = {first_name: $scope.first_name,
						last_name: $scope.last_name,
						password: $scope.password,
						zipcode: $scope.zipcode,
						city: $scope.city,
						country_id: $scope.country_id
						}
		$http.post('api/v1/step7Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/success';
			}
		});				
	  }

	  
}]);

















//Check Microchip data
main.controller('MicrochipController', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut();
	
		var logged = '';
		var userid = '';
		logged = $rootScope.globals.currentUser
		userid = ( logged )?logged.userid:'';
		if(userid){
			window.location = '#/my-profile';
		}
	
	 var playerid = 'check';
	 $http.get('api/v1/clearstep1', {params: {id: playerid}}).success(function(checkstep) {});
	 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	 $scope.purchase_type_name ='';
	 $scope.microchip_purchase ='';
	 $scope.setpurchase = setpurchase;
	 function setpurchase(type_name) {
		 switch ($scope.microchip_purchase) {
			case '1':
                $scope.purchase_type_name = 'Doctor';
				$scope.purchase_name = 'Dr. ';
                break;
			case '2':
                $scope.purchase_type_name = 'Club Name';
				$scope.purchase_name = '';
                break;
			case '3':
                $scope.purchase_type_name = 'Website Address';
				$scope.purchase_name = 'http://';
                break;
			case '4':
                $scope.purchase_type_name = 'Store Name';
				$scope.purchase_name = '';
                break;
			default:
			$scope.purchase_type_name = '';
			$scope.purchase_name = '';
				 break;
		 }
	 } 
	var handleFileSelect=function(evt) { 
      var file=evt.currentTarget.files[0];
	  
      var reader = new FileReader();
      reader.onload = function (evt) {
        $scope.$apply(function($scope){
          $scope.image1=evt.target.result;
		 // alert(evt.target.result);
        });
      };
      reader.readAsDataURL(file);
    };
    angular.element(document.querySelector('#image')).on('change',handleFileSelect);

	
	 $scope.chipSubmit = chipSubmit;
	 
	 function chipSubmit() {
		 //$scope.dataLoading = true;  
		var formData = {chipnumber: $scope.microchip,chiptype: $scope.microchip_purchase,image: $scope.image1,chip_brand: $scope.chip_brand,purchase_name: $scope.purchase_name,submittype: $scope.submittype}
		$http.post('api/v1/chipcheck', formData).success(function(response) {
			//alert(response.success);
			if(response.success){
				
				window.location = '#/register-step2';
			} 
			else{
				FlashService.Error(response.message);
				//$scope.dataLoading = false;
			}
		});				
	  }
				
}]);
//Update Profile details   
main.controller('MyProfileController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {  
$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 var playerid = 'myprofile';	
	 $http.get('api/v1/myprofiledetails', {params: {id: playerid}}).success(function(cusdata) {
		//alert(cusdata.customer['owner_name']);
		 
		$.each(cusdata.customer, function(key, data) {
			$scope[key] = data;
		});
		//var pets = [];
		//$.each(cusdata.custpets, function(key, data) {
			//alert(data['pet_id']);
		//pets.push(data)
		//});
     });
	 
	 $http.get('api/v1/mypetdetails', {params: {id: playerid}}).success(function(petdata) {
	 $scope.pets=petdata;
     });
	 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	 $scope.edit_pet = edit_pet;
	 function edit_pet(petid) {  
		window.location = '#/edit-pet/'+petid;
	 } 
	  
	 $scope.profileUpdate = profileUpdate;
	 
	 function profileUpdate() {  
		var formData = {playerid: $scope.playerid,shirt_size: $scope.shirt_size,
						 fname: $scope.fname,lname: $scope.lname,gender: $scope.gender,
						 email: $scope.email,address_1: $scope.address_1,address_2: $scope.address_2,city: $scope.city,post: $scope.post,region: $scope.region,country: $scope.country,phone_1: $scope.phone_1,phone_2: $scope.phone_2,status: $scope.status,submittype: $scope.submittype, user_id:$scope.user_id
						}
		$http.post('api/v1/customerupdate', formData).success(function(customer) {
			if(customer.sucess){
				if(customer.type=='save') {
					$window.scrollTo(0, 0);
					FlashService.Success(customer.message);
					
					//window.location = '#/customer-management';
				}
				else {
					window.location = '#/tshirt-management';
				}
			}  
		});				
	  }
				
	$scope.ViewCustomer = function() {  
		window.location = '#/view-customer/'+$scope.playerid;
	}
	$scope.AddNewCustomer = function() {  
		window.location = '#/add-new-user/';
	}
}]);
 
  
//Create New Customer
main.controller('CreateUserController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {  
	$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 var playerid = 'check';
	 $http.get('api/v1/checkstep1', {params: {id: playerid}}).success(function(checkstep) {
		if(checkstep.success){
			$scope.microchip = checkstep.microchip;
			if(checkstep.customer){
				$.each(checkstep.customer, function(key, data) {
					$scope[key] = data;
				});
	 			//$scope.password='';
			}
		}
		else{
			window.location = '#/register-step1';
		}
     });
	 $scope.datePicker = (function () {
		var method = {};
		method.instances = [];
	
		method.open = function ($event, instance) {
			$event.preventDefault();
			$event.stopPropagation();
	
			method.instances[instance] = true;
		};
	
		method.options = {
			'show-weeks': false,
			startingDay: 0
		};
	
		var formats = ['MM/dd/yyyy', 'dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
		method.format = formats[0];
	
		return method;
	}());
	 
	 $http.get('api/v1/clearstep2', {params: {id: playerid}}).success(function(checkstep) {});
	 $scope.go_to_step1 = go_to_step1;
	 function go_to_step1(type) {  
		//window.location = '#/register-step1';
	 } 
	 
	 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.userCreate = userCreate;
	 
	 function userCreate() {  
		var formData = {fname: $scope.fname,lname: $scope.lname,email: $scope.email,password: $scope.password,gender: $scope.gender,address_1: $scope.address_1,address_2: $scope.address_2,city: $scope.city,post: $scope.post,country: $scope.country,region: $scope.region,phone_1: $scope.phone_1,phone_2: $scope.phone_2,submittype: $scope.submittype
						}
		$http.post('api/v1/customercreatestep', formData).success(function(customer) {
			if(customer.sucess){
					/* OTPP POPUP  */
					$.fancybox({maxWidth:400, maxHeight:400, width:300, height:237, fitToView: false, 
					autoSize: false, href:"#undelete"});
					//window.location = '#/register-step3';
			}
				else {
					window.location = '#/register-step2';
				}
		});				
	  }
				
}]);
//Update Customer details   
main.controller('EditUserController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {  
	$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 var playerid = $routeParams.id;	
	 
	 $http.get('api/v1/getprofiledetails', {params: {id: playerid}}).success(function(cusdata) {
		 
		 $.each(cusdata.customer, function(key, data) {
			$scope[key] = data;
		});
		
     });
	 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.go_to_profile = go_to_profile;
	 function go_to_profile(type) {  
		window.location = '#/my-profile';
	 } 
	 $scope.customerUpdate = customerUpdate;
	 
	 function customerUpdate() {  
		var formData = {playerid: $scope.playerid,shirt_size: $scope.shirt_size,
						 fname: $scope.fname,lname: $scope.lname,gender: $scope.gender,
						 email: $scope.email,address_1: $scope.address_1,address_2: $scope.address_2,city: $scope.city,post: $scope.post,region: $scope.region,country: $scope.country,phone_1: $scope.phone_1,phone_2: $scope.phone_2, password: $scope.npassword, status: $scope.status,submittype: 'save', user_id:$scope.user_id
						}
		$http.post('api/v1/updateprofile', formData).success(function(customer) {
			if(customer.sucess){
				if(customer.type=='save') {
					$window.scrollTo(0, 0);
					$scope.npassword ='';
					$scope.cnpassword ='';
					FlashService.Success(customer.message);
					//window.location = '#/customer-management';
				}
				else {
					FlashService.Error(customer.message);
					window.location = '#/edit-profile';
				}
			}  
		});				
	  }
				
	$scope.ViewCustomer = function() {  
		window.location = '#/view-customer/'+$scope.playerid;
	}
	$scope.AddNewCustomer = function() {  
		window.location = '#/add-new-user/';
	}
}]);
//Update Customer details   
main.controller('ViewUserController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {  
	$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 var playerid = $routeParams.id;	
	 
	 $http.get('api/v1/customerbyid', {params: {id: playerid}}).success(function(cusdata) {
		 
		 $.each(cusdata.customer, function(key, data) {
			 if(key == 'payment_status') {
				 data = (data==0)?'Not Paid':'Paid';
			 }
			$scope[key] = data;
		});
		
		if(cusdata.tryout!='') { 
			var weekday = new Array(7);
				weekday[0]=  "Sunday";
				weekday[1] = "Monday";
				weekday[2] = "Tuesday";
				weekday[3] = "Wednesday";
				weekday[4] = "Thursday";
				weekday[5] = "Friday";
				weekday[6] = "Saturday";
		 
			$scope.tryout_name = cusdata.tryout.name
			$scope.tryout_division = cusdata.tryout.division
			$scope.tryout_date = cusdata.tryout.date
			var d = new Date(cusdata.tryout.time); 
			$scope.tryout_day = weekday[d.getDay()]
			var hour = d.getHours(); 
			var min = d.getMinutes(); 
			var time = hour +":"+ min;
			$scope.tryout_time = time;
		}
     });
	 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.customerUpdate = customerUpdate;
	 
	 function customerUpdate() {  
		var formData = {playerid: $scope.playerid,shirt_size: $scope.shirt_size,
						 firstname: $scope.firstname,lastname: $scope.lastname,gender: $scope.gender,
						 dob: $scope.dob,nationality: $scope.nationality,street_address: $scope.street_address,school_attending: $scope.school_attending,home_phone: $scope.home_phone,email: $scope.email,civil_id: $scope.civil_id,numyrs_played: $scope.numyrs_played,division: $scope.division,numyrs_tournteam: $scope.numyrs_tournteam,lastdiv_played: $scope.lastdiv_played,playedprevious_league: $scope.playedprevious_league,played_howmanyyrs: $scope.played_howmanyyrs,special_request: $scope.special_request,gender: $scope.gender,
						 league_comments: $scope.league_comments,amount: $scope.amount,payment_status: $scope.payment_status,fathername: $scope.fathername,father_workphone: $scope.father_workphone,father_mobile: $scope.father_mobile,father_employer: $scope.father_employer,father_occupation: $scope.father_occupation,father_email: $scope.father_email,father_volunteer: $scope.father_volunteer,mothername: $scope.mothername,mother_workphone: $scope.mother_workphone,mother_mobile: $scope.mother_mobile,mother_employer: $scope.mother_employer,mother_occupation: $scope.mother_occupation,mother_email: $scope.mother_email,mother_volunteer: $scope.mother_volunteer,player_mediconditions: $scope.player_mediconditions,emergency_contact: $scope.emergency_contact,relationship: $scope.relationship,contact_number: $scope.contact_number,insurance_carrier: $scope.insurance_carrier,policy_number: $scope.policy_number,submittype: $scope.submittype
						}
		$http.post('api/v1/customerupdate', formData).success(function(customer) {
			if(customer.sucess){
				if(customer.type=='save') {
					$window.scrollTo(0, 0);
					FlashService.Success(customer.message);
					
					//window.location = '#/customer-management';
				}
				else {
					window.location = '#/tshirt-management';
				}
			}  
		});				
	  }
	    var el = this;
		var defaults = {
						separator: ',',
						ignoreColumn: [],
						tableName:'yourTableName',
						type:'excel',
						pdfFontSize:14,
						pdfLeftMargin:20,
						escape:'true',
						htmlContent:'false',
						consoleLog:'false'
				};
				
	$scope.PrintPDf = function() { 
			//var pdf = new jsPDF('p','pt','a4');
			 
			var pdf = new jsPDF('p','pt','a4');
			var source = $('.form-horizontal').first();
			//var name =  $scope.firstname
			pdf.addHTML(source,function() {
				pdf.save('player_details.pdf')
				
				//var string = pdf.output('datauristring');
				//$('.preview-pane').attr('src', string);
			});
	}
	
}]);
//Create New Pet User registration step
main.controller('CreatePetController', ['$scope', '$filter', '$cookieStore', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $cookieStore, $http, $window, $routeParams, FlashService) {
	$('#headerlogo').show();  
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 var otpvalidation = $cookieStore.get('otpvalidation');  // ENABLE THIS LINE for OTP
	 //var otpvalidation=1;	// ENABLE THIS LINE for BLOCK OTP
	 $scope.microchip= '';$scope.chip_brand= '';
 	 var playerid = 'check';
	 $http.get('api/v1/checkstep2', {params: {id: playerid}}).success(function(checkstep) {
		if(checkstep.success && otpvalidation){
			//alert(checkstep);
			 $scope.microchip=checkstep.microchip;
			 $scope.chip_brand=checkstep.chip_brand;
			 $scope.purchase_name=checkstep.purchase_name;
			 $scope.chip_barcodemiage=checkstep.chip_barcodemiage;
			 $scope.purchase_type=checkstep.purchase_type;
		}
		else{
			window.location = '#/register-step2';
		}
     });

 	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
 	$http.get('api/v1/breedlist').success(function(breeddropdown) {
		$scope.breeddropdown = breeddropdown
     });
    $scope.breedListDropdown = function() {
     return this.breeddropdown;
    };
	 $scope.go_to_step2 = go_to_step2;
	 function go_to_step2(type) {  
		window.location = '#/register-step2';
	 } 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	 
	 $scope.dobchange = function(data){ 
		 var date = data.getDate(); 
		 var month = data.getMonth() + 1; 
		 var year = data.getFullYear(); 
		 var fulldate = year +"-"+ month +"-"+ date;
		 $scope.dob = fulldate;
	 }
	 
	 $scope.arvdatechange = function(data){
		 var date = data.getDate(); 
		 var month = data.getMonth() + 1; 
		 var year = data.getFullYear(); 
		 var fulldate = year +"-"+ month +"-"+ date;
		 $scope.arv_date = fulldate;
	 }
	 
	 var handleFileSelect=function(evt) { 
      var file=evt.currentTarget.files[0];
	  
      var reader = new FileReader();
      reader.onload = function (evt) {
        $scope.$apply(function($scope){
          $scope.image=evt.target.result;
        });
      };
      reader.readAsDataURL(file);
    };
    angular.element(document.querySelector('#image')).on('change',handleFileSelect);
	 
	 $scope.petCreate = petCreate;
	 
	 function petCreate() {  
		var formData = {  
						pet_name: $scope.pet_name,
						species_id: $scope.species_id,
						breed_type:$scope.breed_type,
						gender:$scope.gender,
						dob:$scope.dob,
						arvdate:$scope.arv_date,
						primary_color:$scope.primary_color,
						special_marking:$scope.special_marking,
						description:$scope.description,
						image:$scope.image,
						submittype: $scope.submittype}
		$http.post('api/v1/petcreatestep', formData).success(function(pet) {
			//return false;
			if(pet.sucess){
					$cookieStore.put('petid', pet.petid);
					$cookieStore.put('user_id', pet.userid);
					$cookieStore.put('paystatus', pet.paystatus);
					$cookieStore.put('otpvalidation', '');
					
					FlashService.Success(pet.message);
					window.location = '#/register-final';
			}
			else {
					FlashService.Error(pet.message);
					window.location = '#/register-step3';
			}
		});				
	  }
				
}]);
//Create New Pet Logged in User
main.controller('CreateUserPetController', ['$scope', '$cookieStore', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $cookieStore, $filter, $http, $window, $routeParams, FlashService) { 
	$('#headerlogo').show(); 
	$('.preloader').show().delay(2000).fadeOut(1000);
	
 	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
 	$http.get('api/v1/breedlist').success(function(breeddropdown) {
		$scope.breeddropdown = breeddropdown
     });
    $scope.breedListDropdown = function() {
     return this.breeddropdown;
    };
	$scope.go_to_profile = go_to_profile;
		function go_to_profile(type) {  
			window.location = '#/my-profile';
	} 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	 
	 $scope.dobchange = function(data){ 
		 var date = data.getDate(); 
		 var month = data.getMonth() + 1; 
		 var year = data.getFullYear(); 
		 var fulldate = year +"-"+ month +"-"+ date;
		 $scope.dob = fulldate;
	 }
	 
	 $scope.arvdatechange = function(data){
		 var date = data.getDate(); 
		 var month = data.getMonth() + 1; 
		 var year = data.getFullYear(); 
		 var fulldate = year +"-"+ month +"-"+ date;
		 $scope.arv_date = fulldate;
	 }
	 
	 var handleFileSelect=function(evt) { 
      var file=evt.currentTarget.files[0];
	  
      var reader = new FileReader();
      reader.onload = function (evt) {
        $scope.$apply(function($scope){
          $scope.image=evt.target.result;
        });
      };
      reader.readAsDataURL(file);
    };
    angular.element(document.querySelector('#image')).on('change',handleFileSelect);
	 
	 $scope.petCreate = petCreate;
	 
	 function petCreate() {  
		var formData = {  
						chipnumber: $scope.microchip,
						chiptype: $scope.microchip_purchase,
						pet_name: $scope.pet_name,
						species_id: $scope.species_id,
						breed_type:$scope.breed_type,
						gender:$scope.gender,
						dob:$scope.dob,
						arvdate:$scope.arv_date,
						primary_color:$scope.primary_color,
						special_marking:$scope.special_marking,
						description:$scope.description,
						image:$scope.image,
						submittype: $scope.submittype}
		$http.post('api/v1/petcreatestepuser', formData).success(function(pet) {
			//return false;
			if(pet.sucess){
				$cookieStore.put('petid', pet.petid);
				$cookieStore.put('paystatus', pet.paystatus);
				FlashService.Success(pet.message);
				window.location = '#/register-success';
			}
			else {
				FlashService.Error(pet.message);
				window.location = '#/add-new-pet';
			}
		});				
	  }
				
}]);
// Update Pet Details by Logged in users
main.controller('EditUserPetController', ['$scope', '$cookieStore', '$routeParams', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $cookieStore, $routeParams, $filter, $http, $window, $routeParams, FlashService) {  
	$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 var petid = $routeParams.id;	
	 $http.get('api/v1/petbyidall', {params: {id: petid}}).success(function(pet) {
		 $.each(pet, function(key, data) {
			$scope[key] = data;
		});
     });
	 
 	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
 	$http.get('api/v1/breedlist').success(function(breeddropdown) {
		$scope.breeddropdown = breeddropdown
     });
    $scope.breedListDropdown = function() {
     return this.breeddropdown;
    };
	$scope.go_to_profile = go_to_profile;
		function go_to_profile(type) {  
			window.location = '#/my-profile';
	} 
	$scope.Button = Button;
	function Button(type) {  
		$scope.submittype = type;
	} 
	$scope.dobchange = function(data){ 
		var date = data.getDate(); 
		var month = data.getMonth() + 1; 
		var year = data.getFullYear(); 
		var fulldate = year +"-"+ month +"-"+ date;
		$scope.dob = fulldate;
	}
 
	$scope.arvdatechange = function(data){
		 var date = data.getDate(); 
		 var month = data.getMonth() + 1; 
		 var year = data.getFullYear(); 
		 var fulldate = year +"-"+ month +"-"+ date;
		 $scope.arv_date = fulldate;
	}
	 
	var handleFileSelect=function(evt) { 
    var file=evt.currentTarget.files[0];
	var reader = new FileReader();
    reader.onload = function (evt) {
    	$scope.$apply(function($scope){
        	$scope.image_new=evt.target.result;
        });
   };
      reader.readAsDataURL(file);
   };
   angular.element(document.querySelector('#image_new')).on('change',handleFileSelect);
	 
	$scope.petUpdate = petUpdate;
	
	function petUpdate() {  
		var formData = { 
						pet_id: petid, 
						pet_name: $scope.pet_name,
						species_id: $scope.species_id,
						breed_type:$scope.breed_type,
						gender:$scope.gender,
						dob:$scope.dob,
						arvdate:$scope.arv_date,
						primary_color:$scope.primary_color,
						special_marking:$scope.special_marking,
						description:$scope.description,
						image_new:$scope.image_new,
						submittype: 'update'}
		$http.post('api/v1/peteditstepuser', formData).success(function(pet) {
			//return false;
			if(pet.sucess){
				FlashService.Success(pet.message);
				window.location = '#/my-profile';
			}
			else{
				FlashService.Error(pet.message);
				window.location = '#/edit-pet/'+petid;
			}
		});				
	  }
				
}]);
// Update Pet Details
main.controller('EditPetController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {  
	$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 var petid = $routeParams.id;	
	 
	 $http.get('api/v1/petbyid', {params: {id: petid}}).success(function(pet) {
		 $.each(pet, function(key, data) {
			$scope[key] = data;
		});
     });
 	$http.get('api/v1/customer').success(function(customerdropdown) {
		$scope.customerdropdown = customerdropdown
     });
    $scope.customerListDropdown = function() {
     return this.customerdropdown;
    };
	 
 	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
 	$http.get('api/v1/breedlist').success(function(breeddropdown) {
		$scope.breeddropdown = breeddropdown
     });
    $scope.breedListDropdown = function() {
     return this.breeddropdown;
    };
	 
	 $scope.ButtonPet = ButtonPet;
	 function ButtonPet(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.petUpdate = petUpdate;
	 
	 function petUpdate() { 
		var formData = {pet_id: pet_id,
						owner_id: $scope.owner_id,
						pet_name: $scope.pet_name,
						species_id: $scope.species_id,
						breed_type:$scope.breed_type,
						microchip_no:$scope.microchip_no,
						gender:$scope.gender,
						primary_color:$scope.primary_color,
						special_marking:$scope.special_marking,
						description:$scope.description,
						image:$scope.image,
						status:$scope.status,
						submittype: $scope.submittype}
		$http.post('api/v1/petupdate', formData).success(function(pet) {
			if(status.sucess){
				if(status.type=='save') {
					$window.scrollTo(0, 0);
					FlashService.Success(status.message);
				}
				else {
					window.location = '#/pets-management';
				}
			}  
		});				
	  }
}]);
// Listout Lost Pet Details
main.controller('ViewLostpetController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {  
	$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	var limit='';	 
 	$http.get('api/v1/lostpetlist', {params: {limit: limit}}).success(function(lostepets) {
		$scope.lostpets = lostepets
     });
	  
}]);
// Update Lost Pet Details
main.controller('EditLostpetController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) { 
	$('#headerlogo').show(); 
	 var petid = $routeParams.id;	
	 
	 $http.get('api/v1/lostpetbyid', {params: {id: petid}}).success(function(pet) {
		 $.each(pet, function(key, data) {
			$scope[key] = data;
		});
     });
 	$http.get('api/v1/customer').success(function(customerdropdown) {
		$scope.customerdropdown = customerdropdown
     });
    $scope.customerListDropdown = function() {
     return this.customerdropdown;
    };
	 
 	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
 	$http.get('api/v1/breedlist').success(function(breeddropdown) {
		$scope.breeddropdown = breeddropdown
     });
    $scope.breedListDropdown = function() {
     return this.breeddropdown;
    };
	 
	 $scope.ButtonPet = ButtonPet;
	 function ButtonPet(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.lostpetUpdate = lostpetUpdate;
	 
	 function lostpetUpdate() { 
		var formData = {lost_pet_id: petid,
						owner_id: $scope.owner_id,
						nick_name: $scope.nick_name,
						species_id: $scope.species_id,
						breed_type:$scope.breed_type,
						microchip_no:$scope.microchip_no,
						gender:$scope.gender,
						primary_color:$scope.primary_color,
						last_seen_on:$('#last_seen_on').val(),
						last_seen_at:$scope.last_seen_at,
						special_marking:$scope.special_marking,
						description:$scope.description,
						image:$scope.image,
						status:$scope.status,
						submittype: $scope.submittype}
		$http.post('api/v1/lostpetupdate', formData).success(function(pet) {
			if(status.sucess){
				if(status.type=='save') {
					$window.scrollTo(0, 0);
					FlashService.Success(status.message);
				}
				else {
					window.location = '#/pets-management';
				}
			}  
		});				
	  }
}]);
//Create New Lost Pet
main.controller('CreateLostpetController', ['$scope', '$filter', '$http', '$window', '$routeParams', '$rootScope', 'FlashService', function($scope, $filter, $http, $window, $routeParams, $rootScope, FlashService) {  
	$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	var logged = '';
 	logged = $rootScope.globals.currentUser
	$scope.owner_id = 0;
	$scope.availablepets = 0;
	if(logged){
		//alert(logged.userid)
		 $http.get('api/v1/customerbyid', {params: {id: logged.userid}}).success(function(cusdata) {
			var customer = cusdata.customer;
			$scope.owner_id = customer.user_id;
			$scope.address_1 = customer.fname +' '+customer.lname;
			$scope.address_2 = customer.address_1;
			$scope.email = customer.email;
			$scope.contact_number = customer.phone_1;
		});
		
		$http.get('api/v1/mypetlistlost', {params: {id: logged.userid}}).success(function(petdata) {
	 		$scope.petsdropdown=petdata;
			$scope.availablepets = petdata.length;
     	});
	}
    $scope.petsListDropdown = function() {
     return this.petsdropdown;
    };
	
	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
 	$http.get('api/v1/breedlist').success(function(breeddropdown) {
		$scope.breeddropdown = breeddropdown
     });
    $scope.breedListDropdown = function() {
     return this.breeddropdown;
    };
	$scope.getPetdetails = getPetdetails;
	function getPetdetails(){
		if($scope.pet_id){
		 $http.get('api/v1/petbyidall', {params: {id: $scope.pet_id}}).success(function(pet) {
			 $.each(pet, function(key, data) {
				 if(key == 'pet_name'){
					$scope.nick_name = data;
				 }
				$scope[key] = data;
			});
		 });
		}
	}
	
	$scope.reward_offered=1;
	 $scope.ButtonLostdog = ButtonLostdog;
	 function ButtonLostdog(type) {  
		$scope.submittype = type;
	 } 
	 $scope.datechange = function(data){ 
		 var date = data.getDate(); 
		 var month = data.getMonth() + 1; 
		 var year = data.getFullYear(); 
		 var fulldate = year +"-"+ month +"-"+ date;
		 $scope.last_seen_on = fulldate;
	 }
	 
	 var handleFileSelect=function(evt) { 
      var file=evt.currentTarget.files[0];
	  
      var reader = new FileReader();
      reader.onload = function (evt) {
        $scope.$apply(function($scope){
          $scope.image=evt.target.result;
        });
      };
      reader.readAsDataURL(file);
    };
    angular.element(document.querySelector('#image')).on('change',handleFileSelect);
	 $scope.lostpetCreate = lostpetCreate;
	 
	 function lostpetCreate() {  
	 
	var	fd = {nick_name: $scope.nick_name,
						species_id: 1,
						breed_type:$scope.breed_type,
						microchip_no:$scope.microchip_no,
						pet_id:$scope.pet_id,
						gender:$scope.gender,
						spay_neuter_status:$scope.spay_neuter_status,
						primary_color:$scope.primary_color,
						special_marking:$scope.special_marking,
						description:$scope.description,
						last_seen_on:$scope.last_seen_on,
						last_seen_at:$scope.last_seen_at,
						reward_offered:$scope.reward_offered,
						reward_amount:$scope.reward_amount,
						image:$scope.image,
						status:$scope.status,
						address_1:$scope.address_1,
						address_2:$scope.address_2,
						email:$scope.email,
						owner_id:$scope.owner_id,
						contact_number:$scope.contact_number,
						
						submittype: $scope.submittype}
		$http.post('api/v1/lostpetcreate', fd).success(function(pet) {
			if(pet.sucess){
				if(pet.type=='create') {
					$window.scrollTo(0, 0);
					FlashService.Success(pet.message);
					window.location = '#/lost-pet';
				}
				else if(pet.type=='createflyer' &&  pet.flyerid) {
					$window.scrollTo(0, 0);
					FlashService.Success(pet.message);
					window.location = pet.redirect;
				}
				else {
					window.location = '#/report-lost-pet';
				}
			}  
		});				
	  }
				
}]);
// Listout Found Pet Details
main.controller('ViewFoundpetController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {  
	$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	var limit='';	 
 	$http.get('api/v1/foundpetlist', {params: {limit: limit}}).success(function(foundepets) {
		$scope.foundepets = foundepets
     });
	  
}]);
// Update Lost Pet Details
main.controller('EditFoundpetController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {
	$('#headerlogo').show();  
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 var petid = $routeParams.id;	
	 
	 $http.get('api/v1/foundpetbyid', {params: {id: petid}}).success(function(pet) {
		 $.each(pet, function(key, data) {
			$scope[key] = data;
		});
     });
 	$http.get('api/v1/customer').success(function(customerdropdown) {
		$scope.customerdropdown = customerdropdown
     });
    $scope.customerListDropdown = function() {
     return this.customerdropdown;
    };
	 
 	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
 	$http.get('api/v1/breedlist').success(function(breeddropdown) {
		$scope.breeddropdown = breeddropdown
     });
    $scope.breedListDropdown = function() {
     return this.breeddropdown;
    };
	 
	 $scope.ButtonPet = ButtonPet;
	 function ButtonPet(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.foundpetUpdate = foundpetUpdate;
	 
	 function foundpetUpdate() { 
		var formData = {found_pet_id: petid,
						found_by: $scope.found_by,
						nick_name: $scope.nick_name,
						species_id: $scope.species_id,
						breed_type:$scope.breed_type,
						microchip_no:$scope.microchip_no,
						gender:$scope.gender,
						primary_color:$scope.primary_color,
						found_on:$('#found_on').val(),
						found_at:$scope.found_at,
						special_marking:$scope.special_marking,
						description:$scope.description,
						image:$scope.image,
						status:$scope.status,
						submittype: $scope.submittype}
		$http.post('api/v1/foundpetupdate', formData).success(function(pet) {
			if(status.sucess){
				if(status.type=='save') {
					$window.scrollTo(0, 0);
					FlashService.Success(status.message);
				}
				else {
					window.location = '#/foundpets-management';
				}
			}  
		});				
	  }
}]);
//Create New Lost Pet
main.controller('CreateFoundpetController', ['$scope', '$filter', '$http', '$window', '$routeParams', '$rootScope', 'FlashService', function($scope, $filter, $http, $window, $routeParams, $rootScope, FlashService) {
	$('#headerlogo').show();  
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	$scope.owner_id = 0;
	var logged = '';
 	logged = $rootScope.globals.currentUser
	if(logged){
		//alert(logged.userid)
		 $http.get('api/v1/customerbyid', {params: {id: logged.userid}}).success(function(cusdata) {
			var customer = cusdata.customer;
			$scope.owner_id = logged.userid;
			$scope.address_1 = customer.fname +' '+customer.lname;
			$scope.address_2 = customer.address_1;
			$scope.email = customer.email;
			$scope.contact_number = customer.phone_1;
		});
	}	 
 	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
 	$http.get('api/v1/breedlist').success(function(breeddropdown) {
		$scope.breeddropdown = breeddropdown
     });
    $scope.breedListDropdown = function() {
     return this.breeddropdown;
    };
	$scope.reward_offered=1;
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	 $scope.datechange = function(data){ 
		 var date = data.getDate(); 
		 var month = data.getMonth() + 1; 
		 var year = data.getFullYear(); 
		 var fulldate = year +"-"+ month +"-"+ date;
		 $scope.found_on = fulldate;
	 }
	 
	 var handleFileSelect=function(evt) { 
      var file=evt.currentTarget.files[0];
	  
      var reader = new FileReader();
      reader.onload = function (evt) {
        $scope.$apply(function($scope){
          $scope.image=evt.target.result;
		  //alert(evt.target.result);
        });
      };
      reader.readAsDataURL(file);
    };
    angular.element(document.querySelector('#image')).on('change',handleFileSelect);
	 $scope.foundpetCreate = foundpetCreate;
	 
	 function foundpetCreate() {  
	 
	var	fd = {nick_name: $scope.nick_name,
						species_id: 1,
						breed_type:$scope.breed_type,
						microchip_no:$scope.microchip_no,
						gender:$scope.gender,
						spay_neuter_status:$scope.spay_neuter_status,
						primary_color:$scope.primary_color,
						special_marking:$scope.special_marking,
						description:$scope.description,
						found_on:$scope.found_on,
						found_at:$scope.found_at,
						image:$scope.image,
						status:$scope.status,
						address_1:$scope.address_1,
						address_2:$scope.address_2,
						email:$scope.email,
						owner_id:$scope.owner_id,
						contact_number:$scope.contact_number,
						submittype: 'create'}
		$http.post('api/v1/foundpetcreate', fd).success(function(pet) {
			if(pet.sucess){
				if(pet.type=='create') {
					$window.scrollTo(0, 0);
					FlashService.Success(pet.message);
					window.location = '#/found-pet';
				}
				else {
					window.location = '#/report-found-pet';
				}
			}  
		});				
	  }
				
}]);
//Create New Chip
main.controller('CreateChipController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {  
	$('#headerlogo').show();
	 
 	$http.get('api/v1/petwithoutchip').success(function(petnochipdropdown) {
		$scope.petnochipdropdown =petnochipdropdown
     });
    $scope.petnochipListDropdown = function() {
     return this.petnochipdropdown;
    };
	 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.chipCreate = chipCreate;
	 
	 function chipCreate() {  
		var formData = {chip_code: $scope.chip_code,
						chip_type: $scope.chip_type,
						pet_id: $scope.pet_id,
						status:$scope.status,
						submittype: $scope.submittype}
		$http.post('api/v1/chipcreate', formData).success(function(chip) {
			if(chip.sucess){
				if(chip.type=='Create') {
					$window.scrollTo(0, 0);
					window.location = '#/chip-management';
					FlashService.Success(chip.message);
				}
				else {
					window.location = '#/add-new-chip';
				}
			}  
		});				
	  }
				
}]);
// Update Micro chip Details
main.controller('EditChipController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {  
	$('#headerlogo').show();
	 var chipid = $routeParams.id;	
	 
	 $http.get('api/v1/chipbyid', {params: {id: chipid}}).success(function(chip) {
		 $.each(chip, function(key, data) {
			$scope[key] = data;
		});
     });
 	$http.get('api/v1/getpetwithoutchip', {params: {id: chipid}}).success(function(petnochipdropdown) {
		$scope.petnochipdropdown =petnochipdropdown
     });
    $scope.petnochipListDropdown = function() {
     return this.petnochipdropdown;
    };
	 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.chipUpdate = chipUpdate;
	 
	 function chipUpdate() { 
		var formData = {chip_id: chipid,
						pet_id: $scope.pet_id,
						chip_code: $scope.chip_code,
						chip_type: $scope.chip_type,
						status:$scope.status,
						submittype: $scope.submittype}
		$http.post('api/v1/chipupdate', formData).success(function(chip) {
			if(chip.sucess){
				if(chip.type=='Update') {
					$window.scrollTo(0, 0);
					FlashService.Success(chip.message);
				}
				else {
					window.location = '#/chip-management';
				}
			}  
		});				
	  }
}]);
// Create Breed type Details
main.controller('CreateBreedController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) {
	$('#headerlogo').show();  
	 
 	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.breedCreate = breedCreate;
	 
	 function breedCreate() { 
		var formData = {breed_name: $scope.breed_name,
						species_id: $scope.species_id,
						status:$scope.status,
						submittype: $scope.submittype}
		$http.post('api/v1/breedcreate', formData).success(function(breeddata) {
			if(breeddata.sucess){
				if(breeddata.type=='Update') {
					$window.scrollTo(0, 0);
					FlashService.Success(breeddata.message);
				}
				else {
					window.location = '#/breed-management';
				}
			}  
		});				
	  }
}]);
// Update Breed type Details
main.controller('EditBreedController', ['$scope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $routeParams, FlashService) { 
	$('#headerlogo').show(); 
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 var breedid = $routeParams.id;	
	 
	 $http.get('api/v1/breedbyid', {params: {id: breedid}}).success(function(breed) {
		 $.each(breed, function(key, data) {
			$scope[key] = data;
		});
     });
 	$http.get('api/v1/specieslist').success(function(speciesdropdown) {
		$scope.speciesdropdown = speciesdropdown
     });
    $scope.speciesListDropdown = function() {
     return this.speciesdropdown;
    };
	 
	 $scope.Button = Button;
	 function Button(type) {  
		$scope.submittype = type;
	 } 
	  
	 $scope.breedUpdate = breedUpdate;
	 
	 function breedUpdate() { 
		var formData = {breed_id: breedid,
						breed_name: $scope.breed_name,
						species_id: $scope.species_id,
						status:$scope.status,
						submittype: $scope.submittype}
		$http.post('api/v1/breedupdate', formData).success(function(breeddata) {
			if(breeddata.sucess){
				if(breeddata.type=='Update') {
					$window.scrollTo(0, 0);
					FlashService.Success(breeddata.message);
				}
				else {
					window.location = '#/breed-management';
				}
			}  
		});				
	  }
}]);
// Update Account Details
main.controller('EditAccountController', ['$scope', '$filter', '$http', '$window', '$rootScope', 'FlashService', function($scope, $filter, $http, $window, $rootScope, FlashService) {  
	$('#headerlogo').show();
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 
	var logged = '';
	var userid = '';
	logged = $rootScope.globals.currentUser
	userid = ( logged )?logged.userid:'';
	
	 $http.get('api/v1/account', {params: {userid: userid}}).success(function(user) {
		 //console.log(user)
		 $scope.userid = userid;
		 $scope.email = user.email;	
     });
	 $scope.password = '';
	 
	 $scope.accountUpdate = accountUpdate;
	 
	 function accountUpdate() { 
		var formData = {userid: userid, email: $scope.email,
						userid: $scope.userid, password: $scope.password
						}
		$http.post('api/v1/accountupdate', formData).success(function(tshirt) {
			if(tshirt.sucess){
				$window.scrollTo(0, 0);
				FlashService.Success(tshirt.message);
			}  
		});				
	  }
}]);

// Display How It works page
main.controller('HowitworksController', ['$scope', '$filter', '$http', '$window', '$rootScope', 'FlashService', function($scope, $filter, $http, $window, $rootScope, FlashService) {
	$('#headerlogo').show();  
	$('.preloader').show().delay(2000).fadeOut(1000);
	
	 
	$http.get('api/v1/howitworks', {params: {view: 'howitworks'}}).success(function(homedata) {
		//alert(cusdata.customer['owner_name']);
		//alert(homedata)		 
		$.each(homedata.datahowitworks, function(key, data) {
			//alert(key);
			$scope[key] = data;
		});
	});
}]);

main.controller('PaymentController', ['$scope', '$rootScope', '$cookieStore', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $rootScope, $cookieStore, $filter, $http, $window, $routeParams, FlashService) {  
	 
 	 $scope.petid =  $cookieStore.get('petid');
	 
	 if($scope.petid!='') { 
		 var loggedIn  = $rootScope.globals.currentUser;
		 $scope.userid = loggedIn.userid;
		 $scope.amount = 100;
		 $scope.paystatus =  $cookieStore.get('paystatus');
		 if($scope.paystatus) {
			 $scope.message = 'Success! You have completed your registration. Please pay and activate your pet.'
		 }
		 else {
			$scope.message = 'Success! You have completed your registration.' 
		 }
		 $scope.click_pay = click_pay;
		 
		 function click_pay() {  
			var formData = {petid: $scope.petid,
							userid: $scope.userid,
							}
			$http.post('api/v1/paymentInstamojo', formData).success(function(pay) {
				if(pay.success){
					window.location = pay.url;
				}	
				else {
					$scope.paymentfailed = pay.message
					//window.location = '#/add-new-chip';
				}  
			});				
		  }
	}
	else {
		window.location = '#/add-new-pet';
	}	
				
}]);
main.controller('PaymentResponseController', ['$scope', '$rootScope', '$cookieStore', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $rootScope, $cookieStore, $filter, $http, $window, $routeParams, FlashService) { 
	$scope.petid =  $cookieStore.get('petid');
	
	if($scope.petid!='') {
	 $scope.payment_request_id = $routeParams.payment_request_id;
	 $scope.payment_id = $routeParams.payment_id;
	 $scope.petid =  $cookieStore.get('petid');
	 $scope.userid = $cookieStore.get('user_id');
	 
		var formData = { petid: $scope.petid,
						 userid: $scope.userid,
						 payment_request_id: $scope.payment_request_id,
						 payment_id: $scope.payment_id,
					   }
		$http.post('api/v1/paymentResponse', formData).success(function(pay) {
			$scope.title = pay.title;
			$scope.message = pay.message;
			$scope.error = pay.error;
			$scope.paymentid = pay.paymentid;
			
			$cookieStore.put('petid', '');
			$cookieStore.put('user_id', '');
		});	
	}
	else {
		window.location = '#/add-new-pet';
	}	
}]);
main.controller('PaymentRegisterController', ['$scope', '$rootScope', '$cookieStore', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $rootScope, $cookieStore, $filter, $http, $window, $routeParams, FlashService) {  
	 
 	 $scope.petid =  $cookieStore.get('petid');
	 
	 if($scope.petid!='') { 
		 $scope.userid = $cookieStore.get('user_id');
		 $scope.amount = 100;
		 $scope.paystatus =  $cookieStore.get('paystatus');
		 if($scope.paystatus) {
			 $scope.message = 'Success! You have completed your registration. Please pay and activate your pet.'
		 }
		 else {
			$scope.message = 'Success! You have completed your registration.' 
		 }
		 
		 $scope.click_pay = click_pay;
		 
		 function click_pay() {  
			var formData = {petid: $scope.petid,
							userid: $scope.userid,
							}
			$http.post('api/v1/paymentRegisterInstamojo', formData).success(function(pay) {
				if(pay.success){
					window.location = pay.url;
				}	
				else {
					$scope.paymentfailed = pay.message
					//window.location = '#/add-new-chip';
				}  
			});				
		  }
	}
	else {
		window.location = '#/register-step1';
	}	
				
}]);
main.controller('PaymentRegisterResponseController', ['$scope', '$rootScope', '$cookieStore', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $rootScope, $cookieStore, $filter, $http, $window, $routeParams, FlashService) { 
	$scope.petid =  $cookieStore.get('petid');
	
	if($scope.petid!='') {
	 $scope.payment_request_id = $routeParams.payment_request_id;
	 $scope.payment_id = $routeParams.payment_id;
	 $scope.petid =  $cookieStore.get('petid');
	 $scope.userid = $cookieStore.get('user_id');
	 
		var formData = { petid: $scope.petid,
						 userid: $scope.userid, 	
						 payment_request_id: $scope.payment_request_id,
						 payment_id: $scope.payment_id,
					   }
		$http.post('api/v1/paymentResponse', formData).success(function(pay) {
			$scope.title = pay.title;
			$scope.message = pay.message;
			$scope.error = pay.error;
			$scope.paymentid = pay.paymentid;
			
			$cookieStore.put('petid', '');
			$cookieStore.put('user_id', '');
		});	
	}
	else {
		window.location = '#/register-step1';
	}	
}]);
main.controller('Imageupload',['$scope', function($scope) {
    $scope.myImage='';
    $scope.myCroppedImage='';
	
    var handleFileSelect=function(evt) { 
      var file=evt.currentTarget.files[0];
	  
      var reader = new FileReader();
      reader.onload = function (evt) {
        $scope.$apply(function($scope){
          $scope.myImage=evt.target.result;
		  //alert($scope.myImage)
        });
      };
      reader.readAsDataURL(file);
    };
    angular.element(document.querySelector('#fileInput')).on('change',handleFileSelect);
  }]);
// Update Account Details
main.controller('footerController', ['$scope', '$filter', '$http', '$window', '$rootScope', 'FlashService', '$timeout', function($scope, $filter, $http, $window, $rootScope, FlashService, $timeout) {  
	 $scope.newsletterSubscribe = newsletterSubscribe;
	 
	 function newsletterSubscribe() { 
		 $scope.successmsg = '';
		 $scope.errormsg = '';
		 $scope.showsuccess = 0;
		 $scope.showerror = 0;
		var formData = {email: $scope.emailaddress,}
		$http.post('api/v1/newsletterSubscribe', formData).success(function(emailStatus) {
			if(emailStatus.sucess){
				$scope.emailaddress = '';
				$scope.newsletter_form.$setPristine();
				  $scope.showsuccess = 1; 
				 $scope.successmsg = emailStatus.message;
				 $timeout(function () { 
					 $scope.showsuccess = 0; 
					 $scope.successmsg = '';
				 }, 4000);   
				
			}  
			else{
					$scope.showerror = 1;
					$scope.errormsg = emailStatus.message;
				$timeout(function () { 
					$scope.showerror = 0;
					$scope.errormsg = '';
				 }, 4000);   
				
			}
		});				
	  }
}]);
// Verify OTP
main.controller('VerifyOTP', ['$scope', '$filter', '$cookieStore', '$http', '$window',  '$rootScope', 'FlashService', '$timeout', function($scope, $filter, $cookieStore, $http, $window, $rootScope, FlashService, $timeout) {  
	 
	 $scope.otpValidate = otpValidate;
	 
	 function otpValidate() { 
		 //alert($scope.otp)
		var formData = {otp: $scope.otp}
		$http.post('api/v1/verifyOTP', formData).success(function(otpStatus) {
			if(otpStatus.success){
				parent.$.fancybox.close();
				$cookieStore.put('otpvalidation', true);
				
				window.location = '#/register-step3';
			}  
			else{
				$scope.message = 	otpStatus.message;
			}
		});				
	  }
}]);

// User Password reset controller

main.controller('UserPasswordResetController', ['$scope', '$filter', '$http', '$window', '$rootScope', 'FlashService', function($scope, $filter, $http, $window, $rootScope, FlashService) { 
	$('#headerlogo').show(); 
	$('.preloader').show().delay(2000).fadeOut(1000);
	 
	var logged = '';
	var userid = '';
	logged = $rootScope.globals.currentUser
	userid = ( logged )?logged.userid:'';
	if(userid){
		window.location = '#/my-profile';
	}
	/*$http.get('api/v1/account', {params: {userid: userid}}).success(function(user) {
		 //console.log(user)
		 $scope.userid = userid;
		 $scope.email = user.email;	
     });*/
	 $scope.password = '';
	 
	 $scope.resetPassword = resetPassword;
	 
	 function resetPassword() { 
		var formData = {email: $scope.email	}
		$http.post('api/v1/resetpassword', formData).success(function(tshirt) {
			if(tshirt.success){
				FlashService.Success(tshirt.message);
			}  
			else{
				FlashService.Error(tshirt.message);
			}
		});				
	  }
}]);

