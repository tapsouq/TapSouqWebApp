<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" type="text/css" href="{{ url('resources/assets/home') }}/assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ url('resources/assets/home') }}/assets/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="{{ url('resources/assets/home') }}/assets/custom/helper.css">
    <link rel="stylesheet" type="text/css" href="{{ url('resources/assets/home') }}/assets/custom/hover.css">
    <title>{{ getSiteInfo()->site_title }}</title>
    
    @yield('head')
    <link rel="stylesheet" type="text/css" href="{{ url('resources/assets/home') }}/assets/custom/css.css">
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-90420504-1', 'auto');
        ga('send', 'pageview');
    </script>
    <meta name="google-site-verification" content="lE5knuMD-BLZt7K4zc8zWvpuf6ZVdpcK-qOrFvRhJR0" />
</head>
<body>
    <div class="main">
        <!-- Menu -->
        @include('home.layout.menu')
        <!-- End Menu -->
        <main>
            <!-- Content -->
            @yield( 'content' )
            <!-- End content -->
        </main>
        <!-- Footer -->
        @include('home.layout.footer')
        <!-- End footer -->
    </div>
    <script src="{{ url('resources/assets/home') }}/assets/custom/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="{{ url('resources/assets/home') }}/assets/bootstrap/js/bootstrap.js"></script>
    @yield('script')
    <script type="text/javscript">
        $('.carousel').carousel();
    </script>
</body>
</html>