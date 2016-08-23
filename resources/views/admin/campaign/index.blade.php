@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="all-camps">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                </h3>
            </div>
            <div class="box-body">
                <div class="table">
                    @if( sizeof( $camps ) > 0 )
                        <?php $css = [ RUNNING_CAMP => 'label-success', PAUSED_CAMP => 'label-warning', COMPLETED_CAMP => 'label-info', DELETED_CAMP=> 'label-danger' ]; ?>
                        <table class="table table-hover table-responsive table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans( 'lang.name' ) }}</th>
                                    @if( Auth::user()->role == ADMIN_PRIV )
                                        <th>{{ trans( 'admin.user' ) }}</th>
                                    @endif
                                    <th>{{ trans( 'admin.start_date' ) }}</th>
                                    <th>{{ trans( 'admin.end_date' ) }}</th>
                                    <th>{{ trans( 'admin.platform' ) }}</th>
                                    <th>{{ trans( 'admin.serving' ) }}</th>
                                    <th>{{ trans( 'lang.status' ) }}</th>
                                    <th>{{ trans( 'lang.actions' ) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $camps as $key => $camp )
                                    <tr>
                                        <td>{{ $camp->id }}</td>
                                        <td>{{ $camp->name }}</td>
                                        @if( Auth::user()->role == ADMIN_PRIV )
                                            <td>{{ $camp->fname . " " . $camp->lname }}</td>
                                        @endif
                                        <td>{{ date_create_from_format( "Y-m-d H:i:s", $camp->start_date )->format('m/d/Y g:i A') }}</td>
                                        <td>{{ date_create_from_format( "Y-m-d H:i:s", $camp->end_date )->format('m/d/Y g:i A') }}</td>
                                        <td>{{ config('consts.app_platforms')[ $camp->target_platform ] }}</td>
                                        <td>{{ $camp->ad_servinf_pace ? config('consts.camp_serving')[ $camp->ad_serving_pace ] : '' }}</td>
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