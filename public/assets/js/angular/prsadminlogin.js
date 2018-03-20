var app = angular.module('psrAdminLoginApp',['ngRoute']).
constant('ADMIN_LOGIN_URL','API/internalapi.php?action=webmasterlogin&t='+ Math.random());

app.config(function($routeProvider) {
  $routeProvider
  .when('/', {
    templateUrl : 'pages/admin/login.html',
    controller  : 'LoginController'
  })
  .when('/forgotpassword', {
    templateUrl : 'pages/forgotpassword.html',
    controller  : 'PasswordController'
  })
  .when('/register', {
    templateUrl : 'pages/register.html',
    controller  : 'RegisterController'
  })
  .otherwise({redirectTo: '/'});
});

//Password Controller Start
app.controller('PasswordController', function($scope) {
  $scope.message = 'Hello from PasswordController';
});




//Password Controller Start
app.controller('RegisterController', function($scope) {
  $scope.message = 'Hello from RegisterController';
  window.location='register.html'

});


//Login Controller Start
app.controller('LoginController', function($scope,$http,ADMIN_LOGIN_URL) {
  $scope.class='';
  $scope.message='';
  $scope.user='';
  $scope.show =true;
 
  $('#loginBtn').click(function(){
      if($scope.user.email_address==undefined){
        $scope.class='danger';
        $scope.message='Login is Successfully';
        //alert('Please Choose User Type First');
      }else{
        loginUser();  
      }
      
  });

  var loginUser = function(){
     
     var userType=$scope.user.userType;
     $http.post(ADMIN_LOGIN_URL,
     {
       data: {user: $scope.user}
      
      })
      .success(function(data,status){
        var responseData=JSON.stringify(data);
        if(data.status=='success'){
          $scope.class='success';
          $scope.message='Login is Successfully';
          window.location='dashboard.php'
        }else{
          $scope.class='danger';
          $scope.message='Invalid Login';
        }
      
      })
      .error(function(data,status){
        $scope.class='danger';
        $scope.message='Login is not Successfully';
      });
  }

});