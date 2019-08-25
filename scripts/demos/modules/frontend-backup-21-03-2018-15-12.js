var frontend = angular.module('theme.demos.dashboard', [
    'angular-skycons',
    'theme.demos.forms'
  ])
  frontend.controller('FrontendController', ['$scope', '$rootScope', '$http', '$timeout', '$window', '$filter', '$location', function($scope,$rootScope, $http, $timeout, $window, $filter, $location) {
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
	$scope.responsiveProfileDetail = true;
	$scope.ifAddGroupFormShow = false;
	$scope.response_text ='';
  $scope.currentPage = 0;
	$scope.pbox_profileid ='';
	$scope.removeprofileid  ='';
	$scope.removegroupid ='';
  $scope.pageSize = 40;
  $scope.profiles = [];
	$scope.gpprofilecount =0;
	$scope.photoiconclass='photo-iconactive';
	$scope.mediaiconclass='media-icon';
	$scope.infoiconclass='info-icon';
  $scope.q = '';
  $scope.loading = 'Indlaeser';
	$scope.enableNotesBox = false;
	
	if($( window ).width() <= 860){
		$scope.scroll_counter_number = 9;
	}
	else{
		$scope.scroll_counter_number = 18;
	}
	

	function removeDuplicates (json_all) {
		var arr = [],
			collection = [];
		$.each(json_all, function (index, value) {
			
			if ($.inArray(value.id, arr) == -1) {
				arr.push(value.id);
				collection.push(value);
			}
		});
		return collection;
	}

	function removeDuplicatesNew (json_all) {
		var arr = [],
			collection = [];
		
		$.each(json_all, function (index, value) {
			if ($.inArray(value.id, arr) == -1) {
				arr.push(value.id);
				collection.push(value);
			}
		});
		return collection;
	}

  $scope.enableNotesBoxFN = function(){
	$scope.enableNotesBox = !$scope.enableNotesBox;
  }
  $scope.updateNotesFN = function($event){
	  let data = { 
			'group_id' : $event.currentTarget.attributes.group_id.nodeValue,
			'profile_id': $event.currentTarget.attributes.profile_id.nodeValue,
			'notes': $event.currentTarget.value
		}
	  $http.post('api/v1/updatenotes', data);
  }
	let argument_data = {params: {view: 'home'}};
	if('group' in $location.search()){
			if((window.location.hash).slice(-6) != 'loaded') {
				window.location.hash = window.location.hash + '&loaded';
				window.location.reload();
			}
		let group_id = $location.search().group;
		argument_data = {params: {view: 'home', group_id: group_id}}
		$scope.group_username = $location.search().username;
		$scope.group_shared_name = $location.search().groupname;
		if($scope.isDanish){
			$scope.group_share_text = "<p>LIGHTBOX: </p><p>"+$scope.group_shared_name+"</p><br><p>Oprettet af: </p><p>"+$scope.group_username+"</p><br><p>Du kan slette profiler og rette i tekst ved at klikke på gruppe symbolet til højre. Brug piletasterne til at navigerer i grupperne.</p><span class='side-icon3' id='tab_group1' style='border: none;padding: 0 0 10px 20px;'></span><br><p>Når du er færdig med at redigere, klik send</p>";
		}
		else{
			$scope.group_share_text = "<p>LIGHTBOX: </p><p>"+$scope.group_shared_name+"</p><br><p>Created by: </p><p>"+$scope.group_username+"</p><br><p>You can delete profiles and correct text by clicking the group symbol in the right side menu. Use the arrow keys to navigatethe groups.</p><span class='side-icon3' id='tab_group1' style='border: none;padding: 0 0 10px 20px;'></span><br><p>When you have finished editing, click on send</p>";
		}
	}
	$http.get('api/v1/getprofiles', argument_data).success(function(homedata) {
		$scope.hasresults = false;
		if(homedata.success){
			$scope.profiles = homedata.profiles;
			if('group_token' in homedata && 'group' in $location.search()){
			localStorage.setItem('grouptoken', homedata.group_token);
			localStorage.setItem('grouptoken_groupid', argument_data.params.group_id);
			// $scope.groupToken = homedata.group_token;
			$(".leftbar").html($scope.group_share_text);
			}
		}else{
			$scope.profiles ='';
		}
   });

   $scope.groupToken = localStorage.getItem('grouptoken') || '';

   if($scope.groupToken ==''){
     $http.get('api/v1/getgrouptoken', {params: {view: 'home'}}).success(function(groupdata) {
       if(groupdata.success){
         $scope.groupToken = groupdata.grouptoken;
         localStorage.setItem('grouptoken', $scope.groupToken);
       }else{
         $scope.groupToken ='';
       }
      });
   }

   var getprofiles_offset = 0;

   $("#search_text").change(function (e) { 
      if($(this).val() == ""){
		$(".rightbar-row").removeClass("filteractive");
		$(".no_result_ajax").hide();
      }     
   });

   $("#search_text").on('input', function(){
	if($(this).val() == ""){
		$http.get('api/v1/getprofiles', {params: {view: 'home'}}).success(function(homedata) {
			$scope.hasresults = false;
			if(homedata.success){
				$(".no_result_ajax").hide();
				$scope.profiles = homedata.profiles;
			}else{
				$(".no_result_ajax").show();
				$scope.profiles ='';
			}
		 });
	}
   });

   $("#gender").change(function (e) { 
    if($(this).val() == ""){
      $(".rightbar-row").removeClass("filteractive");
    } 
   });

   $("#age_to").change(function (e) { 
    if($(this).val() == "" && $("#age_from").val() == ""){
      $(".rightbar-row").removeClass("filteractive");
    } 
   });

   $scope.scroll_counter = 0;
   $('.scroll-down').click(function(){
	   $scope.scroll_counter = $scope.scroll_counter + $scope.scroll_counter_number;
	   var nextElement = document.getElementsByClassName('wrapPM'+$scope.scroll_counter);
	   var nextTopPos = nextElement[0].offsetTop;
	   $('.rightbar-row').animate({
		   scrollTop: nextTopPos
	   }, 500);
	//    console.log($('.rightbar-row').height());
	//    console.log("nextTopPos", nextTopPos);
   });
   $('.scroll-up').click(function(){
	   if($scope.scroll_counter > 0){
			 $scope.scroll_counter = $scope.scroll_counter - $scope.scroll_counter_number;
			 console.log($scope.scroll_counter);
		   var prevElement = document.getElementsByClassName('wrapPM'+$scope.scroll_counter);
		   var prevTopPos = prevElement[0].offsetTop;
			 $('.rightbar-row').animate({
				 scrollTop: prevTopPos
			 }, 500);
		// console.log($('.rightbar-row').height());
		// console.log("prevTopPos", prevTopPos);
	   }
   });

	$(".rightbar-row").scroll(function(){
		let wrapPMelement = $('[class*="wrapPM"]').withinviewport({
			container: $('.rightbar-row'),
		});
		// console.log(wrapPMelement[0].className);
		if(wrapPMelement[0]){
			if(wrapPMelement[0].className){
				let classes = wrapPMelement[0].className.split(' ');
				classes.filter(function(elemClass){
					if(elemClass.indexOf('wrapPM') !== -1){
						$scope.scroll_counter = parseInt(elemClass.replace("wrapPM",''));
					}
				})
			}
		}
        if(($(this).scrollTop() >= (this.scrollHeight - $(this).height() - 1)) && ($scope.profiles.length > 18) && !('group' in $location.search())){
        getprofiles_offset = getprofiles_offset + 1;
        if($(".rightbar-row").hasClass("filteractive")) {
          $http.get('api/v1/getfilterprofiles', {params:{search_text: $scope.search_text,age_from: $scope.age_from,age_to: $scope.age_to,genderval: $scope.genderval,purchase_name: $scope.purchase_name,submittype: $scope.submittype, offset: getprofiles_offset}}).success(function(homedata) {
            $(".rightbar-row").addClass("filteractive");
            $(".loading_ajax").hide();
            $scope.homedata = homedata;
            if(homedata.success === true){
			  $scope.profiles = $scope.profiles.concat(homedata.profiles);
			  $(".no_result_ajax").hide();
			}
			else{
				if(!$scope.hasresults){
					$(".no_result_ajax").show();
					$scope.profiles ='';
					$scope.loading = 'Ingen data fundet';
				}
            }
          });	
        }
        else{
          $http.get('api/v1/getprofiles', {params: {view: 'home', offset: getprofiles_offset}}).success(function(homedata) {
			$scope.hasresults = false;
            if(homedata.success){
			//   $scope.profiles = removeDuplicates($scope.profiles.concat(homedata.profiles));
			  $scope.profiles = $scope.profiles.concat(homedata.profiles);
			//   let profiles_set = new Set($scope.profiles.concat(homedata.profiles));
			//   let profiles_array = [...profiles_set];
			//   $scope.profiles = profiles_array;
			//   console.log($scope.profiles);
            }else{
				if(getprofiles_offset > 2){
					getprofiles_offset = getprofiles_offset - 1;
				}
              $scope.profiles = $scope.profiles;
            }
          });
        }
    }
    });

	let x = 0;
	$(".thumb").scroll(function(){
		// $("span").text( x+= 1);
		$scope.scrollDirection;
		
    });

	$scope.scrollDirection = function(){
		var scrollableElement = document.getElementById('scrollableElement');
		scrollableElement.addEventListener('wheel', function (event){
			var delta;
			if (event.wheelDelta){
				delta = event.wheelDelta;
			}else{
				delta = -1 * event.deltaY;
			}
			if (delta < 0){
				console.log("DOWN");
			}else if (delta > 0){
				console.log("UP");
			}
		});
	}

	// var $currentElement = $(".wrapPM0").first();
	// var scroll_index = 0;

	// $(".scroll-down").click(function () {
	// 	var $nextElement = $('.wrapPM'+ scroll_index+1);
	// 	// Check if next element actually exists
	// 	if($nextElement.length) {
	// 		// If yes, update:
	// 		// 1. $currentElement
	// 		// 2. Scroll position
	// 		$currentElement = $nextElement;
	// 		$('html, body').stop(true).animate({
	// 			scrollTop: $nextElement.offset().top
	// 		}, 1000);
	// 		scroll_index = scroll_index + 1;
	// 	}
	// 	return false;
	// });
	
	// $(".scroll-up").click(function () {
	// 	var $prevElement = $('.wrapPM'+ scroll_index-1);
	// 	// Check if previous element actually exists
	// 	if($prevElement.length) {
	// 		// If yes, update:
	// 		// 1. $currentElement
	// 		// 2. Scroll position
	// 		$currentElement = $prevElement;
	// 		$('html, body').stop(true).animate({
	// 			scrollTop: $prevElement.offset().top
	// 		}, 1000);
	// 		scroll_index = scroll_index - 1;
	// 	}
	// 	return false;  
	// });

	
    // $(function() {
    //   $( ".scroll-up" ).click(function(){
    //      $('.rightbar-row').scrollTop($('.rightbar-row').scrollTop()-400);
    //   }); 
   
    //   $( ".scroll-down" ).click(function(){
    //     $('.rightbar-row').scrollTop($('.rightbar-row').scrollTop()+400);;
    //   }); 
   	// });
   

	
	// $('.scroll-down').click(function (e) { //#A_ID is an example. Use the id of your Anchor
	// 	$('.rightbar-row').animate({
	// 		scrollTop: $('.wrapPM').offset().top - 20 //#DIV_ID is an example. Use the id of your destination on the page
	// 	}, 'slow');
	// });


	
	$(".sidebar1-close").click(function(){
		$("#sidebar1").hide("slow"); 
		 });
		 
	$(".side-top").click(function(){
		// $("#sidebar1")
		$("#sidebar1").show("slow").css("display","inline-flex");
	  });
	
	$("#tabnewblack").click(function(){
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

	$("#tab_ligtbox1").click(function(){
	    $(".title1").addClass("active");
		$('.tab1').removeClass('tabactive');
		$('#tab_ligtbox').addClass('tabactive');
		$(".title1_1").removeClass("active");
		 $("tab1").addClass(".side-tabs ul li.active");
		$(".add-grupper").hide();
		$(".lightbox").fadeIn(); 
		$(".grupper").fadeOut();
		$("#sidebar1").show("slow"); 
	});


	/*	$("#profile_popup").click(function(event){

	    $scope.IsProfileImage = true;
		$scope.IsProfileVideo = false;
		$scope.responsiveProfileDetail = true;
		$('#video_nav').hide();
		$scope.currentIndex=0;
		document.getElementById('image_icons').style.visibility = "visibility";
		document.getElementById('video_icons').style.visibility = "visibility";
		document.getElementById('row-res').style.width = null;
		$scope.photoiconclass = 'photo-iconactive';
		$scope.mediaiconclass = 'media-icon';
		$scope.infoiconclass = 'info-icon';

		var myEl = angular.element( document.querySelector( '.videodiv' ) );
		myEl.addClass('video_call4');
		myEl.removeClass('video_call8');
		var video = $('#pro_video')[0];
		video.pause();
		video.currentTime = 0;
		/*$("#profile_popup").fadeOut();*/
			
	
	  
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
		 
	$("#tab_group1").click(function(){
	    $(".title1_1").addClass("active");
		$('.tab1').removeClass('tabactive');
		$('#tab_group').addClass('tabactive');
		$(".add-grupper").show();
        $("tab2").addClass(".side-tabs ul li.active");
		$(".title1").removeClass("active");
		$(".grupper").fadeIn(); 
		$(".lightbox").fadeOut();
		$("#sidebar1").show("slow").css("display","inline-flex"); 
	});
	$(".add-grupper").click(function(){
			$(".addbox").show();
			$("#new_group_name1").show().focus();
			 
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
	   
	/*$(".video_upload").click(function(){
		$("#profilebox").show();
	});*/
	  
	$(".kontakt").click(function(){
		$(this).toggleClass("active");
		$(".kon-dropdown").toggle("slow"); 
	});
		 
	
	$(".res-menu").unbind("click").click(function(){
		$(".res_menu ul li").fadeToggle("slow"); 
	});
	
	$(".res_menu ul li").unbind('click').click(function(){
		$(".res_menu ul li").hide();
	});

	$(".information-inside .poup-close2").click(function(){
		$(".res_menu ul li").fadeOut("slow"); 
	});

		$("#lightboxsubmit").click(function(){
		$("#lightboxsubmit").fadeOut("slow"); 
	});
	$scope.closeProfileboxpopup = closeProfileboxpopup;
	function closeProfileboxpopup() {
		$("#profilebox").fadeOut('slow');
	}
	
	$(".poup-close3 .video_download").click(function(e){
		e.stopPropagation();
	});

	$(".poup-close3").click(function(){
		$scope.IsProfileImage = true;
		$scope.IsProfileVideo = false;
		$scope.responsiveProfileDetail = false;
		$('#video_nav').hide();
		$scope.currentIndex=0;
		document.getElementById('image_icons').style.visibility = "visible";
		document.getElementById('video_icons').style.visibility = "visible";
		document.getElementById('row-res').style.width = null;
		$scope.photoiconclass = 'photo-iconactive';
		$scope.mediaiconclass = 'media-icon';
		$scope.infoiconclass = 'info-icon';

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

	$http.get('api/v1/getlightboxprofiles', {params: {view: 'home', grouptoken : $scope.groupToken}}).success(function(lightboxdata) {
		$scope.lbprofilecount =lightboxdata.count;
		if(lightboxdata.count){
			$scope.lbprofiles = lightboxdata.lbprofiles;
		}else{
			$scope.lbprofiles ='';
		}
	 });
  let grouptoken_groupid = localStorage.getItem('grouptoken_groupid');
  let grouping_arguments = {params: {view: 'home', grouptoken : $scope.groupToken}};
  if(grouptoken_groupid != undefined && grouptoken_groupid != ''){
    grouping_arguments = {params: {view: 'home', grouptoken : $scope.groupToken, grouptoken_groupid: grouptoken_groupid}};
  }
  // console.log(grouping_arguments);
	$http.get('api/v1/getgroupingprofiles', grouping_arguments).success(function(groupingdata) {
		$scope.gpprofilecount =groupingdata.count;
		if(groupingdata.count){
			$scope.gpprofiles = groupingdata.gpprofiles;
		}else{
			$scope.gpprofiles ='';
		}	
		// console.log($scope.gpprofiles);
	});

	var logged = '';
	var usertype = '';
	logged = $rootScope.globals.currentUser
	usertype = ( logged ) ? logged.usertype:'';
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
		//alert(genderval);
		if($scope.genderval == genderval){
			genderval = '';
		}
		$scope.genderval =genderval;
		if(genderval==1){
			angular.element(document.querySelector("#gender1")).addClass("active");
			angular.element(document.querySelector("#gender2")).removeClass("active");
			angular.element(document.querySelector("#gender3")).removeClass("active");
		}else if(genderval==2){
			angular.element(document.querySelector("#gender2")).addClass("active");
			angular.element(document.querySelector("#gender1")).removeClass("active");
			angular.element(document.querySelector("#gender3")).removeClass("active");
		}else{
			angular.element(document.querySelector("#gender2")).removeClass("active");
			angular.element(document.querySelector("#gender1")).removeClass("active");
			angular.element(document.querySelector("#gender3")).addClass("active");
		}
	}
	
	$scope.filterProfiles = filterProfiles;
	function filterProfiles() {
    $scope.currentPage = 0;
    $scope.pageSize = 40;
    $scope.profiles = [];
    $scope.q = '';
	$scope.loading = 'soger';
	if(!$scope.genderval){
		$scope.genderval='';
	}
     //$scope.dataLoading = true;  
    getprofiles_offset = 0;
		var formData = {search_text: $scope.search_text,age_from: $scope.age_from,age_to: $scope.age_to,genderval: $scope.genderval,purchase_name: $scope.purchase_name,submittype: $scope.submittype, offset: getprofiles_offset}
		$(".no_result_ajax").hide();
		$(".loading_ajax").show();
		$http.get('api/v1/getfilterprofiles', {params:formData}).success(function(homedata) {
			$scope.hasresults = true;
      		$(".rightbar-row").addClass("filteractive");
			$(".loading_ajax").hide();
			$scope.homedata = homedata;
			if(homedata.success === true){
				$scope.profiles = homedata.profiles;
				$(".no_result_ajax").hide();
			}else{
				$(".no_result_ajax").show();
				$scope.profiles ='';
				$scope.loading = 'Ingen data fundet';
			}
		});				
	  }
	  
	$scope.pbox_singleimage='';	
	$scope.getProfileBox = function (profileid){
    $scope.selectedgroupings = '';
	$scope.pbox_singleimage = '';
	$scope.profile_notes = '';
    $scope.pbox_profileid = profileid;
    $scope.disable_lightbox_submit = true;
    $scope.disable_lightbox_submit_style = {"background-color": "grey"};
		$http.get('api/v1/getgroupinglist', {params: {view: 'home', grouptoken : $scope.groupToken}}).success(function(groupingdata) {
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
	
	$(".group-view-toggle").click(function(){
		alert('aaa');
	});

	$scope.togglegrouping = togglegrouping;
	$scope.togglegrouping_top = togglegrouping_top;
	function togglegrouping($event){
		let this_element = $event.currentTarget;
		$(this_element).parent().siblings('.group-full').slideToggle();
		$(this_element).parent().parent().find(".group-view-toggle").toggleClass('rotated-arrow');
	}
	function togglegrouping_top($event){
		let this_element = $event.currentTarget;
		$(this_element).parent().parent().parent().siblings('.group-full').slideToggle();
		$(this_element).parent().parent().parent().parent().find(".group-view-toggle").toggleClass('rotated-arrow');
	}
	


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
    }
    else{			
			if(myElBut.hasClass('button2')){
      	selgroupings.push(groupingid);
			}
				 
    }
    $scope.selectedgroupings = selgroupings.toString();
    if($scope.selectedgroupings == ""){
      $scope.disable_lightbox_submit = true;
      $scope.disable_lightbox_submit_style = {"background-color": "grey"};
    }
    else{
      $scope.disable_lightbox_submit = false;
      $scope.disable_lightbox_submit_style = {};
    }

	}

	$scope.add_new_group = add_new_group;
	function add_new_group() {

		if($scope.new_group_name){

			var formData = {groupname: $scope.new_group_name, grouptoken : $scope.groupToken};
	
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

	$scope.addToGroup = addToGroup;
	function addToGroup(group_id){
		
	}


	function addToLightbox() {
    let pbox_profileid, profile_notes, selectedgroupings, groupToken;


    /*
		var formData = {
      userid            : userid, 
      profile_notes     : profile_notes, 
      selectedgroupings : selectedgroupings, 
      grouptoken        : groupToken
    };
    
    $.post("api/groupings/groupings.php", formData,
    function (data, textStatus, jqXHR) {
      alert('aa');
      console.log(formData);
      console.log(data);        
    },
  );
  */

 var formData = {profileid: $scope.pbox_profileid, profile_notes : $scope.profile_notes, selectedgroupings:$scope.selectedgroupings};
		$http.get('api/v1/updatelightboxprofiles', {params:formData}).success(function(lightboxdata) {
			$scope.lbprofilecount =lightboxdata.count;
			if(lightboxdata.count){
				$scope.lbprofiles = lightboxdata.lbprofiles;
					$http.get('api/v1/getgroupingprofiles', {params: {view: 'home', grouptoken : $scope.groupToken}}).success(function(groupingdata) {
						$scope.gpprofilecount =groupingdata.count;
						
						if(groupingdata.count){
							$scope.gpprofiles = groupingdata.gpprofiles;
						}else{
							$scope.gpprofiles ='';
						}

						$("#lightboxsubmit").fadeIn();
						setTimeout(function(){
							$("#lightboxsubmit").fadeOut();
						}, 3000);
					 });
			$("#profilebox").fadeOut();
			}else{
				$scope.lbprofiles ='';
			}
    });
    
	}
	
	$scope.removeFromLightboxPopup = removeFromLightboxPopup;
	function removeFromLightboxPopup(profileid, groupid) {
		$scope.removeprofileid = profileid;
		$scope.removeProfileFromGroupId = groupid;
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
			var formData = {profileid: $scope.removeprofileid, grouptoken : $scope.groupToken}
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

	$scope.removeProfileFromGroup = removeProfileFromGroup;
	function removeProfileFromGroup(profileid, groupid) {
		if($scope.removeprofileid){
			var formData = {profileid: $scope.removeprofileid, grouptoken : $scope.groupToken, groupid : $scope.removeProfileFromGroupId}
			// console.log(formData);
			$scope.ifLightboxFormRemove = false;
			$http.get('api/v1/removeProfileFromGroup', {params:formData}).success(function() {
				$http.get('api/v1/getgroupingprofiles', {params: {view: 'home', grouptoken : $scope.groupToken}}).success(function(groupingdata) {
					// console.log(groupingdata);
					$scope.gpprofilecount =groupingdata.count;
					if(groupingdata.count){
						$scope.gpprofiles = groupingdata.gpprofiles;
					}else{
						$scope.gpprofiles ='';
					}
					// console.log($scope.gpprofiles);
				 });
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
			var formData = {groupid: $scope.removegroupid, grouptoken : $scope.groupToken}
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
		$("input[id=new_group_name1]").trigger('click');
	};

	$scope.addgroupintoGrouplist = addgroupintoGrouplist;
	function addgroupintoGrouplist() {
		if($scope.new_group_name1){
			var formData = {groupname: $scope.new_group_name1, grouptoken : $scope.groupToken}
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
	$("#new_group_name1").blur(function(){
		$(".addbox").fadeOut();
	});
	$("#thumb1").click(function(){
		$("inside-popup").show();
		$("popup-video").hide(); 
	});
	$(".searchmini").click(function(){
		// $rootScope.interface = 'ansog';
		$(".leftbar").fadeIn();
	});
	$(".leftbar .side-submit").click(function(){
		// $rootScope.interface = 'home';
		// $(".leftbar").fadeOut();
	});

	$scope.singleimage = '';
	$scope.IsProfileImage = true;
	$scope.IsProfileVideo = false;
	$scope.isGetSingleLoading=false;
	
	$scope.getSingleProfile = function (profileid){
		$scope.IsProfileImage = true;
		$scope.IsProfileVideo = false;
		$scope.responsiveProfileDetail = false;
		// console.log($window.outerWidth);
		if($window.outerWidth >= 992){
			$scope.responsiveProfileDetail = true;
		}
		var videoElement = $('video')[0];
		// console.log(videoElement);
		if(videoElement != undefined){
			videoElement.pause();
			videoElement.removeAttribute('src'); // empty source
			videoElement.load();
		}

		$scope.currVideoUrl = '';
		$scope.pbox_profileid = profileid;
		$scope.isGetSingleLoading=true;
		let current_language = ($scope.isDanish) ? 'dk':'en';
		$http.get('api/v1/getsingleprofiles', {params:{profileid:profileid, lang: current_language}}).success(function(profiledata) {
			//alert(response.success);
			// console.log(profiledata);
			if(profiledata.success){
				
				$scope.isGetSingleLoading=false;
				$scope.singleprofile = profiledata.profile;
				
				$scope.profile_images = profiledata.profile_images;
				$scope.skills = profiledata.skills;
				$scope.categories = profiledata.categories;
				$scope.lang = profiledata.lang;
				$scope.licenses=profiledata.licenses;
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
		jQuery('.video_side').removeClass('inactive');
		jQuery('#imagediv').addClass('inactive');
		jQuery('#photo_nav').hide();
		//alert($scope.profile_videos.length)
		jQuery('#video_nav').show();
		$scope.IsProfileVideo = true;
		$scope.IsProfileImage = false;
		$scope.currVideoUrl = profilevideo;
		// console.log($scope.currVideoUrl);
		// $scope.singleimage = $scope.currVideoUrl;
		var video = $('#pro_video')[0];
		jQuery('video').mediaelementplayer({
			alwaysShowControls: true,
			videoVolume: 'horizontal',
			usePluginFullScreen : false,
			features: ['playpause','progress', 'fullscreen']
		});
		video.load();
		// video.play();
		// video.pause();
	};
	$scope.selectedThumb=0;
	$scope.changeSingleImageBig = changeSingleImageBig;
	function changeSingleImageBig(profileimage, index) {
		//alert(profileimage);
		$scope.selectedThumb=index;
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
		$scope.currentIndexVideo < $scope.profile_videos.length-1 ? $scope.currentIndexVideo++ : $scope.currentIndexVideo=0;
		
	};
	$scope.prev_video=function(){
		//alert($scope.currentIndexVideo);
		$scope.currentIndexVideo>0?$scope.currentIndexVideo--:$scope.currentIndexVideo=$scope.profile_videos.length-1;
	};
	$scope.$watch('currentIndexVideo',function(){
		// alert($scope.currentIndexVideo);
		if($scope.profile_videos){
			$scope.currVideoUrl = $scope.profile_videos[$scope.currentIndexVideo].fullpath;
			var video = $('#pro_video')[0];
			/*$('video').mediaelementplayer({
				alwaysShowControls: false,
				videoVolume: 'horizontal',
				usePluginFullScreen : false,
				features: ['playpause','progress', 'fullscreen']
			});*/
			video.load();
			// video.play();

		}
	});

	$scope.changeToImageView = changeToImageView;
	function changeToImageView() {
		$scope.responsiveProfileDetail = false;
		$scope.IsProfileVideo = false;
		$scope.IsProfileImage = true;
		$scope.photoiconclass = 'photo-iconactive';
		$scope.mediaiconclass = 'media-icon';
		$scope.infoiconclass = 'info-icon';
		if($scope.profile_images){
			$scope.singleimage = $scope.profile_images[0].fullpath;
		}
		var video = $('#pro_video')[0];
		video.pause();
		video.currentTime = 0;
	}

	$scope.changeToVideoView = changeToVideoView;
	function changeToVideoView() {
		$scope.responsiveProfileDetail = false;
		$scope.IsProfileVideo = true;
		$scope.IsProfileImage = false;
		$scope.photoiconclass = 'photo-icon';
		$scope.mediaiconclass = 'media-iconactive';
		$scope.infoiconclass = 'info-icon';
		$('#video_nav').show();
		if($scope.profile_videos){
			$scope.currVideoUrl = $scope.profile_videos[$scope.currentIndexVideo].fullpath;
			// console.log($scope.currVideoUrl);
			// changeSingleVideoBig($scope.currVideoUrl);

			// jQuery('#video_nav').show();
			$scope.IsProfileVideo = true;
			$scope.IsProfileImage = false;
			// $scope.currVideoUrl = profilevideo;
			// console.log($scope.currVideoUrl);
			// $scope.singleimage = $scope.currVideoUrl;
			var video = $('video');
			/*
			jQuery('video').mediaelementplayer({
				alwaysShowControls: true,
				videoVolume: 'horizontal',
				usePluginFullScreen : false,
				features: ['playpause','progress', 'fullscreen']
			});*/
			video.load();

			// var video = $('#pro_video')[0];
			// $('video').mediaelementplayer({
			// 	alwaysShowControls: false,
			// 	videoVolume: 'horizontal',
			// 	usePluginFullScreen : false,
			// 	features: ['playpause','progress', 'fullscreen']
			// });
			// video.load();
			// video.play();

		}
	}

	$scope.changeToDetailView = changeToDetailView;
	function changeToDetailView() {
		$scope.responsiveProfileDetail = true;
		$scope.photoiconclass = 'photo-icon';
		$scope.mediaiconclass = 'media-icon';
		$scope.infoiconclass = 'info-iconactive';
		$(".text-res").fadeIn('slow');
		$scope.IsProfileVideo = false;
		$scope.IsProfileImage = false;
		var video = $('#pro_video')[0];
		video.pause();
		video.currentTime = 0;
	}
	var w = angular.element($window);
	w.bind('resize', function () {
		//console.log('resize');
		//alert($window.innerWidth);
		if($window.innerWidth > 991){
			$scope.responsiveProfileDetail = true;
		}
		$scope.IsProfileVideo = false;
		$scope.IsProfileImage = true;
		if($scope.profile_images){
			$scope.singleimage = $scope.profile_images[0].fullpath;
		}
		var video = $('#pro_video')[0];
		video.pause();
		video.currentTime = 0;
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

	$scope.closeProfileboxpopup2 = function (){
		$("#profilebox").fadeOut(); 
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

  $scope.showEmailPopup = function (){
    $("#lightbox_popup.email").fadeIn('slow');
    $("#lightbox_popup.email #to_email").val("tony.grahn@themethodlab.com");
  };
  $scope.cancelEmailPopup = function (){
    $("#lightbox_popup.email").fadeOut('slow'); 
  };
  $scope.sendEmailForm = function (){
    $scope.to_email = "tony.grahn@themethodlab.com";
    // $scope.to_email = "vs@anewnative.com, padmanabhan.code@gmail.com";
    var formData = {
            from_email: $scope.from_email,
            to_email: $scope.to_email,
            to_cc: $scope.to_cc,
            mail_body: $scope.mail_body,
            }
    $http.post('api/v1/sendemail', formData).success(function(response) {
      if(response.success){
    	$("#lightbox_popup.email").fadeOut('slow'); 
        $scope.from_email ='';
        $scope.to_email ='';
        $scope.to_cc ='';
        $scope.mail_body ='';
        $scope.response_text =response.message;
        $scope.apply;
      }
    }); 
    // $("#profile_popup").fadeIn("slow"); 
  }

$scope.sendGroupForm = sendGroupForm;

function sendGroupForm(groupid) {

	//alert($scope.groupid);

	var formData = {
						gpid:$scope.gid,
						form_email: $scope.form_email,
						to_email: $scope.to_email,
						to_cc: $scope.to_cc,
            mail_body: $scope.mail_body,
            username: $scope.from_name,
						}
						

		$http.post('api/v1/sendgroup', formData).success(function(sendgroupstatus) {
			if(sendgroupstatus.success){
				$scope.ifLightboxForm = false;
				$scope.ifLightboxFormSuccess = true;
				$scope.form_email ='';$scope.to_email ='';$scope.to_cc ='';$scope.mail_body ='';
				$scope.response_text =sendgroupstatus.message;
				$scope.apply;
				setTimeout(function(){
					$("#send_group_popup").fadeOut();
					$scope.ifLightboxFormSuccess = false;
					$scope.ifLightboxForm = true;
				}, 3000);
			}
		});	
	}
/*$scope.sendGroupForm = function (){
		var formData = {
						gpid: $scope.groupid,
						form_email: $scope.form_email,
						to_email: $scope.to_email,
						to_cc: $scope.to_cc,
						mail_body: $scope.mail_body,
						}

						alert(formData);
		$http.post('api/v1/sendgroup', formData).success(function(sendlightboxstatus) {
			if(sendlightboxstatus.success){
				$scope.ifLightboxForm = false;
				$scope.ifLightboxFormSuccess = true;
				$scope.form_email ='';$scope.to_email ='';$scope.to_cc ='';$scope.mail_body ='';
				$scope.response_text =sendlightboxstatus.message;
				$scope.apply;
			}
		});	
		$("#profile_popup").fadeIn("slow"); 
	}*/


	$scope.showNotesPopup = function (){
		$("#notes_popup").fadeIn('slow'); 
	};
	$("#close_notes_popup").click(function(){
		$("#notes_popup").fadeOut(); 
	});
	
	/*$scope.showSendGroupPopup = function (){
		$("#send_group_popup").fadeIn('slow'); 
	};*/

	$scope.showSendGroupPopup = showSendGroupPopup;

function showSendGroupPopup(groupid) {
		$scope.gid = groupid;
		//alert(groupid);
		$("#send_group_popup").fadeIn('slow');
	}


	$("#close_sendgroup").click(function(){
		$("#send_group_popup").fadeOut(); 
		$scope.ifLightboxFormSuccess = false;
		$scope.ifLightboxForm = true;
	});

	$scope.cancelLigtbox = function (){
		$("#send_group_popup").fadeOut(); 
	};

		$scope.cancelLigtbox2 = function (){
		$("#lightbox_popup").fadeOut(); 
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
    $scope.apply;

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