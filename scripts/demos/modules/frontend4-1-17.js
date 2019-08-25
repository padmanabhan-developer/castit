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
	$scope.ifLightboxForm = true;
	$scope.ifLightboxFormRemove = true;
	$scope.ifGroupFormRemove = true;
	$scope.ifLightboxFormSuccess = false;
	$scope.ifAddGroupFormShow = false;
	$scope.response_text ='';
    $scope.currentPage = 0;
	$scope.removeprofileid  ='';
	$scope.removegroupid ='';
    $scope.pageSize = 12;
    $scope.profiles = [];
	$scope.gpprofilecount =0;
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
	  
	  $(".div-close").click(function(){
		$(this).toggle(); 
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
		document.getElementById('image_icons').style.visibility = "visible";
		document.getElementById('video_icons').style.visibility = "visible";
		document.getElementById('row-res').style.width = null;;
		var myEl = angular.element( document.querySelector( '.videodiv' ) );
		myEl.addClass('video_call4');
		myEl.removeClass('video_call8');
		var video = $('#pro_video')[0];
		video.pause();
		video.currentTime = 0;
		$("#profile_popup").fadeOut(); 
	});
	
	
    $scope.getDataProfile = function () {
      return $filter('filter')($scope.profiles, $scope.q)
    }
    $scope.numberOfPages=function(){
        return Math.ceil($scope.getDataProfile().length/$scope.pageSize);                
    }

	$http.get('api/v1/getlightboxprofiles', {params: {view: 'home'}}).success(function(lightboxdata) {
		$scope.lbprofilecount =lightboxdata.count;
		if(lightboxdata.count){
			$scope.lbprofiles = lightboxdata.lbprofiles;
		}else{
			$scope.lbprofiles ='';
		}
	 });

	$http.get('api/v1/getgroupingprofiles', {params: {view: 'home'}}).success(function(groupingdata) {
		$scope.gpprofilecount =groupingdata.count;
		
		if(groupingdata.count){
			$scope.gpprofiles = groupingdata.gpprofiles;
		}else{
			$scope.gpprofiles ='';
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
	  
	$scope.pbox_singleimage='';	
	$scope.getProfileBox = function (profileid){
		$scope.pbox_singleimage = '';
		$scope.profile_notes = '';
		$scope.pbox_profileid = profileid
		$http.get('api/v1/getgroupinglist').success(function(groupingdata) {
			$scope.groupingcount =groupingdata.count;
			if(groupingdata.count){
				$scope.groupings = groupingdata.grouping;
			}else{
				$scope.groupings ='';
			}
		});

		$http.get('api/v1/getsingleprofiles', {params:{profileid:profileid}}).success(function(profiledata) {
			//alert(response.success);
			if(profiledata.success){
				$scope.pbox_singleimage = profiledata.profile_images[0].fullpath;
				$scope.apply;
			}else{
				$scope.pbox_loading = 'Ingen data fundet';
			}
		});	
		$("#profilebox").fadeIn("slow"); 
	}
	$(".poup-close-profilebox").click(function(){
		$("#profilebox").fadeOut(); 
	});
	$scope.selectedgroupings = '';
	$scope.checkuncheckgrouping = checkuncheckgrouping;
	function checkuncheckgrouping(groupingid) {  
		var myElBut = angular.element( document.querySelector( '#groupingbut'+groupingid ) );
		myElBut.toggleClass('button').toggleClass('button2');
		var selgroupings = new Array();
		if($scope.selectedgroupings){
			 selgroupings = $scope.selectedgroupings.split(',');
			 var selindex = selgroupings.indexOf(groupingid);
			if( selindex !== -1) {
					selgroupings.splice(selindex, 1); 
			}else{
					selgroupings.push(groupingid);
			}
		}else{
			
			if(myElBut.hasClass('button2'))
				 selgroupings.push(groupingid);
		}
		$scope.selectedgroupings = selgroupings.toString();
	}

	$scope.add_new_group = add_new_group;
	function add_new_group() {  
		if($scope.new_group_name){
			var formData = {groupname: $scope.new_group_name, };
	
			$http.get('api/v1/addnewgrouping', {params:formData}).success(function(groupingdata) {
				$scope.groupingcount =groupingdata.count;
				if(groupingdata.count){
					$scope.groupings = groupingdata.grouping;
					 $scope.new_group_name='';
					 
				}else{
					$scope.groupings ='';
				}
			});	
		}
	}
	$scope.profile_notes	=	'';
	$scope.addToLightbox = addToLightbox;
	function addToLightbox() {

		var formData = {profileid: $scope.pbox_profileid, profile_notes : $scope.profile_notes, selectedgroupings:$scope.selectedgroupings};

		$http.get('api/v1/updatelightboxprofiles', {params:formData}).success(function(lightboxdata) {
			$scope.lbprofilecount =lightboxdata.count;
			if(lightboxdata.count){
				$scope.lbprofiles = lightboxdata.lbprofiles;
				
					$http.get('api/v1/getgroupingprofiles', {params: {view: 'home'}}).success(function(groupingdata) {
						$scope.gpprofilecount =groupingdata.count;
						
						if(groupingdata.count){
							$scope.gpprofiles = groupingdata.gpprofiles;
						}else{
							$scope.gpprofiles ='';
						}
					 });
			$("#profilebox").fadeOut(); 		 

			}else{
				$scope.lbprofiles ='';
			}
		});	
		
	}
	
	$scope.removeFromLightboxPopup = removeFromLightboxPopup;
	function removeFromLightboxPopup(profileid) {
		$scope.removeprofileid = profileid;
		$("#lightbox_remove_popup").fadeIn('slow');
	}
	
	$("#close_remove_lightbox").click(function(){
		$scope.removeprofileid = ''; 
		$scope.ifLightboxFormRemove = true;
		$("#lightbox_remove_popup").fadeOut();
	});

	$scope.hideRemoveLoghtboxPopup = function (){
		$scope.removeprofileid = ''; 
		$scope.ifLightboxFormRemove = true;
		$("#lightbox_remove_popup").fadeOut();
	};
	
	$scope.removeFromLightbox = removeFromLightbox;
	function removeFromLightbox() {
		if($scope.removeprofileid){
			var formData = {profileid: $scope.removeprofileid}
			$http.get('api/v1/removelightboxprofiles', {params:formData}).success(function(lightboxdata) {
				$scope.lbprofilecount =lightboxdata.count;
				if(lightboxdata.count){
					$scope.lbprofiles = lightboxdata.lbprofiles;
				}else{
					$scope.lbprofiles ='';
				}
				$scope.ifLightboxFormRemove = false;
			});	
		}
	}
	
		 
	$scope.removeGroup = removeGroup;
	function removeGroup(groupid) {
		$scope.removegroupid = groupid;
		$("#group_remove_popup").fadeIn('slow');
	}

	$("#close_remove_group").click(function(){
		$scope.removegroupid = ''; 
		$scope.ifGroupFormRemove = true;
		$("#group_remove_popup").fadeOut();
	});

	$scope.hideRemoveGroupPopup = function (){
		$scope.removegroupid = ''; 
		$scope.ifGroupFormRemove = true;
		$("#group_remove_popup").fadeOut();
	};

	$scope.removeFromGrouplist = removeFromGrouplist;
	function removeFromGrouplist() {
		if($scope.removegroupid){
			var formData = {groupid: $scope.removegroupid}
			$http.get('api/v1/removegroupfromgrouping', {params:formData}).success(function(groupingdata) {
				$scope.gpprofilecount =groupingdata.count;
		
				if(groupingdata.count){
					$scope.gpprofiles = groupingdata.gpprofiles;
				}else{
					$scope.gpprofiles ='';
				}
				$scope.ifGroupFormRemove = false;
			});	
		}
	}

	$scope.addNewGroupInstant = function (){
		$scope.ifAddGroupFormShow = true;
	};

	$scope.addgroupintoGrouplist = addgroupintoGrouplist;
	function addgroupintoGrouplist() {
		if($scope.new_group_name1){
			var formData = {groupname: $scope.new_group_name1}
			$http.get('api/v1/addgroupintogrouping', {params:formData}).success(function(groupingdata) {
				$scope.gpprofilecount =groupingdata.count;
		
				if(groupingdata.count){
					$scope.gpprofiles = groupingdata.gpprofiles;
				}else{
					$scope.gpprofiles ='';
				}
				$scope.new_group_name1='';
				$scope.ifAddGroupFormShow = false;
			});	
		}
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
		$("#profile_popup").fadeIn("slow"); 
	}
	
	$scope.changeSingleVideoBig = changeSingleVideoBig;
	function changeSingleVideoBig(profilevideo) {
		//alert(profilevideo);
		$('.video_side').removeClass('inactive');
		$('#imagediv').addClass('inactive');
		$('#photo_nav').hide();
		//alert($scope.profile_videos.length)
		$('#video_nav').show();
		$scope.IsProfileVideo = true;
		$scope.IsProfileImage = false;
		$scope.currVideoUrl = profilevideo;
		var video = $('#pro_video')[0];
		$('video').mediaelementplayer({
			alwaysShowControls: false,
			videoVolume: 'horizontal',
			usePluginFullScreen : false,
			features: ['playpause','progress', 'fullscreen']
		});
		video.load();
		video.play();
	};
	
	$scope.changeSingleImageBig = changeSingleImageBig;
	function changeSingleImageBig(profileimage) {
		//alert(profileimage);
		
		$('.video_side').removeClass('inactive');
		$('#videodiv').addClass('inactive');
		$('#video_nav').hide();
		$('#photo_nav').show();
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

	$scope.currentIndexVideo=0;
	$scope.next_video=function(){
		//alert($scope.profile_images.length);
		$scope.currentIndexVideo<$scope.profile_videos.length-1?$scope.currentIndexVideo++:$scope.currentIndexVideo=0;
	};
	$scope.prev_video=function(){
		//alert($scope.currentIndexVideo);
		$scope.currentIndexVideo>0?$scope.currentIndexVideo--:$scope.currentIndexVideo=$scope.profile_videos.length-1;
	};
	$scope.$watch('currentIndexVideo',function(){
		//alert($scope.currentIndexVideo);
		if($scope.profile_videos){
			$scope.currVideoUrl = $scope.profile_videos[$scope.currentIndex].fullpath;
			var video = $('#pro_video')[0];
			$('video').mediaelementplayer({
				alwaysShowControls: false,
				videoVolume: 'horizontal',
				usePluginFullScreen : false,
				features: ['playpause','progress', 'fullscreen']
			});
			video.load();
			video.play();

		}
	});

	
	$scope.showLightBoxPopup = function (){
		$("#lightbox_popup").fadeIn('slow'); 
	};
	$("#close_lightbox").click(function(){
		$("#lightbox_popup").fadeOut(); 
		$scope.ifLightboxFormSuccess = false;
		$scope.ifLightboxForm = true;
	});

	$scope.cancelLigtbox = function (){
		$("#lightbox_popup").fadeOut(); 
	};

	$scope.sendLightboxForm = function (){
		var formData = {form_email: $scope.form_email,
						to_email: $scope.to_email,
						to_cc: $scope.to_cc,
						mail_body: $scope.mail_body,
						}
		$http.post('api/v1/sendlightbox', formData).success(function(sendlightboxstatus) {
			if(sendlightboxstatus.success){
				$scope.ifLightboxForm = false;
				$scope.ifLightboxFormSuccess = true;
				$scope.form_email ='';$scope.to_email ='';$scope.to_cc ='';$scope.mail_body ='';
				$scope.response_text =sendlightboxstatus.message;
				$scope.apply;
			}
		});	
		$("#profile_popup").fadeIn("slow"); 
	}


	$scope.showNotesPopup = function (){
		$("#notes_popup").fadeIn('slow'); 
	};
	$("#close_notes_popup").click(function(){
		$("#notes_popup").fadeOut(); 
	});
	
	$scope.showSendGroupPopup = function (){
		$("#send_group_popup").fadeIn('slow'); 
	};
	$("#close_sendgroup").click(function(){
		$("#send_group_popup").fadeOut(); 
		$scope.ifLightboxFormSuccess = false;
		$scope.ifLightboxForm = true;
	});

	$scope.cancelLigtbox = function (){
		$("#send_group_popup").fadeOut(); 
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
	/*$('.thumb').hover(function()
	{
		var thisat = $(this).attr('profileid');
		alert(thisat) ;
		 if($('#add_to_lb_'+thisat).is(':visible'))
			$('#add_to_lb_'+thisat).fadeOut(750 , function()
		 {
			// animation complete callback
			 $('#add_to_lb_'+thisat).fadeIn(750);
		 });
	}, function()
	{ 
		var thisat = $(this).attr('profileid');
		 // Mouse Leave callback
		 $('#add_to_lb_'+thisat).fadeOut(750 );
	});*/
	$scope.hoverInLb = function(){
        this.hoverAddtoLb = true;
    };

    $scope.hoverOutLb = function(){
        this.hoverAddtoLb = false;
    };


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

	
