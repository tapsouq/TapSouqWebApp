<header class="menu-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav class="navbar navbar-info">
                    <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="{{ url() }}">
                            <img src="{{ url('resources/assets/home') }}/images/logo.png" alt="TabSouq">
                        </a>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="active">
                                <a href="{{ url() }}">Home</a></li>
                            <li>
                                <a href="#">Blog</a>
                            </li>
                            <li><a href="{{ url('resources-page') }}">Resources</a></li>
                            <li><a href="#">About us</a></li>
                            <li>
                                <a href="{{ url('auth/login') }}">Login</a>
                            </li>
                            <li>
                                <a href="{{ url('auth/register') }}">Sign up</a>
                            </li>
                            
                        </ul>
                    </div><!-- /.navbar-collapse -->
                  </div><!-- /.container-fluid -->
                </nav>
            </div>
        </div>
    </div>
</header>