<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>@yield('title')</title>
        <meta name="description" content="Download free Bootstrap 4 product landing page template Comply." />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--Bootstrap 4-->
        <link rel="stylesheet" href="{{ Config('global.THEME_URL_CSS') }}bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
        <!--icons-->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
    </head>
<body>
      

<!--header-->
<nav class="navbar navbar-expand-md navbar-dark fixed-top sticky-navigation">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="ion-grid icon-sm"></span>
    </button>
    <a class="navbar-brand hero-heading" href="#">COMPLY</a>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item mr-3">
                <a class="nav-link page-scroll" href="#main">Product <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item mr-3">
                <a class="nav-link page-scroll" href="#features">Features</a>
            </li>
            <li class="nav-item mr-3">
                <a class="nav-link page-scroll" href="#pricing">Pricing</a>
            </li>
            <li class="nav-item mr-3">
                <a class="nav-link page-scroll" href="#team">Team</a>
            </li>
            <li class="nav-item mr-3">
                <a class="nav-link page-scroll" href="#blog">Blog</a>
            </li>
            <li class="nav-item mr-3">
                <a class="nav-link page-scroll" href="#contact">Contact</a>
            </li>
        </ul>
    </div>
</nav>

  <!-- BEGIN MAIN CONTENT -->
    @yield('content')
  <!-- END MAIN CONTENT -->

      

        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.js"></script>
        <script src="{{ Config('global.THEME_URL_JS') }}scripts.js"></script>


  <!-- begin page level js -->
    @yield('footer_scripts')
  <!-- end page level js -->
    </body>
</html>
