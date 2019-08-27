angular
  .module('castit', [
    'theme',
    'theme.demos',
  ])
  .config(['$provide', '$routeProvider', function($provide, $routeProvider) {
    'use strict';
    $routeProvider
      .when('/', {
        templateUrl: 'views/login.html',
        resolve: {
          loadCalendar: ['$ocLazyLoad', function($ocLazyLoad) {
            return $ocLazyLoad.load([
              'bower_components/fullcalendar/fullcalendar.js',
            ]);
          }]
        }
      })
      .when('/:templateFile', {
        templateUrl: function(param) {
          //alert(param.templateFile);
          return 'views/' + param.templateFile + '.html';
        }
      })
	  .when('/:templateFile/:id', {
        templateUrl: function(param) {
          return 'views/' + param.templateFile + '.html';
        }
      })
      .when('#', {
        templateUrl: 'views/index.html',
      })
      .when('#/mediaupload',{
        templateUrl: function(param) {
          return 'views/' + mediaupload + '.html';
        }
      })
      .when('#/customercreate',{
        templateUrl: function(param){
          return 'views/'+customercreate+'.html';
        }
      })
      .when('#/customerupdate',{
        templateUrl: function(param){
          return 'views/'+customercreate+'.html';
        }
      })
      .when('#/customerlogin',{
        templateUrl: function(param){
          return 'views/'+customerlogin+'.html';
        }
      })
      .otherwise({
        redirectTo: '/'
      });
  }])
  .run(['$rootScope', '$location', '$cookieStore', '$http', function($rootScope, $location, $cookieStore, $http) {
	  $rootScope.globals = $cookieStore.get('globals') || {};
        if ($rootScope.globals.currentUser) {
            $http.defaults.headers.common['Authorization'] = 'Basic ' + $rootScope.globals.currentUser.authdata; // jshint ignore:line
        }

        $rootScope.$on('$locationChangeStart', function (event, next, current) {
            // redirect to login page if not logged in and trying to access a restricted page
            var currentPathArray = $location.path().split('/');
            var currentPath = $location.path();
            if(currentPathArray.length > 2) {
              currentPath = '/' + currentPathArray[1];
            }
            var restrictedPage = $.inArray(currentPath, ['/my-profile_1','/my-profile_2','/my-profile_3','/my-profile_4','/my-profile_5','/my-profile_6','/mediaupload','/customercreate','/customerlogin','/customerupdate','/landing','/profiles', '/index', '/about-us', '/contact', '/ansog-trin1', '/ansog-trin2', '/ansog-trin3', '/ansog-trin4', '/ansog-trin5', '/login', '/ansog-trin6', '/ansog-trin7', '/reset-password']) === -1;
            var loggedIn = $rootScope.globals.currentUser;
            if (restrictedPage && !loggedIn) {
                $location.path('/landing');
            }
        });
  }]);