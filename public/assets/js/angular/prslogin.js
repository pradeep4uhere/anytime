var app = angular.module('psrLoginApp',['ngRoute']).
constant('LOGIN_URL','API/internalapi.php?action=login&t='+ Math.random());

app.config(function($routeProvider) {
  $routeProvider
  .when('/', {
    templateUrl : 'login',
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
app.controller('LoginController', function($scope,$http,LOGIN_URL) {
  $scope.class='';
  $scope.message='';
  $scope.user='';
  $scope.show =false;
 
  $('#loginBtn').click(function(){
      if($scope.user.userType==undefined){
        $scope.title='Invalid User Type !';
        $scope.text='Please Choose valid user type.';
        $scope.type='error';
        $scope.messageAlert();
        return false;
      }else{
        loginUser();  
      }
      
  });

  $scope.messageAlert = function(){
    swal({
            title: $scope.title,
            text: $scope.text,
            type: $scope.type,
            showCancelButton: false,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Ok!",
            closeOnConfirm: true
          },
          function(){
            //swal("Ok!", "Your imaginary file has been deleted.", "success");
          });
  } 

  var loginUser = function(){
     
     var userType=$scope.user.userType;
     console.log($scope.user.email_address);
     
     if($scope.user.email_address==undefined){
        $scope.title='Invalid username !';
        $scope.text='Please enter valid email or mobile.';
        $scope.type='error';
        $scope.messageAlert();
        return false;
     }
     if($scope.user.password==undefined){
        $scope.title='Invalid Password';
        $scope.text='Please enter valid password.';
        $scope.type='error';
        $scope.messageAlert();
        return false;
     }

     if($scope.user.email_address!=undefined && $scope.user.password!=undefined){
       //Formate Valid Post Method for Login
       /*
               {  
           "data":{  
              "user":{  
                 "mobile":"9015446567",
                 "emaladdress":"pradeep7384@gmail.com",
                 "password":"123456",
                 "userType":1
              },
              "from":"mobile",
              "checksum":"5a828ca5302b19ae8c7a66149f3e1e98",
              "ipaddress":"127.0.0.7"
           }
        }*/
       $scope.postLoginData={
                              "user":{  
                                       "username":$scope.user.email_address,
                                       "password":$scope.user.password,
                                       "userType":$scope.user.userType
                                    },
                                    "from":"web",
                                    "checksum":"",
                                    "ipaddress":ipaddress
                           }; 

         $http.post(LOGIN_URL,{
           data: $scope.postLoginData
         })
        .success(function(data,status){
          var responseData=JSON.stringify(data);
          if(data.status=='success'){
            $scope.title='You Logged!!';
            $scope.text='Please wait redirect to dashboard.';
            $scope.type='success';
            $scope.messageAlert();
            setTimeout(function(){
              window.location='profile.php'
            },2000);
          }else{
            $scope.title='Invalid Credentials!!';
            $scope.text='Please enter valid Credentials.';
            $scope.type='error';
            $scope.messageAlert();
            return false;
          }
        })
        .error(function(data,status){
          $scope.title='Oops!!';
          $scope.text='somthing went wrong, please try agian.';
          $scope.type='error';
          $scope.messageAlert();
          return false;
        });
    }
  }

});
