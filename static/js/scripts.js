angular.module('cms', ['service']).
  config(function($routeProvider) {
    $routeProvider.
      when('/', {controller:ListCtrl, templateUrl:BASE_URL+'cms/service/test'}).
      otherwise({redirectTo:'/'});
  });

function ListCtrl($scope, $location, cms) {
  $scope.cms = cms.query();
  $scope.destroy = function(cms) {
    cms.destroy(function() {
      $scope.cms.splice($scope.projects.indexOf(cms), 1);
    });
  };
}
