@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="zone-details">
        <div class="box box-info">
            <div class="box-header with-border">
                {{ $title }}
            </div>
            <div class="box-body">
                @include('admin.partial.filterTimePeriod')
                <div id="chart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                <div class="container-fluid mt20">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-yellow">
                                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        {{ trans( 'admin.requests' ) }}
                                    </span>
                                    <span class="info-box-number">
                                        {{ $zoneDetails->requests }}
                                    </span>
                                    <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                  </div>
                                        <span class="progress-description">
                                        </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-aqua">
                                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        {{ trans( 'admin.impressions' ) }}  
                                    </span>
                                    <span class="info-box-number">
                                        {{ $zoneDetails->impressions }}
                                    </span>

                                  <div class="progress">
                                    <?php $progress = $zoneDetails->requests ? ( round( $zoneDetails->impressions / $zoneDetails->requests, 2)  * 100 ) : 0 ?>
                                    <div class="progress-bar" style="width: {{$progress}}%"></div>
                                  </div>
                                        <span class="progress-description">
                                        </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-green">
                                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        {{ trans( 'admin.clicks' ) }}
                                    </span>
                                    <span class="info-box-number">
                                        {{ $zoneDetails->clicks }}
                                    </span>

                                    <div class="progress">
                                        <?php $progress = $zoneDetails->requests ? ( round($zoneDetails->clicks / $zoneDetails->requests,2) * 100 ) : 0; ?>
                                        <div class="progress-bar" style="width: {{ $progress }}%"></div>
                                    </div>
                                        <span class="progress-description">
                                        </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-purple">
                                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        {{ trans( 'admin.fill_rate' ) }}  
                                    </span>
                                    <span class="info-box-number">
                                        {{ $fillRate = $zoneDetails->requests ? round($zoneDetails->impressions / $zoneDetails->requests, 2 ) * 100 : 0 }}%
                                    </span>

                                  <div class="progress">
                                    <div class="progress-bar" style="width: {{ $fillRate * 100  }}%"></div>
                                  </div>
                                        <span class="progress-description">
                                        </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-red">
                                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        {{ trans( 'admin.ctr' ) }}
                                    </span>
                                    <span class="info-box-number">
                                        {{ $ctr = $zoneDetails->impressions ? round($zoneDetails->clicks / $zoneDetails->impressions ,2) * 100 : 0 }}%
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
                        <div class="col-md-4">
                            <div class="info-box bg-light-blue">
                                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">{{ trans( 'admin.convs' ) }}</span>
                                    <span class="info-box-number">
                                        {{ $convs = $zoneDetails->clicks ? round( $zoneDetails->installed/$zoneDetails->clicks, 2) * 100 : 0 }}%  
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
            </div>
        </div>
    </section>
@stop

@section( 'script' )
    
@stop