@extends('admin.layout.layout')

@section('head')
	<link href="{{ url('resources/assets/plugins/jqvmap') }}/jqvmap.css" rel="stylesheet" type="text/css"/>
	<!-- DataTables -->
	<link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/datatables/dataTables.bootstrap.css">
@stop

@section('content')
	<section class="all-zones">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                   {{ $title }}
                </h3>
            </div>
            <div class="box-body">
            	@include('admin.partial.filterTimePeriod')
            	<div class="nav-tabs-custom">
	        		<ul class="nav nav-tabs">
	        			<?php $otherQueries = http_build_query(Request::except('t')); ?>
			            <li class="active">
			            	<a data-toggle="tab" href="#all-devices">
			            		{{ trans('admin.all_devices') }}
			            	</a>
			            </li>
			            <li class="">
			            	<a data-toggle="tab" href="#new-devices">
			            		{{ trans('admin.new_devices') }}
			            	</a>
			            </li>
			            <li class="">
			            	<a data-toggle="tab" href="#active-devices">
			            		{{ trans('admin.active_devices') }}
			            	</a>
			            </li>
			        </ul>
			        <div class="tab-content">
			        	<div id="all-devices" class="tab-pane active">
			            	@if( sizeof( $items->allDevicesCount ) > 0 )
				            	<div id="allChart" ></div>
				            @else
				            	<p>
				            		{{ trans('admin.there_is_no_records') }}
				            	</p>
			            	@endif
			        	</div>
			        	<div id="new-devices" class="tab-pane">
			        		@if( sizeof( $items->newDevicesCount ) > 0 )
				            	<div id="newChart" ></div>
			            	@else
			            		<p>
				            		{{ trans('admin.there_is_no_records') }}
				            	</p>
			        		@endif
			        	</div>
			        	<div id="active-devices" class="tab-pane">
			            	@if( sizeof( $items->activeDevicesCount ) > 0 )
				            	<div id="activeChart" ></div>
			            	@else
			            		<p>
				            		{{ trans('admin.there_is_no_records') }}
				            	</p>
			        		@endif
			        	</div>
			        </div>
            	</div>
	            <div class="table table-responsive">
	            	@if( sizeof($items->allDevices) > 0 )
	            		<table id="countryTable" class="table table-bordered table-hover table-striped">
		            		<thead>
		            			<tr> 
		            				<th>Report Title</th>
                                    <th>{{ trans( 'admin.all_devices' ) }}</th>
                                    <th>{{ trans( 'admin.new_devices' ) }}</th>
                                    <th>{{ trans( 'admin.active_devices' ) }}</th>
		            			</tr>
		            		</thead>
		            		<tbody>
		            		@foreach($items->allDevices as $key => $value)
		            			<tr>
		            				<td>
		            					{{ $value->name }}
		            				</td>
		            				<td>
		            					{{ $value->all }}
		            				</td>
		            				<td>
		            					{{ $value->new }}
		            				</td>
		            				<td>
		            					{{ $value->active }}
		            				</td>
		            			</tr>
		            		@endforeach
		            		</tbody>
	            		</table>
	            	@else
	            		<p>
		            		{{ trans('admin.there_is_no_relevant') }}
	            		</p>
	            	@endif
	            </div>
            </div>
        </div>
    	<div class="modal" id="actions-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans( 'lang.close' ) }}</button>
                    	<a class="btn btn-outline pull-right action"></a>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </section>
@stop

@section('script')
   	
   	<!-- DataTables -->
	<script src="{{ url('resources/assets/plugins') }}/datatables/jquery.dataTables.min.js"></script>
	<script src="{{ url('resources/assets/plugins') }}/datatables/dataTables.bootstrap.min.js"></script>

	@include('admin.partial.createPie')

	<script type="text/javascript">
		$(function(){
			// Data table
			$('#countryTable').DataTable();
		});
	</script>
@stop