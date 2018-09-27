<!DOCTYPE html>
<html lang="en">
<head>

    @include("admin.partials.meta")
    <!-- Global Spark Object -->
    <script>
        window.Spark = <?php echo json_encode(array_merge(
            Spark::scriptVariables(), []
        )); ?>;
    </script>
</head>

<body class="@yield("bodyClasses") hold-transition skin-black sidebar-mini">
<div class="wrapper" id="spark-app">
  

  @include('admin.partials.header')
  <!-- Left side column. contains the logo and sidebar -->
  @include('admin.partials.left-nav')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      @include('flash::message')

      @yield('content')
    </section>
    <!-- /.content -->
  </div>

  @if (Auth::check())
    @section('footer')
        @include('spark::modals.notifications')
        @include('spark::modals.support')
        @include('spark::modals.session-expired')
    @append
  @endif
  <!-- /.content-wrapper -->
  @include('admin.partials.footer')
  @include('admin.partials.footer-meta')
  
  <div class="control-sidebar-bg"></div>
</div>

</body>
</html>
