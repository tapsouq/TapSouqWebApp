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
	            	<div class="row">
	            		<div class="col-md-6 col-md-offset-3"> 
			            	<div class="filter-by-country"> 
			            		<form method="get" class="change-country filter-form form-horizontal mb10">
			            			@if( sizeof( $countries ) > 0 )
				            			<div class="form-group"> 
				            				<label class="col-md-4">
					            				{{ trans('admin.select_country') }}
				            				</label>
					            			<div class="col-md-8"> 
						            			<select class="form-control filter-input" name="country">
						            				@foreach( $countries as $key => $country )
						            					<option value="{{ $country->id }}" {{ $country->id == $countryId ? 'selected' : ''  }}>
						            						{{ $country->name }}
						            					</option>
						            				@endforeach
						            			</select>
					            			</div>
				            			</div>
			            			@endif
			            		</form>
			            	</div>	
	            		</div>
	            	</div>
	            	@if( sizeof($items) > 0 )
	            		<table class="table table-bordered table-hover table-striped">
		            		<thead>
		            			<tr>                                    
		            				<th>{{ trans( 'lang.name' ) }}</th>
                                    <th>{{ trans( 'admin.campaign' ) }}</th>
                                    <th>{{ trans( 'admin.advertiser' ) }}</th>
                                    <th>{{ trans( 'admin.format' ) }}</th>
                                    <th>{{ trans( 'admin.categories' ) }}</th>
                                    <th>{{ trans( 'admin.keywords' ) }}</th>
                                    <th>{{ trans( 'admin.suitability' ) }}</th>
		            			</tr>
		            		</thead>
		            		<tbody>
		            		@foreach($items as $key => $ad)
		            			<tr>
		            				<td>
		            					<a href="{{ $ad->click_url }}" >
		            						<span class="app-icon">
				            					<img src="{{ url('public/uploads/ad-images/' . $ad->image_file) }}" >
		            						</span>
			            					{{ $ad->name }}
		            					</a>
		            				</td>
		            				<td>
		            					{{ $ad->campName }}
		            				</td>
		            				<td>
		            					{{ $ad->fname . " " . $ad->lname }}
		            				</td>
		            				<td>
		            					{{ config('consts.all_formats')[$ad->format] }}
		            				</td>
		            				<td>
		            					{{ $categories[$ad->fcategory] . " , " . $categories[$ad->scategory] }}
		            				</td>
		            				<td>
		            					<button data-toggle="modal" data-target="#show-keywords" data-name="{{ $ad->name }}" data-keywords="{{ getCampKeywords($ad->camp_id) }}" class="btn btn-sm btn-info show-keywords">{{ trans('admin.keywords') }}</button>
		            				</td>
		            				<td>
		            					{{ $ad->priority }}
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