<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ $siteTitle }} | {{ $mTitle }} | {{ $title }} </title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/iCheck/all.css">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/select2/select2.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/dist/css/skins/_all-skins.min.css">
        <link rel="stylesheet" href="{{ url('resources/assets') }}/custom/css.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @yield( 'head' )
    </head>
    <body class="hold-transition skin-blue sidebar-mini" >
        <div class="wrapper">
            <!-- Header section -->
            @include( 'admin.layout.header' )
            <!-- End Header section -->
            
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                @include( 'admin.layout.sidebar' )
            </aside>
            <!-- End sidebar -->

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        {{ $mTitle }}
                        <small>{{ $title }}</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> {{ trans( 'admin.dashboard' ) }}</a></li>
                        <li><a href="#">{{ $mTitle }}</a></li>
                        <li class="active">{{ $title }}</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <!-- Alert section -->
                    @include( 'admin.layout.alert' )
                    <!-- End alert section -->
                    @yield( 'content' )
                </section>
            </div>
        </div>
        <!-- jQuery 2.2.3 -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/jQuery/jquery-2.2.3.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="{{ url( 'resources/assets' ) }}/bootstrap/js/bootstrap.min.js"></script>
        <!-- Select2 -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/select2/select2.full.min.js"></script>
        <!-- iCheck 1.0.1 -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/iCheck/icheck.min.js"></script>
        <!-- FastClick -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/fastclick/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="{{ url( 'resources/assets' ) }}/dist/js/app.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="{{ url( 'resources/assets' ) }}/dist/js/demo.js"></script>
        <script>
          $(function () {
            
            //Initialize Select2 Elements
            $(".select2").select2();

            //iCheck for checkbox and radio inputs
            $('input[type="checkbox"].minimal-blue, input[type="radio"].minimal-blue').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
            });
        });
        </script>
        @yield( 'script' )
    </body>
</html>