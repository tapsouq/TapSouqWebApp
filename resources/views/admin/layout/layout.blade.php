<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ isset($siteTitle) ? $siteTitle : '' }} | {{ isset($mTitle) ? $mTitle : '' }} | {{ isset($title) ? $title : '' }} </title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- daterange picker -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/daterangepicker/daterangepicker.css">
        <!-- bootstrap datepicker -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/datepicker/datepicker3.css">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/iCheck/all.css">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/select2/select2.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{ url('resources/assets') }}/dist/css/skins/_all-skins.min.css">
        <link rel="stylesheet" href="{{ url('resources/assets') }}/custom/css.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @yield( 'head' )
    </head>
    <body class="skin-blue sidebar-mini sidebar-collapse" >
    
        <div class="wrapper">
            <!-- Header section -->
            @include( 'admin.layout.header' )
            <!-- End Header section -->
            
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                @include( 'admin.layout.sidebar' )
            </aside>
            <!-- End sidebar -->

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        {{ isset($mTitle) ? $mTitle : trans( 'admin.dashboard' ) }}
                        <small>{{ isset($title) ? $title : trans( 'admin.dashboard' ) }}</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> {{ trans( 'admin.admin' ) }}</a></li>
                        <li><a href="#">{{ isset($mTitle) ? $mTitle : trans( 'admin.dashboard' ) }}</a></li>
                        <li class="active">{{ isset($title) ? $title : trans( 'admin.dashboard' ) }}</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <!-- Alert section -->
                    @include( 'admin.layout.alert' )
                    <!-- End alert section -->
                    @yield( 'content' )
                </section>
            </div>
        </div>
        <!-- jQuery 2.2.3 -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/jQuery/jquery-2.2.3.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="{{ url( 'resources/assets' ) }}/bootstrap/js/bootstrap.min.js"></script>
        <!-- date-range-picker -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
        <script src="{{ url( 'resources/assets' ) }}/plugins/daterangepicker/daterangepicker.js"></script>
        <!-- bootstrap datepicker -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/datepicker/bootstrap-datepicker.js"></script>
        <!-- Select2 -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/select2/select2.full.min.js"></script>
        <!-- iCheck 1.0.1 -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/iCheck/icheck.min.js"></script>
        <!-- FastClick -->
        <script src="{{ url( 'resources/assets' ) }}/plugins/fastclick/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="{{ url( 'resources/assets' ) }}/dist/js/app.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="{{ url( 'resources/assets' ) }}/dist/js/demo.js"></script>
        <script>
            function adaptRange(start, end){
                $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#daterange-btn').find('input[name=from]').val(start.format('YYYY-MM-DD'));
                $('#daterange-btn').find('input[name=to]').val(end.format('YYYY-MM-DD'));
            }
            $(function () {
                
                $('form button[type=submit], form input[type=submit], .submit-btns').not('.ajax-submit').on('click', function(){
                    var requiredFlag = false;
                    $requiredInputs = $('[required]');
                    $requiredInputs.each(function(index, formControl){
                        if( $(formControl).attr('type') == 'radio' ){
                            var checkedFlag = false;
                            var radioName = $(formControl).attr('name');
                            var $radioInputs =  $("[required][type=radio][name='" + radioName + "']");
                            $radioInputs.each(function(index, radioControl){
                                if( $(radioControl).prop('checked') == true ){
                                    checkedFlag = true;
                                }
                            });
                            if( ! checkedFlag ){
                                requiredFlag = true;
                                return;
                            }
                        }else{

                            if($(formControl).val() == ''){
                                requiredFlag = true;
                            }
                        }
                        console.log(requiredFlag);
                        console.log($(formControl).val());
                    }); 
                    if( ! requiredFlag ){
                        var loading = "{{ trans('lang.loading') }}  <i class='fa fa-spinner fa-pulse'></i>";
                        $(this).html( loading ).addClass('disabled').css('pointer-events', 'none');
                    }
                });
                
                //Initialize Select2 Elements
                $(".select2").select2();

                //iCheck for checkbox and radio inputs
                $('input[type="checkbox"].minimal-blue, input[type="radio"].minimal-blue').iCheck({
                    checkboxClass: 'icheckbox_minimal-blue',
                    radioClass: 'iradio_minimal-blue'
                });
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                });

                // Date range
                var startDate = moment().subtract(29, 'days');
                var endDate   = moment();
                @if( Request::has('to') &&  Request::has('from'))
                    startDate = moment("{{Request::input('from')}}");
                    endDate = moment("{{Request::input('to')}}");
                @elseif(Request::segment(1) == 'admin')
                    startDate = moment().subtract(6, 'days');
                @endif
                //Date range as a button
                $('#daterange-btn').daterangepicker(
                    {
                      ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                      },
                      startDate: startDate,
                      endDate: endDate
                    },
                    function (start, end) {
                        adaptRange(start, end);
                        $('form.time-period-form').trigger('submit');
                    }
                );
            });
        </script>
        @if( isset( $chartData ) )
            @if( sizeof($chartData ) > 0 )
                <script type="text/javascript" src="{{ url( 'resources/assets/plugins/highcharts/highcharts.js' ) }}"></script>
                <script type="text/javascript">
                    // Yellow, aqua, green, purple, red
                    Highcharts.setOptions().colors = [ "#f39c12", "#00c0ef", "#00a65a", "#605ca8", "#dd4b39", '#792e86', '#333' ];
                    data = JSON.parse( '{!! json_encode($chartData) !!}' );
                    var count = 0;
                    var chartOptions = {
                            renderTo : 'chart-container',
                            type: 'spline'
                        };
                    var chartTitle = {
                            text : "{!! $title !!}"
                        };
                    var xAxis = {
                            type: 'datetime'
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
                                    html += "<span class='span-" + name + "'><i class='fa fa-circle'></i>" + values[i][0] + " : </span>" + values[i][1] + "<br>"
                                }
                                html += "</div>"
                                return html;
                            }
                        };
                    var plotOptions = {
                            spline: {
                                marker: {
                                    radius: 3,
                                    lineColor: '#666666',
                                    lineWidth: 1
                                }
                            }
                        };
                    var legend = {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'top',
                            borderWidth: 0,
                            y : 20
                        };

                    var yAxes = [
                            { // Primary yAxis
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
                            { 
                                title: {
                                    text: '{{ trans( 'admin.ratio' ) }}',
                                    style: {
                                        color: '#000'
                                    }
                                },
                                labels: {
                                    format: '{value}%',
                                    style: {
                                        color: '#000'
                                    }
                                },
                                opposite: true,
                                min: 0
                            }
                            @if(isset($chartData['credit']))
                            ,{
                                title: {
                                    text: '{{ isset($chartData['requests']) ? trans( 'admin.gained_credits' ) : trans( 'admin.spent_credits' ) }}',
                                    style: {
                                        color: Highcharts.getOptions().colors[5]
                                    }
                                },
                                labels: {
                                    format: '{value}',
                                    style: {
                                        color: Highcharts.getOptions().colors[5]
                                    }
                                },
                                opposite: true,
                                min: 0
                            }
                            @endif
                        ];
                    var series = [
                            @if( isset($chartData['requests']) )
                            {
                                name: '{{ trans( 'admin.requests' ) }}',
                                yAxis:0,
                                data: data.requests,
                                color : Highcharts.getOptions().colors[0]
                            },
                            @endif
                            {
                                name: '{{ trans( 'admin.impressions' ) }}',
                                yAxis:0,
                                data: data.impressions,
                                color: Highcharts.getOptions().colors[1]
                            }, {
                                name: '{{ trans( 'admin.clicks' ) }}',
                                yAxis:0,
                                data: data.clicks,
                                color: Highcharts.getOptions().colors[2]
                            },
                            @if( isset($chartData['fill_rate']) )
                            {
                                name: '{{ trans( 'admin.fill_rate' ) }}',
                                yAxis:1,
                                data: data.fill_rate,
                                visible: false,
                                tooltip: {
                                    valueSuffix: '%'
                                },
                                color: Highcharts.getOptions().colors[3]
                            },
                            @endif
                            {
                                name: '{{ trans( 'admin.ctr' ) }}',
                                yAxis: 1,
                                data: data.ctr,
                                visible: false,
                                tooltip: {
                                    valueSuffix: '%'
                                },
                                color: Highcharts.getOptions().colors[4]
                            }
                            @if(isset($chartData['credit']))
                            ,{
                                name: '{{ isset($chartData['requests']) ? trans( 'admin.gained_credits' ) : trans( 'admin.spent_credits' ) }}',
                                yAxis: 2,
                                data: data.credit,
                                color: Highcharts.getOptions().colors[5]
                            }
                            @endif
                            @if(isset($chartData['adminCredit']))
                            ,{
                                name: '{{  trans( 'admin.admin_credits' ) }}',
                                yAxis: 2,
                                data: data.adminCredit,
                                color: Highcharts.getOptions().colors[6]
                            }
                            @endif
                        ];
                     var chart = new Highcharts.chart({
                        chart: chartOptions,
                        title : chartTitle,
                        xAxis: xAxis,
                        yAxis: yAxes,
                        tooltip: tooltip,
                        plotOptions: plotOptions,
                        legend: legend,
                        series: series
                    });
                    function getOtherValues(x){
                        var array = [];

                        for (var i = 0; i < chart.series.length; i++) {
                            if(chart.series[i].visible){
                                var points = chart.series[i].points;
                                for (var j =0; j < points.length; j++) {
                                    if( points[j].x == x){
                                        var suffix = chart.series[i].tooltipOptions.valueSuffix
                                        if( suffix == undefined ){
                                            suffix = "";
                                        }
                                        y = points[j].y + suffix;
                                        break;
                                    }
                                } 
                                array.push( [ chart.series[i].name, y ] );
                            }
                        }
                        return array;
                    } 
                </script>
            @endif
        @endif
        @if( Request::has('to') && Request::has('from') )
            <script type="text/javascript">
                adaptRange( moment("{{Request::input('from') }}"), moment("{{Request::input('to') }}") );
            </script>
        @elseif( Request::segment(1) == 'admin' )
            <script type="text/javascript">
                adaptRange( moment().subtract(6, 'days'), moment() );
            </script>
        @endif
        @yield( 'script' )
    </body>
</html>