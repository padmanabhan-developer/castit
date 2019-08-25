angular.module('theme.demos.dashboard', [
    'angular-skycons',
    'theme.demos.forms'
  ])
  .controller('FrontendController', ['$scope', '$rootScope', '$http', '$timeout', '$window', function($scope,$rootScope, $http, $timeout, $window) {
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
	
	$http.get('api/v1/totalrec').success(function(totalrec) {
		$scope.usertotal = totalrec.usertotal
		$scope.foundpetscount = totalrec.foundpetscount
		$scope.lostpetscount = totalrec.lostpetscount
		$scope.petscount = totalrec.petscount
     });
	 
    $scope.customerList = function() {
      return this.customer;
    };
	
	$scope.tshirtList = function() {
      return this.tshirt;
    };
	$scope.petsList = function() {
      return this.pets;
    };
	
	$scope.foundpetsList = function() {
      return this.foundpets;
    };
	$scope.lostpetsList = function() {
      return this.lostpets;
    };

    $scope.uaHandleSelected = function() {
      this.customer = _.filter(this.customer, function(item) {
        return (item.rem === false || item.rem === undefined);
      });
    };
	
	$http.get('api/v1/customer', {params: {limit: 10}}).success(function(customer) {
		$scope.customer = customer
     });
	 
	 
	$http.get('api/v1/pets', {params: {limit: 10}}).success(function(pets) {
		$scope.pets = pets
     });
	
	$http.get('api/v1/foundpets', {params: {limit: 10}}).success(function(foundpets) {
		$scope.foundpets = foundpets
     });	
	$http.get('api/v1/lostpets', {params: {limit: 10}}).success(function(lostpets) {
		$scope.lostpets = lostpets
     });	
	 

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
		 $http.get('api/v1/customer').success(function(customer) {
					var customerData = customer; 
					//console.log($scope.customerData);
					  
					
					var excel="<table>";
					// Header
					$.each(customerData, function(key, value) {
						//console.log(value)
						excel += "<tr>";
						$.each(value, function(key1, value1) {
							excel += "<td>" + value1 + "</td>";
						});
						excel += '</tr>';
					}) 
					excel += '</table>'
					console.log(excel)
					/*$(el).find('thead').find('tr').each(function() {
						excel += "<tr>";
						$(this).filter(':visible').find('th').each(function(index,data) {
							if ($(this).css('display') != 'none'){					
								if(defaults.ignoreColumn.indexOf(index) == -1){
									excel += "<td>" + parseString($(this))+ "</td>";
								}
							}
						});	
						excel += '</tr>';						
						
					});*/					
					
					
					// Row Vs Column
					/*var rowCount=1;
					$(el).find('tbody').find('tr').each(function() {
						excel += "<tr>";
						var colCount=0;
						$(this).filter(':visible').find('td').each(function(index,data) {
							if ($(this).css('display') != 'none'){	
								if(defaults.ignoreColumn.indexOf(index) == -1){
									excel += "<td>"+parseString($(this))+"</td>";
								}
							}
							colCount++;
						});															
						rowCount++;
						excel += '</tr>';
					});
					*/	
					
					
					if(defaults.consoleLog == 'true'){
						console.log(excel);
					}
					
					var excelFile = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:"+defaults.type+"' xmlns='http://www.w3.org/TR/REC-html40'>";
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
					window.open('data:application/vnd.ms-'+defaults.type+';filename=exportData.doc;' + base64data);
				  });
					
		
	}
	
  }]);