var app = angular.module('prsApp', ['ngRoute']).
constant('TOKEN',TOKEN).
constant('ADD_VENDOR_URL','API/internalapi.php?action=addvendor&t='+ Math.random()+'&token='+TOKEN).
constant('UPDATE_VENDOR_PROFILE_URL','API/internalapi.php?action=updatevendorprofile&t='+ Math.random()+'&token='+TOKEN).
constant('ALL_VENDOR_URL','API/internalapi.php?action=allvendor&t='+ Math.random()+'&token='+TOKEN).
constant('EDIT_VENDOR_URL','API/internalapi.php?action=editvendor&t='+ Math.random()+'&token='+TOKEN).
constant('DETAILS_VENDOR_URL','API/internalapi.php?action=getvendorprofile&t='+ Math.random()+'&token='+TOKEN).
constant('ADD_SUPPLIER_URL','API/internalapi.php?action=addsupplier&t='+ Math.random()+'&token='+TOKEN).
constant('EDIT_SUPPLIER_URL','API/internalapi.php?action=editsupplier&t='+ Math.random()+'&token='+TOKEN).
constant('UPDATE_PWD_SUPPLIER_URL','API/internalapi.php?action=updatepasswordsupplier&t='+ Math.random()+'&token='+TOKEN).
constant('ALL_SUPPLIER_URL','API/internalapi.php?action=allsupplier&t='+ Math.random()+'&token='+TOKEN).
constant('ADD_CLIENT_URL','API/internalapi.php?action=addclient&t='+ Math.random()+'&token='+TOKEN).
constant('DETAILS_SUPPLIER_URL','API/internalapi.php?action=supplierdetails&t='+ Math.random()+'&token='+TOKEN).
constant('ALL_CLIENT_URL','API/internalapi.php?action=allclientlist&t='+ Math.random()+'&token='+TOKEN).
constant('DETAILS_CLIENT_URL','API/internalapi.php?action=allclientlist&t='+ Math.random()+'&token='+TOKEN).
constant('ADD_PRODUCT_URL','API/internalapi.php?action=addproduct&t='+ Math.random()+'&token='+TOKEN).
constant('ALL_PRODUCT_URL','API/internalapi.php?action=allproduct&t='+ Math.random()+'&token='+TOKEN).
constant('ALL_MASTER_LIST_URL','API/internalapi.php?action=getlist&t='+ Math.random()+'&token='+TOKEN).
constant('GET_PRODUCT_URL','API/internalapi.php?action=getproduct&t='+ Math.random()+'&token='+TOKEN);



app.config(function($routeProvider) {
  $routeProvider

  .when('/', {
    templateUrl : 'pages/dashboard.html',
    controller  : 'DashboardController'
  })

  .when('/addvendor', {
    templateUrl : 'pages/vendor/addvendor.html',
    controller  : 'VendorAddController'
  })

  .when('/vendorlist', {
    templateUrl : 'pages/vendor/allvendor.html',
    controller  : 'VendorController'
  })

  .when('/vendorprofile/:id', {
    templateUrl : 'pages/vendor/vendorprofile.html',
    controller  : 'VendorEditController'
  })
  .when('/editvendor/:id', {
    templateUrl : 'pages/vendor/editvendor.html',
    controller  : 'VendorEditController'
  })
  .when('/changepassword/:id', {
    templateUrl : 'pages/vendor/changepwdvendor.html',
    controller  : 'VendorEditController'
  })

  .when('/addsupplier/:id', {
    templateUrl : 'pages/supplier/addsupplier.html',
    controller  : 'SupplierController'
  })
  .when('/supplierlist/:id', {
    templateUrl : 'pages/supplier/allsupplier.html',
    controller  : 'SupplierListController'
  })
  .when('/changepassword/:id/:vendor_id', {
    templateUrl : 'pages/supplier/changepassword.html',
    controller  : 'SupplierEditController'
  })
  .when('/editsupplier/:id/:vendor_id', {
    templateUrl : 'pages/supplier/editsupplier.html',
    controller  : 'SupplierEditController'
  })
  .when('/addclient/:id/:vendor_id', {
    templateUrl : 'pages/client/addclient.html',
    controller  : 'ClientSaveController'
  })
  .when('/allclient/:id/:vendor_id', {
    templateUrl : 'pages/client/allclient.html',
    controller  : 'ClientController'
  })
  .when('/editclient/:id/:vendor_id/:client_id', {
    templateUrl : 'pages/client/addclient.html',
    controller  : 'ClientController'
  })
  .when('/addproduct/:vendor_id', {
    templateUrl : 'pages/product/addproduct.html',
    controller  : 'ProductController'
  })
  .when('/allproducts/:vendor_id', {
    templateUrl : 'pages/product/allproduct.html',
    controller  : 'ProductListController'
  })
  .when('/editproduct/:vendor_id/:id', {
    templateUrl : 'pages/product/editproduct.html',
    controller  : 'ProductEditController'
  })
  .otherwise({redirectTo: '/'});
});


//Dashboard Controller Start
app.controller('DashboardController', function($scope) {
  $scope.message = 'Hello from DashboardController';
});


//Vendor Controller Start
app.controller('VendorController', function($scope,$http,ALL_VENDOR_URL) {
  //var md5 = CryptoJS.MD5('123456').toString();
  
  $scope.message = 'Hello from VendorController';
  var start=0;
  var offset=250;
  var postData={'ipaddress':ipaddress,'start':start,'offset':offset};
  var checksum=getCheckSum(postData).done(function(res){
    if(res.checksum.length>0){
      var checksum=res.checksum;
    }
  //'what':'allvendor','ipaddress':ipaddress,'start':'0','offset':'250'
  var URL=ALL_VENDOR_URL+'&ipaddress='+ipaddress+'&start='+start+'&offset='+offset+'&checksum='+checksum;  
  $http.get(URL)
  .then(function(response){
    if(response.data.length>0){
      $scope.itemlist = response.data; 
    }else{
      $scope.showVal=true;
      $scope.class='danger';
      $scope.message='No Records Found.';

    }
  });


  });
 

});


//Vendor Add Controller Start
app.controller('VendorAddController', function($scope,$http,ADD_VENDOR_URL) {
  $scope.message = 'Hello from VendorAddController';
  $scope.vendors={};
  $scope.class='';
  $scope.message='';
  $('#sbtn').click(function(){
    postVendorDetail();
  });


  var postVendorDetail = function(){
    $http.post(ADD_VENDOR_URL,
      {
       data: {vendors: $scope.vendors}
      })
      .success(function(data,status){
        $scope.class='success';
        $scope.message='Vender added Successfully';
        window.location='index.php#/vendorlist';
      })
      .error(function(data,status){
         $scope.class='danger';
        $scope.message='Profile is not updated Successfully';

      });
  }

});





//Vendor Edit Controller Start
app.controller('VendorEditController', function($scope,$http, $routeParams,DETAILS_VENDOR_URL,UPDATE_VENDOR_PROFILE_URL) {
  $scope.id = $routeParams.id;
  $scope.vendors={};
  $scope.class='';
  $scope.message='';
  $http.get(DETAILS_VENDOR_URL+'&id='+$scope.id+'&from=web')
  .then(function(response){
    //console.log(response.data.data);
    $scope.vendors = response.data.data; 
    $scope.vendors.pasword = ''; 
  });

  $('#cnlBtn').click(function(){
    window.location='profile.php';
  });



  $('#sbtn').click(function(){
     postVendorDetail();
  });

  $('#psbtn').click(function(){
    if($scope.vendors.pasword.length==0){
      $scope.class='danger';
      $scope.message='Please enter new password';
    }else if($scope.vendors.cpasword.length==0){
      $scope.class='danger';
      $scope.message='Please enter new password';
    }else if($scope.vendors.pasword!=$scope.vendors.cpasword){
      $scope.class='danger';
      $scope.message='password did not matched';
    }else{
      postVendorDetail();
    }
  });

  var postVendorDetail = function(){
    $scope.vendorData={
                        "user": {
                          "type":"profile",
                          "companyName": $scope.vendors.companyName,
                          "PKCompanyRefNo": $scope.vendors.PKCompanyRefNo,
                          "firstName": $scope.vendors.fname,
                          "lastName": $scope.vendors.lname,
                          "comapnyAdress": $scope.vendors.comapnyAdress,
                          "mobile": $scope.vendors.mobile,
                          "pincode":$scope.vendors.pincode,
                          "emailAddress":$scope.vendors.companyEmailIds,
                          "compIsActive":$scope.vendors.compIsActive
                        },
                        "from": "web",
                        "checksum": "",
                        "ipaddress": ipaddress
                    };
      

      var checksumJson=getCheckSum($scope.vendorData).done(function(res){
      	  if(res.status=='success'){
      	  		$scope.vendorData.checksum=res.checksum;
      	  }else{
	      	  	$scope.vendorData.checksum='';
      	  }	
	      $http.post(UPDATE_VENDOR_PROFILE_URL,
	      {
	       data:$scope.vendorData
	      })
	      .success(function(data,status){
	        if(data.status=='success'){
	    	    $scope.type='success';
	    	    $scope.title='Success !';
		    }else{
		    	$scope.type='error';
		    	$scope.title='Error !';
		    }
		    $scope.text=data.message;
	        $scope.messageAlert();
	      })
	      .error(function(data,status){
	        $scope.class='danger';
	        $scope.message='Profile is not updated Successfully';
	    	$scope.type='error';
	    	$scope.title='Error !';
		    $scope.text=data.message;
	        $scope.messageAlert();
	      });
   });   

  }

  /***********Sweet Alert Mesage Goes Here********/
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
  /***********Sweet Alert Mesage Goes Here********/



});




//Supplier Add Controller Start
app.controller('SupplierController', function($scope,$http, $routeParams,ADD_SUPPLIER_URL,DETAILS_VENDOR_URL) {
  $scope.id = $routeParams.id;
  $scope.message = 'Hello from SupplierController';
  $scope.supplier={};
  $scope.class='';
  $scope.message='';
  $http.get(DETAILS_VENDOR_URL+'&id='+$scope.id)
  .then(function(response){
    $scope.supplier.companyName = response.data.data.companyName; 
    //console.log(response.data.data.companyName);
    $('#companyName').val(response.data.data.companyName);
    $scope.supplier.PKCompanyRefNo = response.data.data.PKCompanyRefNo; 
    $('#PKCompanyRefNo').val(response.data.data.PKCompanyRefNo);
  });



  $('#sbtn').click(function(){
    if($scope.supplier.password!=$scope.supplier.cpassword){
      $scope.class='danger';
      $scope.message='PAssword is not matched.';
      return false;
    }else{
      postSupplierDetail();
    }
  });


  var postSupplierDetail = function(){
   
    //Formate the Post Data
    $scope.postData={
                      "user":{  
                             "PKCompanyRefNo":$scope.supplier.PKCompanyRefNo,
                             "first_name":$scope.supplier.first_name,
                             "last_name":$scope.supplier.last_name,
                             "email_address":$scope.supplier.email_address,
                             "mobile":$scope.supplier.mobile,
                             "pincode":$scope.supplier.pincode,
                             "address":$scope.supplier.address,
                             "password":$scope.supplier.password,
                             "locality":$scope.supplier.landmark
                          },
                          "from":"website",
                          "checksum":"",
                          "ipaddress":"127.0.0.7"
                    };

    $http.post(ADD_SUPPLIER_URL,
      {
       data:$scope.postData
      })
      .success(function(data,status){
        $scope.class='success';
        $scope.message='supplier added Successfully';
        //window.location='dashboard.php#/supplierlist/'+$scope.supplier.PKCompanyRefNo;
      })
      .error(function(data,status){
      
        $scope.class='danger';
        $scope.message='Data is not updated Successfully';

      });
  }

});



//Supplier List Controller Start
app.controller('SupplierListController', function($scope,$http, $routeParams,ALL_SUPPLIER_URL,DETAILS_VENDOR_URL) {
  $scope.id = $routeParams.id;
  $scope.message = 'Hello from SupplierListController';
  $scope.supplier={};
  $scope.itemlist=[];
  $scope.class='';
  $scope.message='';
  $http.get(ALL_SUPPLIER_URL+'&id='+$scope.id)
  .then(function(response){
    $scope.itemlist = response.data; 
  });



  $('#sbtn').click(function(){
    postSupplierDetail();
  });


  var postSupplierDetail = function(){
    $http.post(ADD_SUPPLIER_URL,
      {
       data: {supplier: $scope.supplier}
      
      })
      .success(function(data,status){
        $scope.class='success';
        $scope.message='supplier added Successfully';
        window.location='dashboard.php#/supplierlist/'+$scope.id;
      })
      .error(function(data,status){
        $scope.class='danger';
        $scope.message='Data is not updated Successfully';

      });
  }

});



//Supplier Edit Controller
app.controller('SupplierEditController', function($scope,$http, $routeParams,EDIT_SUPPLIER_URL,UPDATE_PWD_SUPPLIER_URL) {
  $scope.id = $routeParams.id;
  $scope.vendor_id = $routeParams.vendor_id;
  $scope.supplier={};
  $scope.class='';
  $scope.message='';
  $scope.supplier.password = ''; 
  $scope.supplier.cpassword='';

  $http.get(EDIT_SUPPLIER_URL+'&id='+$scope.id+'&vendor_id='+$scope.vendor_id)
  .then(function(response){
    $scope.supplier = response.data.data[0]; 
    $scope.supplier.password = ''; 
    $scope.supplier.cpassword='';
  });

  $('#cnlBtn').click(function(){
    window.location='dashboard.php#/supplierlist/'+$scope.vendor_id;
  });


  $('#ecnlBtn').click(function(){
    window.location='dashboard.php#/supplierlist/'+$scope.vendor_id;
  });





  $('#sbtn').click(function(){
     postValidDetail();
  });

  $('#psbtn').click(function(){
    if($scope.supplier.password.length==0){
      $scope.class='danger';
      $scope.message='Please enter new password';
    }if($scope.supplier.cpassword.length==0){
      $scope.class='danger';
      $scope.message='Please enter new confirm password';
    }else if($scope.supplier.password!=$scope.supplier.cpassword){
      $scope.class='danger';
      $scope.message='password did not matched';
    }else{
      postValidDetail();
    }
  });

  var postValidDetail = function(){
    $scope.supplier=
    {
      "user":$scope.supplier,
      "from":"website",
      "checksum":"",
      "ipaddress":""
    }
    $http.post(UPDATE_PWD_SUPPLIER_URL,
      {
       data:$scope.supplier
      
      })
      .success(function(data,status){
      
        $scope.class='success';
        $scope.message='Profile is updated Successfully';
      
      })
      .error(function(data,status){
      
        $scope.class='danger';
        $scope.message='Profile is not updated Successfully';

      });
  }
});









//Client Add Controller Start
app.controller('ClientController', function($scope,$http, $routeParams,ADD_CLIENT_URL,DETAILS_SUPPLIER_URL,ALL_CLIENT_URL,DETAILS_CLIENT_URL) {
  $scope.supplier_id = $routeParams.id;
  $scope.vendor_id = $routeParams.vendor_id;
  $scope.id = $routeParams.client_id;
  
  $scope.message = 'Hello from ClientController';
  $scope.client={};
  $scope.clientlist=[];
  $scope.class='';
  $scope.message='';
  $scope.client.vendor_id='';
  $scope.client.supplier_id=''; 
  $scope.showVal=false; 
  
  $http.get(DETAILS_SUPPLIER_URL+'&id='+$scope.supplier_id+'&vendor_id='+$scope.vendor_id)
  .then(function(response){
    $scope.client =""; 

    $scope.client.supplier_id = response.data[0].id;
    $scope.supplier_name = response.data[0].first_name;
    $('#vendor_id').val(response.data[0].PKCompanyRefNo);
    $('#supplier_id').val(response.data[0].id);
    $scope.client.vendor_id = response.data[0].PKCompanyRefNo;

  });


  if($scope.id>0){
    $http.get(DETAILS_CLIENT_URL+'&supplier_id='+$scope.supplier_id+'&vendor_id='+$scope.vendor_id+'&client_id='+$scope.id)
  .then(function(response){
    $scope.client =response.data[0]; 
    $scope.client.password = '';
  });
  }else{
  $http.get(ALL_CLIENT_URL+'&supplier_id='+$scope.supplier_id+'&vendor_id='+$scope.vendor_id)
  .then(function(response){
    if(response.data.length==0){
      $scope.showVal=true; 
      $scope.class='danger';
      $scope.message='No Records Found';
    }
    $scope.itemlist =response.data; 
  });
  }

  $('#ecntBtn').click(function(){
    window.location='dashboard.php#/allclient/'+$scope.client.supplier_id+'/'+$scope.client.vendor_id;
  });

 

  $('#sbtn').click(function(){
    postClientDetail();
  });


  var postClientDetail = function(){
    $scope.client.vendor_id=$('#vendor_id').val();;
    $scope.client.supplier_id= $('#supplier_id').val(); 
    $http.post(ADD_CLIENT_URL,
      {
       data: {client: $scope.client}
      
      })
      .success(function(data,status){
        $scope.class='success';
        $scope.message='client added Successfully';
        window.location='dashboard.php#/allclient/'+$scope.client.supplier_id+'/'+$scope.client.vendor_id;
      })
      .error(function(data,status){
      
        $scope.class='danger';
        $scope.message='Data is not updated Successfully';

      });
  }

});




//Client Add Controller Start
app.controller('ClientSaveController', function($scope,$http, $routeParams,ADD_CLIENT_URL,DETAILS_SUPPLIER_URL,ALL_CLIENT_URL,DETAILS_CLIENT_URL) {
  $scope.supplier_id = $routeParams.id;
  $scope.vendor_id = $routeParams.vendor_id;
  $scope.id = $routeParams.client_id;
  
  $scope.message = 'Hello from ClientController';
  $scope.client={};
  $scope.clientlist=[];
  $scope.class='';
  $scope.message='';
  $scope.client.id=0;
  $scope.client.vendor_id='';
  $scope.client.supplier_id=''; 
  $scope.showVal=false; 
  $scope.client.id = '';
  $scope.client.password = '';
  
  $http.get(DETAILS_SUPPLIER_URL+'&id='+$scope.supplier_id+'&vendor_id='+$scope.vendor_id)
  .then(function(response){
    $scope.client.supplier_id = response.data[0].id;
    $scope.supplier_name = response.data[0].first_name;
    $('#vendor_id').val(response.data[0].PKCompanyRefNo);
    $('#supplier_id').val(response.data[0].id);
    $scope.client.vendor_id = response.data[0].PKCompanyRefNo;
    $scope.client.id = 0;
  });


  if($scope.id>0){
    $http.get(DETAILS_CLIENT_URL+'&supplier_id='+$scope.supplier_id+'&vendor_id='+$scope.vendor_id+'&client_id='+$scope.id)
  .then(function(response){
    console.log(response.data[0]);
    $scope.client =response.data[0]; 
  });
  }



  $('#sbtn').click(function(){
    postClientDetail();
  });


  var postClientDetail = function(){
    $scope.client.vendor_id=$('#vendor_id').val();;
    $scope.client.supplier_id= $('#supplier_id').val(); 
    $http.post(ADD_CLIENT_URL,
      {
       data: {client: $scope.client}
      
      })
      .success(function(data,status){
        $scope.class='success';
        $scope.message='client added Successfully';
        window.location='dashboard.php#/allclient/'+$scope.client.supplier_id+'/'+$scope.client.vendor_id;
      })
      .error(function(data,status){
      
        $scope.class='danger';
        $scope.message='Data is not updated Successfully';

      });
  }

});



//add Product

//Vendor Controller Start
app.controller('ProductController', function($scope,$http,$routeParams,ADD_PRODUCT_URL,GET_PRODUCT_URL,ALL_MASTER_LIST_URL) {
  $scope.product={};
  $scope.product.status=1;
  //Get all the Master Category List
  $scope.catlist={};
  var start=0;
  var offset=50;
  var postData={'type':'servicecategory','ipaddress':ipaddress,'start':start,'offset':offset};
  var checksum=getCheckSum(postData).done(function(res){
    if(res.checksum.length>0){
      var checksum=res.checksum;
    }
  //'what':'allvendor','ipaddress':ipaddress,'start':'0','offset':'250'
  var URL=ALL_MASTER_LIST_URL+'&type=servicecategory&ipaddress='+ipaddress+'&start='+start+'&offset='+offset+'&checksum='+checksum;  
  $http.get(URL)
  .then(function(response){
    if(response.data.status=='success'){
      $scope.catlist = response.data.data; 
    }else{
      $scope.showVal=true;
      $scope.class='danger';
      $scope.message='No Records Found.';
    }
  });
  });
  //Get all the Master Category List Ends***


  //Get all the Master Unit List Start***
  var postData={'type':'unit','ipaddress':ipaddress,'start':start,'offset':offset};
  var checksum=getCheckSum(postData).done(function(res){
    if(res.checksum.length>0){
      var checksum=res.checksum;
    }
  //'what':'allvendor','ipaddress':ipaddress,'start':'0','offset':'250'
  var URL=ALL_MASTER_LIST_URL+'&type=unit&ipaddress='+ipaddress+'&start='+start+'&offset='+offset+'&checksum='+checksum;  
  $http.get(URL)
  .then(function(response){
    if(response.data.status=='success'){
      $scope.unitlist = response.data.data; 
    }else{
      $scope.showVal=true;
      $scope.class='danger';
      $scope.message='No Records Found.';
    }
  });
  });

  //Get all the Master Unit List Ends***



  $scope.vendor_id = $routeParams.vendor_id;
  $scope.product.id = 0;
  $scope.product.id = $routeParams.id;
  $('#id').val($scope.product.id);
  $('#vendor_id').val($scope.vendor_id);
  $('#sbtn').click(function(){
    postValidDetail();
  });


  if($scope.product.id>0){
    //Get All the List
    $http.get(GET_PRODUCT_URL+'&vendor_id='+$scope.vendor_id+'&id='+$scope.product.id)
    .then(function(response){
      $scope.product =response.data[0]; 
    });
  }


 $('#cntBtn').click(function(){
    window.location='dashboard.php#/allproducts/'+$scope.vendor_id;
  });


  //created New Entry here for new products
  var postValidDetail = function(){
    $scope.product.vendorId=$('#vendor_id').val();
    console.log($scope.product);
    $scope.postData={
                      "user": {
                              "vendorId": $scope.product.vendor_id,
                              "catID": $scope.product.catID,
                              "unitID": $scope.product.unitID,
                              "title": $scope.product.name,
                              "description": $scope.product.description,
                              "vendorId": $scope.product.vendorId,
                              "sku": $scope.product.sku,
                              "capacity": $scope.product.capacity,
                              "price": $scope.product.price,
                              "status": $scope.product.status,
                              "created_by": $scope.product.vendor_id
                          },
                          "from": "mobile",
                          "checksum": "",
                          "ipaddress": ipaddress
                    };
    
        var checksum=getCheckSum($scope.postData).done(function(res){
          if(res.checksum.length>0){
            var checksum=res.checksum;
            $scope.postData.checksum=checksum;
          }

           $http.post(ADD_PRODUCT_URL,
           {
             data:$scope.postData
            })
            .success(function(data,status){
              $scope.class='success';
              $scope.message='Product added Successfully';
              window.location='dashboard.php#/allproducts/'+$scope.product.vendorId;
            })
            .error(function(data,status){
            
              $scope.class='danger';
              $scope.message='Data is not updated Successfully';
            });
        });
  }
});


//Vendor Controller Start
app.controller('ProductListController', function($scope,$http,$routeParams,ALL_PRODUCT_URL) {
  $scope.product={};
  $scope.vendor_id = $routeParams.vendor_id;
  $scope.PKCompanyRefNo=$scope.vendor_id;
  $scope.product.id = 0;
  $('#id').val($scope.product.id);
  $('#vendor_id').val($scope.vendor_id);

  //Get All the List
  $http.get(ALL_PRODUCT_URL+'&vendor_id='+$scope.vendor_id)
  .then(function(response){
    console.log(response.data);
    $scope.itemlist =response.data.data; 
  });


  $('.deleteProduct').click(function(){
    alert(54545);
  });

});



//Vendor Controller Start
app.controller('ProductEditController', function($scope,$http,$routeParams,ADD_PRODUCT_URL,GET_PRODUCT_URL,ALL_MASTER_LIST_URL) {
  $scope.product={};
  $scope.product.status=1;
  //Get all the Master Category List
  $scope.catlist={};
  var start=0;
  var offset=50;
  var postData={'type':'servicecategory','ipaddress':ipaddress,'start':start,'offset':offset};
  var checksum=getCheckSum(postData).done(function(res){
    if(res.checksum.length>0){
      var checksum=res.checksum;
    }
  //'what':'allvendor','ipaddress':ipaddress,'start':'0','offset':'250'
  var URL=ALL_MASTER_LIST_URL+'&type=servicecategory&ipaddress='+ipaddress+'&start='+start+'&offset='+offset+'&checksum='+checksum;  
  $http.get(URL)
  .then(function(response){
    if(response.data.status=='success'){
      $scope.catlist = response.data.data; 
    }else{
      $scope.showVal=true;
      $scope.class='danger';
      $scope.message='No Records Found.';
      $scope.catlist = {}; 
    }
  });
  });
  //Get all the Master Category List Ends***


  //Get all the Master Unit List Start***
  var postData={'type':'unit','ipaddress':ipaddress,'start':start,'offset':offset};
  var checksum=getCheckSum(postData).done(function(res){
    if(res.checksum.length>0){
      var checksum=res.checksum;
    }
  //'what':'allvendor','ipaddress':ipaddress,'start':'0','offset':'250'
  var URL=ALL_MASTER_LIST_URL+'&type=unit&ipaddress='+ipaddress+'&start='+start+'&offset='+offset+'&checksum='+checksum;  
  $http.get(URL)
  .then(function(response){
    if(response.data.status=='success'){
      $scope.unitlist = response.data.data; 
    }else{
      $scope.showVal=true;
      $scope.class='danger';
      $scope.message='No Records Found.';
    }
  });
  });

  //Get all the Master Unit List Ends***



  $scope.vendor_id = $routeParams.vendor_id;
  $scope.product.id = 0;
  $scope.product.id = $routeParams.id;
  $('#id').val($scope.product.id);
  $('#vendor_id').val($scope.vendor_id);
  $('#sbtn').click(function(){
      postValidDetail();
  });


  if($scope.product.id>0){
    //Get All the List
    $http.get(GET_PRODUCT_URL+'&vendor_id='+$scope.vendor_id+'&prod_id='+$scope.product.id)
    .then(function(response){
      //Formate For Edit Product Json
      $scope.product.id=response.data.data.id; 
      $scope.product.catID=response.data.data.category_id; 
      $scope.product.unitID=response.data.data.unit_id; 
      $scope.product.name=response.data.data.title; 
      $scope.product.description=response.data.data.description; 
      $scope.product.sku=response.data.data.product_sku; 
      $scope.product.capacity=response.data.data.capacity; 
      $scope.product.price=response.data.data.price; 
      $scope.product.status=response.data.data.status; 
    });
  }


 $('#cntBtn').click(function(){
    window.location='dashboard.php#/allproducts/'+$scope.vendor_id;
  });


  //created New Entry here for new products
  var postValidDetail = function(){
    $scope.product.vendorId=$('#vendor_id').val();
    $scope.postData={
                      "user": {
                            "id": $scope.product.id,
                            "vendorId": $scope.product.vendor_id,
                            "catID": $scope.product.catID,
                            "unitID": $scope.product.unitID,
                            "title": $scope.product.name,
                            "description": $scope.product.description,
                            "vendorId": $scope.product.vendorId,
                            "sku": $scope.product.sku,
                            "capacity": $scope.product.capacity,
                            "price": $scope.product.price,
                            "status": $scope.product.status
                          },
                          "from": "mobile",
                          "checksum": "",
                          "ipaddress": ipaddress
                    };
    
    var checksum=getCheckSum($scope.postData).done(function(res){
      if(res.checksum.length>0){
        var checksum=res.checksum;
        $scope.postData.checksum=checksum;
     }

     $http.post(ADD_PRODUCT_URL,
     {
       data:$scope.postData
     }).success(function(data,status){
        $scope.class='success';
        $scope.message='Product updated Successfully';
        window.location='dashboard.php#/allproducts/'+$scope.product.vendorId;
     }).error(function(data,status){
        $scope.class='danger';
        $scope.message='Data is not updated Successfully';
     });
  });
  }
});
