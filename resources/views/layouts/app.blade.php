<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>
  <link rel="icon" href="{{url('img/favicon.ico')}}" sizes="16x16">

  <!-- plugins:css -->
  <link rel="stylesheet" href="{{url('skydash/vendors/feather/feather.css')}}">
  <link rel="stylesheet" href="{{url('skydash/vendors/ti-icons/css/themify-icons.css')}}">
  <link rel="stylesheet" href="{{url('skydash/vendors/css/vendor.bundle.base.css')}}">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{url('skydash/css/vertical-layout-light/style.css')}}">
  <!-- endinject -->
</head>

<body>
            @yield('content')
  <!-- plugins:js -->
  <script src="{{url('skydash/vendors/js/vendor.bundle.base.js')}}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{url('skydash/js/off-canvas.js')}}"></script>
  <script src="{{url('skydash/js/hoverable-collapse.js')}}"></script>
  <script src="{{url('skydash/js/template.js')}}"></script>
  <script src="{{url('skydash/js/settings.js')}}"></script>
  <script src="{{url('skydash/js/todolist.js')}}"></script>
  <!-- endinject -->
</body>

</html>
