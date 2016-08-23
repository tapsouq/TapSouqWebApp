@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="all-zones">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                </h3>
            </div>
            <div class="box-body">
                @if( sizeof( $apps ) > 0 )
                    <div class="box-group" id="apps_zones">
                        <?php $formats = config('consts.zone_formats'); $devices = config('consts.zone_devices'); ?>
                        <?php  $states = config('consts.zone_status'); $layouts = config('consts.zone_layouts'); ?>
                        <?php $css = [ ACTIVE_ZONE => 'label-info', DELETED_ZONE => 'label-warning' ]; ?>
                        <?php $appCss = [ PENDING_APP => 'label-info', ACTIVE_APP => 'label-success', DELETED_APP => 'label-warning' ]; ?>
                        @foreach( $apps as $key => $application )
                            <div class="panel box box-success">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" data-parent="#apps_zones" href="#app_{{ $application->id }}" aria-expanded="false" class="accord-title collapsed">
                                            {{ $application->name }}
                                        </a>
                                    </h4>
                                    <span class="pull-right-container">
                                        <span class="label {{ $appCss[ $application->status ] }}">
                                            {{ config('consts.app_status')[ $application->status ] }}
                                        </span>
                                        <span class="label label-primary pull-right">
                                            {{ count( $ads = getAppAds( $application->id ) ) }}
                                        </span>
                                    </span>
                                </div>
                                <div id="app_{{ $application->id }}" class="panel-collapse collapse">
                                    <div class="box-body">
                                        @if( sizeof( $ads ) > 0 )
                                            <div class="table">
                                                <table class="table table-hover table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ trans( 'lang.name' ) }}</th>
                                                            <th>{{ trans( 'admin.format' ) }}</th>
                                                            <th>{{ trans( 'admin.device_type' ) }}</th>
                                                            <th>{{ trans( 'admin.layout' ) }}</th>
                                                            <th>{{ trans( 'admin.refresh_interval' ) }}</th>
                                                            <th>{{ trans( 'admin.hourly_freq' ) }}</th>
                                                            <th>{{ trans( 'admin.daily_freq' ) }}</th>
                                                            <th>{{ trans( 'lang.status' ) }}</th>
                                                            <th>{{ trans( 'lang.actions' ) }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach( $ads as $_key => $ad )
                                                            <tr>
                                                                <td>{{ $ad->id }}</td>
                                                                <td>
                                                                    {{ $ad->name }}
                                                                </td>
                                                                <td>{{ $formats[ $ad->format ] }}</td>
                                                                <td>{{ $devices[ $ad->device_type ] }}</td>
                                                                <td>{{ $ad->layout ? $layouts[ $ad->layout ] : '' }}</td>
                                                                <td>{{ $ad->refresh_interval }}</td>
                                                                <td>{{ $ad->hourly_freq_cap }}</td>
                                                                <td>{{ $ad->daily_freq_cap }}</td>
                                                                <td>
                                                                    <div class="label {{ $css[ $ad->status ] }}">
                                                                        {{ $states[ $ad->status ] }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @if( $application->status != DELETED_APP )
                                                                        <div class="btn-group">
                                                                            <a href="{{ url('zone/edit/' . $ad->id ) }}" class="btn btn-sm btn-info">
                                                                                <i class="fa fa-edit"></i>
                                                                            </a>
                                                                            @if( $ad->status != DELETED_ZONE )
                                                                                <a data-toggle="modal" data-target="#deactivate-zone-modal" data-id="{{ $ad->id }}" class="btn btn-sm btn-danger deactivate-zone">
                                                                                    <i class="fa fa-trash"></i>
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    @else
                                                                        <p>
                                                                            {{ trans( 'admin.app_is_deleted' ) }}
                                                                        </p>
                                                                    @endif
                                                                </td>
                                                            </tr>     
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p>
                                                {{ trans( 'admin.no_ads_for_app' ) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>
                        {{ trans( 'admin.no_ads' ) }}
                    </p>
                @endif
            </div>
        </div>
        <div class="modal modal-danger" id="deactivate-zone-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">{{ trans( 'lang.delete' ) }} {{ trans( 'admin.ad_placement' ) }} </h4>
                    </div>
                    <div class="modal-body">
                        <p>{{ trans( 'admin.sure_delete' ) }} {{ trans( 'admin.ad_placement' ) }} </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">{{ trans( 'lang.close' ) }}</button>
                        <a href="" class="btn btn-outline">
                            {{ trans( 'lang.delete' ) }}
                        </a>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </section>
@stop

@section( 'script' )
    <script type="text/javascript">
        $(document).ready(function(){
            if(location.hash) {
                // find accordion href ending with that anchor
                // and trigger a click
                var hash = location.hash;
                $( "#apps_zones .accord-title[href='" + hash + "']" ).trigger('click');
            }
        
            $('.deactivate-zone').on('click', function(){
                var id = $(this).attr('data-id');
                var $link = $('#deactivate-zone-modal .modal-footer a');
                var src = "{!! url( 'delete-zone?token=' . csrf_token() . '&id=' ) !!}" + id;
                $link.attr( 'href', src );
            });
        });

    </script>
@stop