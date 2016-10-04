@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="all-zones">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ isset( $application ) ? $application->name . " > " : '' }} {{ $title }}
                </h3>
                @if( isset( $application ) )
                    <div class="pull-right">
                        <a class="btn btn-sm btn-info" href="{{ url( 'zone/create' ) }}">
                            <i class="fa fa-plus"></i>
                            {{ trans( 'admin.add_new_place_ad' ) }}
                        </a>
                    </div>
                @endif
            </div>
            <div class="box-body">
                <div id="chart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                <?php $formats = config('consts.zone_formats'); $devices = config('consts.zone_devices'); ?>
                <?php  $states = config('consts.zone_status'); $layouts = config('consts.zone_layouts'); ?>
                <?php $css = [ ACTIVE_ZONE => 'label-info', DELETED_ZONE => 'label-warning' ]; ?>
                <?php $appCss = [ PENDING_APP => 'label-info', ACTIVE_APP => 'label-success', DELETED_APP => 'label-warning' ]; ?>
                @if( sizeof( $ads ) > 0 )
                    <div class="table">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>{{ trans( 'lang.name' ) }}</th>
                                    <th>{{ trans( 'admin.requests' ) }}</th>
                                    <th>{{ trans( 'admin.impressions' ) }}</th>
                                    <th>{{ trans( 'admin.clicks' ) }}</th>
                                    <th>{{ trans( 'admin.ctr' ) }}</th>
                                    <th>{{ trans( 'admin.fill_rate' ) }}</th>
                                    <th>{{ trans( 'admin.convs' ) }}</th>
                                    <th>{{ trans( 'lang.status' ) }}</th>
                                    <th>{{ trans( 'lang.actions' ) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $ads as $key => $ad )
                                    <tr>
                                        <td>
                                            <a href="{{ url( 'zone/' . $ad->id ) }}">
                                                {{ $ad->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $ad->requests ?: 0 }}
                                        </td>
                                        <td>
                                            {{ $ad->impressions ?: 0 }}
                                        </td>
                                        <td>
                                            {{ $ad->clicks ?: 0 }}
                                        </td>
                                        <td>
                                            {{ $ad->impressions ? round($ad->clicks / $ad->impressions, 2): 0 }}
                                        </td>
                                        <td>
                                            {{ $ad->requests ? round($ad->impressions/$ad->requests, 2) : 0 }}
                                        </td>
                                        <td>
                                            {{ $ad->impressions ? round($ad->installed / $ad->impressions, 2) : 0 }}
                                        </td>
                                        <td>
                                            <div class="label {{ $css[ $ad->status ] }}">
                                                {{ $states[ $ad->status ] }}
                                            </div>
                                        </td>
                                        <td>
                                            @if( $ad->status != DELETED_APP )
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
        $(function(){
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