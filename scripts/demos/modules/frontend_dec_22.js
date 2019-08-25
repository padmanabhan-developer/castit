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
	$rootScope.isMaincontent = true;
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

	$(".sidebar1-close").click(function(){
		$("#sidebar1").hide("slow"); 
		 });
		 
	$(".side-top").click(function(){
		$("#sidebar1").show("slow"); 
	  });
	$(".thumb").click(function(){
		$(".popup").fadeIn("slow"); 
	});
	
	$(".poup-close").click(function(){
		$(".popup").fadeOut(); 
	  });
	$(".poup-close2").click(function(){
		$rootScope.interface = 'home';
		$rootScope.isMaincontent = true;
		$rootScope.$apply()
		$(".contact_section").fadeOut(); 
		$(".course_section").fadeOut(); 
		$(".main_content").fadeIn("slow"); 
	  });
	  
	$("#event1").click(function(){
	    $(".title1").addClass("active");
		$(".title1_1").removeClass("active");
		 $("tab1").addClass(".side-tabs ul li.active");
		$(".add-grupper").hide();
		$(".lightbox").fadeIn(); 
		$(".grupper").fadeOut();
		
	});
	  
	$("#event2").click(function(){
	    $(".title1_1").addClass("active");
		$(".add-grupper").show();
        $("tab2").addClass(".side-tabs ul li.active");
		$(".title1").removeClass("active");
		$(".grupper").fadeIn(); 
		$(".lightbox").fadeOut();
	});

	$("#tab_ligtbox").click(function(){
	    $(".title1").addClass("active");
		$('.tab1').removeClass('tabactive');
		$('#tab_ligtbox').addClass('tabactive');
		$(".title1_1").removeClass("active");
		 $("tab1").addClass(".side-tabs ul li.active");
		$(".add-grupper").hide();
		$(".lightbox").fadeIn(); 
		$(".grupper").fadeOut();
		
	});
	  
	$("#tab_group").click(function(){
	    $(".title1_1").addClass("active");
		$('.tab1').removeClass('tabactive');
		$('#tab_group').addClass('tabactive');
		$(".add-grupper").show();
        $("tab2").addClass(".side-tabs ul li.active");
		$(".title1").removeClass("active");
		$(".grupper").fadeIn(); 
		$(".lightbox").fadeOut();
	});
		 
	$(".add-grupper").click(function(){
			$(".addbox").show(); 
			 
		 });
		 
	$(".img_layer").click(function(){
		$(".videobig_thumb").show(); 
	  });
	
	$(".video_call3").click(function(){
		$("#thumb1").show();
		$("#thumb2").hide(); 
	});
	  
	$(".video_call6").click(function(){
		$("#thumb2").show();
		$("#thumb1").hide(); 
	});
	   
	$(".video_upload").click(function(){
		$("#profilebox").show();
	});
	  
	$(".kontakt").click(function(){
		$(this).toggleClass("active");
		$(".kon-dropdown").toggle("slow"); 
	});
		 
	$(".res-menu").click(function(){
		$(".res_menu ul li").toggle("slow"); 
	});

	$(".information-inside .poup-close2").click(function(){
		$(".res_menu ul li").fadeOut("slow"); 
	});
		 
	$(".poup-close3").click(function(){
		$scope.IsProfileImage = true;
		$scope.IsProfileVideo = false;
		var video = $('#pro_video')[0];
		video.pause();
		video.currentTime = 0;
		$(".popup").fadeOut(); 
	});


    $scope.getDataProfile = function () {
      return $filter('filter')($scope.profiles, $scope.q)
    }
    $scope.numberOfPages=function(){
        return Math.ceil($scope.getDataProfile().length/$scope.pageSize);                
    }
    //$scope.data = $scope.profiles;
    //alert($scope.profiles.length);

	$http.get('api/v1/getlightboxprofiles', {params: {view: 'home'}}).success(function(lightboxdata) {
		$scope.lbprofilecount =lightboxdata.count;
		if(lightboxdata.count){
			$scope.lbprofiles = lightboxdata.lbprofiles;
		}else{
			$scope.lbprofiles ='';
		}
	 });


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
	
	  
	$scope.addToLightbox = addToLightbox;
	function addToLightbox(profileid) {
		var formData = {profileid: profileid}
		$http.get('api/v1/updatelightboxprofiles', {params:formData}).success(function(lightboxdata) {
			$scope.lbprofilecount =lightboxdata.count;
			if(lightboxdata.count){
				$scope.lbprofiles = lightboxdata.lbprofiles;
			}else{
				$scope.lbprofiles ='';
			}
		});	
		
	}
	$scope.removeFromLightbox = removeFromLightbox;
	function removeFromLightbox(profileid) {
		var formData = {profileid: profileid}
		$http.get('api/v1/removelightboxprofiles', {params:formData}).success(function(lightboxdata) {
			$scope.lbprofilecount =lightboxdata.count;
			if(lightboxdata.count){
				$scope.lbprofiles = lightboxdata.lbprofiles;
			}else{
				$scope.lbprofiles ='';
			}
		});	
		
	}
	
		 
		
	$("#thumb1").click(function(){
		$("inside-popup").show();
		$("popup-video").hide(); 
	});

	$scope.singleimage = '';
	$scope.IsProfileImage = true;
	$scope.IsProfileVideo = false;
	
	$scope.getSingleProfile = function (profileid){

		$http.get('api/v1/getsingleprofiles', {params:{profileid:profileid}}).success(function(profiledata) {
			//alert(response.success);
			if(profiledata.success){
				$scope.singleprofile = profiledata.profile;
				$scope.profile_images = profiledata.profile_images;
				$scope.skills = profiledata.skills;
				$scope.experiences = profiledata.experiences;
				$scope.lang = profiledata.lang;
				$scope.singleimage = profiledata.profile_images[0].fullpath;
				$scope.profile_videos = profiledata.profile_videos;

			}else{
				$scope.profiles ='';
				$scope.loading = 'Ingen data fundet';
			}
		});	
		$(".popup").fadeIn("slow"); 
	}
	
	
	$scope.changeSingleImageBig = changeSingleImageBig;
	function changeSingleImageBig(profileimage) {
		//alert(profileimage);
		
		$('.video_side').removeClass('inactive');
		$('#videodiv').addClass('inactive');

		$scope.IsProfileImage = true;
		$scope.IsProfileVideo = false;
		$scope.singleimage = profileimage;
		var video = $('#pro_video')[0];
		video.pause();
		video.currentTime = 0;
	}
	
	$scope.currentIndex=0;
	$scope.next=function(){
		//alert($scope.profile_images.length);
		$scope.currentIndex<$scope.profile_images.length-1?$scope.currentIndex++:$scope.currentIndex=0;
	};
	$scope.prev=function(){
		$scope.currentIndex>0?$scope.currentIndex--:$scope.currentIndex=$scope.profile_images.length-1;
	};
	$scope.$watch('currentIndex',function(){
		//alert($scope.currentIndex);
		if($scope.profile_images){
			$scope.singleimage = $scope.profile_images[$scope.currentIndex].fullpath;
		}
	});

	$scope.changeSingleVideoBig = changeSingleVideoBig;
	function changeSingleVideoBig(profilevideo) {
		//alert(profilevideo);
		$('.video_side').removeClass('inactive');
		$('#imagediv').addClass('inactive');
		var video = $('#pro_video')[0];
		$scope.IsProfileVideo = true;
		$scope.IsProfileImage = false;
		$scope.currVideoUrl = profilevideo;
		video.load();
		video.play();
	};
	var config = {};
    $scope.scrollbar = function(direction, autoResize, show) {
        config.direction = direction;
        config.autoResize = autoResize;
        config.scrollbar = {
            show: true
        };
        return config;
    }

  }])
  .filter("trusted", function($sce) {
    return function(Url) {
        return $sce.trustAsResourceUrl(Url);
    };
});


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

