<!doctype html>
<html lang="{{ Config::get('app.locale') }}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>{{ HTML::entities('EduCal') }}</title>
    <!-- Bootstrap core CSS -->
    {{ HTML::style("css/bootstrap.min.css") }}
    {{ HTML::style("css/bootstrap-theme.min.css") }}
    <!-- Bootstrap Datepicker -->
    {{ HTML::style("css/jquery.datetimepicker.css") }}
    <!-- jQuery UI -->
    {{ HTML::style("css/jquery-ui.min.css") }}
    {{ HTML::style("css/jquery-ui.structure.min.css") }}
    {{ HTML::style("css/jquery-ui.theme.min.css") }}
    <!-- Global styles for this template -->
    @yield('header')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta property="og:title" content="">
    <meta property="og:site_name" content="">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ asset('') }}">
  <link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-touch-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-touch-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="144x144" href="favicons/apple-touch-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-touch-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-touch-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-touch-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="152x152" href="favicons/apple-touch-icon-152x152.png">
  <link rel="icon" type="image/png" href="favicons/favicon-196x196.png" sizes="196x196">
  <link rel="icon" type="image/png" href="favicons/favicon-160x160.png" sizes="160x160">
  <link rel="icon" type="image/png" href="favicons/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/png" href="favicons/favicon-16x16.png" sizes="16x16">
  <link rel="icon" type="image/png" href="favicons/favicon-32x32.png" sizes="32x32">
  <meta name="msapplication-TileColor" content="#39acb0">
  <meta name="msapplication-TileImage" content="favicons/mstile-144x144.png">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    {{ HTML::script("js/html5shiv.js") }}
    {{ HTML::script("js/respond.min.js") }}
    <![endif]-->
    <!--<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>-->
</head>
<body>

<div class="page-container">

  <!-- top navbar -->
  <div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target=".sidebar-nav">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">EduCal</a>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row row-offcanvas row-offcanvas-left">
      <!-- sidebar -->
      <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
        <ul class="nav">
          <li><a href="{{route('calendar.index')}}"><span class="glyphicon glyphicon-calendar"></span> Calendar</a></li>
          <li><a href="schools"><span class="glyphicon glyphicon-home"></span> Schools</a></li>
          <li><a href="users"><span class="glyphicon glyphicon-user"></span> Users</a></li>
          <li><a href="groups"><span class="glyphicon glyphicon-th-large"></span> Groups</a></li>
          <li><a href="events"><span class="glyphicon glyphicon-glass"></span> Events</a></li>
          <li><a href="about"><span class="glyphicon glyphicon-question-sign"></span> About</a></li>
          <li><a href="settings"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
          <li><a href="{{route('user.logout')}}"><span class="glyphicon glyphicon-log-out"></span> Log out</a></li>
        </ul>
      </div>
      <div class="col-xs-12 col-sm-9">

      @yield('content')

      </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->

{{ HTML::script("js/jquery-1.11.1.min.js") }}
{{ HTML::script("js/moment.js") }}
{{ HTML::script("js/bootstrap.min.js") }}
{{ HTML::script("js/jquery.datetimepicker.js") }}
@yield('footerScript')
</body>
</html>