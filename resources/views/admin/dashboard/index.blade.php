@extends( 'admin.layout.layout' )

@section( 'content' )
	<section class="admin">
	    <div class="box box-info">
	        <div class="box-header with-border">
	            <h3 class="box-title">
	                {{ $title }}
	            </h3>
                <span class="pull-right">
                    <div class="btn-toolbar">
                        <a href="{{ url('admin?camps=1') }}" class="btn btn-info {{ Request::input('camps') ? 'disabled' : '' }}">
                            {{ trans( 'admin.campaigns' ) }}
                        </a>
                        <a  href="{{ url('admin') }}" class="btn btn-success {{ Request::input('camps') ? '' : 'disabled' }}" >
                            {{ trans('admin.applications') }}
                        </a>
                    </div>
                </span>
	        </div>
	        <div class="box-body">
                <div class="table">
                    @include('admin.partial.filterTimePeriod')
                    @if( sizeof( $creditCharts ) )
                        <div id="creditchart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                    @endif
                    @if( sizeof( $chartData ) > 0 )
                        <div id="chart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                        <div class="container-fluid mt20">
                            <div class="row">
                                @if( ! Request::has('camps') )
                                <div class="col-md-4">
                                    <div class="info-box bg-yellow">
                                        <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.requests' ) }}  
                                            </span>
                                            <span class="info-box-number">
                                                {{ $total->requests }}
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
                                @endif
                                <div class="{{ Request::input('camps') ? 'col-md-3' : 'col-md-4' }}">
                                    <div class="info-box bg-aqua">
                                        <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.impressions' ) }}  
                                            </span>
                                            <span class="info-box-number">
                                                {{ $total->impressions }}
                                            </span>

                                          <div class="progress">
                                            <?php $progress = $total->requests ? ( round( $total->impressions / $total->requests, 2)  * 100 ) : 0 ?>
                                            <div class="progress-bar" style="width: {{$progress}}%"></div>
                                          </div>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                <div class="{{ Request::has('camps') ? 'col-md-3' : 'col-md-4'}}">
                                    <div class="info-box bg-green">
                                        <span class="info-box-icon"><i class="fa  fa-clock-o"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.clicks' ) }}
                                            </span>
                                            <span class="info-box-number">
                                                {{ $total->clicks }}
                                            </span>

                                            <div class="progress">
                                                <?php $progress = $total->requests ? ( round($total->clicks / $total->requests,2) * 100 ) : 0; ?>
                                                <div class="progress-bar" style="width: {{ $progress }}%"></div>
                                            </div>
                                                <span class="progress-description">
                                                </span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                @if( ! Request::has('camps'))
                                <div class="col-md-4">
                                    <div class="info-box bg-purple">
                                        <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.fill_rate' ) }}  
                                            </span>
                                            <span class="info-box-number">
                                                {{ $fillRate = $total->requests ? round($total->impressions / $total->requests, 2 ) * 100 : 0 }}%
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
                                @endif
                                <div class="{{ Request::has('camps') ? 'col-md-3' : 'col-md-4' }}">
                                    <div class="info-box bg-red">
                                        <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.ctr' ) }}
                                            </span>
                                            <span class="info-box-number">
                                                {{ $ctr = $total->impressions ? round($total->clicks / $total->impressions ,2) * 100 : 0 }}%
                                            </span>

                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ $ctr }}%"></div>
                                            </div>
                                            <span class="progress-description">
                                            </span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                <div class="{{ Request::has('camps') ? 'col-md-3' : 'col-md-4' }}">
                                    <div class="info-box bg-move">
                                        <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ Request::has('camps') ? trans( 'admin.spent_credits' ) : trans( 'admin.gained_credits' ) }}
                                            </span>
                                            <span class="info-box-number">
                                                {{ $credits = $total->credit ?: 0 }}$  
                                            </span>
                                            <?php $progress = $total->requests ? ( round($total->credit / $total->requests,2) * 100 ) : 0; ?>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{  $progress }}%"></div>
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
                        {{ Request::input('camps') ? trans('admin.there_is_no_camps') : trans('admin.there_is_no_apps') }}
                    @endif
                </div> 
            </div>
        </div>
    </section>
@stop

@section('script')
    <script type="text/javascript">
        $(function(){
            creditData = JSON.parse( '{!! json_encode($creditCharts) !!}' );

            var creditChart = new Highcharts.chart({
                chart: {
                   renderTo : 'creditchart-container',
                   type: 'spline' 
                },
                title : {
                    text : '{!! trans('admin.credit') !!}'
                },
                xAxis: {
                    type: 'datetime'
                },
                yAxis: { // Primary yAxis
                    labels: {
                        format: '{value}',
                        style: {
                            color: '#000'
                        }
                    },
                    title: {
                        text: '{{ trans( 'admin.actions' ) }}',
                        style: {
                            color: '#000'
                        }
                    },
                    min: 0
                },
                tooltip : {
                    crosshairs : true
                },
                plotOptions: {
                    spline: {
                        marker: {
                            radius: 3,
                            lineColor: '#666666',
                            lineWidth: 1
                        }
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'top',
                    borderWidth: 0,
                    y : 20
                },
                series: [{
                    name: '{{ trans( 'admin.credit' ) }}',
                    data: creditData.credit
                }]
            });
        });
    </script>
@stop