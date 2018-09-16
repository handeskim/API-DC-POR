<link href="<?php echo base_url(); ?>static/css/bootstrap.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>static/css/bootstrap-responsive.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>static/js/bootstrap.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.4/angular.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.4/angular-resource.min.js"></script>

<script>
var App = angular.module('myApp',[]);
App.controller("app_home", function($scope, $http){
	$scope.name = 'APPLICATION DOMAIN';
	$scope.domains = [];
	$http({
		url: '<?php echo base_url();?>cms/core/QueryService?app_key=800f5dd9c89fb4c96db5837c893c1010',
		method: "get",
	}).success(function(data){
		$scope.domains = data;
			//console.log(data);
	});
});
</script>

<body ng-app="myApp">
	<div class="" ng-controller="app_home" >
		<p><b>{{name}}</b></p>
		<table class="table table-striped table-bordered">
			<tr>
				<td align="right">Search :</td>
					<td><input ng-model="search" /></td>
			</tr>  
			<tr> 
				<th> Domain Name </th>
				<th> Extends Domain </th>
				<th> Domain Exdate </th>
				<th> Status Registrar </th>
			</tr>
			<tr ng-repeat="domain in domains | filter:search" > 
				<td> {{domain.domain_name}}</td>
				<td> {{domain.extends_domain}}</td>
				<td> {{domain.domain_exdate}}</td>
				<td> {{domain.domain_status_registrar}}</td>
			</tr>
		</table>
	</div>
</body>