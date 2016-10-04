    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ url('resources/assets') }}/dist/img/avatar.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Auth::user()->fname }}</p>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">{{ trans( 'admin.main_nav' ) }}</li>
            <li class="treeview">
              	<a href="#">
                	<i class="fa fa-dashboard"></i> <span>Dashboard</span>
                	<span class="pull-right-container">
                  		<i class="fa fa-angle-left pull-right"></i>
                	</span>
              	</a>
              	<ul class="treeview-menu">
                	<li><a href="../../index.html"><i class="fa fa-circle-o"></i> Dashboard v1</a></li>
                	<li><a href="../../index2.html"><i class="fa fa-circle-o"></i> Dashboard v2</a></li>
              	</ul>
            </li>
            @if( Auth::user()->role == ADMIN_PRIV )
            <li class="{{ Request::segment(1) == 'user' ? 'active' : '' }}">
                <a href="{{ url( 'user/all' ) }}">
                    <i class="fa fa-users"></i>
                    <span> {{ trans( 'admin.users' ) }} </span>
                </a>
            </li>
            <li class="treeview {{ Request::segment(1) == 'matching' ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-link"></i>
                    <span>{{ trans('admin.matching') }}</span>
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::url() == url('matching/matched-keywords') ? 'active' : '' }}">
                        <a href="{{ url('matching/matched-keywords') }}">
                            <i class="fa fa-circle-o"></i> 
                            {{ trans( 'admin.matched_keywords' ) }}
                        </a>
                    </li>
                    <li class="{{ Request::url() == url('matching/unmatched-keywords') ? 'active' : '' }}">
                        <a href="{{ url('matching/unmatched-keywords') }}">
                            <i class="fa fa-circle-o"></i> 
                            {{ trans( 'admin.empty_keywords' ) }}
                        </a>
                    </li>
              </ul>
            </li>
            @endif
            <li class="treeview {{ in_array( Request::segment(1), [ 'app', 'zone' ] ) ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-android"></i> <span>{{ trans( 'admin.applications' ) }}</span>
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::url() == url('app/all') ? 'active' : '' }}">
                        <a href="{{ url('app/all') }}">
                            <i class="fa fa-circle-o"></i> {{ trans( 'admin.all_applications' ) }}
                        </a>
                    </li>
                    <li class="{{ Request::url() == url('app/create') ? 'active' : '' }}" >
                        <a href="{{ url('app/create') }}">
                            <i class="fa fa-plus"></i> {{ trans( 'admin.add_new_app' ) }}
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview {{ in_array( Request::segment(1), [ 'campaign', 'ads' ] ) ? 'active' : '' }} ">
              	<a href="#">
                    <i class="fa fa-photo"></i>
                    <span>{{ trans( 'admin.campaigns' ) }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::url() == url('campaign/all') ? 'active' : '' }}">
                        <a href="{{ url('campaign/all') }}">
                            <i class="fa fa-circle-o"></i> {{ trans( 'admin.all_campaigns' ) }}
                        </a>
                    </li>
                    <li class="{{ Request::url() == url('campaign/create') ? 'active' : '' }}">
                        <a href="{{ url('campaign/create') }}">
                            <i class="fa fa-plus"></i> {{ trans( 'admin.add_new_campaign' ) }}
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->