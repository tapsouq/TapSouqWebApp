	<!-- Vmap -->
	<script type="text/javascript">
        var allCount  	= JSON.parse('{!! json_encode($items->allDevicesCount) !!}');
        var newCount  	= JSON.parse('{!! json_encode($items->newDevicesCount) !!}');
        var activeCount = JSON.parse('{!! json_encode($items->activeDevicesCount) !!}');
	</script>
	<script type="text/javascript" src="{{ url('resources/assets/plugins/jqvmap') }}/jquery.vmap.js"></script>
	<script type="text/javascript" src="{{ url('resources/assets/plugins/jqvmap') }}/values.js"></script>
   	<script type="text/javascript">
        var options = {
                backgroundColor: '#333333',
                color: '#ffffff',
                hoverOpacity: 0.7,
                selectedColor: '#666666',
                enableZoom: true,
                showTooltip: true,
                scaleColors: ["#C8EEFF", "#006491"],
                normalizeFunction: 'polynomial',
            };    
    </script>

    @if(  Request::input('t') == 'new' )
       	<script type="text/javascript" src="{{ url('resources/assets/plugins/jqvmap') }}/maps/jquery.vmap.new_devices.js" charset="utf-8"></script>
        <script type="text/javascript">
            options.map =  'new_devices';
            options.values = newCount;          
        </script>
    @elseif( Request::input('t') == 'active' )
       	<script type="text/javascript" src="{{ url('resources/assets/plugins/jqvmap') }}/maps/jquery.vmap.active_devices.js" charset="utf-8"></script>
        <script type="text/javascript">
            options.map =  'active_devices';
            options.values = activeCount;          
        </script>
    @else
       	<script type="text/javascript" src="{{ url('resources/assets/plugins/jqvmap') }}/maps/jquery.vmap.all_devices.js" charset="utf-8"></script>
        <script type="text/javascript">
            options.map =  'all_devices';
            options.values = allCount;
        </script>
    @endif

    <script type="text/javascript">
        $('#DevicesMap').vectorMap(options);
    </script>

