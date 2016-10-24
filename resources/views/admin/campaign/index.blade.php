@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="all-camps">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                    ({{ count($camps) }})
                    |
                    <a href="{{ url( 'ads/all' ) }}">
                        {{ trans( 'admin.ads' ) }}
                        ({{ $adsCount }})
                    </a>
                </h3>
                <div class="pull-right">
                    <a class="btn btn-info btn-sm" href="{{ url( 'campaign/create' ) }}">
                        <i class="fa fa-plus"></i>
                        {{ trans( 'admin.add_new_campaign' ) }}
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table">
                    @include('admin.partial.filterTimePeriod')
                    @if( sizeof( $camps ) > 0 )
                        <?php $css = [ RUNNING_CAMP => 'label-success', PAUSED_CAMP => 'label-warning', COMPLETED_CAMP => 'label-info', DELETED_CAMP=> 'label-danger' ]; ?>
                        <div id="chart-container" style="min-width: 310px; max-width: 100%; height: 400px; margin: 0 auto"></div>
                        <table class="table table-hover table-responsive table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans( 'lang.name' ) }}</th>
                                    <th>{{ trans( 'admin.impressions' ) }}</th>
                                    <th>{{ trans( 'admin.clicks' ) }}</th>
                                    <th>{{ trans( 'admin.convs' ) }}</th>
                                    <th>{{ trans( 'admin.start_date' ) }}</th>
                                    <th>{{ trans( 'admin.end_date' ) }}</th>
                                    <th>{{ trans( 'admin.num_of_ads' ) }}</th>
                                    <th>{{ trans( 'lang.status' ) }}</th>
                                    <th>{{ trans( 'lang.actions' ) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $camps as $key => $camp )
                                    <tr>
                                        <td>
                                            <a href="{{ url( 'ads/all/' . $camp->id ) }}" title="{{ trans('admin.show_camp_ads') }}">
                                                {{ $camp->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $camp->impressions?: 0 }}
                                        </td>
                                        <td>
                                            {{ $camp->clicks?: 0 }}
                                        </td>
                                        <td>
                                            {{ $camp->clicks ? round($camp->installed / $camp->clicks , 2) * 100 : 0 }}%
                                        </td>
                                        <td>{{ date_create_from_format( "Y-m-d H:i:s", $camp->start_date )->format('m/d/Y g:i A') }}</td>
                                        <td>{{ date_create_from_format( "Y-m-d H:i:s", $camp->end_date )->format('m/d/Y g:i A') }}</td>
                                        <td>
                                            {{ getCampAdsCount( $camp->id ) }}
                                        </td>
                                        <td>
                                            <div class="label {{ $css[ $camp->status ] }}">
                                                {{ config('consts.camp_status')[ $camp->status ] }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ url('campaign/edit/' . $camp->id ) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @if( in_array( $camp->status, [ RUNNING_CAMP, PAUSED_CAMP ] ) )
                                                    <?php $href = url( 'camp/change-status?id=' . $camp->id . '&token=' . csrf_token() . '&s=' ); ?>
                                                    <a data-href="{{ $href . ( $camp->status == RUNNING_CAMP ? PAUSED_CAMP : RUNNING_CAMP ) }}" data-toggle="modal" data-target="#change-status-modal" class="btn btn-sm change-status {{ $camp->status == RUNNING_CAMP ? 'btn-warning pause' : 'btn-success run' }}">
                                                        {!! $camp->status == RUNNING_CAMP ? '<i class="fa fa-pause"></i>' : '<i class="fa fa-play"></i>' !!}
                                                    </a>
                                                @endif
                                                @if( $camp->status != DELETED_CAMP )
                                                <a data-toggle="modal" data-target="#change-status-modal" data-id="{{ $camp->id }}" class="btn btn-sm btn-danger deactivate-camp">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>
                            {{ trans( 'admin.no_active_camps' ) }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal modal-danger" id="change-status-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">{{ trans( 'lang.delete' ) }} {{ trans( 'admin.application' ) }} </h4>
                    </div>
                    <div class="modal-body">
                        <p> </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">{{ trans( 'lang.close' ) }}</button>
                        <a  class="btn btn-outline action"></a>
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
            $('.deactivate-camp').on('click', function(){
                var id = $(this).attr('data-id');
                $('#change-status-modal').removeClass('modal-warning modal-success')
                                        .addClass('modal-danger')
                                        .find('.modal-body p')
                                        .text("{{ trans( 'admin.sure_delete' ) . ' ' . trans( 'admin.application' ) }}")
                                        .parents('.modal')
                                        .find('.modal-footer .action')
                                        .text("{{ trans( 'lang.delete' ) }}")
                                        .parents('.modal')
                                        .find('.modal-header .modal-title')
                                        .text( "{{ trans('admin.delete_camp') }}" );
                var $link = $('#change-status-modal .modal-footer a');
                var src = "{!! url( 'camp/change-status?&token=' . csrf_token() . '&s=' . DELETED_CAMP  . '&id=' ) !!}" + id;
                $link.attr( 'href', src );
            });

            $('.change-status').on('click', function(){
                var href = $(this).attr('data-href');
                if( $(this).hasClass('run')){
                    $('#change-status-modal').removeClass('modal-danger modal-warning')
                                            .addClass('modal-success')
                                            .find('.modal-body p')
                                            .text("{{ trans('admin.are_u_sure_run') }}")
                                            .parents('.modal')
                                            .find('.modal-footer .action')
                                            .text("{{ trans( 'lang.run' ) }}")
                                            .parents('.modal')
                                            .find('.modal-header .modal-title')
                                            .text( "{{ trans('admin.run_camp') }}" );
                }else{
                    $('#change-status-modal').removeClass('modal-success modal-danger')
                                            .addClass('modal-warning')
                                            .find('.modal-body p')
                                            .text("{{ trans('admin.are_u_sure_pause') }}")
                                            .parents('.modal')
                                            .find('.modal-footer .action')
                                            .text("{{ trans( 'lang.pause' ) }}")
                                            .parents('.modal')
                                            .find('.modal-header .modal-title')
                                            .text( "{{ trans('admin.pause_camp') }}" );
                }
                var $link = $('#change-status-modal .modal-footer a');
                $link.attr( 'href', href );
            });

        });
    </script>
@stop