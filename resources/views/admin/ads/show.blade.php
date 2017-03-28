@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="ads-details">
        <div class="box box-info">
            <div class="box-header with-border">
                {{ $title }}
                @if(isset($ads))
                    <div class="pull-right">
                        <div class="btn-toolbar">
                            <a href="{{ url('ads/edit/' . $ads->id ) }}" title="{{ trans('lang.edit') }}" class="btn btn-sm btn-info">
                                <i class="fa fa-edit"></i>
                            </a>
                            @if( $ads->status != DELETED_AD )
                            <a title="{{ trans('lang.delete') }}" data-toggle="modal" data-target="#deactivate-zone-modal" data-id="{{ $ads->id }}" class="btn btn-sm btn-danger deactivate-zone" >
                                <i class="fa fa-trash"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <div class="box-body">
                @include('admin.partial.filterTimePeriod')
                @if(isset($adsDetails))
                    <div id="chart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                    <div class="container-fluid mt20">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box bg-aqua">

                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            {{ trans( 'admin.impressions' ) }}  
                                        </span>
                                        <span class="info-box-number">
                                            {{ number_format($adsDetails->impressions, 0, ".", "," ) }}
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-green">
                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            {{ trans( 'admin.clicks' ) }}
                                        </span>
                                        <span class="info-box-number">
                                            {{ number_format($adsDetails->clicks, 0, ".", "," ) }}
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-red">

                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            {{ trans( 'admin.ctr' ) }}
                                        </span>
                                        <span class="info-box-number">
                                            {{ $ctr = $adsDetails->impressions ? number_format( ( $adsDetails->clicks * 100 / $adsDetails->impressions) ,2) : 0 }}%
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                            @if( Auth::user()->role == ADMIN_PRIV )
                            <div class="col-md-3">
                                <div class="info-box bg-light-blue">
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ trans( 'admin.convs' ) }}</span>
                                        <span class="info-box-number">
                                            {{ number_format($adsDetails->installed, 0, ".", "," ) ?: 0 }}  
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{ trans('admin.there_is_no_records') }}
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
            $('.deactivate-zone').on('click', function(){
                var id = $(this).attr('data-id');
                var $link = $('#deactivate-zone-modal .modal-footer a');
                var src = "{!! url( 'ads/change-status?&token=' . csrf_token() . '&s=' . DELETED_AD  . '&id=' ) !!}" + id;
                $link.attr( 'href', src );
            });
        });

    </script>    
@stop