@extends( 'admin.layout.layout' )

@section( 'head' )
    
@stop

@section( 'content' )
    <section class="all-apps">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                    ({{ count($apps) }})
                     | 
                    <a href="{{ url('zone/all') }}">
                        {{ trans('admin.all_placement_ads') }}
                        ({{ $adsCount }})
                    </a>
                </h3>
                <div class="pull-right">
                    <a class="btn btn-sm btn-info" href="{{ url('app/create') }}">
                        <i class="fa fa-plus m5"></i>
                        {{ trans('admin.add_new_app') }}
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table">
                    @include('admin.partial.filterTimePeriod')
                    @if( sizeof( $apps ) > 0 )
                        <div id="chart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans( 'lang.name' ) }}</th>
                                    <th>{{ trans( 'admin.requests' ) }}</th>
                                    <th>{{ trans( 'admin.impressions' ) }}</th>
                                    <th>{{ trans( 'admin.clicks' ) }}</th>
                                    <th>{{ trans( 'admin.ctr' ) }}</th>
                                    <th>{{ trans( 'admin.fill_rate' ) }}</th>
                                    <th>{{ trans( 'admin.convs' ) }}</th>
                                    <th>{{ trans( 'admin.num_of_ads' ) }}</th>
                                    <th>{{ trans( 'lang.status' ) }}</th>
                                    <th>{{ trans( 'lang.actions' ) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $css = [ PENDING_APP => 'label-info', ACTIVE_APP => 'label-success', DELETED_APP => 'label-warning' ]; ?>
                                @foreach( $apps as $key => $value )
                                    <tr>
                                        <td>
                                            <span class="app-icon">
                                                <img height="50px" width="50px" src="{{ url('public/uploads/app-icons/' . $value->icon) }}" alt="{{ $value->name }}">
                                            </span>
                                            <a href="{{ url( 'zone/all/' . $value->id ) }}" title="{{ trans('admin.show_app_ads') }}" >
                                                {{ $value->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $value->requests ?: 0 }}
                                        </td>
                                        <td>
                                            {{ $value->impressions ?: 0 }}
                                        </td>
                                        <td>
                                            {{ $value->clicks ?: 0 }}
                                        </td>
                                        <td>
                                            {{ $value->impressions ? round($value->clicks/$value->impressions,2)*100 : 0 }}%
                                        </td>
                                        <td>
                                            {{ $value->requests ? round($value->impressions / $value->requests,2)*100: 0 }}%
                                        </td>
                                        <td>
                                            {{ $value->impressions ? round( $value->installed/$value->impressions,2)*100 : 0  }}%
                                        </td>
                                        <td>
                                            {{ getAppAdsCount($value->id) }}
                                        </td>
                                        <td>
                                            <div class="label {{ $css[$value->status] }}">
                                                {{ config('consts.app_status')[$value->status] }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ url('app/edit/' . $value->id ) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @if( $value->status != DELETED_APP )
                                                <a data-toggle="modal" data-target="#deactivate-app-modal" data-id="{{ $value->id }}" class="btn btn-sm btn-danger deactivate-app">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>
                        {{ trans( 'admin.no_apps' ) }}
                    </p>
                @endif
            </div>
        </div>
        <div class="modal modal-danger" id="deactivate-app-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">{{ trans( 'lang.delete' ) }} {{ trans( 'admin.application' ) }} </h4>
                    </div>
                    <div class="modal-body">
                        <p>{{ trans( 'admin.sure_delete' ) }} {{ trans( 'admin.application' ) }} </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">{{ trans( 'lang.close' ) }}</button>
                        <a  class="btn btn-outline">
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
            $('.deactivate-app').on('click', function(){
                var id = $(this).attr('data-id');
                var $link = $('#deactivate-app-modal .modal-footer a');
                var src = "{!! url( 'delete-app?token=' . csrf_token() . '&id=' ) !!}" + id;
                $link.attr( 'href', src );
            });
        });
    </script>
@stop