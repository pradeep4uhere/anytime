var app = angular.module('psrRegisterApp', ['ngRoute'])
.constant('USERREGISTERAPI','API/internalapi.php?action=addnewvendor&t='+ Math.random());

app.config(function($routeProvider) {
  $routeProvider
  .when('/', {
    templateUrl : 'pages/register.html',
    controller  : 'RegisterController'
  })
  .otherwise({redirectTo: '/'});
});



//Password Controller Start
app.controller('RegisterController', function($scope,$http,USERREGISTERAPI) {

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


  $scope.message = 'Hello from RegisterController';
  $scope.show=true;
  $scope.user={
                userType:'',
                fname:'',
                lname:'',
                compname:'',
                address:'',
                pincode:'',
                emailaddress:'',
                mobile_number:'',
                password:''
              };


  $('#sbtn').click(function(){
      saveUser();
  });

  
  $('#loginPage').click(function(){
    window.location='login.php';
  });

  $scope.hideSellerField = function(){
    alert('5454');
    // $('.class').each(function(e){  
    //     $(this).hide();
    // });
  };


  var saveUser = function(){
    var userType=$scope.user.userType;
       console.log($scope.user);
       $scope.title='Invalid Input !';
       $scope.type='error';
       if($('#userType').val()==''){
          $scope.text='Please choose user type.';
          $scope.messageAlert();
          $('#userType').focus();
          return false;
       }else{
          $scope.userType=$('#userType').val();
       }


       if($('#firstname').val()==''){
          $scope.text='Please enter First Name.';
          $scope.messageAlert();
          $('#firstname').focus();
          return false;
       }else{
          $scope.fname=$('#firstname').val();
       }


       if($('#lastname').val()==''){
          $scope.text='Please enter last name.';
          $scope.messageAlert();
          return false;
       }else{
        $scope.lname=$('#lastname').val();
       }

       //This is when user is seller type
       if($scope.userType==1){
         if($('#compname').val()==''){
            $scope.text='Please enter company name.';
            $scope.messageAlert();
            return false;
         }else{
          $scope.compname=$('#compname').val();
         }
      }else{
        $scope.compname="";
      }

       if($('#address').val()==''){
          $scope.text='Please enter address.';
          $scope.messageAlert();
          return false;
       }else{
        $scope.address=$('#address').val();
       }


       if($('#locality').val()==''){
          $scope.text='Please enter locality.';
          $scope.messageAlert();
          return false;
       }else{
        $scope.locality=$('#locality').val();
       }


       if($('#Pincode').val()==''){
          $scope.text='Please enter pincode.';
          $scope.messageAlert();
          return false;
       }else{
        $scope.Pincode=$('#Pincode').val();
       }

       if($('#phonenumber').val()==''){
          $scope.text='Please enter mobile number.';
          $scope.messageAlert();
          return false;
       }else{
        $scope.phonenumber=$('#phonenumber').val();
       }

       if($('#emailaddress').val()==''){
          $scope.text='Please enter email address.';
          $scope.messageAlert();
          return false;
       }else{
        $scope.emailaddress=$('#emailaddress').val();
       }


       if($('#password').val()==''){
          $scope.text='Please enter password.';
          $scope.messageAlert();
          return false;
       }else{
        $scope.password=$('#password').val();
       }


       if($('#confirmPassword').val()==''){
          $scope.text='Please enter confirm password.';
          $scope.messageAlert();
          alert('Please enter confirm password');
          return false;
       }else{
        $scope.confirmPassword=$('#confirmPassword').val();
       }

     $scope.user={
              "user":{  
                       "userType":$scope.userType,
                       "fname":$scope.fname,
                       "lname":$scope.lname,
                       "compname":$scope.compname,
                       "address":$scope.address,
                       "locality":$scope.locality,
                       "pincode":$scope.Pincode,
                       "emailaddress":$scope.emailaddress,
                       "mobile":$scope.phonenumber,
                       "password":$scope.password,
                       "cpassword":$scope.confirmPassword
                    },
                    "from":"web",
                    "checksum":"",
                    "ipaddress":ipaddress
              }; 
       $http.post(USERREGISTERAPI,
       {
         data:$scope.user
        
        })
        .success(function(data,status){
          $scope.title='Success !';
          $scope.type='success';
          $scope.text='Please wait redirect to login page.';
          $scope.messageAlert();
          setTimeout(function(){
            window.location='login.php';
          },2000);
        })
        .error(function(data,status){
         $scope.class='danger';
         $scope.message='User not register';
         alert('User Not register !!');
        });
  
}

  });