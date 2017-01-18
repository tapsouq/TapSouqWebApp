@extends( 'admin.layout.layout' )

@section( 'head' )
    <link rel="stylesheet" type="text/css" href="{{url()}}/resources/assets/plugins/tooltipster-master/dist/css/tooltipster.bundle.min.css" />
@stop

@section( 'content' )
    <section class="all-ads">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                    ({{ count($allAds) }})
                </h3>
                @if( isset( $camp ) )
                    <div class="pull-right">
                        <a class="btn btn-sm btn-info" href="{{ url( 'ads/create?camp=' . $camp->id ) }}">
                            <i class="fa fa-plus"></i>
                            {{ trans( 'admin.add_new_ad' ) }}
                        </a>
                    </div>
                @endif
            </div>
            <div class="box-body">
                @include('admin.partial.filterTimePeriod')
                @if( sizeof( $allAds ) > 0 )
                    <div id="chart-container"></div>
                    <div class="box-group" id="apps_zones">
                        <?php $formats = config('consts.all_formats'); $types = config('consts.ads_types'); ?>
                        <?php  $states = config('consts.camp_status'); ?>
                        <?php $css = [ RUNNING_AD => 'label-success', PAUSED_AD => 'label-warning', COMPLETED_AD => 'label-info', DELETED_AD => 'label-danger' ]; ?>
                        <?php $campCss = [ RUNNING_CAMP => 'label-success', PAUSED_CAMP => 'label-warning', COMPLETED_CAMP => 'label-info', DELETED_CAMP => 'label-danger' ]; ?>
                        <div class="table">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ trans( 'lang.name' ) }}</th>
                                        <th>{{ trans( 'admin.impressions' ) }}</th>
                                        <th>{{ trans( 'admin.clicks' ) }}</th>
                                        <th>{{ trans( 'admin.ctr' ) }}</th>
                                        <th>{{ trans( 'admin.convs' ) }}</th>
                                        <th>{{ trans( 'lang.status' ) }}</th>
                                        <th>{{ trans( 'lang.actions' ) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $ids = [];?>
                                    @if(sizeof($ads) > 0)
                                        @foreach( $ads as $_key => $ad )
                                            <?php $ids[] = $ad->id;?>
                                            <tr>
                                                <td>
                                                    <?php $imgSrc = url('public/uploads/ad-images') . '/' . $ad->image_file;?>
                                                    <a class="ads-tooltip" title="<img src='{{$imgSrc}}' />" href="{{ url( 'ads/' . $ad->id ) }}">
                                                        {{ $ad->name }}
                                                        <img src="{{ $imgSrc }}" style="display:none;">
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $ad->impressions ?: 0 }}
                                                </td>
                                                <td>
                                                    {{ $ad->clicks ?: 0 }}
                                                </td>
                                                <td>
                                                    {{ $ad->impressions ? round($ad->clicks / $ad->impressions , 2) * 100 : 0 }}%
                                                </td>
                                                <td>
                                                    {{ $ad->clicks ? round($ad->installed / $ad->clicks , 2) * 100 : 0 }}%
                                                </td>
                                                <td>
                                                    <div class="label {{ $css[ $ad->status ] }}">
                                                        {{ $states[ $ad->status ] }}
                                                    </div>
                                                </td>
                                                <td>
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
                                                </td>
                                            </tr>     
                                        @endforeach
                                    @endif

                                    @foreach( $allAds as $index => $_value )
                                        @if( ! in_array($_value->id, $ids) )
                                            <tr>
                                                <td>
                                                    <?php $imgSrc = url('public/uploads/ad-images') . '/' . $_value->image_file;?>
                                                    <a class="ads-tooltip" title="<img src='{{$imgSrc}}' />" href="{{ url( 'ads/' . $_value->id ) }}">
                                                        {{ $_value->name }}
                                                    </a>
                                                </td>
                                                <td> 0 </td>
                                                <td> 0 </td>
                                                <td> 0% </td>
                                                <td> 0% </td>
                                                <td>
                                                    <div class="label {{ $css[ $_value->status ] }}">
                                                        {{ $states[ $_value->status ] }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ url('ads/edit/' . $_value->id ) }}" class="btn btn-sm btn-info">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        @if( in_array( $_value->status, [ RUNNING_AD, PAUSED_AD ] ) )
                                                            <?php $href = url( 'ads/change-status?id=' . $_value->id . '&token=' . csrf_token() . '&s=' ); ?>
                                                            <a data-href="{{ $href . ( $_value->status == RUNNING_AD ? PAUSED_AD : RUNNING_AD ) }}" data-toggle="modal" data-target="#change-status-modal" class="btn btn-sm change-status {{ $_value->status == RUNNING_AD ? 'btn-warning pause' : 'btn-success run' }}">
                                                                {!! $_value->status == RUNNING_AD ? '<i class="fa fa-pause"></i>' : '<i class="fa fa-play"></i>' !!}
                                                            </a>
                                                        @endif
                                                        @if( $_value->status != DELETED_AD )
                                                            <a data-toggle="modal" data-target="#change-status-modal" data-id="{{ $_value->id }}" class="btn btn-sm btn-danger deactivate-ad">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif     
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <p>
                        {{ trans( 'admin.no_ads_yet' ) }}
                    </p>
                @endif
            </div>
        </div>
        <div class="modal modal-danger" id="change-status-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">{{ trans( 'lang.delete' ) }} {{ trans( 'admin.ads' ) }} </h4>
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
    <script type="text/javascript" src="{{url()}}/resources/assets/plugins/tooltipster-master/dist/js/tooltipster.bundle.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.ads-tooltip').tooltipster({
                contentAsHTML : true,
                delay         : 600
            });

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
                var src = "{!! url( 'ads/change-status?token=' . csrf_token() . '&s=' . DELETED_AD  . '&id=' ) !!}" + id;
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