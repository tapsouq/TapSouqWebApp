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
                                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            {{ trans( 'admin.impressions' ) }}  
                                        </span>
                                        <span class="info-box-number">
                                            {{ $adsDetails->impressions }}
                                        </span>

                                      <div class="progress">
                                        <?php $progress = $adsDetails->requests ? ( round( $adsDetails->impressions / $adsDetails->requests, 2)  * 100 ) : 0 ?>
                                        <div class="progress-bar" style="width: {{$progress}}%"></div>
                                      </div>
                                            <span class="progress-description">
                                            </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa  fa-clock-o"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            {{ trans( 'admin.clicks' ) }}
                                        </span>
                                        <span class="info-box-number">
                                            {{ $adsDetails->clicks }}
                                        </span>

                                        <div class="progress">
                                            <?php $progress = $adsDetails->requests ? ( round($adsDetails->clicks / $adsDetails->requests,2) * 100 ) : 0; ?>
                                            <div class="progress-bar" style="width: {{ $progress }}%"></div>
                                        </div>
                                            <span class="progress-description">
                                            </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">
                                            {{ trans( 'admin.ctr' ) }}
                                        </span>
                                        <span class="info-box-number">
                                            {{ $ctr = $adsDetails->impressions ? round($adsDetails->clicks / $adsDetails->impressions ,2) * 100 : 0 }}%
                                        </span>

                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $ctr * 100 }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-light-blue">
                                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ trans( 'admin.convs' ) }}</span>
                                        <span class="info-box-number">
                                            {{ $convs = $adsDetails->clicks ? round( $adsDetails->installed/$adsDetails->clicks, 2) * 100 : 0 }}%  
                                        </span>

                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{  $convs * 100 }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
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