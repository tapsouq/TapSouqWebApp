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
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.requests' ) }}  
                                            </span>
                                            <span class="info-box-number">
                                                {{ number_format($total->requests, 0, ".", ",") }}
                                            </span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                @endif
                                <div class="{{ Request::input('camps') ? 'col-md-3' : 'col-md-4' }}">
                                    <div class="info-box bg-aqua">
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.impressions' ) }}  
                                            </span>
                                            <span class="info-box-number">
                                                {{ number_format($total->impressions, 0, ".", ",") }}
                                            </span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                <div class="{{ Request::has('camps') ? 'col-md-3' : 'col-md-4'}}">
                                    <div class="info-box bg-green">
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.clicks' ) }}
                                            </span>
                                            <span class="info-box-number">
                                                {{ number_format($total->clicks, 0, ".", ",") }}
                                            </span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                @if( ! Request::has('camps'))
                                <div class="col-md-4">
                                    <div class="info-box bg-purple">
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.fill_rate' ) }}  
                                            </span>
                                            <span class="info-box-number">
                                                {{ $total->requests ? round($total->impressions / $total->requests, 2 ) * 100 : 0 }}%
                                            </span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                @endif
                                <div class="{{ Request::has('camps') ? 'col-md-3' : 'col-md-4' }}">
                                    <div class="info-box bg-red">
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ trans( 'admin.ctr' ) }}
                                            </span>
                                            <span class="info-box-number">
                                                {{ $total->impressions ? number_format(($total->clicks * 100 / $total->impressions), 2) : 0 }}%
                                            </span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                <div class="{{ Request::has('camps') ? 'col-md-3' : 'col-md-4' }}">
                                    <div class="info-box bg-move">
                                        <div class="info-box-content">
                                            <span class="info-box-text">
                                                {{ Request::has('camps') ? trans( 'admin.spent_credits' ) : trans( 'admin.gained_credits' ) }}
                                            </span>
                                            <span class="info-box-number">
                                                {{ number_format($total->credit, 0, ".", ",") ?: 0 }}
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
            
            var tooltip = {
                    crosshairs : true,
                    useHTML : true,
                    formatter : function(){
                        var day = moment(this.point.x).format('dd, MMMM Do YYYY');
                        var html = "<div id='custom-tooltip'> " + day + "<br>";
                        html += "<span class='span-requests'><i class='fa fa-circle'></i>" + this.series.name + " : </span>" + number_format(this.point.y, 0, ".", ",") + "</div>"
                        
                        return html;
                    }
                };
            var tooltip = {
                    crosshairs : true,
                    useHTML : true,
                    formatter : function(){
                        var values = getOtherValues( this.point.x );
                        var day = moment(this.point.x).format('dd, MMMM Do YYYY');
                        var html = "<div id='custom-tooltip'> " + day + "<br>";
                        for( i=0; i< values.length; i++ ){
                            var name = (values[i][0]).replace(/\s/g, '').toLowerCase();
                            var metricName = values[i][0];
                            var metricVal  = values[i][1];
                            metricVal = number_format(metricVal, 0, ".", ",");

                            html += "<span class='span-" + name + "'><i class='fa fa-circle'></i>" + metricName + " : </span>" + metricVal + "<br>"
                        }
                        html += "</div>"
                        return html;
                    }
                };

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
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '{value}',
                        style: {
                            color: '#000'
                        }
                    },
                    title: {
                        text: '{{ trans( 'admin.netCredit' ) }}',
                        style: {
                            color: '#000'
                        }
                    },
                    min: 0
                },{ // secondary yAxis
                    labels: {
                        format: '{value}',
                        style: {
                            color: '#000'
                        }
                    },
                    title: {
                        style: {
                            color: '#000'
                        }
                    },
                    min: 0,
                    opposite: true
                }],
                tooltip : tooltip,
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
                    name: '{{ trans( 'admin.netCredit' ) }}',
                    data: creditData.netCredit
                },{
                    name: '{{ trans( 'admin.gainedCredit' ) }}',
                    data: creditData.gainedCredit,
                    yAxis: 1
                },{
                    name: '{{ trans( 'admin.spentCredit' ) }}',
                    data: creditData.spentCredit,
                    yAxis:1
                }]
            });

            function getOtherValues(x){
                var array = [];

                for (var i = 0; i < creditChart.series.length; i++) {
                    if(creditChart.series[i].visible){
                        var points = creditChart.series[i].points;
                        for (var j =0; j < points.length; j++) {
                            if( points[j].x == x){
                                var suffix = creditChart.series[i].tooltipOptions.valueSuffix
                                if( suffix == undefined ){
                                    suffix = "";
                                }
                                y = points[j].y + suffix;
                                break;
                            }
                        } 
                        array.push( [ creditChart.series[i].name, y ] );
                    }
                }
                return array;
            } 
        });
    </script>
@stop