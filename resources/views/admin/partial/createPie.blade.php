<script type="text/javascript">
    $(function () {
        allData = <?= json_encode($items->allDevicesCount) ?>;
        newData = <?= json_encode($items->newDevicesCount) ?>;
        activeData = <?= json_encode($items->activeDevicesCount) ?>;
        
        function pieOptions( data ){
            this.chart = {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            };
            this.title = {
                text: '{{ $title }}'
            };
            this.tooltip = {
                pointFormat: '{series.name}: <b>{point.percentage:.3f}%</b>'
            };
            this.plotOptions = {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    turboThreshold: 5000,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: ({point.y}) - {point.percentage:.3f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            };

            this.series = [{
                name : 'devices',
                colorByPoint : true,
                data : data
            }];
        }

        if( allData.length > 0 ){
            var allPieOptions = new pieOptions(allData);
            Highcharts.chart('allChart', allPieOptions);
        }
        
        if( newData.length > 0 ){
            var newPieOptions = new pieOptions(newData);
            Highcharts.chart('newChart', newPieOptions);
        }

        if( activeData.length ){
            var activePieOptions = new pieOptions( activeData );
            Highcharts.chart('activeChart', activePieOptions);
        }
    });
</script>