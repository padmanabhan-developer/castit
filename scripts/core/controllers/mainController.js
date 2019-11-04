main = angular.module('theme.core.main_controller', ['theme.core.services'])
main.controller('MainController', ['$scope', '$theme', '$cookies' , '$timeout', 'progressLoader', '$rootScope', '$location', 'AuthenticationServiceUser',
    function($scope, $theme, $cookies, $timeout, progressLoader, $rootScope, $location, AuthenticationServiceUser) {
    'use strict';
		// $scope.layoutIsSmallScreen = false;
    $rootScope.isDanish = true;
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
		
		$(document).ready(function(){
			$('.logo').click(function(){
				// window.location.hash('#/index/da');
				if(history.pushState) {
					history.pushState(null, null, '#/index/da');
					location.reload();
				}
				else {
					location.hash = '#/index/da';
					location.reload();
				}
			});
			$('.main-menu-trigger').click(function(){
				$('.landing-wrapper').toggleClass('menu-active');
			});

			$('.popup-close-btn').click(function(){
				$('.landing-wrapper').removeClass('menu-active');
				$('.res_menu').removeClass('active');
				$(".res_menu ul li").fadeOut("slow");
			});
			$(".side-top-arrow").click(function(){
				$scope.profile_from_lightbox = false;
				$("#sidebar1").show("slow").css("display","inline-flex");
			  });
		});

	$scope.customerLoggedIn = function(){
		// console.log($cookies.customer_id);
		if($cookies.customer_id != undefined && $cookies.customer_id != ''){
			$scope.customer_set = true;
			if($rootScope.interface != 'login' && $rootScope.interface != 'ansog'){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}

    $scope.layoutLoading = true;
    $scope.getLayoutOption = function(key) {
      return $theme.get(key);
		};
		$scope.changeLanguage = function(isDanish) {
	//     $rootScope.$apply(function() {
	      $rootScope.isDanish = isDanish;
				var currentPathArray = $location.path().split('/');
				$location.path('/' + currentPathArray[1] + (isDanish ? '/da' : '/en'));
	//     });
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
    $scope.chatters = [];
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
	
	$scope.show_about = function() {
		$rootScope.isMaincontent = false;
		$rootScope.interface = 'aboutus';
		$(".main_content").fadeOut(); 
		$(".course_section").fadeOut();
		$(".right-sidebar").hide();
		// close menu-section
		closeMenuSection();

		$(".contact_section").fadeIn("slow"); 
	};
	$scope.show_course = function() {
		$rootScope.isMaincontent = false;
		$rootScope.interface = 'course';
		$(".main_content").fadeOut(); 
		$(".contact_section").fadeOut();
		$(".right-sidebar").hide();
		closeMenuSection();
		
		$(".course_section").fadeIn("slow"); 
	};

	$scope.toggleMenu = function(){
		$("#main-menu-context").toggleClass('active');
		$('.landing-wrapper').addClass('menu-active');
	};

	$scope.goto = function(path){

		$('#main-menu-context').removeClass('active');

		$location.path(path);
	};

	$scope.hideMenu = function(){
		$("#main-menu-context").removeClass('active');
	};

	// $("#main-menu-context li").click(function(){
	// 	$("#main-menu-context").removeClass('active');
	// });

	function closeMenuSection(){
		$('#main-menu-context').removeClass('active');
		$('.landing-wrapper').removeClass('menu-active');
	}
	

		if(!$scope.isDanish){
			alert('asd');
			$('.black-prev').css('width','135px !important');
		}

 }]);






// Regsiter Step 1
main.controller('RegisterStep1Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 
 	$http.get('api/v1/checkstep1', {params: {limit: limit}}).success(function(reponse) {
		if(reponse.success){
			$scope.first_name = reponse.step1.first_name;
			$scope.last_name = reponse.step1.last_name;
			$scope.password = reponse.step1.password;
			$scope.cpassword = reponse.step1.password;
			$scope.zipcode = reponse.step1.zipcode;
			$scope.address = reponse.step1.address;
			$scope.city = reponse.step1.city;
			$scope.selectedCountry_id = reponse.step1.country_id;
		}


		
     });

	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown
	});
	$scope.countriesListDropdown = function() {
		return this.countriesdropdown;
	}; 
	$scope.selectedCountryChanged = selectedCountryChanged;
	function selectedCountryChanged() {  
	alert('sel');
	}
	$scope.step1Create = step1Create;
	function step1Create() {  
		var formData = {first_name: $scope.first_name,
						last_name: $scope.last_name,
						password: $scope.password,
						zipcode: $scope.zipcode,
						address: $scope.address,
						city: $scope.city,
						country_id: $scope.selectedCountry_id
						}
		$http.post('api/v1/step1Create', formData).success(function(response) {
			//alert(response.success);
			//alert($scope.first_name);
			if(response.success){
				window.location = '#/ansog-trin2' + ($rootScope.isDanish ? '/da' : '/en' );
			}
		});				
	  }

	  
}]);

// Regsiter Step 2
main.controller('RegisterStep2Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	$scope.phone_at_work='';
	$scope.ethnic_origin = '';
	$scope.job = '';
	var limit='';	 
 	$http.get('api/v1/checkstep2', {params: {limit: limit}}).success(function(reponse) {
		if(reponse.step1status){
			if(reponse.step2status){
				$scope.email = reponse.step2.email;
				$scope.phone = reponse.step2.phone;
				$scope.phone_at_work = reponse.step2.phone_at_work;
				$scope.gender_id = reponse.step2.gender_id;
				$scope.birth_day = reponse.step2.birth_day;
				$scope.birth_month = reponse.step2.birth_month;
				$scope.birth_year = reponse.step2.birth_year;
				$scope.ethnic_origin = reponse.step2.ethnic_origin;
				$scope.job = reponse.step2.job;
			}
		}
		else{
			window.location = '#/ansog-trin1' + ($rootScope.isDanish ? '/da' : '/en' );
		}
	 });
	 
	
	$http.get('api/v1/years').success(function(yearsdropdown) {
		$scope.yearsdropdown = yearsdropdown
	});
	$scope.yearsListDropdown = function() {
		return this.yearsdropdown;
	}; 
	
  var genderDa = [{
		'id': 1,
		'name': 'Mand'
	}, {
		'id': 2,
		'name': 'Kvinde'
	}];  
	var genderEn = [{
		'id': 1,
		'name': 'Man'
	}, {
		'id': 2,
		'name': 'Woman'
	}];
	$scope.gender = $rootScope.isDanish ? genderDa : genderEn;

	$scope.step2Create = step2Create;
	function step2Create() {
		$scope.email = $("#email").val();
		$scope.phone = $("#phone").val();
		$scope.phone_at_work = $("#phone_at_work").val();
		$scope.gender_id = $("#gender_id").val();
		$scope.birth_day = $("#birth_day").val();
		$scope.birth_month = $("#birth_month").val();
		$scope.birth_year = $("#birth_year").val();
		$scope.ethnic_origin = $("#ethnic_origin").val();
		$scope.job = $("#job").val();
		var formData = {
            email: $scope.email,
						phone: $scope.phone,
						phone_at_work: $scope.phone_at_work,
						gender_id: $scope.gender_id,
						birth_day: $scope.birth_day,
						birth_month: $scope.birth_month,
						birth_year: $scope.birth_year,
						ethnic_origin: $scope.ethnic_origin,
						job: $scope.job
						}


		$http.post('api/v1/step2Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/ansog-trin3' + ($rootScope.isDanish ? '/da' : '/en' );
			}
		});				
	  }

	  
}]);

// Regsiter Step 3
main.controller('RegisterStep3Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';
	var limit='';
	$scope.showKidSizes = false;
	function clearValues(){
		$scope.shirt_size_from	= '-';	$scope.shirt_size_to= '-'; 	$scope.pants_size_from	= '-'; 	$scope.pants_size_to='-';
		$scope.shoe_size_from 	= '-';	$scope.shoe_size_to	= '-';	$scope.suite_size_from 	= '-';	$scope.suite_size_to= '-';
		$scope.children_sizes 	= '-';	$scope.eye_color_id = '-';	$scope.hair_color_id 	= '-';	$scope.bra_size 	= '';
		$scope.height 			= '';	$scope.weight 		= '';
	}
	$("#showKidSizes").change(function(event){
		clearValues();
		if(event.target.checked){
			$scope.showKidSizes = true;
		}else{
			$scope.showKidSizes = false;
		}
	});

	$http.get('api/v1/checkstep2', {params: {limit: limit}}).success(function(reponse) {
		if(reponse.step1status){
			if(reponse.step2status){
				$scope.gender_id = reponse.step2.gender_id;
				$scope.age=reponse.age;
				
			}
		}
	});
	$http.get('api/v1/getstep3data', {params: {limit: limit}}).success(function(response) {
		$scope.gender=response.genders;
		$scope.eye_colors=response.eye_colors;
		$scope.hair_colors=response.hair_colors;
	});

	$scope.shirt_size_from	= '-';	$scope.shirt_size_to= '-'; 	$scope.pants_size_from	= '-'; 	$scope.pants_size_to='-';
	$scope.shoe_size_from 	= '-';	$scope.shoe_size_to	= '-';	$scope.suite_size_from 	= '-';	$scope.suite_size_to= '-';
	$scope.children_sizes 	= '-';	$scope.eye_color_id = '-';	$scope.hair_color_id 	= '-';	$scope.bra_size 	= '';
	$scope.height 			= '';	$scope.weight 		= '';	 
 	$http.get('api/v1/checkstep3', {params: {limit: limit}}).success(function(reponse) {
		if((reponse.step1status) && (reponse.step2status)){
			if(reponse.step3status){
				$scope.shirt_size_from 	= reponse.step3.shirt_size_from;
				$scope.shirt_size_to 	= reponse.step3.shirt_size_to;
				
				$scope.pants_size_from 	= reponse.step3.pants_size_from;
				$scope.pants_size_to 	= reponse.step3.pants_size_to;
				
				$scope.shoe_size_from 	= reponse.step3.shoe_size_from;
				$scope.shoe_size_to 	= reponse.step3.shoe_size_to;
				
				$scope.suite_size_from 	= reponse.step3.suite_size_from;
				$scope.suite_size_to 	= reponse.step3.suite_size_to;
				
				$scope.children_sizes 	= reponse.step3.children_sizes;
				
				$scope.eye_color_id 	= reponse.step3.eye_color_id;
				$scope.hair_color_id 	= reponse.step3.hair_color_id;
				
				$scope.bra_size 		= reponse.step3.bra_size;
				
				$scope.height 			= reponse.step3.height;
				$scope.weight 			= reponse.step3.weight;
			}
		}
		else{
			window.location = '#/ansog-trin2' + ($rootScope.isDanish ? '/da' : '/en' );
		}
     });
		 
	$scope.step3Create = step3Create;
	
	function step3Create() { 
		$scope.suite_size_from = $("#suite_size_from").val();
		$scope.suite_size_to = $("#suite_size_to").val();
		$scope.bra_size = $("#bra_size").val();

		var formData = {
            shirt_size_from: $scope.shirt_size_from,
						shirt_size_to: $scope.shirt_size_to,
						pants_size_from: $scope.pants_size_from,
						pants_size_to: $scope.pants_size_to,
						shoe_size_from: $scope.shoe_size_from,
						shoe_size_to: $scope.shoe_size_to,
						suite_size_from: $scope.suite_size_from,
						suite_size_to: $scope.suite_size_to,
						children_sizes: $scope.children_sizes,
						eye_color_id: $scope.eye_color_id,
						hair_color_id: $scope.hair_color_id,
						bra_size: $scope.bra_size,
						height: ($scope.height) ? $scope.height : '-',
						weight: ($scope.weight) ? $scope.weight : '-',
						}
		$http.post('api/v1/step3Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/ansog-trin4' + ($rootScope.isDanish ? '/da' : '/en' );
			}
		});				
	  }

	  
}]);

// Regsiter Step 4
main.controller('RegisterStep4Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', '$timeout', function($scope, $filter, $http, $windo, $rootScope, $routeParams, $FlashService, $timeout) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	$scope.selectedcategories='';$scope.selectedskills='';$scope.selectedlicences='';$scope.notes='';
	$scope.sports_hobby='';
	var limit='';	 

	$http.get('api/v1/getcategories' + ($rootScope.isDanish ? '' : '/en')).success(function(categoriesdropdown) {
		$scope.categoriesdropdown = categoriesdropdown
	});
	$http.get('api/v1/getskills' + ($rootScope.isDanish ? '' : '/en')).success(function(skillsdropdown) {
		$scope.skillsdropdown = skillsdropdown
	});
	$http.get('api/v1/getlicences' + ($rootScope.isDanish ? '' : '/en')).success(function(licencesdropdown) {
		$scope.licencesdropdown = licencesdropdown
	});

 	$http.get('api/v1/checkstep4', {params: {limit: limit}}).success(function(response) {
		if((response.step1status) && (response.step2status) && (response.step3status)){
			if(response.step4status){
				$scope.notes 	= response.step4.notes;
				$scope.sports_hobby 	= response.step4.sports_hobby;
				$scope.selectedcategories 	= response.step4.selectedcategories;
				$scope.selectedskills 	= response.step4.selectedskills;
				$scope.selectedlicences 	= response.step4.selectedlicences;
				
				if($scope.selectedcategories){
					var selscats1 = $scope.selectedcategories.split(',');
					for (i = 0; i < selscats1.length; i++) {
						var curcatid = selscats1[i];
						var myElBut = angular.element( document.querySelector( '#catbut'+curcatid ) );
						var myElSpan = angular.element( document.querySelector( '#catspan'+curcatid ) );
						myElBut.removeClass('button1').addClass('button2');
						myElSpan.removeClass('plus-icon').addClass('close-icon');
					}
				}
				if($scope.selectedskills){
					var selskill1 = $scope.selectedskills.split(',');
					for (i = 0; i < selskill1.length; i++) {
						var curskillid = selskill1[i];
						var myElBut = angular.element( document.querySelector( '#skillbut'+curskillid ) );
						var myElSpan = angular.element( document.querySelector( '#skillspan'+curskillid ) );
						myElBut.removeClass('button1').addClass('button2');
						myElSpan.removeClass('plus-icon').addClass('close-icon');
					}
				}
				if($scope.selectedlicences){
					var selslic1 = $scope.selectedlicences.split(',');
					for (i = 0; i < selslic1.length; i++) {
						var curlicid = selslic1[i];
						var myElBut = angular.element( document.querySelector( '#licbut'+curlicid ) );
						var myElSpan = angular.element( document.querySelector( '#licspan'+curlicid ) );
						myElBut.removeClass('button1').addClass('button2');
						myElSpan.removeClass('plus-icon').addClass('close-icon');
					}
				}
			}else{
				/*
					$timeout(function(){
						let curcatid = '9';
						$scope.selectedcategories = curcatid;
						let myElButtonTag = angular.element( document.querySelector( '#catbut'+curcatid ) );
						let myElSpanTag = angular.element( document.querySelector( '#catspan'+curcatid ) );
						myElButtonTag.removeClass('button1').addClass('button2');
						myElSpanTag.removeClass('plus-icon').addClass('close-icon');
						}, 200);
					$timeout(function(){
						let curskillid = '16';
						$scope.selectedskills = curskillid;
						let myElButtonTag = angular.element( document.querySelector( '#skillbut'+curskillid ) );
						let myElSpanTag = angular.element( document.querySelector( '#skillspan'+curskillid ) );
						myElButtonTag.removeClass('button1').addClass('button2');
						myElSpanTag.removeClass('plus-icon').addClass('close-icon');
						}, 200);
					$timeout(function(){
						let curlicid = '5';
						$scope.selectedlicences = curlicid;
						let myElButtonTag = angular.element( document.querySelector( '#licbut'+curlicid ) );
						let myElSpanTag = angular.element( document.querySelector( '#licspan'+curlicid ) );
						myElButtonTag.removeClass('button1').addClass('button2');
						myElSpanTag.removeClass('plus-icon').addClass('close-icon');
					}, 200);
					*/
			}
		}else{
			window.location = '#/ansog-trin3' + ($rootScope.isDanish ? '/da' : '/en' );
		}
     });

	$scope.checkuncheckcategory = checkuncheckcategory;
	function checkuncheckcategory(catid) {
		if(catid != '9'){
			let nilButton = angular.element( document.querySelector( '#catbut9') );
			let nilSpan = angular.element( document.querySelector( '#catspan9') );

			nilButton.removeClass('button2').addClass('button1');
			nilSpan.removeClass('close-icon').addClass('plus-icon');
		}

		var myElBut = angular.element( document.querySelector( '#catbut'+catid ) );
		var myElSpan = angular.element( document.querySelector( '#catspan'+catid ) );
		myElBut.toggleClass('button1').toggleClass('button2');
		myElSpan.toggleClass('plus-icon').toggleClass('close-icon');
		var selcats = new Array();

		if($scope.selectedcategories){
			 selcats = $scope.selectedcategories.split(',');
			 var selindex = selcats.indexOf(catid);
			if( selindex !== -1) {
					selcats.splice(selindex, 1); 
			}else{
					selcats.push(catid);
			}
		}else{
			if(myElBut.hasClass('button2'))
				 selcats.push(catid);
		}
		$scope.selectedcategories = selcats.toString();
		
		if($scope.selectedcategories != '9'){
			$scope.selectedcategories = $scope.selectedcategories.replace('9,','');
		}

		if($scope.selectedcategories == ""){
			// checkuncheckcategory('9');
		}

	};
	
	$scope.checkuncheckskill = checkuncheckskill;
	function checkuncheckskill(skillid) {  
		if(skillid != '16'){
			let nilButton = angular.element( document.querySelector( '#skillbut16') );
			let nilSpan = angular.element( document.querySelector( '#skillspan16') );

			nilButton.removeClass('button2').addClass('button1');
			nilSpan.removeClass('close-icon').addClass('plus-icon');
		}

		var myElBut = angular.element( document.querySelector( '#skillbut'+skillid ) );
		var myElSpan = angular.element( document.querySelector( '#skillspan'+skillid ) );
		myElBut.toggleClass('button1').toggleClass('button2');
		myElSpan.toggleClass('plus-icon').toggleClass('close-icon');
		var selskills = new Array();
		if($scope.selectedskills){
			 selskills = $scope.selectedskills.split(',');
			 var selindex = selskills.indexOf(skillid);
			if(selindex !== -1) {
				selskills.splice(selindex, 1); 
			}else{
				selskills.push(skillid);
			}
		}else{
			if(myElBut.hasClass('button2'))
				 selskills.push(skillid);
		}
		$scope.selectedskills = selskills.toString();
		if($scope.selectedskills != '16'){
			$scope.selectedskills = $scope.selectedskills.replace('16,','');
		}

		if($scope.selectedskills == ""){
			// checkuncheckskill('16');
		}

	};
	
	$scope.checkunchecklicence = checkunchecklicence;
	function checkunchecklicence(licid) {
		if(licid != '5'){
			let nilButton = angular.element( document.querySelector( '#licbut5') );
			let nilSpan = angular.element( document.querySelector( '#licspan5') );

			nilButton.removeClass('button2').addClass('button1');
			nilSpan.removeClass('close-icon').addClass('plus-icon');
		}  
		var myElBut = angular.element( document.querySelector( '#licbut'+licid ) );
		var myElSpan = angular.element( document.querySelector( '#licspan'+licid ) );
		myElBut.toggleClass('button1').toggleClass('button2');
		myElSpan.toggleClass('plus-icon').toggleClass('close-icon');
		var sellics = new Array();
		if($scope.selectedlicences){
			 sellics = $scope.selectedlicences.split(',');
			 var selindex = sellics.indexOf(licid);
			if( selindex !== -1) {
				sellics.splice(selindex, 1); 
			}else{
				sellics.push(licid);
			}
		}else{
			if(myElBut.hasClass('button2'))
				 sellics.push(licid);
		}
		$scope.selectedlicences = sellics.toString();
		if($scope.selectedlicences != '5'){
			$scope.selectedlicences = $scope.selectedlicences.replace('5,','');
		}

		if($scope.selectedlicences == ""){
			// checkunchecklicence('5');
		}
		
	};
	
	$scope.step4Create = step4Create;
	function step4Create() {  
		let formData = {
            notes: $scope.notes,
						selectedcategories: $scope.selectedcategories,
						selectedskills: $scope.selectedskills,
						selectedlicences: $scope.selectedlicences,
						sportshobby: $scope.sports_hobby,
						}
		
		/*
		if(
			($scope.selectedskills != undefined && $scope.selectedskills != '') && 
			($scope.selectedcategories != undefined && $scope.selectedcategories != '') &&
			($scope.selectedlicences != undefined && $scope.selectedlicences != '')
			){
		*/
		if(true){
			$http.post('api/v1/step4Create', formData).success(function(sucess) {
				if(sucess){
					window.location = '#/ansog-trin5' + ($rootScope.isDanish ? '/da' : '/en' );
				}
			});
		}
		else{
			// $(".alert_message").fadeIn("fast");
		}		
	}

	$(".alert_message").click(function(){
		$(this).fadeOut("fast");
	});
	  
}]);

// Regsiter Step 5
main.controller('RegisterStep5Controller', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {  
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 

	$http.get('api/v1/getlanguages' + ($rootScope.isDanish ? '' : '/en')).success(function(languagesdropdown) {
		$scope.languagesdropdown = languagesdropdown
	});

	$scope.languagesListDropdown = function() {
		return this.languagesdropdown;
	}; 
	
	$http.get('api/v1/getlanguageratings').success(function(languageratings) {
		$scope.languageratings = languageratings
	});

	$scope.setlangraing = setlangraing;
	function setlangraing(langrowid,langrateid) {  
		if($('#lang'+langrowid).val()) {
			for (i = 1; i <= 4; i++) {
				if(i <= langrateid)
					$('#start_'+langrowid+'_'+i).attr('src','images/star-white.png');
				else
					$('#start_'+langrowid+'_'+i).attr('src','images/star-black.png');
			}
			$('#langrateval'+langrowid).val(langrateid);
		}
	};
 	$http.get('api/v1/checkstep5', {params: {limit: limit}}).success(function(response) {
		if((response.step1status) && (response.step2status) && (response.step3status) && (response.step4status)){
			if(response.step5status){
				$scope.lang1 = response.step5.lang1;
				$scope.lang2 = response.step5.lang2;
				$scope.lang3 = response.step5.lang3;
				$scope.lang4 = response.step5.lang4;
				$scope.langrateval1 = response.step5.langrateval1;
				$scope.langrateval2 = response.step5.langrateval2;
				$scope.langrateval3 = response.step5.langrateval3;
				$scope.langrateval4 = response.step5.langrateval4;
				$scope.dealekter1 = response.step5.dealekter1;
				$scope.dealekter2 = response.step5.dealekter2;
				$scope.dealekter3 = response.step5.dealekter3;
				if($scope.langrateval1){
					var selrateval = $scope.langrateval1;
					for (i = 1; i <=4; i++) {
						if(i <= selrateval)
							$('#start_1_'+i).attr('src','images/star-white.png');
					}
				}
				if($scope.langrateval2){
					var selrateval = $scope.langrateval2;
					for (i = 1; i <=4; i++) {
						if(i <= selrateval)
							$('#start_2_'+i).attr('src','images/star-white.png');
					}
				}
				if($scope.langrateval3){
					var selrateval = $scope.langrateval3;
					for (i = 1; i <=4; i++) {
						if(i <= selrateval)
							$('#start_3_'+i).attr('src','images/star-white.png');
					}
				}
				if($scope.langrateval4){
					var selrateval = $scope.langrateval4;
					for (i = 1; i <=4; i++) {
						if(i <= selrateval)
							$('#start_4_'+i).attr('src','images/star-white.png');
					}
				}
			}
		}else{
				window.location = '#/ansog-trin4' + ($rootScope.isDanish ? '/da' : '/en' );
		}
     });
	$scope.step5Create = step5Create;
	function step5Create() {  
			

		var formData = {
            lang1: $scope.lang1,
						lang2: $scope.lang2,
						lang3: $scope.lang3,
						lang4: $scope.lang4,
						langrateval1: $('#langrateval1').val(),
						langrateval2: $('#langrateval2').val(),
						langrateval3: $('#langrateval3').val(),
						langrateval4: $('#langrateval4').val(),
						dealekter1: $scope.dealekter1,
						dealekter2: $scope.dealekter2,
						dealekter3: $scope.dealekter3
						}
						if(formData.langrateval1 != '' || formData.langrateval2 != '' || formData.langrateval3 != '' || formData.langrateval4 != ''){
							$http.post('api/v1/step5Create', formData).success(function(sucess) {
								if(sucess){
									window.location = '#/ansog-trin6' + ($rootScope.isDanish ? '/da' : '/en' );
								}
							});	
						}
						else{
							alert($rootScope.isDanish ? "Vælg venligst og bedøm mindst et sprog." : "Please Choose and choose atleast one language.");
						}			
	  }

  $("select[id^=lang]").on("change",function(){
    if($(this).val() == ""){
      var row_id = this.id.slice(-1);
      $("input[id=langrateval"+row_id+"]").val("");
      $("span.ratings img[id^=start_"+row_id+"]").attr("src","images/star-black.png");
		}
		else{
			var row_id = this.id.slice(-1);
			$(".choose-level").addClass("white-color");
			$(".choose-language").addClass("white-color");
			if(row_id == '1'){
				$scope.langrateval1 = "4";
				$("input[id=langrateval"+row_id+"]").val("4");
				$("span.ratings img[id^=start_"+row_id+"]").attr("src","images/star-black.png");
				$("span.ratings img[id^=start_"+row_id+"_1]").attr("src","images/star-white.png");
				$("span.ratings img[id^=start_"+row_id+"_2]").attr("src","images/star-white.png");
				$("span.ratings img[id^=start_"+row_id+"_3]").attr("src","images/star-white.png");
				$("span.ratings img[id^=start_"+row_id+"_4]").attr("src","images/star-white.png");
			}
			else{
				$("input[id=langrateval"+row_id+"]").val("1");
      	$("span.ratings img[id^=start_"+row_id+"]").attr("src","images/star-black.png");
				$("span.ratings img[id^=start_"+row_id+"_1]").attr("src","images/star-white.png");
				switch (row_id) {
					case 2:
						$scope.langrateval2 = "1";
						break;
					case 3:
						$scope.langrateval3 = "1";
						break;
					case 4:
						$scope.langrateval4 = "1";
						break;
				
					default:
						break;
				}
			}
		}
  });
	  
}]);

main.controller('profileCreateController',['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {
	var operation = '';
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';

	var final_message = $rootScope.isDanish ? 'Tak for din oprettelse. Du modtager en mail fra os inden for 2 uger, når vi har kigget din ansøgning igennem.' : 'Thank you for submitting your application. You will receive an email from us within 2 weeks.';
	$scope.final_msg_caption = "Tak for din oprettelse!";
	$scope.final_msg_subcaption = "En bekræftigelse er sendt til din mail.";

  if($(".operation") != undefined){
    operation = $(".operation").val();
  }
  if(operation == 'update'){
  	$scope.profileinfo = JSON.parse(sessionStorage.getItem('profileinfo'));
		final_message = $rootScope.isDanish ? 'Profil opdateret' : 'Profile updated';
  }

	$scope.step7Create = step7Create;
	function step7Create() {  
		$scope.ifRegistring=true;		
		var fd = new FormData();
		localStorage.removeItem('imagecount');
		localStorage.removeItem('preview_html');
		$http.post('api/v1/step7Create',  fd, 
		{
			transformRequest: angular.identity,
			operation: operation,
			headers: {'Content-Type': undefined,'Process-Data': false}
		}
		).success(function(response) {
			if(response.status != undefined){
				$scope.ifRegistring=false;
				
				data = { "email": response.email, "first_name": response.first_name, "last_name": response.last_name, "profile_number": response.profile_number, "profile_id": response.profile_id };

				sessionStorage.setItem('registered_user_profile_id', data.profile_id);
				sessionStorage.setItem('registered_user_profile_number', data.profile_number);
				sessionStorage.setItem('registered_user_first_name', data.first_name);
				sessionStorage.setItem('registered_user_last_name', data.last_name);

				if(operation == "insert"){
					$.post("/api/v1/welcome_email", data,
							function (data, textStatus, jqXHR) {},
					);
				}
				window.location = '#/mediaupload' + ($rootScope.isDanish ? '/da' : '/en' );

			}
			else{
				alert($rootScope.isDanish ? "Ikke registreret, venligst prøv igen" : "Couldn't Register, Please Try again later");
				window.location = '#/ansog-trin1' + ($rootScope.isDanish ? '/da' : '/en' );
			}
		}).error(function(error){
			alert('Something went wrong, Please try again later'); 
			window.location = '#/ansog-trin1' + ($rootScope.isDanish ? '/da' : '/en' );
		});	
	}
}]);



main.controller('mediaUploadController',['$scope', '$http', '$rootScope', function($scope, $http, $rootScope) {
	let profile_id 					= sessionStorage.getItem('registered_user_profile_id');
	let loggedin_user_email	= sessionStorage.getItem('loginemail');
	let loggedin_user_info 	= sessionStorage.getItem('profileinfo');
	let mediaupload_source 	= sessionStorage.getItem('mediaupload_source');

	if(profile_id == undefined && loggedin_user_email == undefined && loggedin_user_info == undefined){
		window.location = '#/ansog-trin5' + ($rootScope.isDanish ? '/da' : '/en' );
	}else{
		if(mediaupload_source == 'update'){
			profile_id = JSON.parse(loggedin_user_info).id;
			$rootScope.interface = 'login';
		}
		else{
			$rootScope.interface = 'ansog';
		}

		$rootScope.bodylayout = 'black';
		
		let loadProfileMediaAjax = new XMLHttpRequest();
		loadProfileMediaAjax.addEventListener("load", loadProfileMedia);
		loadProfileMediaAjax.open("GET", "api/v1/getmediadata?profile_id="+profile_id);
		loadProfileMediaAjax.send();
	
		// $scope.profile_number 	= sessionStorage.getItem('registered_user_profile_number');
		// $scope.first_name 			= sessionStorage.getItem('registered_user_first_name');
		// $scope.last_name 			= sessionStorage.getItem('registered_user_last_name');
		$scope.profileinfo = JSON.parse(loggedin_user_info);
		let position = '';
	
		$(".reg-success-popup").click(function(event){
			$(this).fadeOut('fast');
			window.location = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
		});
		$(".imagefiles").on('click',".remove",function(event){
			let position = $(this).parent()[0].classList[1].replace('uploadinput','');
			let api_url = 'api/v1/mediafiledelete?type=image&profile_id='+profile_id+'&position='+position ;
			let formdata = {};
			let deleteAjax = new XMLHttpRequest();
			deleteAjax.inputdom = $(this);
			deleteAjax.addEventListener("load", deleteHandler, false);
			deleteAjax.addEventListener("error", errorHandler, false);
			deleteAjax.addEventListener("abort", abortHandler, false);
			deleteAjax.open("POST", api_url); 
			deleteAjax.send(formdata);
	
		});
		$(".videofiles").on('click',".remove",function(event){
			let position = $(this).parent()[0].classList[1].replace('uploadinput','');
			let api_url = 'api/v1/mediafiledelete?type=video&profile_id='+profile_id+'&position='+position ;
			let formdata = {};
			let deleteAjax = new XMLHttpRequest();
			deleteAjax.inputdom = $(this);
			deleteAjax.addEventListener("load", deleteHandler, false);
			deleteAjax.addEventListener("error", errorHandler, false);
			deleteAjax.addEventListener("abort", abortHandler, false);
			deleteAjax.open("POST", api_url); 
			deleteAjax.send(formdata);
		});
	
	
		$("input[name=usermedia]").change(function(event){
			$(".placeholder-text").hide();
			$(".placeholder-svg, .placeholder-svg img").show();
			$("input[name=usermedia]").attr('disabled',true);
			for(let i = 1; i<50; i++){
				if($(".imagefiles #uploadinput"+(i+1)).length > 0){
					let image_fileplace = $(".imagefiles #uploadinput"+i).val();
					if(image_fileplace == ''){
						imagePosition = i;
						break;
					}
				}
				else{
					let image_fileplace = $(".imagefiles #uploadinput"+i).val();
					if(image_fileplace == ''){
						imagePosition = i;
					}
					let newHTML = generateThumbnailHTML(i);
					$(".imagefiles").append(newHTML);
					$(".imagefiles .remove").bind('click');
					break;
				}
			}
	
			for(let i = 1; i<50; i++){
				if($(".videofiles #uploadinput"+(i+1)).length > 0){
					let video_fileplace = $(".videofiles #uploadinput"+i).val();
					if(video_fileplace == ''){
						videoPosition = i;
						break;
					}
				}
				else{
					let video_fileplace = $(".videofiles #uploadinput"+i).val();
					if(video_fileplace == ''){
						videoPosition = i;
					}
					let newHTML = generateThumbnailHTML(i);
					$(".videofiles").append(newHTML);
					$(".videofiles .remove").bind('click');
					break;
				}
			}
	
			var file = $(this)[0].files[0];
			let isImage = (file) => {
				return (file['type'].includes('image/j') || file['type'].includes('image/J') || file['type'].includes('image/p') || file['type'].includes('image/P'));
			}
			let isVideo = (file) => file['type'].includes('video');
			let api_url = '';
	
			if(isImage(file)){
				position = imagePosition;
				api_url = 'api/v1/fileuploadparser?profile_id='+profile_id+'&position='+position ;
				$(".imagefiles input#uploadinput"+position).val($(this).val());
			}
			if(isVideo(file)){
				position = videoPosition;
				api_url = 'api/v1/fileuploadparser?uploaded_file_type=video&profile_id='+profile_id+'&position='+position ;
				$(".videofiles input#uploadinput"+position).val($(this).val());
			}
			if(!isImage(file) && !isVideo(file)){
				if($rootScope.isDanish){
					alert('Du har uploaded et forkert filformat. Upload venligst JPG, JPEG eller PNG');
				}
				else{
					alert('You’ve uploaded a wrong file format. Please upload a JPG, JPEG or PNG');
				}
			}
			var formdata = new FormData();
			(isImage(file)) ? formdata.append("Image_file", file) : '';
			(isVideo(file)) ? formdata.append("Video_file", file) : '';
			var ajax = new XMLHttpRequest();
			ajax.inputdom = $(this);
			ajax.upload.inputdom = $(this);
			ajax.upload.addEventListener("progress", progressHandler, false);
			ajax.addEventListener("load", completeHandler, false);
			ajax.addEventListener("error", errorHandler, false);
			ajax.addEventListener("abort", abortHandler, false);
			ajax.open("POST", api_url); 
			ajax.send(formdata);
		});
		function _(el) {
			return document.getElementById(el);
		}
	
		function progressHandler(event) {
			var percent = (event.loaded / event.total) * 50;
		}
		function loadProfileMedia(event){
			let jsonresponse 	= JSON.parse(event.target.response);
			let imagesObject 	= jsonresponse.images;
			let videoObject		= jsonresponse.videos;

			if(imagesObject.length > 0){
				let img_slots_count = imagesObject.length;
				let img_fraction = img_slots_count / 3;
				let img_slot_rows = Math.floor(img_fraction); 
				if(img_fraction > Math.floor(img_fraction)){
					img_slot_rows = Math.floor(img_fraction) + 1;
				}

				for(let i = 4; i <= img_slot_rows; i++){
					let newHTML = generateThumbnailHTML((i-1)*3 , 'login');
					newHTML.replace('<span class="remove">X</span>','');
						$(".imagefiles").append(newHTML);
				}

				for(let image of imagesObject){
					$(".imagefiles input#uploadinput"+image.position).val(image.filename);
					let bg_img = "url('/images/uploads/" + image.filename + "')";
					let imageThumb = $(".imagefiles").find('.upload-img.uploadinput'+image.position);
					imageThumb.css('background-image',bg_img);
					imageThumb.unbind('mouseenter mouseleave');
					imageThumb.on('mouseenter mouseleave', function(){
						$(this).children().toggle();
					});
				}
			}
			if(videoObject.length > 0){
				console.log(videoObject);
				let img_slots_count = videoObject.length;
				let img_fraction = img_slots_count / 3;
				let img_slot_rows = Math.floor(img_fraction); 
				if(img_fraction > Math.floor(img_fraction)){
					img_slot_rows = Math.floor(img_fraction) + 1;
				}

				for(let i = 2; i <= img_slot_rows; i++){
					let newHTML = generateThumbnailHTML((i-1)*3 , 'login');
						$(".videofiles").append(newHTML);
				}

				for(let image of videoObject){
					$(".videofiles input#uploadinput"+image.position).val(image.filename);
					let bg_img = "'http://assets3.castit.dk"+image.thumbnail_photo_path+"/"+image.thumbnail_photo_filename+"'";
					// $thumbpath = 'http://assets3.castit.dk'.$row_video['thumbnail_photo_path']."/".$row_video['thumbnail_photo_filename'];
					let imageThumb = $(".videofiles").find('.upload-img.uploadinput'+image.position);
					imageThumb.css('background-image',bg_img);
					imageThumb.unbind('mouseenter mouseleave');
					imageThumb.on('mouseenter mouseleave', function(){
						$(this).children().toggle();
					});
				}
			}

			// console.log(jsonresponse);
		}
		function deleteHandler(event){
			let jsonresponse = JSON.parse(event.target.response);
			if(event.target.status == 200  &&  jsonresponse.status_message != undefined){
				let imageThumb = $("."+jsonresponse.type+"files").find('.upload-img.uploadinput'+jsonresponse.position);
				imageThumb.css('background-image','');
				imageThumb.removeClass("video_upload_notify");
				imageThumb.unbind('mouseenter mouseleave');
				imageThumb.children().hide();
				$("."+jsonresponse.type+"files #uploadinput"+jsonresponse.position).val("");
			}
		}
		function completeHandler(event) {
			$(".placeholder-text").show();
			$(".placeholder-svg, .placeholder-svg img").hide();
			$("input[name=usermedia]").attr('disabled', false);
			
			let jsonresponse = JSON.parse(event.target.response);
			
			if(event.target.status == 200  &&  jsonresponse.status_message != undefined){
	
				$scope.cdnfilename = jsonresponse.filename;
				let bg_img = "url('/images/uploads/" + $scope.cdnfilename + "')";
				let bg_temp_vdo = "url('/images/eclipse_loader.svg')";
				let bg_vdo = "'http://assets3.castit.dk"+jsonresponse.cdnfilepath+"/"+jsonresponse.thumbnail+"'";
				let mediatype = jsonresponse.type;
				$scope.cdnfilepath = jsonresponse.cdnfilepath;
	
				if(mediatype == 'image'){
					// console.log(bg_img);
					let imageThumb = $(".imagefiles").find('.upload-img.uploadinput'+jsonresponse.position);
					imageThumb.css('background-image',bg_img);
					imageThumb.unbind('mouseenter mouseleave');
					imageThumb.on('mouseenter mouseleave', function(){
						$(this).children().toggle();
					});
	
				}
				if(mediatype == 'video'){
					// '/images/eclipse_loader.svg';
					let imageThumb = $(".videofiles").find('.upload-img.uploadinput'+jsonresponse.position);
					// imageThumb.addClass('video_upload_notify');
					// imageThumb.css('background-image',bg_vdo);
					console.log(imageThumb);
					let label_text = '';
					label_text = ($rootScope.isDanish) ? "Fortsæt! - video uploader" : "Continue! - Video is uploading";
					imageThumb.text(label_text);
					// imageThumb.append(label_text);
					imageThumb.css("top","25%");
					imageThumb.css("color","#aaa");
					imageThumb.css("text-align","center");
					imageThumb.unbind('mouseenter mouseleave');
					imageThumb.on('mouseenter mouseleave', function(){
						$(this).children().toggle();
					});
				}
			}
		}
		$scope.showSuccessPopup = showSuccessPopup;
		function showSuccessPopup(){
			let imageExists = false;
			let input_fields = $("input[name^='upload']");
			// console.log(input_fields);
			$.each(input_fields, function(key, value){
				if(value.value){
					imageExists = true;
				}
			})
			console.log(imageExists);
			if(!imageExists){
				$(".image-reminder-popup").fadeIn();
				$(".image-reminder-popup").click(function(){
					$(".image-reminder-popup").fadeOut();
				});
			}
			else{
				sessionStorage.removeItem('registered_user_profile_id');
				sessionStorage.removeItem('registered_user_profile_number');
				sessionStorage.removeItem('registered_user_first_name');
				sessionStorage.removeItem('registered_user_last_name');
				sessionStorage.removeItem('profileinfo');
				sessionStorage.removeItem('loginemail');
	
				$http.post('api/v1/clearsessions');
				$(".reg-success-popup").fadeIn();
				$(".reg-success-popup").click(function(){
					$(".reg-success-popup").fadeOut();
					window.location = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
				});
			}
		}
	
		function errorHandler(event) {
			_("status").innerHTML = $rootScope.isDanish ? "Upload fejlede" : "Upload Failed";
		}
	
		function abortHandler(event) {
			_("status").innerHTML = $rootScope.isDanish ? "Upload afbrudt" : "Upload Aborted";
			$(".placeholder-text").show();
			$(".placeholder-svg").hide();
		}
		
		$scope.step6Create = step6Create;
		function step6Create() {  
			$scope.ifRegistring=true;
			
			var fd = new FormData();
			angular.forEach($scope.files,function(file){
				fd.append('Image_file[]',file._file);
			});
			angular.forEach($scope.videos,function(video){
				fd.append('Video_file["cdnfilename"]', $scope.cdnfilename);
				fd.append('Video_file["cdnfilepath"]', $scope.cdnfilepath);
				fd.append('Video_file["thumbnail"]', $scope.thumbnail);
				});
		
			$http.post('api/v1/step6Create',  fd, 
						{
							transformRequest: angular.identity,
							headers: {'Content-Type': undefined,'Process-Data': false}
						}
						).success(function(response) {
								window.location = '#/ansog-trin7' + ($rootScope.isDanish ? '/da' : '/en' );
							}
						
				
				).error(function(error){
					alert('Something went wrong, Please try again later'); 
					window.location = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
				});				
			}
			
			function generateThumbnailHTML(offset, mode = ''){				
				let html_string = `<div class="upload-box-row" id="upload-box-row">
				<div class="upload-box">
				<input type="hidden" name="uploadinput${offset + 1}" class="uploadinput" id="uploadinput${offset + 1}">
				<label for="uploadinput${offset + 1}" class="upload-img uploadinput${offset + 1}">
					<span class="remove">X</span>
				</label>
				</div>
				<div class="upload-box">
				<input type="hidden" name="uploadinput${offset + 2}" class="uploadinput" id="uploadinput${offset + 2}">
				<label for="uploadinput${offset + 2}" class="upload-img uploadinput${offset + 2}">
					<span class="remove">X</span>
				</label>
				</div>
				<div class="upload-box">
				<input type="hidden" name="uploadinput${offset + 3}" class="uploadinput" id="uploadinput${offset + 3}">
				<label for="uploadinput${offset + 3}" class="upload-img uploadinput${offset + 3}">
					<span class="remove">X</span>
				</label>
				</div>
			</div>`;
			if(mode == 'login'){
				html_string = `<div class="upload-box-row" id="upload-box-row">
				<div class="upload-box">
				<input type="hidden" name="uploadinput${offset + 1}" class="uploadinput" id="uploadinput${offset + 1}">
				<label for="uploadinput${offset + 1}" class="upload-img uploadinput${offset + 1}">
				</label>
				</div>
				<div class="upload-box">
				<input type="hidden" name="uploadinput${offset + 2}" class="uploadinput" id="uploadinput${offset + 2}">
				<label for="uploadinput${offset + 2}" class="upload-img uploadinput${offset + 2}">
				</label>
				</div>
				<div class="upload-box">
				<input type="hidden" name="uploadinput${offset + 3}" class="uploadinput" id="uploadinput${offset + 3}">
				<label for="uploadinput${offset + 3}" class="upload-img uploadinput${offset + 3}">
				</label>
				</div>
			</div>`;
			}
				return html_string;
			}
	
	}
	
}]);

// Regsiter Step 6
main.controller('RegisterStep6Controller_old', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {
  var operation = '';
	var final_message = $rootScope.isDanish ? 'Tak for din oprettelse. Du modtager en mail fra os inden for 2 uger, når vi har kigget din ansøgning igennem.' : 'Thank you for submitting your application. You will receive an email from us within 2 weeks.';
  if($(".operation") != undefined){
    operation = $(".operation").val();
  }
  if(operation == 'update'){
  	$scope.profileinfo = JSON.parse(sessionStorage.getItem('profileinfo'));
		final_message = $rootScope.isDanish ? 'Profil opdateret' : 'Profile updated';
  }

  // document.getElementById("ansigt_text_field").value    = (sessionStorage.getItem('ansigt_text_field')) ? sessionStorage.getItem('ansigt_text_field') : "" ;
  // document.getElementById("profil_text_field").value    = (sessionStorage.getItem('profil_text_field')) ? sessionStorage.getItem('profil_text_field') : "" ;
  // document.getElementById("helfigur_text_field").value  = (sessionStorage.getItem('helfigur_text_field')) ? sessionStorage.getItem('helfigur_text_field') : "";
  // document.getElementById("ekstra_text_field").value    = (sessionStorage.getItem('ekstra_text_field')) ? sessionStorage.getItem('ekstra_text_field') : "";
  // document.getElementById("ekstra1_text_field").value   = (sessionStorage.getItem('ekstra1_text_field')) ? sessionStorage.getItem('ekstra1_text_field') : "";
  // document.getElementById("ekstra2_text_field").value   = (sessionStorage.getItem('ekstra2_text_field')) ? sessionStorage.getItem('ekstra2_text_field') : "";
  
  // document.getElementById("hand_text_field").value      = (sessionStorage.getItem('hand_text_field')) ? sessionStorage.getItem('hand_text_field') : "";
  // document.getElementById("foot_text_field").value      = (sessionStorage.getItem('foot_text_field')) ? sessionStorage.getItem('foot_text_field') : "";
  // document.getElementById("family_text_field").value    = (sessionStorage.getItem('family_text_field')) ? sessionStorage.getItem('family_text_field') : "";
  // document.getElementById("tvilling_text_field").value  = (sessionStorage.getItem('tvilling_text_field')) ? sessionStorage.getItem('tvilling_text_field') : "";
  
  // document.getElementById("video1_text_field").value    = (sessionStorage.getItem('video1_text_field')) ? sessionStorage.getItem('video1_text_field') : "";
  // document.getElementById("video2_text_field").value    = (sessionStorage.getItem('video2_text_field')) ? sessionStorage.getItem('video2_text_field') : "";
  // document.getElementById("video3_text_field").value    = (sessionStorage.getItem('video3_text_field')) ? sessionStorage.getItem('video3_text_field') : "";
  // document.getElementById("video4_text_field").value    = (sessionStorage.getItem('video4_text_field')) ? sessionStorage.getItem('video4_text_field') : "";



	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	var limit='';	 
	$scope.files = []; 
	$scope.agree;
	$scope.ifRegistring=false;
 	// $http.get('api/v1/step6', {params: {limit: limit}}).success(function(lostepets) {
	//  });
	$http.get('api/v1/checkstep5', {params: {limit: limit}}).success(function(response) {
		if((!response.step1status) && (!response.step2status) && (!response.step3status) && (!response.step4status)){
			window.location = '#/ansog-trin4' + ($rootScope.isDanish ? '/da' : '/en' );
		}
	});
		
	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown
	});
	$scope.countriesListDropdown = function() {
		return this.countriesdropdown;
	}; 
	
  $scope.progress = {
    min: 0,
    max: 100
  };

  $scope.uploadFile = function(event){
    var files = event.target.files;
    var file = files[0];

    switch($(this)[0].id) {
      case "ansigt":
        sessionStorage.setItem('ansigt_text_field', file.name);
        break;
      case "profil":
        sessionStorage.setItem('profil_text_field', file.name);
        break;
      case "helfigur":
        sessionStorage.setItem('helfigur_text_field', file.name);
        break;
      case "ekstra":
        sessionStorage.setItem('ekstra_text_field', file.name);
        break;
      case "ekstra1":
        sessionStorage.setItem('ekstra1_text_field', file.name);
        break;
      case "ekstra2":
        sessionStorage.setItem('ekstra2_text_field', file.name);
        break;

      case "hænder":
        sessionStorage.setItem('hand_text_field', file.name);
        break;
      case "fødder":
        sessionStorage.setItem('foot_text_field', file.name);
        break;
      case "familie":
        sessionStorage.setItem('family_text_field', file.name);
        break;
      case "tvilling":
        sessionStorage.setItem('tvilling_text_field', file.name);
        break;


    }

    var formdata = new FormData();
    formdata.append("Image_file[]", file);
    var ajax = new XMLHttpRequest();
    ajax.inputdom = $(this);
    ajax.upload.inputdom = $(this);
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.addEventListener("error", errorHandler, false);
    ajax.addEventListener("abort", abortHandler, false);
    ajax.open("POST", "api/v1/fileuploadparser"); 
    // http://www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP
    //use file_upload_parser.php from above url
    ajax.send(formdata);
  }

  $scope.uploadFile_video = function(event){
    var files = event.target.files;
    var file = files[0];

    switch($(this)[0].id) {
      case "video1":
        sessionStorage.setItem('video1_text_field', file.name);
        break;
      case "video2":
        sessionStorage.setItem('video2_text_field', file.name);
        break;
      case "video3":
        sessionStorage.setItem('video3_text_field', file.name);
        break;
      case "video4":
        sessionStorage.setItem('video4_text_field', file.name);
        break;
    }

    var formdata = new FormData();
    formdata.append("Video_file[]", file);
    var ajax = new XMLHttpRequest();
    ajax.inputdom = $(this);
    ajax.upload.inputdom = $(this);
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.addEventListener("error", errorHandler, false);
    ajax.addEventListener("abort", abortHandler, false);
    ajax.open("POST", "api/v1/fileuploadparser?uploaded_file_type=video"); 
    // http://www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP
    //use file_upload_parser.php from above url
    ajax.send(formdata);
  }

  function _(el) {
    return document.getElementById(el);
  }

  function progressHandler(event) {
    var percent = (event.loaded / event.total) * 50;
    event.target.inputdom.siblings("progress")[0].style.display = "table-row";
    event.target.inputdom.siblings("progress")[0].value = Math.round(percent);
    // _("status").innerHTML = Math.round(percent) + "% uploaded... please wait";
    // _("loaded_n_total").innerHTML = "Uploaded " + event.loaded + " bytes of " + event.total;
  }

  function completeHandler(event) {
    if(event.target.status == 200  &&  JSON.parse(event.target.response).status_message != undefined){
      event.target.inputdom.siblings().find("input").prevObject[0].value = JSON.parse(event.target.response).filename;
      $scope.cdnfilename = JSON.parse(event.target.response).filename;
      $scope.cdnfilepath = JSON.parse(event.target.response).cdnfilepath;
      $scope.fieldone = $scope.cdnfilename;

      event.target.inputdom.siblings("progress")[0].value = 100; //will clear progress bar after successful upload
      // do sessions storage for file details
    }
  };

  function errorHandler(event) {
    _("status").innerHTML = $rootScope.isDanish ? "Upload fejlede" : "Upload Failed";
  }

  function abortHandler(event) {
    _("status").innerHTML = $rootScope.isDanish ? "Upload afbrudt" : "Upload Aborted";
  }

	$scope.step6Create = step6Create;
	function step6Create() {  
		$scope.ifRegistring=true;
		

		var fd = new FormData();
		angular.forEach($scope.files,function(file){
		  fd.append('Image_file[]',file._file);
		});
		angular.forEach($scope.videos,function(video){
      fd.append('Video_file["cdnfilename"]', $scope.cdnfilename);
			fd.append('Video_file["cdnfilepath"]', $scope.cdnfilepath);
			fd.append('Video_file["thumbnail"]', $scope.thumbnail);
	  	});
	
		$http.post('api/v1/step6Create',  fd, 
					{
						transformRequest: angular.identity,
            operation: operation,
						headers: {'Content-Type': undefined,'Process-Data': false}
					}
					).success(function(response) {
						
					if(response.status){
						$scope.ifRegistring=false;
						console.log(response);
						data = { "email": response.email };
						if(operation == "insert"){
						$.post("/api/v1/welcome_email", data,
								function (data, textStatus, jqXHR) {},
						);
						}

						alert(final_message);
						window.location = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
					}
					else{
						alert($rootScope.isDanish ? "Ikke registreret, venligst prøv igen" : "Couldn't Register, Please Try again later");
						window.location = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
					}
					
			
			}).error(function(error){
				alert('Something went wrong, Please try again later'); 
				window.location = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
			});				
	  }

	  
}]);

main.directive('ngFileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            var model = $parse(attrs.ngFileModel);
            var isMultiple = attrs.multiple;
            var modelSetter = model.assign;
            element.bind('change', function () {
				var values = [];
			
				var value = {
					
					 name: element[0].files[0].name,
					 size: element[0].files[0].size,
					 url: URL.createObjectURL(element[0].files[0]),
					 _file: element[0].files[0]
				 };
				 
				 values.push(value);
                scope.$apply(function () {
                    if (isMultiple) {
                        modelSetter(scope, values);
                    } else {
                        modelSetter(scope, values[0]);
                    }
                });
            });
        }
    };
}]);
/*
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
						address: $scope.address,
						city: $scope.city,
						country_id: $scope.country_id
						}
		$http.post('api/v1/step7Create', formData).success(function(sucess) {
			if(sucess){
				window.location = '#/success' + ($rootScope.isDanish ? '/da' : '/en' );
			}
		});				
	  }

	  
}]);
*/
// Footer
main.controller('FooterController', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', 'FlashService', function($scope, $filter, $http, $window, $rootScope, $routeParams, FlashService) {}]);




main.controller('ResetPasswordController', ['$scope', '$filter', '$http', '$window', '$rootScope', '$routeParams', function($scope, $filter, $http, $window, $rootScope, $routeParams) {
	function getUrlVars(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++){
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
	}

	let query_param = getUrlVars();
	$scope.resetPasswordFormSubmit = function(){
		if($scope.resetpassword2 != $scope.resetpassword1){
			$(".alert_message").fadeIn("fast");
		}
		else{
			if($scope.resetpassword2 === $scope.resetpassword1){
				var user_data = {
					resetpassword : $scope.resetpassword1,
					email: query_param.email,
					resethash: query_param.resethash
				};
				if(query_param.type == 'customer'){
					user_data.type = 'customer';
				}
				$.post('api/v1/newresetpassword.php', user_data).done(
					function(data){
						if (data == 'success') {
							$(".success_message").fadeIn("fast");
							setTimeout(function(){
								window.location.href = '#/login' + ($rootScope.isDanish ? '/da' : '/en' );
							}, 5000)
						}
						else {

						}
					}
				);
			}
		}
	}

	$(".alert_message").click(function(){
		$(".alert_message").fadeOut("fast");
	});
	$(".success_message").click(function(){
		$(".alert_message").fadeOut("fast");
	});

}]);



//Update Profile details   
main.controller('MyProfileController', ['$scope', '$rootScope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $rootScope, $filter, $http, $window, $routeParams, FlashService) {  
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
 
//Update Customer details   
main.controller('EditUserController', ['$scope', '$rootScope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $rootScope, $filter, $http, $window, $routeParams, FlashService) {  
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
		window.location = '#/my-profile' + ($rootScope.isDanish ? '/da' : '/en' );
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
					window.location = '#/edit-profile' + ($rootScope.isDanish ? '/da' : '/en' );
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
main.controller('ViewUserController', ['$scope', '$rootScope', '$filter', '$http', '$window', '$routeParams', 'FlashService', function($scope, $rootScope, $filter, $http, $window, $routeParams, FlashService) {  
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
main.controller('Imageupload',['$scope', '$rootScope', function($scope, $rootScope) {
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
		window.location = '#/my-profile' + ($rootScope.isDanish ? '/da' : '/en' );
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

main.controller('AngularLoginController', ['$scope', '$filter', '$http', '$window', '$rootScope', 'FlashService', '$cookies', function($scope, $filter, $http, $window, $rootScope, FlashService, $cookies) { 
	
	$(document).ready(function(){
		$('.main-menu-trigger').click(function(){
			$('.landing-wrapper').toggleClass('menu-active');
		});

		$('.popup-close-btn').click(function(){
			$('.landing-wrapper').removeClass('menu-active');
			$('.res_menu').removeClass('active');
			$(".res_menu ul li").fadeOut("slow");
		});

		/*
		$("input[name='proceedNormal']").click(function(){
			$cookies.customer_open = 'true';
			window.location.href = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
		});
		*/
	});

	this.loginForm = function() {

	var user_data = {
		user_email : this.inputData.email,
		user_password : this.inputData.password,
	};
	// console.log(user_data);
	
	

	$.post('api/v1/login.php', user_data).done(
		function(data){
		if (data !== 'wrong') {
				data = JSON.parse(data);
				// console.log(data.email);
				sessionStorage.setItem('loginemail', data.email);
			$rootScope.globals.currentUser = data;
			$scope.isLoggedIn = true;
			window.location.href = '#/my-profile_1' + ($rootScope.isDanish ? '/da' : '/en' );
			}
			else {
			$scope.errorMsg = $rootScope.isDanish ? "Forkert email/password" : "Invalid Email/Password";
			$(".login-error-message").html($rootScope.isDanish ? "Brugernavn og/eller adgangskode kan ikke genkendes." : "Username or password is not recognized.");
		}
	}
	);
	}

	this.customerLoginForm = function(){
		// console.log(this.inputData);
		$http.post('api/v1/customer-login', this.inputData).success(function(response) {
			console.log(response);
			if(response.status == 'success'){
				// alert(response.message);
				window.location.href = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
			}else{
				if($rootScope.isDanish){
					alert(response.message_dk);
				}
				else{
					alert(response.message_en);
				}
			}
		});
	}

	$(".login_link").click(function(){
		if($("#login_email").hasClass('ng-valid') && $("#login_email").val()!=""){
			$("#login_email").removeClass("blue-login");

			$("input[type=submit].login_button1").addClass("blue-login");
			$("input[type=submit].login_button1").val("Send Email");
			$("input[type=submit].login_button1").attr("type","button");

			$("input[type=button].blue-login").click(function(){
				$(".login-error-message").html('<img alt="loading" src="assets/loader/3.gif" width="15%" height="15%">');
				let user_email = $("#login_email").val();
				if(user_email != "" && user_email != undefined){
					$http({
						method: 'POST',
						url: 'api/v1/resetpassword',
						data: {email: user_email, type: 'profile'},
						headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					})
					.success(function(data) {
						if($rootScope.isDanish){
							$(".login-error-message").html(data.message);
						}
						else{
							$(".login-error-message").html(data.message_en);
						}
						setTimeout(function(){
							location.reload();
						}, 10000);     
					});
				}
				else{
					$(".login-error-message").html("Email skal udfyldes.");
				}
				
			});
		}
		else{
			$("#login_email").addClass("blue-login");

			$("input[type=submit].login_button1").addClass("blue-login");
			$("input[type=submit].login_button1").val("Send Email");
			$("input[type=submit].login_button1").attr("type","button");

			$("input[type=button].blue-login").click(function(){
				$(".login-error-message").html('<img alt="loading" src="assets/loader/3.gif" width="15%" height="15%">');
				let user_email = $("#login_email").val();
				if(user_email != "" && user_email != undefined){
					$http({
						method: 'POST',
						url: 'api/v1/resetpassword',
						data: {email: user_email, type: 'profile'},
						headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					})
					.success(function(data) {
						$(".login-error-message").html(data.message);
						setTimeout(function(){
							location.reload();
						}, 10000);
					});
				}
				else{
					$(".login-error-message").html("Email skal udfyldes.");
				}
			});
		}
	});

	$(".customer_login_link").click(function(){
		if($("#customer_login_email").hasClass('ng-valid') && $("#customer_login_email").val()!=""){
			$("#customer_login_email").removeClass("blue-login");

			$("input[type=submit].customer_login_button1").addClass("blue-login");
			$("input[type=submit].customer_login_button1").val("Send Email");
			$("input[type=submit].customer_login_button1").attr("type","button");

			$("input[type=button].blue-login").click(function(){
				$(".customer-login-error-message").html('<img alt="loading" src="assets/loader/3.gif" width="35px" height="35px">');
				let user_email = $("#customer_login_email").val();
				if(user_email != "" && user_email != undefined){
					$http({
						method: 'POST',
						url: 'api/v1/resetpassword',
						data: {email: user_email, type: 'customer'},
						headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					})
					.success(function(data) {
						if($rootScope.isDanish){
							$(".customer-login-error-message").html(data.message);
						}
						else{
							$(".customer-login-error-message").html(data.message_en);
						}
						setTimeout(function(){
							location.reload();
						}, 10000);     
					});
				}
				else{
					$(".customer-login-error-message").html("Email skal udfyldes.");
				}
				
			});
		}
		else{
			$("#customer_login_email").addClass("blue-login");

			$("input[type=submit].customer_login_button1").addClass("blue-login");
			$("input[type=submit].customer_login_button1").val("Send Email");
			$("input[type=submit].customer_login_button1").attr("type","button");

			$("input[type=button].blue-login").click(function(){
				$(".customer-login-error-message").html('<img alt="loading" src="assets/loader/3.gif" width="15%" height="15%">');
				let user_email = $("#customer_login_email").val();
				if(user_email != "" && user_email != undefined){
					$http({
						method: 'POST',
						url: 'api/v1/resetpassword',
						data: {email: user_email, type: 'customer'},
						headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					})
					.success(function(data) {
						$(".customer-login-error-message").html(data.message);
						setTimeout(function(){
							location.reload();
						}, 10000);
					});
				}
				else{
					$(".customer-login-error-message").html("Email skal udfyldes.");
				}
			});
		}
	});

}]);



main.controller('MyProfileController1',['$scope', '$rootScope','$http', function($scope, $rootScope, $http, $cookies){
	// var cookies_current = document.cookie;
	var argument_data = {
		email : sessionStorage.getItem('loginemail'),
	};
  // $http.post('api/v1/getprofileinfo.php', { cookies_current: cookies_current})
	$http.get('api/v1/countries').success(function(countriesdropdown) {
		$scope.countriesdropdown = countriesdropdown;
	});
	
	$http({
		method: 'POST',
		url: 'api/v1/getprofileinfo.php',
		data: {email : sessionStorage.getItem('loginemail')},
		// headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		headers: {'Content-Type': 'application/json'}
	})
	.success(function (response) {
     $scope.profileinfo = response;
     $scope.selectedCountry_id = $scope.profileinfo.country_id;
     sessionStorage.setItem('profileinfo', JSON.stringify(response));
   });
	 
	/*
	$.post('api/v1/getprofileinfo.php', argument_data).done(
		function (response) {
			console.log(response);
			// response = JSON.parse(response);
			// $scope.profileinfo = response;
			var result_profileinfo = response;
			$scope.selectedCountry_id = $scope.profileinfo.country_id;
			sessionStorage.setItem('profileinfo', JSON.stringify(response));
		}
	);
	*/
	
	// $scope.profileinfo = result_profileinfo;
 	$scope.step1Update = step1Update;
	function step1Update() {  
		var formData = {first_name: $scope.profileinfo.first_name,
						last_name: $scope.profileinfo.last_name,
						zipcode: $scope.profileinfo.zipcode,
						address: $scope.profileinfo.address,
						city: $scope.profileinfo.city,
						country_id: $scope.profileinfo.country_id,
            password: $scope.profileinfo.password,
            hashed_password: $scope.profileinfo.hashed_password
						}
						if(typeof $scope.profileinfo.password == 'string' ){
							formData.password = $scope.profileinfo.password;
						}
		$http.post('api/v1/step1Create', formData).success(function(response) {
			if(response.success){
				sessionStorage.setItem('profileinfo', JSON.stringify($scope.profileinfo));
				window.location = '#/my-profile_2' + ($rootScope.isDanish ? '/da' : '/en' );
			}
		});				
  }
}]);

main.controller('MyProfileController2',['$scope', '$rootScope','$http', function($scope, $rootScope, $http, $cookies){
  $scope.profileinfo = JSON.parse(sessionStorage.getItem('profileinfo'));
  var birth_array = $scope.profileinfo.birthday.split('-');
  $scope.profileinfo.birth_day    = birth_array[2];
  $scope.profileinfo.birth_month  = +birth_array[1];
  $scope.profileinfo.birth_year   = birth_array[0];
	
	// console.log($scope.profileinfo);

  $http.get('api/v1/years').success(function(yearsdropdown) {
    $scope.yearsdropdown = yearsdropdown
  });
  var limit='';
  $http.get('api/v1/getstep2data', {params: {limit: limit}}).success(function(response) {
    $scope.gender=response.gender;
  });
  // var birth_month = ("0" + $scope.profileinfo.birth_month).slice(-2);
  // $("#birth_day option[value="+$scope.profileinfo.birth_day+"]").attr("selected","selected");
	// $("#birth_day option[value="+$scope.profileinfo.birth_day+"]").prop('selected');
	$("#birth_day").val($scope.profileinfo.birth_day);

  // $("#birth_month option[value="+$scope.profileinfo.birth_month+"]").attr("selected","selected");
  // $("#birth_month option[value="+$scope.profileinfo.birth_month+"]").prop('selected');
	$("#birth_month").val($scope.profileinfo.birth_month);

	// $("#birth_year option[value="+$scope.profileinfo.birth_year+"]").attr("selected","selected");
	// $("#birth_year option[value="+$scope.profileinfo.birth_year+"]").prop('selected');
	$("#birth_year").val($scope.profileinfo.birth_year);

	// $("#gender_id option[value='"+$scope.profileinfo.gender_id+"']").attr("selected","selected");
	$("#gender_id").val($scope.profileinfo.gender_id);
  
  $scope.step2Update = step2Update;
  function step2Update() {
		$scope.profileinfo.email = $("#email").val();
		$scope.profileinfo.phone = $("#phone").val();
		$scope.profileinfo.phone_at_work = $("#phone_at_work").val();
		$scope.profileinfo.gender_id = $("#gender_id").val();
		$scope.profileinfo.birth_day = $("#birth_day").val();
		$scope.profileinfo.birth_month = $("#birth_month").val();
		$scope.profileinfo.birth_year = $("#birth_year").val();
		$scope.profileinfo.ethnic_origin = $("#ethnic_origin").val();
		$scope.profileinfo.job = $("#job").val();
    if($scope.profileinfo.ethnic_origin == undefined){
      $scope.profileinfo.ethnic_origin = '';
    }
    var formData = {
          email: $scope.profileinfo.email,
          phone: $scope.profileinfo.phone,
          phone_at_work: $scope.profileinfo.phone_at_work,
          gender_id: $scope.profileinfo.gender_id,
          birth_day: $scope.profileinfo.birth_day,
          birth_month: $scope.profileinfo.birth_month,
          birth_year: $scope.profileinfo.birth_year,
          ethnic_origin: $scope.profileinfo.ethnic_origin,
          job: $scope.profileinfo.job
        }
    $http.post('api/v1/step2Create', formData).success(function(response) {
      if(response.success){
				sessionStorage.setItem('profileinfo', JSON.stringify($scope.profileinfo));
        window.location = '#/my-profile_3' + ($rootScope.isDanish ? '/da' : '/en' );
      }
    });       
  }
}]);

main.controller('MyProfileController3',['$scope', '$rootScope','$http', function($scope, $rootScope, $http, $cookies){
	$scope.profileinfo = JSON.parse(sessionStorage.getItem('profileinfo'));
	
	let loaded_info = $scope.profileinfo;
  $scope.gender_id = $scope.profileinfo.gender_id;
  
  var birthday = +new Date($scope.profileinfo.birthday);
  $scope.age = ~~((Date.now() - birthday) / (31557600000));   
  var limit = '';
//   $scope.showKidSizes = false;
  function clearValues(){
	  $scope.shirt_size_from	= '-';	$scope.shirt_size_to= '-'; 	$scope.pants_size_from	= '-'; 	$scope.pants_size_to='-';
	  $scope.shoe_size_from 	= '-';	$scope.shoe_size_to	= '-';	$scope.suite_size_from 	= '-';	$scope.suite_size_to= '-';
	  $scope.children_sizes 	= '-';	$scope.eye_color_id = '-';	$scope.hair_color_id 	= '-';	$scope.bra_size 	= '';
	  $scope.height 			= '';	$scope.weight 		= '';
  }
  $("#showKidSizes").change(function(event){
	  clearValues();
	  if(event.target.checked){
		  $scope.showKidSizes = true;
	  }else{
		  $scope.showKidSizes = false;
	  }
  });

  $http.get('api/v1/getstep3data', {params: {limit: limit}}).success(function(response) {
    $scope.gender=response.genders;
    $scope.eye_colors=response.eye_colors;
    $scope.hair_colors=response.hair_colors;
  });

	/*
  // $("#hair_color_id option[value='"+$scope.profileinfo.hair_color_id+"']").attr("selected","selected");
	$("#hair_color_id").val($scope.profileinfo.hair_color_id);

	// $("#eye_color_id option[value='"+$scope.profileinfo.eye_color_id+"']").attr("selected","selected");  
	$("#eye_color_id").val($scope.profileinfo.eye_color_id);
	
	// $("#shirt_size_from option[value='"+$scope.profileinfo.shirt_size_from+"']").attr("selected","selected");
	$("#shirt_size_from").val($scope.profileinfo.shirt_size_from);

	// $("#shirt_size_to option[value='"+$scope.profileinfo.shirt_size_to+"']").attr("selected","selected");
	$("#shirt_size_to").val($scope.profileinfo.shirt_size_to);

  // $("#pants_size_from option[value='"+$scope.profileinfo.pants_size_from+"']").attr("selected","selected");
	$("#pants_size_from").val($scope.profileinfo.pants_size_from);

	// $("#pants_size_to option[value='"+$scope.profileinfo.pants_size_to+"']").attr("selected","selected");
	$("#pants_size_to").val($scope.profileinfo.pants_size_to);

  // $("#shoe_size_from option[value='"+$scope.profileinfo.shoe_size_from+"']").attr("selected","selected");
	$("#shoe_size_from").val($scope.profileinfo.shoe_size_from);

	// $("#shoe_size_to option[value='"+$scope.profileinfo.shoe_size_to+"']").attr("selected","selected");
	$("#shoe_size_to").val($scope.profileinfo.shoe_size_to);
  
  // $("#suite_size_from option[value='"+$scope.profileinfo.suite_size_from+"']").attr("selected","selected");
	$("#suite_size_from").val($scope.profileinfo.suite_size_from);

	// $("#suite_size_to option[value='"+$scope.profileinfo.suite_size_to+"']").attr("selected","selected");
	$("#suite_size_to").val($scope.profileinfo.suite_size_to);
  
  // $("#bra_size option[value='"+$scope.profileinfo.bra_size+"']").attr("selected","selected");
	$("#bra_size").val($scope.profileinfo.bra_size);
	
	*/

  $scope.step3Update = step3Update;
  function step3Update() {
		let updated_info = $scope.profileinfo;
		let information_to_be_saved = {...loaded_info, ...updated_info};
		information_to_be_saved = JSON.stringify(information_to_be_saved);
		sessionStorage.setItem('profileinfo', information_to_be_saved);

    var formData = {
            shirt_size_from: $scope.profileinfo.shirt_size_from,
            shirt_size_to: $scope.profileinfo.shirt_size_to,
            pants_size_from: $scope.profileinfo.pants_size_from,
            pants_size_to: $scope.profileinfo.pants_size_to,
            shoe_size_from: $scope.profileinfo.shoe_size_from,
            shoe_size_to: $scope.profileinfo.shoe_size_to,
            suite_size_from: $scope.profileinfo.suite_size_from,
            suite_size_to: $scope.profileinfo.suite_size_to,
            children_sizes: $scope.profileinfo.children_sizes,
            eye_color_id: $scope.profileinfo.eye_color_id,
            hair_color_id: $scope.profileinfo.hair_color_id,
            bra_size: $scope.profileinfo.bra_size,
            height: $scope.profileinfo.height,
            weight: $scope.profileinfo.weight,
				}
				
    $http.post('api/v1/step3Create', formData).success(function(response) {
      if(response.success){
				sessionStorage.setItem('profileinfo', JSON.stringify($scope.profileinfo));
        window.location = '#/my-profile_4' + ($rootScope.isDanish ? '/da' : '/en' );
      }
    });       
  }
}]);


main.controller('MyProfileController4',['$scope', '$rootScope','$http', function($scope, $rootScope, $http, $cookies){
	$scope.profileinfo = JSON.parse(sessionStorage.getItem('profileinfo'));
	// console.log('loaded data',$scope.profileinfo);
  var limit='';  
	let loaded_info = $scope.profileinfo;
	$http.get('api/v1/getcategories' + ($rootScope.isDanish ? '' : '/en')).success(function(categoriesdropdown) {
		$scope.categoriesdropdown = categoriesdropdown
	});
	$http.get('api/v1/getskills' + ($rootScope.isDanish ? '' : '/en')).success(function(skillsdropdown) {
		$scope.skillsdropdown = skillsdropdown
	});
	$http.get('api/v1/getlicences' + ($rootScope.isDanish ? '' : '/en')).success(function(licencesdropdown) {
		$scope.licencesdropdown = licencesdropdown
	});

  $scope.selectedcategories = $scope.profileinfo.categories ? $scope.profileinfo.categories.toString() : '';
  $scope.selectedskills = $scope.profileinfo.skills ? $scope.profileinfo.skills.toString() : '';
  $scope.selectedlicences = $scope.profileinfo.licenses ? $scope.profileinfo.licenses.toString() : '';

	console.log($scope.selectedcategories, $scope.selectedskills, $scope.selectedlicences);

	$scope.$on('ngRepeatFinished', function(ngRepeatFinishedEvent) {
		if($scope.profileinfo.categories) {
			for (var c of $scope.profileinfo.categories) {
				$(".form_box span#catbut"+c).removeClass('button1');
				$(".form_box span#catbut"+c).addClass('button2');
				$(".form_box span#catspan"+c).removeClass('plus-icon');
				$(".form_box span#catspan"+c).addClass('close-icon');
			};
		}
		if($scope.profileinfo.skills) {
			for (var s of $scope.profileinfo.skills) {
				$(".form_box span#skillbut"+s).removeClass('button1');
				$(".form_box span#skillbut"+s).addClass('button2');
				$(".form_box span#skillspan"+s).removeClass('plus-icon');
				$(".form_box span#skillspan"+s).addClass('close-icon');
			};
		}
		if($scope.profileinfo.licenses) {
			for (var s of $scope.profileinfo.licenses) {
				$(".form_box span#licbut"+s).removeClass('button1');
				$(".form_box span#licbut"+s).addClass('button2');
				$(".form_box span#licspan"+s).removeClass('plus-icon');
				$(".form_box span#licspan"+s).addClass('close-icon');
			};
		}
  });

  $scope.checkuncheckcategory = checkuncheckcategory;
  function checkuncheckcategory(catid) {  
		if(catid != '9'){
			let nilButton = angular.element( document.querySelector( '#catbut9') );
			let nilSpan = angular.element( document.querySelector( '#catspan9') );

			nilButton.removeClass('button2').addClass('button1');
			nilSpan.removeClass('close-icon').addClass('plus-icon');
		}

    var myElBut = angular.element( document.querySelector( '#catbut'+catid ) );
    var myElSpan = angular.element( document.querySelector( '#catspan'+catid ) );
    myElBut.toggleClass('button1').toggleClass('button2');
    myElSpan.toggleClass('plus-icon').toggleClass('close-icon');
    var selcats = new Array();
    if($scope.selectedcategories){
       selcats = $scope.selectedcategories.split(',');
       var selindex = selcats.indexOf(catid);
      if( selindex !== -1) {
          selcats.splice(selindex, 1); 
      }else{
          selcats.push(catid);
      }
    }else{
      
      if(myElBut.hasClass('button2'))
         selcats.push(catid);
    }
		$scope.selectedcategories = selcats.toString();
		
		if($scope.selectedcategories != '9'){
			$scope.selectedcategories = $scope.selectedcategories.replace('9,','');
		}

		if($scope.selectedcategories == ""){
			// checkuncheckcategory('9');
		}

  };
  
  $scope.checkuncheckskill = checkuncheckskill;
  function checkuncheckskill(skillid) {  
		if(skillid != '16'){
			let nilButton = angular.element( document.querySelector( '#skillbut16') );
			let nilSpan = angular.element( document.querySelector( '#skillspan16') );

			nilButton.removeClass('button2').addClass('button1');
			nilSpan.removeClass('close-icon').addClass('plus-icon');
		}
    var myElBut = angular.element( document.querySelector( '#skillbut'+skillid ) );
    var myElSpan = angular.element( document.querySelector( '#skillspan'+skillid ) );
    myElBut.toggleClass('button1').toggleClass('button2');
    myElSpan.toggleClass('plus-icon').toggleClass('close-icon');
    var selskills = new Array();
    if($scope.selectedskills){
       selskills = $scope.selectedskills.split(',');
       var selindex = selskills.indexOf(skillid);
      if(selindex !== -1) {
        selskills.splice(selindex, 1); 
      }else{
        selskills.push(skillid);
      }
    }else{
      if(myElBut.hasClass('button2'))
         selskills.push(skillid);
    }
		$scope.selectedskills = selskills.toString();
		if($scope.selectedskills != '16'){
			$scope.selectedskills = $scope.selectedskills.replace('16,','');
		}

		if($scope.selectedskills == ""){
			// checkuncheckskill('16');
		}
    
  };
  
  $scope.checkunchecklicence = checkunchecklicence;
  function checkunchecklicence(licid) {  
		if(licid != '5'){
			let nilButton = angular.element( document.querySelector( '#licbut5') );
			let nilSpan = angular.element( document.querySelector( '#licspan5') );

			nilButton.removeClass('button2').addClass('button1');
			nilSpan.removeClass('close-icon').addClass('plus-icon');
		} 
    var myElBut = angular.element( document.querySelector( '#licbut'+licid ) );
    var myElSpan = angular.element( document.querySelector( '#licspan'+licid ) );
    myElBut.toggleClass('button1').toggleClass('button2');
    myElSpan.toggleClass('plus-icon').toggleClass('close-icon');
    var sellics = new Array();
    if($scope.selectedlicences){
       sellics = $scope.selectedlicences.split(',');
       var selindex = sellics.indexOf(licid);
      if( selindex !== -1) {
        sellics.splice(selindex, 1); 
      }else{
        sellics.push(licid);
      }
    }else{
      if(myElBut.hasClass('button2'))
         sellics.push(licid);
    }
		$scope.selectedlicences = sellics.toString();
		if($scope.selectedlicences != '5'){
			$scope.selectedlicences = $scope.selectedlicences.replace('5,','');
		}

		if($scope.selectedlicences == ""){
			// checkunchecklicence('5');
		}
  };
  
  $scope.step4Update = step4Update;
  function step4Update() {
		let updated_info = {
			'categories':$scope.selectedcategories.split(','), 
			'skills':$scope.selectedskills.split(','), 
			'licenses':$scope.selectedlicences.split(',')
		};
		let information_to_be_saved = {...loaded_info, ...updated_info};
		information_to_be_saved = JSON.stringify(information_to_be_saved);
		sessionStorage.setItem('profileinfo', information_to_be_saved);
		$scope.profileinfo = JSON.parse(sessionStorage.getItem('profileinfo'));

    var formData = {
          notes: $scope.profileinfo.notes,
          sportshobby: ($scope.profileinfo.sports_hobby) ? $scope.profileinfo.sports_hobby : ' ',
          selectedcategories: $scope.profileinfo.categories,
          selectedskills: $scope.profileinfo.skills,
          selectedlicences: $scope.profileinfo.licenses
				}

    $http.post('api/v1/step4Create', formData).success(function(response) {
      if(response.success){
				sessionStorage.setItem('profileinfo', JSON.stringify($scope.profileinfo));
        window.location = '#/my-profile_5' + ($rootScope.isDanish ? '/da' : '/en' );
      }
    });       
	}
}]);
main.controller('createCustomerController',['$scope', '$rootScope','$http', function($scope, $rootScope, $http, $cookies){
	$rootScope.bodylayout = 'black';
	$rootScope.interface = 'ansog';	
	
	$scope.username 	= $("input[name=username]").val();
	$scope.corporation 	= $("input[name=corporation]").val();
	$scope.telephone 	= $("input[name=telephone]").val();
	$scope.password 	= $("input[name=password]").val();
	$scope.cpassword 	= $("input[name=cpassword]").val();
	
	async function emailCustomer(emailData) {
		try {
			$http.post('api/v1/welcome-email-customer', emailData);
			  console.log('email sent');
		} catch(err) {
		  console.log('Ohh no:', err.message);
		}
	}

	$scope.createCustomer = createCustomer;
	function createCustomer(){
		$("input[name=opretcustomer]").attr("disabled", true);

		if($scope.password !== $scope.cpassword){
			alert("passwords entered doesn't match");
			return;
		}
		else{
			let formData = {
				email: $scope.username,
				corporation: $scope.corporation,
				telephone: $scope.telephone,
				password: $scope.password
			}
			$http.post('api/v1/customer-create', formData).success(function(response) {
				// console.log(response);
				if(response.status == 'success'){
					if($rootScope.isDanish){
						alert(response.message_dk);
					}else{
						alert(response.message_en);
					}
					
					let emailData = { email : $scope.username }
					emailCustomer(emailData)
					window.location.href = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
				}else{
					if($rootScope.isDanish){
						alert(response.message_dk);
					}else{
						alert(response.message_en);
					}
				}
			});
		}
	}
}]);


main.controller('CustomerProfileController',['$scope', '$rootScope','$http','$cookies', function($scope, $rootScope, $http, $cookies){
	let customer_id = $cookies.customer_id;
	$http.get('api/v1/get-customer?id='+customer_id).success(function(data) {
		console.log(data);
		$scope.customerData = data;
	});
	$scope.updateCustomerProfile = function(){
		let email = $("#customer_email").val();
		let corporation = $("#customer_corporation").val();
		let telephone = $("#customer_telephone").val();
		$http.post('api/v1/update-customer',{'email': email, 'corporation': corporation, 'telephone': telephone, 'id': customer_id}).success(function(response){
			alert('Profile Updated');
		});
	}
	$scope.profileLogout = function(){
		$http.post('api/v1/customer-logout').success(alert('logged out'));
		$cookies.customer_id = '';
		$cookies.customer_user = '';
		$cookies.PHPSESSID = '';
		$scope.customer_set = false;
		window.location.href = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
	}

	$scope.deleteLightbox = function(groupId){
		let confirmDelete = confirm("Are you sure to delete this Lightbox?");
		if(confirmDelete){
			let groupToken = localStorage.getItem('grouptoken');
			let formData = {groupid: groupId, grouptoken : groupToken}
			$http.get('api/v1/removegroupfromgrouping', {params:formData}).success(function(groupingdata) {
				$scope.gpprofilecount =groupingdata.count;
				if(groupingdata.count){
					$scope.gpprofiles = groupingdata.gpprofiles;
				}else{
					$scope.gpprofiles ='';
				}
				location.reload();
				// $scope.ifGroupFormRemove = false;
			});	
		}
	}
	
	$scope.showPrompt = function(){
		let displayMessage = ($rootScope.isDanish ? 'Indtast Lightbox navn' : 'Enter Lightbox name' )
		let newLightbox = prompt(displayMessage);
		if(newLightbox != ''){
			let groupToken = localStorage.getItem('grouptoken');
			var formData = {groupname: newLightbox, grouptoken : groupToken};
	
			$http.get('api/v1/addnewgrouping', {params:formData}).success(function(groupingdata) {
				$scope.groupingcount =groupingdata.count;
				if(groupingdata.count){
					$scope.groupings = groupingdata.grouping;
					$scope.new_group_name='';
					
				}else{
					$scope.groupings ='';
				}
				location.reload();
			});	
		}
	}
}]);

main.controller('MyProfileController5',['$scope', '$rootScope','$http', function($scope, $rootScope, $http, $cookies){
	$scope.profileinfo = JSON.parse(sessionStorage.getItem('profileinfo'));
	let loaded_info = $scope.profileinfo;
  $http.get('api/v1/getlanguages').success(function(languagesdropdown) {
    $scope.languagesdropdown = languagesdropdown
  });

  $scope.languagesListDropdown = function() {
    return this.languagesdropdown;
  }; 
  
  $http.get('api/v1/getlanguageratings').success(function(languageratings) {
    $scope.languageratings = languageratings
  });

  $scope.setlangraing = setlangraing;
  function setlangraing(langrowid,langrateid) {
    if($('#lang'+langrowid).val()) {
      for (i = 1; i <= 4; i++) {
        if(i <= langrateid)
          $('#start_'+langrowid+'_'+i).attr('src','images/star-white.png');
        else
          $('#start_'+langrowid+'_'+i).attr('src','images/star-black.png');
      }
      $('#langrateval'+langrowid).val(langrateid);
    }
	};


	if($scope.profileinfo.languages){
  $scope.lang1 = ($scope.profileinfo.languages[0] != undefined) ? $scope.profileinfo.languages[0].lang_id : "";
  $scope.lang2 = ($scope.profileinfo.languages[1] != undefined) ? $scope.profileinfo.languages[1].lang_id : "";
  $scope.lang3 = ($scope.profileinfo.languages[2] != undefined) ? $scope.profileinfo.languages[2].lang_id : "";
  $scope.lang4 = ($scope.profileinfo.languages[3] != undefined) ? $scope.profileinfo.languages[3].lang_id : "";
	

  $("#lang1 option[value='"+ $scope.lang1 +"']").attr("selected","selected");
  $("#lang2 option[value='"+ $scope.lang2 +"']").attr("selected","selected");
  $("#lang3 option[value='"+ $scope.lang3 +"']").attr("selected","selected");
  $("#lang4 option[value='"+ $scope.lang4 +"']").attr("selected","selected");
  
  $scope.langrateval1 = ($scope.profileinfo.languages[0] != undefined) ? $scope.profileinfo.languages[0].rating : "";
  $scope.langrateval2 = ($scope.profileinfo.languages[1] != undefined) ? $scope.profileinfo.languages[1].rating : "";
  $scope.langrateval3 = ($scope.profileinfo.languages[2] != undefined) ? $scope.profileinfo.languages[2].rating : "";
  $scope.langrateval4 = ($scope.profileinfo.languages[3] != undefined) ? $scope.profileinfo.languages[3].rating : "";
	}
	else{
		$scope.lang1 = $scope.lang2 = $scope.lang3 = $scope.lang4 = "";
		$scope.langrateval1 = $scope.langrateval2 = $scope.langrateval3 = $scope.langrateval4 = "";
	}
  $scope.$on('ngRepeatFinished', function(ngRepeatFinishedEvent) {
    ($scope.langrateval1 != "") ? setlangraing(1, $scope.langrateval1) : $scope.langrateval1 = "";
    ($scope.langrateval2 != "") ? setlangraing(2, $scope.langrateval2) : $scope.langrateval2 = "";
    ($scope.langrateval3 != "") ? setlangraing(3, $scope.langrateval3) : $scope.langrateval3 = "";
    ($scope.langrateval4 != "") ? setlangraing(4, $scope.langrateval4) : $scope.langrateval4 = "";

		$("select[id^=lang]").on("change",function(){
			if($(this).val() == ""){
				var row_id = this.id.slice(-1);
				$("input[id=langrateval"+row_id+"]").val("");
				$("span.ratings img[id^=start_"+row_id+"]").attr("src","images/star-black.png");
			}
			else{
				var row_id = this.id.slice(-1);
				$("input[id=langrateval"+row_id+"]").val("1");
				$("span.ratings img[id^=start_"+row_id+"]").attr("src","images/star-black.png");
				$("span.ratings img[id^=start_"+row_id+"_1]").attr("src","images/star-white.png");
			}
		});
		
  });

  $scope.step5Update = step5Update;
  function step5Update() {
		let updated_info = $scope.profileinfo;
		console.log(updated_info);
		let information_to_be_saved = {...loaded_info, ...updated_info};
		information_to_be_saved = JSON.stringify(information_to_be_saved);
		sessionStorage.setItem('profileinfo', information_to_be_saved);

    var formData = {
          lang1: $scope.lang1,
          lang2: $scope.lang2,
          lang3: $scope.lang3,
          lang4: $scope.lang4,
          langrateval1: $('#langrateval1').val(),
          langrateval2: $('#langrateval2').val(),
          langrateval3: $('#langrateval3').val(),
          langrateval4: $('#langrateval4').val(),
          dealekter1: $scope.profileinfo.dealekter1,
          dealekter2: $scope.profileinfo.dealekter2,
          dealekter3: $scope.profileinfo.dealekter3,
          user_profile_id: $scope.profileinfo.id,
          operation: 'update',
          lng_pro_id1: ($scope.profileinfo.languages && $scope.profileinfo.languages[0] != undefined) ? $scope.profileinfo.languages[0].lng_pro_id : "",
          lng_pro_id2: ($scope.profileinfo.languages && $scope.profileinfo.languages[1] != undefined) ? $scope.profileinfo.languages[1].lng_pro_id : "",
          lng_pro_id3: ($scope.profileinfo.languages && $scope.profileinfo.languages[2] != undefined) ? $scope.profileinfo.languages[2].lng_pro_id : "",
          lng_pro_id4: ($scope.profileinfo.languages && $scope.profileinfo.languages[3] != undefined) ? $scope.profileinfo.languages[3].lng_pro_id : "",
        }
    $http.post('api/v1/step5Create', formData).success(function(response) {
      if(response.success){
				operation = 'update';
				sessionStorage.setItem('mediaupload_source','update');
				$scope.step7Create = step7Create;
				step7Create();

				function step7Create() {
					$scope.ifRegistring=true;		
					var fd = new FormData();
					localStorage.removeItem('imagecount');
					localStorage.removeItem('preview_html');
					$http.post('api/v1/step7Create',  fd, 
					{
						transformRequest: angular.identity,
						operation: operation,
						headers: {'Content-Type': undefined,'Process-Data': false}
					}
					).success(function(response) {
						if(response.status != undefined){
							$scope.ifRegistring=false;
							
							data = { "email": response.email, "first_name": response.first_name, "last_name": response.last_name, "profile_number": response.profile_number, "profile_id": response.profile_id };

							sessionStorage.setItem('registered_user_profile_id', data.profile_id);
							sessionStorage.setItem('registered_user_profile_number', data.profile_number);
							sessionStorage.setItem('registered_user_first_name', data.first_name);
							sessionStorage.setItem('registered_user_last_name', data.last_name);

							if(operation == "insert"){
								$.post("/api/v1/welcome_email", data,
										function (data, textStatus, jqXHR) {},
								);
							}
							// window.location = '#/mediaupload' + ($rootScope.isDanish ? '/da' : '/en' );

						}
						else{
							alert($rootScope.isDanish ? "Ikke registreret, venligst prøv igen" : "Couldn't Register, Please Try again later");
							window.location = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
						}
					}).error(function(error){
						alert('Something went wrong, Please try again later'); 
						window.location = '#/index' + ($rootScope.isDanish ? '/da' : '/en' );
					});	
				}
				sessionStorage.setItem('profileinfo', JSON.stringify($scope.profileinfo));
        window.location = '#/my-profile_6' + ($rootScope.isDanish ? '/da' : '/en' );
      }
    });       
  }
}])
.directive('onFinishRender', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            if (scope.$last === true) {
                $timeout(function () {
                    scope.$emit(attr.onFinishRender);
                });
            }
        }
    }
})
.directive('customOnChange', function() {
  return {
    restrict: 'A',
    link: function (scope, element, attrs) {
      var onChangeHandler = scope.$eval(attrs.customOnChange);
      element.on('change', onChangeHandler);
      element.on('$destroy', function() {
        element.off();
      });
    }
  };
});
