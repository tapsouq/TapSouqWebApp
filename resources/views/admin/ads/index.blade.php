@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="all-ads">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                </h3>
            </div>
            <div class="box-body">

                    @if( sizeof( $camps ) > 0 )
                        <div class="box-group" id="apps_zones">
                            <?php $formats = config('consts.all_formats'); $types = config('consts.ads_types'); ?>
                            <?php  $states = config('consts.camp_status'); ?>
                            <?php $css = [ RUNNING_AD => 'label-success', PAUSED_AD => 'label-warning', COMPLETED_AD => 'label-info', DELETED_AD => 'label-danger' ]; ?>
                            <?php $campCss = [ RUNNING_CAMP => 'label-success', PAUSED_CAMP => 'label-warning', COMPLETED_CAMP => 'label-info', DELETED_CAMP => 'label-danger' ]; ?>
                            @foreach( $camps as $key => $camp )
                                <div class="panel box box-success">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#apps_zones" href="#app_{{ $camp->id }}" aria-expanded="false" class="accord-title collapsed">
                                                {{ $camp->name }}
                                            </a>
                                        </h4>
                                        <span class="pull-right-container">
                                            <span class="label {{ $campCss[ $camp->status ] }}">
                                                {{ config('consts.camp_status')[ $camp->status ] }}
                                            </span>
                                            <span class="label label-primary pull-right">
                                                {{ count( $ads = getCampAds( $camp->id ) ) }}
                                            </span>
                                        </span>
                                    </div>
                                    <div id="app_{{ $camp->id }}" class="panel-collapse collapse">
                                        <div class="box-body">
                                            @if( sizeof( $ads ) > 0 )
                                                <div class="table">
                                                    <table class="table table-hover table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{ trans( 'lang.name' ) }}</th>
                                                                <th>{{ trans( 'admin.campaign' ) }}</th>
                                                                <th>{{ trans( 'admin.format' ) }}</th>
                                                                <th>{{ trans( 'lang.type' ) }}</th>
                                                                <th>{{ trans( 'admin.click_url' ) }}</th>
                                                                <th>{{ trans( 'lang.title' ) }}</th>
                                                                <th>{{ trans( 'lang.description' ) }}</th>
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
                                                                    <td>
                                                                        {{ $camp->name }}
                                                                    </td>
                                                                    <td>{{ $formats[ $ad->format ] }}</td>
                                                                    <td>{{ $types[ $ad->type ] }}</td>
                                                                    <td>{{ $ad->click_url }}</td>
                                                                    <td>{{ $ad->title }}</td>
                                                                    <td>{{ $ad->description }}</td>
                                                                    <td>
                                                                        <div class="label {{ $css[ $ad->status ] }}">
                                                                            {{ $states[ $ad->status ] }}
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @if( $camp->status != DELETED_CAMP )
                                                                            <div class="btn-group">
                                                                                <a href="{{ url('ads/edit/' . $ad->id ) }}" class="btn btn-sm btn-info">
                                                                                    <i class="fa fa-edit"></i>
                                                                                </a>
                                                                                @if( in_array( $ad->status, [ RUNNING_AD, PAUSED_AD ] ) )
                                                                                    <?php $href = url( 'ads/change-status?id=' . $ad->id . '&token=' . csrf_token() . '&s=' ); ?>
                                                                                    <a data-href="{{ $href . ( $ad->status == RUNNING_AD ? PAUSED_AD : RUNNING_AD ) }}" data-toggle="modal" data-target="#change-status-modal" class="btn btn-sm change-status {{ $ad->status == RUNNING_AD ? 'btn-warning pause' : 'btn-success run' }}">
                                                                                        {!! $ad->status == RUNNING_AD ? '<i class="fa fa-pause"></i>' : '<i class="fa fa-play"></i>' !!}
                                                                                    </a>
                                                                                @endif
                                                                                @if( $ad->status != DELETED_AD )
                                                                                    <a data-toggle="modal" data-target="#change-status-modal" data-id="{{ $ad->id }}" class="btn btn-sm btn-danger deactivate-ad">
                                                                                        <i class="fa fa-trash"></i>
                                                                                    </a>
                                                                                @endif
                                                                            </div>
                                                                        @else
                                                                            <p>
                                                                                {{ trans( 'admin.camp_is_deleted' ) }}
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
                            {{ trans( 'admin.no_ads_yet' ) }}
                        </p>
                    @endif
                    <!--
                    <div class="table">
                        <table class="table table-hover table-border table-striped table-advance">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans( 'lang.name' ) }}</th>
                                    <th>{{ trans( 'admin.campaign' ) }}</th>
                                    <th>{{ trans( 'admin.format' ) }}</th>
                                    <th>{{ trans( 'lang.type' ) }}</th>
                                    <th>{{ trans( 'admin.click_url' ) }}</th>
                                    <th>{{ trans( 'lang.title' ) }}</th>
                                    <th>{{ trans( 'lang.description' ) }}</th>
                                    <th>{{ trans( 'lang.status' ) }}</th>
                                    <th>{{ trans( 'lang.actions' ) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $css = [ RUNNING_AD => 'label-success', PAUSED_AD => 'label-warning', COMPLETED_AD => 'label-info', DELETED_AD => 'label-danger' ]; ?>
                                @foreach( $ads as $key => $ad )
                                    <tr>
                                        <td>
                                            {{ $ad->id }}
                                        </td>
                                        <td>
                                            {{ $ad->name }}
                                        </td>
                                        <td>
                                            {{ $ad->campaign }}
                                        </td>
                                        <td>
                                            {{ config('consts.all_formats')[$ad->format] }}
                                        </td>
                                        <td>
                                            {{ config('consts.ads_types')[$ad->type] }}
                                        </td>
                                        <td>
                                            {{ $ad->click_url }}
                                        </td>
                                        <td>
                                            {{ $ad->title }}
                                        </td>
                                        <td>
                                            {{ $ad->description }}
                                        </td>
                                        <td>
                                            <div class="label {{ $css[$ad->status] }}">
                                                {{ config('consts.ads_status')[$ad->status] }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ url('ads/edit/' . $ad->id ) }}" class="btn btn-sm btn-info">
                                                    {{ trans( 'lang.edit' ) }}
                                                </a>
                                                @if( in_array( $ad->status, [ RUNNING_AD, PAUSED_AD ] ) )
                                                    <?php $href = url( 'ads/change-status?id=' . $ad->id . '&token=' . csrf_token() . '&s=' ); ?>
                                                    <a data-href="{{ $href . ( $ad->status == RUNNING_AD ? PAUSED_AD : RUNNING_AD ) }}" data-toggle="modal" data-target="#change-status-modal" class="btn btn-sm change-status {{ $ad->status == RUNNING_AD ? 'btn-warning pause' : 'btn-success run' }}">
                                                        {{ $ad->status == RUNNING_AD ? trans( 'lang.pause' ) : trans( 'lang.run' ) }}
                                                    </a>
                                                @endif
                                                @if( $ad->status != DELETED_AD )
                                                <a data-toggle="modal" data-target="#change-status-modal" data-id="{{ $ad->id }}" class="btn btn-sm btn-danger deactivate-ad">
                                                    {{ trans( 'lang.delete' ) }}
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    -->
                
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
            $('.deactivate-ad').on('click', function(){
                var id = $(this).attr('data-id');
                $('#change-status-modal').removeClass('modal-warning modal-success')
                                        .addClass('modal-danger')
                                        .find('.modal-body p')
                                        .text("{{ trans( 'admin.sure_delete' ) . ' ' . trans( 'admin.creative_ads' ) }}")
                                        .parents('.modal')
                                        .find('.modal-footer .action')
                                        .text("{{ trans( 'lang.delete' ) }}")
                                        .parents('.modal')
                                        .find('.modal-header .modal-title')
                                        .text( "{{ trans('admin.delete_ads') }}" );
                var $link = $('#change-status-modal .modal-footer a');
                var src = "{!! url( 'ads/change-status?&token=' . csrf_token() . '&s=' . DELETED_AD  . '&id=' ) !!}" + id;
                $link.attr( 'href', src );
            });

            $('.change-status').on('click', function(){
                var href = $(this).attr('data-href');
                if( $(this).hasClass('run')){
                    $('#change-status-modal').removeClass('modal-danger modal-warning')
                                            .addClass('modal-success')
                                            .find('.modal-body p')
                                            .text("{{ trans('admin.are_u_sure_run_ads') }}")
                                            .parents('.modal')
                                            .find('.modal-footer .action')
                                            .text("{{ trans( 'lang.run' ) }}")
                                            .parents('.modal')
                                            .find('.modal-header .modal-title')
                                            .text( "{{ trans('admin.run_ads') }}" );
                }else{
                    $('#change-status-modal').removeClass('modal-success modal-danger')
                                            .addClass('modal-warning')
                                            .find('.modal-body p')
                                            .text("{{ trans('admin.are_u_sure_pause_ads') }}")
                                            .parents('.modal')
                                            .find('.modal-footer .action')
                                            .text("{{ trans( 'lang.pause' ) }}")
                                            .parents('.modal')
                                            .find('.modal-header .modal-title')
                                            .text( "{{ trans('admin.pause_ads') }}" );
                }
                var $link = $('#change-status-modal .modal-footer a');
                $link.attr( 'href', href );
            });
        });
    </script>
@stop