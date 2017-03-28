@extends('admin.layout.layout')

@section('content')
	<section class="all-zones">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ isset( $zone ) ? $zone->name . " > " : '' }} {{ $title }}
                </h3>
            </div>
            <div class="box-body">
	            <div class="table table-responsive">
	            	@include('admin.partial.filterTimePeriod')
	            	@if( sizeof($items) > 0 )
	            		<table class="table table-bordered table-hover table-striped">
		            		<thead>
		            			<tr>                                    
		            				<th>{{ trans( 'lang.name' ) }}</th>
                                    <th>{{ trans( 'admin.campaign' ) }}</th>
                                    <th>{{ trans( 'admin.advertiser' ) }}</th>
                                    <th>{{ trans( 'admin.format' ) }}</th>
                                    <th>{{ trans( 'admin.categories' ) }}</th>
                                    
                                    <th>{{ trans( 'admin.impressions' ) }}</th>
                                    <th>{{ trans( 'admin.clicks' ) }}</th>
                                    <th>{{ trans( 'admin.ctr' ) }}</th>
                                    
                                    <th>{{ trans( 'admin.keywords' ) }}</th>
                                    <th>{{ trans( 'admin.suitability' ) }}</th>
		            			</tr>
		            		</thead>
		            		<tbody>
		            		@foreach($items as $key => $ad)
		            			<?php $ad = (object) $ad; ?>
		            			<tr>
		            				<td>
		            					<a href="{{ $ad->adCreativeLink }}" >
		            						<span >
				            					<img src="{{ url('public/uploads/ad-images/' . $ad->adCreativeImage) }}" >
		            						</span>
			            					{{ $ad->adName }}
		            					</a>
		            				</td>
		            				<td>
		            					{{ $ad->fatherName }}
		            				</td>
		            				<td>
		            					{{ $ad->accountName }}
		            				</td>
		            				<td>
		            					{{ $ad->format }}
		            				</td>
		            				<td>
		            					{{ $ad->fcategory . " , " . $ad->scategory }}
		            				</td>
		            				<td>
		            					{{ $ad->impressions }}
		            				</td>
		            				<td>
		            					{{ $ad->clicks }}
		            				</td>
		            				<td>
		            					{{ $ad->impressions ? number_format( ( $ad->clicks / $ad->impressions * 100 ), 2) : 0  }}%
		            				</td>
		            				<td>
		            					<button data-toggle="modal" data-target="#show-keywords" data-name="{{ $ad->adName }}" data-keywords="{{ getCampKeywords($ad->fatherId) }}" class="btn btn-sm btn-info show-keywords">{{ trans('admin.keywords') }}</button>
		            				</td>
		            			</tr>
		            		@endforeach
		            		</tbody>
	            		</table>
	            	@else
	            		<p>
		            		{{ trans('admin.there_is_no_shown') }}
	            		</p>
	            	@endif
	            </div>
	            <!-- pagination -->
	            @include('admin.partial.pagination')	
            </div>
        </div>
    	<div class="modal" id="show-keywords" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </section>
@stop

@section('script')
	<script type="text/javascript">
		$(function(){
			$('.show-keywords').on('click', function(){
				var $modal 		= $('#show-keywords');
				var modalTitle 	= $(this).attr('data-name') + " {{ trans('admin.keywords') }}";	
				var modalBody  	= $(this).attr('data-keywords');

				$modal.find('.modal-title').text(modalTitle);
				$modal.find('.modal-body p').text(modalBody);
			});

		});
	</script>
@stop