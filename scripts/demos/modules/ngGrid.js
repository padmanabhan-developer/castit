var nggrid = angular.module('theme.demos.ng_grid', ['ngGrid']);

  nggrid.controller('CustomerManagementController', ['$scope', '$rootScope', '$filter', '$http', function($scope, $rootScope, $filter, $http) {
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
	
	console.log($scope.usertype);
	
    $scope.filterOptions = {
      filterText: '',
      useExternalFilter: true
    };
    $scope.totalServerItems = 0;
    $scope.pagingOptions = {
      pageSizes: [25, 50, 100],
      pageSize: 25,
      currentPage: 1
    };
    $scope.setPagingData = function(data, page, pageSize) {
      var pagedData = data.slice((page - 1) * pageSize, page * pageSize);
      $scope.myData = pagedData;
      $scope.totalServerItems = data.length;
      if (!$scope.$$phase) {
        $scope.$apply();
      }
    };
    $scope.getPagedDataAsync = function(pageSize, page, searchText) {
      setTimeout(function() {
        var data;
        if (searchText) {
          var ft = searchText.toLowerCase();
          $http.get('api/v1/customer').success(function(largeLoad) {
            data = largeLoad.filter(function(item) {
              return JSON.stringify(item).toLowerCase().indexOf(ft) !== -1;
            });
            $scope.setPagingData(data, page, pageSize);
          });
        } else {
          $http.get('api/v1/customer').success(function(largeLoad) {
            $scope.setPagingData(largeLoad, page, pageSize);
          });
        }
      }, 100);
    };

    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

    $scope.$watch('pagingOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);
    $scope.$watch('filterOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);

    $scope.gridOptions = {
      data: 'myData',
	  enableRowSelection: false,
      enablePaging: true,
      showFooter: true,
      totalServerItems: 'totalServerItems',
      pagingOptions: $scope.pagingOptions,
      filterOptions: $scope.filterOptions,
	  columnDefs: [
      { field: 'name', displayName: 'Name'},
	  { field: 'email', displayName: 'Email'},
	  { field: 'city', displayName: 'City'},
	  { field: 'region', displayName: 'Region'},
	  { field: 'status', displayName: 'Status',cellTemplate:'<span class="{{row.getProperty(col.field)}}"></span>'},
	  { field: 'id', displayName: 'Action', cellTemplate: 
             '<div class="grid-action-cell">'+
             '<a href="#/edit-customer/{{row.getProperty(col.field)}}" class="btn-edit btn-small">Edit</a></div>'}]
    };
  }]);
  
  nggrid.controller('PetsManagementController', ['$scope', '$rootScope', '$filter', '$http', function($scope, $rootScope, $filter, $http) {
    'use strict';
	
	var logged = '';
	var usertype = '';
	logged = $rootScope.globals.currentUser
	usertype = ( logged )?logged.usertype:'';
	if(usertype == 1 || usertype == 2) {
		$scope.usertype = true;	
	}
	else {
		$scope.usertype = false;	
	}
	
    $scope.filterOptions = {
      filterText: '',
      useExternalFilter: true
    };
    $scope.totalServerItems = 0;
    $scope.pagingOptions = {
      pageSizes: [25, 50, 100],
      pageSize: 25,
      currentPage: 1
    };
    $scope.setPagingData = function(data, page, pageSize) {
      var pagedData = data.slice((page - 1) * pageSize, page * pageSize);
      $scope.myData = pagedData;
      $scope.totalServerItems = data.length;
      if (!$scope.$$phase) {
        $scope.$apply();
      }
    };
    $scope.getPagedDataAsync = function(pageSize, page, searchText) {
      setTimeout(function() {
        var data;
        if (searchText) {
          var ft = searchText.toLowerCase();
          $http.get('api/v1/pets').success(function(largeLoad) {
            data = largeLoad.filter(function(item) {
              return JSON.stringify(item).toLowerCase().indexOf(ft) !== -1;
            });
            $scope.setPagingData(data, page, pageSize);
          });
        } else {
          $http.get('api/v1/pets').success(function(largeLoad) {
            $scope.setPagingData(largeLoad, page, pageSize);
          });
        }
      }, 100);
    };

    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

    $scope.$watch('pagingOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);
    $scope.$watch('filterOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);

    $scope.gridOptions = {
      data: 'myData',
	  enableRowSelection: false,
      enablePaging: true,
      showFooter: true,
      totalServerItems: 'totalServerItems',
      pagingOptions: $scope.pagingOptions,
      filterOptions: $scope.filterOptions,
	  columnDefs: [
	  { field: 'id', displayName: 'S.No'},
      { field: 'species', displayName: 'Type'},
      { field: 'pet_name', displayName: 'Pet Name'},
      { field: 'owner_name', displayName: 'Owner Name'},
	  { field: 'chip_no', displayName: 'Chip No'},
	  { field: 'status', displayName: 'Status',cellTemplate:'<span class="{{row.getProperty(col.field)}}"></span>'},
	  { field: 'pet_id', displayName: 'Action', cellTemplate: 
             '<div class="grid-action-cell">'+
             '<a href="#/edit-pet/{{row.getProperty(col.field)}}" class="btn-edit btn-small">Edit</a>'}]
    };
  }]);
  
  nggrid.controller('LostpetsManagementController_old', ['$scope', '$rootScope', '$filter', '$http', function($scope, $rootScope, $filter, $http) {
    'use strict';
	
	var logged = '';
	var usertype = '';
	logged = $rootScope.globals.currentUser
	usertype = ( logged )?logged.usertype:'';
	if(usertype == 1 || usertype == 3) {
		$scope.usertype = true;	
	}
	else {
		$scope.usertype = false;	
	}
	
    $scope.filterOptions = {
      filterText: '',
      useExternalFilter: true
    };
    $scope.totalServerItems = 0;
    $scope.pagingOptions = {
      pageSizes: [25, 50, 100],
      pageSize: 25,
      currentPage: 1
    };
    $scope.setPagingData = function(data, page, pageSize) {
      var pagedData = data.slice((page - 1) * pageSize, page * pageSize);
      $scope.myData = pagedData;
      $scope.totalServerItems = data.length;
      if (!$scope.$$phase) {
        $scope.$apply();
      }
    };
    $scope.getPagedDataAsync = function(pageSize, page, searchText) {
      setTimeout(function() {
        var data;
        if (searchText) {
          var ft = searchText.toLowerCase();
          $http.get('api/v1/lostpets').success(function(largeLoad) {
            data = largeLoad.filter(function(item) {
              return JSON.stringify(item).toLowerCase().indexOf(ft) !== -1;
            });
            $scope.setPagingData(data, page, pageSize);
          });
        } else {
          $http.get('api/v1/lostpets').success(function(largeLoad) {
            $scope.setPagingData(largeLoad, page, pageSize);
          });
        }
      }, 100);
    };

    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

    $scope.$watch('pagingOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);
    $scope.$watch('filterOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);

    $scope.gridOptions = {
      data: 'myData',
	  enableRowSelection: false,
      enablePaging: true,
      showFooter: true,
      totalServerItems: 'totalServerItems',
      pagingOptions: $scope.pagingOptions,
      filterOptions: $scope.filterOptions,
	  columnDefs: [
	  { field: 'slno', displayName: 'S.No'},
      { field: 'species', displayName: 'Species'},
	  { field: 'lost_on', displayName: 'Lost from'},
	  { field: 'status', displayName: 'Status',cellTemplate:'<span class="{{row.getProperty(col.field)}}"></span>'},
	  { field: 'id', displayName: 'Action', cellTemplate: 
             '<div class="grid-action-cell">'+
             '<a href="#/edit-lostpet/{{row.getProperty(col.field)}}" class="btn-edit btn-small">Edit</a>'}]
    };
  }]);

   nggrid.controller('FoundpetsManagementController', ['$scope', '$rootScope', '$filter', '$http', function($scope, $rootScope, $filter, $http) {
    'use strict';
	
	var logged = '';
	var usertype = '';
	logged = $rootScope.globals.currentUser
	usertype = ( logged )?logged.usertype:'';
	if(usertype == 1 || usertype == 3) {
		$scope.usertype = true;	
	}
	else {
		$scope.usertype = false;	
	}
	
    $scope.filterOptions = {
      filterText: '',
      useExternalFilter: true
    };
    $scope.totalServerItems = 0;
    $scope.pagingOptions = {
      pageSizes: [25, 50, 100],
      pageSize: 25,
      currentPage: 1
    };
    $scope.setPagingData = function(data, page, pageSize) {
      var pagedData = data.slice((page - 1) * pageSize, page * pageSize);
      $scope.myData = pagedData;
      $scope.totalServerItems = data.length;
      if (!$scope.$$phase) {
        $scope.$apply();
      }
    };
    $scope.getPagedDataAsync = function(pageSize, page, searchText) {
      setTimeout(function() {
        var data;
        if (searchText) {
          var ft = searchText.toLowerCase();
          $http.get('api/v1/foundpets').success(function(largeLoad) {
            data = largeLoad.filter(function(item) {
              return JSON.stringify(item).toLowerCase().indexOf(ft) !== -1;
            });
            $scope.setPagingData(data, page, pageSize);
          });
        } else {
          $http.get('api/v1/foundpets').success(function(largeLoad) {
            $scope.setPagingData(largeLoad, page, pageSize);
          });
        }
      }, 100);
    };

    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

    $scope.$watch('pagingOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);
    $scope.$watch('filterOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);

    $scope.gridOptions = {
      data: 'myData',
	  enableRowSelection: false,
      enablePaging: true,
      showFooter: true,
      totalServerItems: 'totalServerItems',
      pagingOptions: $scope.pagingOptions,
      filterOptions: $scope.filterOptions,
	  columnDefs: [
	  { field: 'slno', displayName: 'S.No'},
      { field: 'species', displayName: 'Species'},
	  { field: 'found_on', displayName: 'Found on'},
	  { field: 'status', displayName: 'Status',cellTemplate:'<span class="{{row.getProperty(col.field)}}"></span>'},
	  { field: 'id', displayName: 'Action', cellTemplate: 
             '<div class="grid-action-cell">'+
             '<a href="#/edit-foundpet/{{row.getProperty(col.field)}}" class="btn-edit btn-small">Edit</a>'}]
    };
  }]);

   nggrid.controller('ChipManagementController', ['$scope', '$rootScope', '$filter', '$http', function($scope, $rootScope, $filter, $http) {
    'use strict';
	
	var logged = '';
	var usertype = '';
	logged = $rootScope.globals.currentUser
	usertype = ( logged )?logged.usertype:'';
	if(usertype == 1 || usertype == 2) {
		$scope.usertype = true;	
	}
	else {
		$scope.usertype = false;	
	}
	
    $scope.filterOptions = {
      filterText: '',
      useExternalFilter: true
    };
    $scope.totalServerItems = 0;
    $scope.pagingOptions = {
      pageSizes: [25, 50, 100],
      pageSize: 25,
      currentPage: 1
    };
    $scope.setPagingData = function(data, page, pageSize) {
      var pagedData = data.slice((page - 1) * pageSize, page * pageSize);
      $scope.myData = pagedData;
      $scope.totalServerItems = data.length;
      if (!$scope.$$phase) {
        $scope.$apply();
      }
    };
    $scope.getPagedDataAsync = function(pageSize, page, searchText) {
      setTimeout(function() {
        var data;
        if (searchText) {
          var ft = searchText.toLowerCase();
          $http.get('api/v1/chips').success(function(largeLoad) {
            data = largeLoad.filter(function(item) {
              return JSON.stringify(item).toLowerCase().indexOf(ft) !== -1;
            });
            $scope.setPagingData(data, page, pageSize);
          });
        } else {
          $http.get('api/v1/chips').success(function(largeLoad) {
            $scope.setPagingData(largeLoad, page, pageSize);
          });
        }
      }, 100);
    };

    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

    $scope.$watch('pagingOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);
    $scope.$watch('filterOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);

    $scope.gridOptions = {
      data: 'myData',
	  enableRowSelection: false,
      enablePaging: true,
      showFooter: true,
      totalServerItems: 'totalServerItems',
      pagingOptions: $scope.pagingOptions,
      filterOptions: $scope.filterOptions,
	  columnDefs: [
	  { field: 'id', displayName: 'S.No'},
      { field: 'chip_code', displayName: 'Chip No'},
      { field: 'pet_name', displayName: 'Pet Name'},
      { field: 'owner_name', displayName: 'Owner Name'},
	  { field: 'status', displayName: 'Status',cellTemplate:'<span class="{{row.getProperty(col.field)}}"></span>'},
	  { field: 'chip_id', displayName: 'Action', cellTemplate: 
             '<div class="grid-action-cell">'+
             '<a href="#/edit-chip/{{row.getProperty(col.field)}}" class="btn-edit btn-small">Edit</a>'}]
    };
  }]);

    nggrid.controller('BreedManagementController', ['$scope', '$rootScope', '$filter', '$http', function($scope, $rootScope, $filter, $http) {
    'use strict';
	
	var logged = '';
	var usertype = '';
	logged = $rootScope.globals.currentUser
	usertype = ( logged )?logged.usertype:'';
	if(usertype == 1 || usertype == 2) {
		$scope.usertype = true;	
	}
	else {
		$scope.usertype = false;	
	}
	
    $scope.filterOptions = {
      filterText: '',
      useExternalFilter: true
    };
    $scope.totalServerItems = 0;
    $scope.pagingOptions = {
      pageSizes: [25, 50, 100],
      pageSize: 25,
      currentPage: 1
    };
    $scope.setPagingData = function(data, page, pageSize) {
      var pagedData = data.slice((page - 1) * pageSize, page * pageSize);
      $scope.myData = pagedData;
      $scope.totalServerItems = data.length;
      if (!$scope.$$phase) {
        $scope.$apply();
      }
    };
    $scope.getPagedDataAsync = function(pageSize, page, searchText) {
      setTimeout(function() {
        var data;
        if (searchText) {
          var ft = searchText.toLowerCase();
          $http.get('api/v1/breeds').success(function(largeLoad) {
            data = largeLoad.filter(function(item) {
              return JSON.stringify(item).toLowerCase().indexOf(ft) !== -1;
            });
            $scope.setPagingData(data, page, pageSize);
          });
        } else {
          $http.get('api/v1/breeds').success(function(largeLoad) {
            $scope.setPagingData(largeLoad, page, pageSize);
          });
        }
      }, 100);
    };

    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

    $scope.$watch('pagingOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);
    $scope.$watch('filterOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);

    $scope.gridOptions = {
      data: 'myData',
	  enableRowSelection: false,
      enablePaging: true,
      showFooter: true,
      totalServerItems: 'totalServerItems',
      pagingOptions: $scope.pagingOptions,
      filterOptions: $scope.filterOptions,
	  columnDefs: [
	  { field: 'id', displayName: 'S.No'},
      { field: 'breed_name', displayName: 'Breed Name'},
      { field: 'speciesname', displayName: 'Species Type'},
	  { field: 'status_short', displayName: 'Status',cellTemplate:'<span class="{{row.getProperty(col.field)}}"></span>'},
	  { field: 'breed_id', displayName: 'Action', cellTemplate: 
             '<div class="grid-action-cell">'+
             '<a href="#/edit-breed/{{row.getProperty(col.field)}}" class="btn-edit btn-small">Edit</a>'}]
    };
  }]);
  
    nggrid.controller('SpeciesManagementController', ['$scope', '$rootScope', '$filter', '$http', function($scope, $rootScope, $filter, $http) {
    'use strict';
	
	var logged = '';
	var usertype = '';
	logged = $rootScope.globals.currentUser
	usertype = ( logged )?logged.usertype:'';
	if(usertype == 1 || usertype == 2) {
		$scope.usertype = true;	
	}
	else {
		$scope.usertype = false;	
	}
	
    $scope.filterOptions = {
      filterText: '',
      useExternalFilter: true
    };
    $scope.totalServerItems = 0;
    $scope.pagingOptions = {
      pageSizes: [25, 50, 100],
      pageSize: 25,
      currentPage: 1
    };
    $scope.setPagingData = function(data, page, pageSize) {
      var pagedData = data.slice((page - 1) * pageSize, page * pageSize);
      $scope.myData = pagedData;
      $scope.totalServerItems = data.length;
      if (!$scope.$$phase) {
        $scope.$apply();
      }
    };
    $scope.getPagedDataAsync = function(pageSize, page, searchText) {
      setTimeout(function() {
        var data;
        if (searchText) {
          var ft = searchText.toLowerCase();
          $http.get('api/v1/species').success(function(largeLoad) {
            data = largeLoad.filter(function(item) {
              return JSON.stringify(item).toLowerCase().indexOf(ft) !== -1;
            });
            $scope.setPagingData(data, page, pageSize);
          });
        } else {
          $http.get('api/v1/species').success(function(largeLoad) {
            $scope.setPagingData(largeLoad, page, pageSize);
          });
        }
      }, 100);
    };

    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

    $scope.$watch('pagingOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);
    $scope.$watch('filterOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);

    $scope.gridOptions = {
      data: 'myData',
	  enableRowSelection: false,
      enablePaging: true,
      showFooter: true,
      totalServerItems: 'totalServerItems',
      pagingOptions: $scope.pagingOptions,
      filterOptions: $scope.filterOptions,
	  columnDefs: [
	  { field: 'id', displayName: 'S.No'},
      { field: 'type_name', displayName: 'Species Type Name'},
	  { field: 'status_short', displayName: 'Status',cellTemplate:'<span class="{{row.getProperty(col.field)}}"></span>'},
	  ]
    };
  }]);

  nggrid.controller('ReportManagementController', ['$scope', '$rootScope', '$filter', '$http', function($scope, $rootScope, $filter, $http) {
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
	
    $scope.filterOptions = {
      filterText: '',
      useExternalFilter: true
    };
    $scope.totalServerItems = 0;
    $scope.pagingOptions = {
      pageSizes: [25, 50, 100],
      pageSize: 25,
      currentPage: 1
    };
    $scope.setPagingData = function(data, page, pageSize) {
      var pagedData = data.slice((page - 1) * pageSize, page * pageSize);
      $scope.myData = pagedData;
      $scope.totalServerItems = data.length;
      if (!$scope.$$phase) {
        $scope.$apply();
      }
    };
    $scope.getPagedDataAsync = function(pageSize, page, searchText) {
      setTimeout(function() {
        var data;
        if (searchText) {
          var ft = searchText.toLowerCase();
          $http.get('api/v1/reportTryout').success(function(largeLoad) {
            data = largeLoad.filter(function(item) {
              return JSON.stringify(item).toLowerCase().indexOf(ft) !== -1;
            });
            $scope.setPagingData(data, page, pageSize);
          });
        } else {
          $http.get('api/v1/reportTryout').success(function(largeLoad) {
            $scope.setPagingData(largeLoad, page, pageSize);
          });
        }
      }, 100);
    };

    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

    $scope.$watch('pagingOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);
    $scope.$watch('filterOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);

    $scope.gridOptions = {
      data: 'myData',
	  enableRowSelection: false,
      enablePaging: true,
      showFooter: true,
      totalServerItems: 'totalServerItems',
      pagingOptions: $scope.pagingOptions,
      filterOptions: $scope.filterOptions,
	  columnDefs: [
	  { field: 'id', displayName: 'S.No'},
      { field: 'name', displayName: 'Name'},
	  { field: 'shirt_size', displayName: 'Shirt Size'},
	  { field: 'division', displayName: 'Division'},
	  { field: 'date', displayName: 'Tryout Date'},
	  { field: 'time', displayName: 'Tryout Time'}]
    };
	
	$scope.generateReport = generateReport;
	
	function generateReport() {
		
		$http.get('api/v1/reportTryout').success(function(report) {
			var reportData = report; 
			
			var excel="<table>";
			// Header
			excel += "<tr><td>S.No</td><td>Name</td><td>Email</td><td>DOB</td><td>Nationality</td><td>Shirt Size</td><td>Division</td><td>Father Email</td><td>Father Mobile</td><td>Father Volunteer</td><td>Mother Email</td><td>Mother Mobile</td><td>Mother Volunteer</td><td>Tryout Date</td><td>Tryout Time</td><td>Additional Info</td><td>Special Request</td><td>League Comments</td><td>Amount</td><td>Payment Status</td></tr>";
			$.each(reportData, function(key, value) {
				//console.log(value)
				excel += "<tr>";
				$.each(value, function(key1, value1) {
					excel += "<td>" + value1 + "</td>";
				});
				excel += '</tr>';
			}) 
			excel += '</table>'
			//console.log(excel)
			
			var excelFile = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
			excelFile += "<head>";
			excelFile += "<!--[if gte mso 9]>";
			excelFile += "<xml>";
			excelFile += "<x:ExcelWorkbook>";
			excelFile += "<x:ExcelWorksheets>";
			excelFile += "<x:ExcelWorksheet>";
			excelFile += "<x:Name>";
			excelFile += "{worksheet}";
			excelFile += "</x:Name>";
			excelFile += "<x:WorksheetOptions>";
			excelFile += "<x:DisplayGridlines/>";
			excelFile += "</x:WorksheetOptions>";
			excelFile += "</x:ExcelWorksheet>";
			excelFile += "</x:ExcelWorksheets>";
			excelFile += "</x:ExcelWorkbook>";
			excelFile += "</xml>";
			excelFile += "<![endif]-->";
			excelFile += "</head>";
			excelFile += "<body>";
			excelFile += excel;
			excelFile += "</body>";
			excelFile += "</html>";

			var base64data = "base64," + $.base64.encode(excelFile);
			window.open('data:application/vnd.ms-excel;filename=exportData.xlsx;' + base64data);
		});
	} 
	
  }]);
  
  nggrid.controller('TablesAdvancedController', ['$scope', '$filter', '$http', function($scope, $filter, $http) {
    'use strict';
    $scope.filterOptions = {
      filterText: '',
      useExternalFilter: true
    };
    $scope.totalServerItems = 0;
    $scope.pagingOptions = {
      pageSizes: [25, 50, 100],
      pageSize: 25,
      currentPage: 1
    };
    $scope.setPagingData = function(data, page, pageSize) {
      var pagedData = data.slice((page - 1) * pageSize, page * pageSize);
      $scope.myData = pagedData;
      $scope.totalServerItems = data.length;
      if (!$scope.$$phase) {
        $scope.$apply();
      }
    };
    $scope.getPagedDataAsync = function(pageSize, page, searchText) {
      setTimeout(function() {
        var data;
        if (searchText) {
          var ft = searchText.toLowerCase();
          $http.get('assets/demo/ng-data.json').success(function(largeLoad) {
            data = largeLoad.filter(function(item) {
              return JSON.stringify(item).toLowerCase().indexOf(ft) !== -1;
            });
            $scope.setPagingData(data, page, pageSize);
          });
        } else {
          $http.get('assets/demo/ng-data.json').success(function(largeLoad) {
            $scope.setPagingData(largeLoad, page, pageSize);
          });
        }
      }, 100);
    };

    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

    $scope.$watch('pagingOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);
    $scope.$watch('filterOptions', function(newVal, oldVal) {
      if (newVal !== oldVal) {
        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
      }
    }, true);

    $scope.gridOptions = {
      data: 'myData',
      enablePaging: true,
      showFooter: true,
      totalServerItems: 'totalServerItems',
      pagingOptions: $scope.pagingOptions,
      filterOptions: $scope.filterOptions
    };
	
	
  }]);
  