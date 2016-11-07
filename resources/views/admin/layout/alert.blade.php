@if( session('notification') )
    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i> {{ trans( 'lang.alert' ) }}</h4>
        {{ session( 'notification' ) }} : <small class="label bg-red">{{ session('notificationData') }}</small>
    </div>
@endif
@if( session( 'success' ) )
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> {{ trans( 'lang.alert' ) }}</h4>
        {{ session( 'success' ) }}
    </div>
@endif

@if( session( 'error' ) )
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> {{ trans( 'lang.alert' ) }}</h4>
        {{ session( 'error' ) }}
    </div>
@endif

@if( session( 'info' ) )
    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i> {{ trans( 'lang.alert' ) }}</h4>
        {{ session( 'info' ) }}
    </div>
@endif

@if( session( 'warning' ) )
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-warning"></i> {{ trans( 'lang.alert' ) }}</h4>
        {{ session( 'warning' ) }}
    </div>
@endif