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
                    @if( Auth::user()->role != ADMIN_PRIV )
                    <div class="pull-right">
                        <a class="btn btn-sm btn-info" href="{{ url( 'zone/create?app=' . $application->id ) }}">
                            <i class="fa fa-plus"></i>
                            {{ trans( 'admin.add_new_place_ad' ) }}
                        </a>
                    </div>
                    @endif
                @endif
            </div>
            <div class="box-body">
                <?php $formats = config('consts.zone_formats'); $devices = config('consts.zone_devices'); ?>
                <?php  $states = config('consts.zone_status'); $layouts = config('consts.zone_layouts'); ?>
                <?php $css = [ ACTIVE_ZONE => 'label-info', DELETED_ZONE => 'label-warning' ]; ?>
                <?php $appCss = [ PENDING_APP => 'label-info', ACTIVE_APP => 'label-success', DELETED_APP => 'label-warning' ]; ?>
                <div class="table">
                    @include('admin.partial.filterTimePeriod')
                    @if( sizeof( $allZones ) > 0 )
                        <div id="chart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>{{ trans( 'lang.id' ) }}</th>
                                    <th>{{ trans( 'lang.name' ) }}</th>
                                    <th>{{ trans( 'admin.requests' ) }}</th>
                                    <th>{{ trans( 'admin.impressions' ) }}</th>
                                    <th>{{ trans( 'admin.fill_rate' ) }}</th>
                                    <th>{{ trans( 'admin.clicks' ) }}</th>
                                    <th>{{ trans( 'admin.ctr' ) }}</th>
                                    @if( Auth::user()->role == ADMIN_PRIV )
                                    <th>{{ trans( 'admin.convs' ) }}</th>
                                    @endif
                                    <th>{{ trans( 'lang.status' ) }}</th>
                                    @if( \Auth::user()->role == ADMIN_PRIV )
                                        <th>{{ trans( 'lang.actions' ) }}</th>
                                    @endif
                                    <th>{{ trans( 'lang.actions' ) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $ids = [];?>
                                @if(sizeof($ads) > 0 )
                                    @foreach( $ads as $key => $ad )
                                        <?php $ids[] = $ad->id;?>
                                        <tr>
                                            <td>{{ $ad->id }}</td>
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
                                                {{ $ad->requests ? round($ad->impressions/$ad->requests, 2) * 100 : 0 }}%
                                            </td>
                                            <td>
                                                {{ $ad->clicks ?: 0 }}
                                            </td>
                                            <td>
                                                {{ $ad->impressions ? round($ad->clicks / $ad->impressions, 4) * 100 : 0 }}%
                                            </td>
                                            @if( Auth::user()->role == ADMIN_PRIV )
                                            <td>
                                                {{ $ad->installed ?: 0 }}
                                            </td>
                                            @endif
                                            <td>
                                                <div class="label {{ $css[ $ad->status ] }}">
                                                    {{ $states[ $ad->status ] }}
                                                </div>
                                            </td>
                                            <td>
                                                @if( $ad->status != DELETED_APP && \Auth::user()->role == ADMIN_PRIV )
                                                    <div class="btn-group">
                                                        <a href="{{ url('reports/relevant-ads/' . $ad->id ) }}" title="{{ trans('admin.show_relevant_ads') }}" class="btn btn-sm btn-warning">
                                                            <i class="fa fa-exchange"></i>
                                                        </a>
                                                        <a href="{{ url('reports/shown-ads/' . $ad->id ) }}" title="{{ trans('admin.show_shown_ads') }}" class="btn btn-sm btn-info">
                                                            <i class="fa fa-laptop"></i>
                                                        </a>
                                                    </div>
                                                @endif
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
                                @endif
                                @foreach( $allZones as $_key => $_value )
                                    @if( ! in_array($_value->id, $ids) )
                                        <tr>
                                            <td>
                                                {{ $_value->id }}
                                            </td>
                                            <td>
                                                <a href="{{ url( 'zone/' . $_value->id ) }}">
                                                    {{ $_value->name }}
                                                </a>
                                            </td>
                                            <td> 0 </td>
                                            <td> 0 </td>
                                            <td> 0% </td>
                                            <td> 0 </td>
                                            <td> 0% </td>
                                            @if( Auth::user()->role == ADMIN_PRIV )
                                            <td> 0 </td>
                                            @endif
                                            <td>
                                                <div class="label {{ $css[ $_value->status ] }}">
                                                    {{ $states[ $_value->status ] }}
                                                </div>
                                            </td>
                                            <td>
                                                @if( $_value->status != DELETED_APP && \Auth::user()->role == ADMIN_PRIV )
                                                    <div class="btn-group">
                                                        <a href="{{ url('reports/relevant-ads/' . $_value->id ) }}" title="{{ trans('admin.show_relevant_ads') }}" class="btn btn-sm btn-warning">
                                                            <i class="fa fa-exchange"></i>
                                                        </a>
                                                        <a href="{{ url('reports/shown-ads/' . $_value->id ) }}" title="{{ trans('admin.show_shown_ads') }}" class="btn btn-sm btn-info">
                                                            <i class="fa fa-laptop"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if( $_value->status != DELETED_APP )
                                                    <div class="btn-group">
                                                        <a href="{{ url('zone/edit/' . $_value->id ) }}" class="btn btn-sm btn-info">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        @if( $_value->status != DELETED_ZONE )
                                                            <a data-toggle="modal" data-target="#deactivate-zone-modal" data-id="{{ $_value->id }}" class="btn btn-sm btn-danger deactivate-zone">
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
                                    @endif    
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>
                            {{ trans( 'admin.no_ads' ) }}
                        </p>
                    @endif
                </div>
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