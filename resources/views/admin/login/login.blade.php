<!DOCTYPE HTML>
<html>
<head>
<title>Sign In</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
 <!-- Bootstrap Core CSS -->
<link href="{{ Config('global.ADMIN_URL_CSS') }}/bootstrap.min.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="{{ Config('global.ADMIN_URL_CSS') }}/style.css" rel='stylesheet' type='text/css' />
<!-- Graph CSS -->
<link href="{{ Config('global.ADMIN_URL_CSS') }}/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->
<!--Sweet Alert CSs-->
<link href="{{ Config('global.ADMIN_URL_CSS') }}/sweetalert.css" rel="stylesheet"> 
<!-- lined-icons -->
<link rel="stylesheet" href="{{ Config('global.ADMIN_URL_CSS') }}/icon-font.min.css" type='text/css' />
<!-- //lined-icons -->
<!-- chart -->
<script src="{{ Config('global.ADMIN_URL_JS') }}/Chart.js"></script>
<!-- //chart -->
<!--animate-->
<link href="{{ Config('global.ADMIN_URL_CSS') }}/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="{{ Config('global.ADMIN_URL_JS') }}/wow.min.js"></script>
  <script>
     new WOW().init();
  </script>
 <!-- Meters graphs -->
<script src="{{ Config('global.ADMIN_URL_JS') }}/jquery-1.10.2.min.js"></script>
<!-- Placed js at the end of the document so the pages load faster -->

</head> 
 <style type="text/css">
   .sweet-alert h2{ font-size: 14px; }
   .sweet-alert p{ font-size: 13px; }
 </style>  
 <body class="sign-in-up" ng-app="psrLoginApp">
    <section>
      <div id="page-wrapper" class="sign-in-wrapper">
        <div class="graphs">
          <div class="sign-in-form">
            <div class="signin">
            <div class="signin-rit">
                <div class="clearfix"> </div>
              </div>
              <div class="log-input">
                <div class="log-input-left" style="border-bottom: solid 1px #Ccc">
                   <h4>Member Login</h4>
                </div>
                <div class="clearfix"> </div>
              </div>
              <div class="row">
              <div class="col-sm-12">
                <div class="alert alert-"></div>
              </div>
              </div>



              <div class="log-input">
                <div class="log-input-left">
                   <input type="text" class="user"  onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Email Address:';}" ng-model="user.email_address" placeholder="Email Address" />
                </div>
                <div class="clearfix"> </div>
              </div>
              <div class="log-input">
                <div class="log-input-left">
                   <input type="password" class="lock" value="password" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'password:';}" ng-model="user.password" placeholder="password" />
                </div>
                <div class="clearfix"> </div>
              </div>
             
              <input type="submit" value="Login" id="loginBtn">
              <div class="signin-rit">
                <span class="checkbox1">
                   <label class="checkbox"><a href="#/forgotpassword">Forgot Password ?</a></label>
                </span>
                <div class="clearfix"> </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    <!--footer section start-->
      <footer>
         <p>&copy Copy 2018 . All Rights Reserved | Design by <a href="https://google.com/" target="_blank">abc.com.</a></p>
      </footer>
        <!--footer section end-->
  </section>
<script type="text/javascript">
  var ipaddress="<?php echo $_SERVER['REMOTE_ADDR']?>";
</script>  
<script src="{{ Config('global.ADMIN_URL_JS') }}jquery.nicescroll.js"></script>
<script src="{{ Config('global.ADMIN_URL_JS') }}scripts.js"></script>
<script src="{{ Config('global.ADMIN_URL_JS') }}angular/sweetalert.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{ Config('global.ADMIN_URL_JS') }}bootstrap.min.js"></script>
<script src="{{ Config('global.ADMIN_URL_JS') }}angular/angular.min.js"></script>
<!-- <script src="{{ Config('global.ADMIN_URL_JS') }}angular/angular-route.min.js"></script> -->
<!-- <script src="{{ Config('global.ADMIN_URL_JS') }}angular/prslogin.js"></script> -->
</body>
</html>

