<script>

    // Function to adapt the daterange plugin inputs dates
    function adaptRange(start, end){
        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#daterange-btn').find('input[name=from]').val(start.format('YYYY-MM-DD'));
        $('#daterange-btn').find('input[name=to]').val(end.format('YYYY-MM-DD'));
    }

    // Function to format numbers, to showing comma after thousands
    function number_format(number, decimals, dec_point, thousands_sep) {
        var n = !isFinite(+number) ? 0 : +number, 
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            toFixedFix = function (n, prec) {
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                var k = Math.pow(10, prec);
                return Math.round(n * k) / k;
            },
            s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
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
        var startDate = moment().subtract(6, 'days');
        var endDate   = moment();
        @if( Request::has('to') &&  Request::has('from'))
            startDate = moment("{{Request::input('from')}}");
            endDate = moment("{{Request::input('to')}}");
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
    
    Highcharts.setOptions().colors = [ "#f39c12", "#00c0ef", "#00a65a", "#605ca8", "#dd4b39", '#792e86', '#333' ];
</script>
@if( isset( $chartData ) )
    @if( sizeof($chartData ) > 0 )
        <script type="text/javascript">
            // Yellow, aqua, green, purple, red
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
                            var metricName = values[i][0];
                            var metricVal  = values[i][1];
                            if( name == 'ctr' ){
                                metricVal =  parseFloat(metricVal).toFixed(2) + '%';
                            }else if( name != "fillrate" ){
                                metricVal = number_format(metricVal, 0, ".", ",");
                            }
                            html += "<span class='span-" + name + "'><i class='fa fa-circle'></i>" + metricName + " : </span>" + metricVal + "<br>"
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
@endif

<script type="text/javascript">
    // To trigger form submitting on change select
    function triggerFormSubmission($formObject){
        var urlQuery = JSON.parse('{!! json_encode(Request::query()) !!}');
        for (inputName in urlQuery){
            var inputVal = urlQuery[inputName];
            if( $formObject.find("[name='" + inputName + "']").length == 0 ){
                $formObject.append("<input type='hidden' name='" + inputName + "' value='" + inputVal + "' />");
            }
        }
        $formObject.trigger('submit');
    }

    $('select[name=per-page]').on('change', function(){
        triggerFormSubmission($('form.per-page-form'));
    });

    $('.filter-input').on('change', function(){
        triggerFormSubmission($('.filter-form'));
    });
</script>