<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ isset($siteTitle) ? $siteTitle : '' }} | {{ isset($mTitle) ? $mTitle : '' }} | {{ isset($title) ? $title : '' }} </title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- daterange picker -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/daterangepicker/daterangepicker.css">
        <!-- bootstrap datepicker -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/datepicker/datepicker3.css">
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
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-90420504-1', 'auto');
            ga('send', 'pageview');

        </script>
    </head>
    <body class="skin-blue sidebar-mini" >
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
                        {{ isset($mTitle) ? $mTitle : trans( 'admin.dashboard' ) }}
                        <small>{{ isset($title) ? $title : trans( 'admin.dashboard' ) }}</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> {{ trans( 'admin.admin' ) }}</a></li>
                        <li><a href="#">{{ isset($mTitle) ? $mTitle : trans( 'admin.dashboard' ) }}</a></li>
                        <li class="active">{{ isset($title) ? $title : trans( 'admin.dashboard' ) }}</li>
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
        <!-- date-range-picker -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
        <script src="{{ url( 'resources/assets' ) }}/plugins/daterangepicker/daterangepicker.js"></script>
        <!-- bootstrap datepicker -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/datepicker/bootstrap-datepicker.js"></script>
        <!-- Select2 -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/select2/select2.full.min.js"></script>
        <!-- iCheck 1.0.1 -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/iCheck/icheck.min.js"></script>
        <!-- FastClick -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/fastclick/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="{{ url( 'resources/assets' ) }}/dist/js/app.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="{{ url( 'resources/assets' ) }}/dist/js/demo.js"></script>
        <script type="text/javascript" src="{{ url( 'resources/assets/plugins/highcharts/highcharts.js' ) }}"></script>
        
        <!-- Layout Scripts -->
        @include('admin.layout.script')

        <!-- for every page specific scripts -->
        @yield( 'script' )

    </body>
</html>