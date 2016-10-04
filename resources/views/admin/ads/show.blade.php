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
                <div id="chart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                <div class="container-fluid mt20">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-yellow">
                                <span class="info-box-icon"><i class="fa fa-question"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        {{ trans( 'admin.requests' ) }}
                                    </span>
                                    <span class="info-box-number">
                                        {{ $adsDetails->requests }}
                                    </span>
                                    <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                  </div>
                                        <span class="progress-description">
                                            {{ $adsDetails->requests }} {{ trans( 'admin.requests' ) }}
                                        </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-aqua">
                                <span class="info-box-icon"><i class="fa fa-eye"></i></span>

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
                                            {{ trans('admin.of') }} {{ $adsDetails->requests }} {{ trans( 'admin.requests' ) }}
                                        </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-green">
                                <span class="info-box-icon"><i class="fa  fa-hand-o-up"></i></span>

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
                                            {{ trans('admin.of') }} {{ $adsDetails->requests }} {{ trans( 'admin.requests' ) }}
                                        </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-purple">
                                <span class="info-box-icon"><i class="fa fa-hourglass-half"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        {{ trans( 'admin.fill_rate' ) }}  
                                    </span>
                                    <span class="info-box-number">
                                        {{ $fillRate = $adsDetails->requests ? round($adsDetails->impressions / $adsDetails->requests, 2 ) : 0 }}
                                    </span>

                                  <div class="progress">
                                    <div class="progress-bar" style="width: {{ $fillRate * 100  }}%"></div>
                                  </div>
                                        <span class="progress-description">
                                            {{ $adsDetails->impressions }} / {{ $adsDetails->requests }}
                                        </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-red">
                                <span class="info-box-icon"><i class="fa fa-star-half-o"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        {{ trans( 'admin.ctr' ) }}
                                    </span>
                                    <span class="info-box-number">
                                        {{ $ctr = $adsDetails->impressions ? round($adsDetails->clicks / $adsDetails->impressions ,2) : 0 }}
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $ctr * 100 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $adsDetails->clicks }} / {{ $adsDetails->impressions }}
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light-blue">
                                <span class="info-box-icon"><i class="fa fa-download"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">{{ trans( 'admin.convs' ) }}</span>
                                    <span class="info-box-number">
                                        {{ $convs = $adsDetails->clicks ? round( $adsDetails->installed/$adsDetails->clicks, 2) : 0 }}  
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{  $convs * 100 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ $adsDetails->installed }} / {{ $adsDetails->clicks }}   
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section( 'script' )
    
@stop