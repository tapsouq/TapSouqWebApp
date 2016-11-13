@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="ads-details">
        <div class="box box-info">
            <div class="box-header with-border">
                {{ $title }}
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
                    {{ trans('admin.there_is_no_ads') }}
                @endif
            </div>
        </div>
    </section>
@stop

@section( 'script' )
    
@stop