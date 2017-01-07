@extends('admin.layout.layout')

@section('content')
	<section class="all-zones">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                </h3>
            </div>
            <div class="box-body">
	            <div class="table table-responsive">
	            	<div class="row">
	            		<div class="col-md-6 col-md-offset-3"> 
			            	<div class="filter-by-status"> 
			            		<form method="get" class="change-status filter-form form-horizontal mb10">
			            			@if( sizeof( $states = config('consts.ads_status') ) > 0 )
				            			<div class="form-group"> 
				            				<label class="col-md-4">
					            				{{ trans('admin.select_status') }}
				            				</label>
					            			<div class="col-md-8"> 
						            			<select class="form-control filter-input" name="s">
						            				@foreach( $states as $key => $value )
						            					<option value="{{ $key }}" {{ $key == $status ? 'selected' : ''  }}>
						            						{{ $value }}
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
                                    <th>{{ trans( 'admin.actions' ) }}</th>
		            			</tr>
		            		</thead>
		            		<tbody>
		            		<?php $formats = config('consts.all_formats'); ?>
		            		@foreach($items as $key => $ad)
		            			<tr>
		            				<td>
		            					<a href="{{ $ad->click_url }}" >
		            						<span >
				            					<img src="{{ url('public/uploads/ad-images/' . $ad->image_file) }}" >
		            						</span>
			            					{{ $ad->name }}
		            					</a>
		            				</td>
		            				<td>
		            					{{ $ad->campName }}
		            				</td>
		            				<td>
		            					{{ $ad->advertiser }}
		            				</td>
		            				<td>
		            					{{ $formats[$ad->format] }}
		            				</td>
		            				<td>
		            					{{ $ad->fcategory ? ( getCategories()[$ad->fcategory] . " ," ) : '' }} {{ $ad->scategory ? getCategories()[$ad->scategory] : ''}}
		            					{{ ( $ad->fcategory && $ad->scategory ) ? '' : trans('admin.all_cats') }}
		            				</td>
		            				<td>
		            					<button data-toggle="modal" data-target="#actions-modal" data-name="{{ $ad->adName }}" data-keywords="{{ getCampKeywords($ad->fatherId) }}" class="btn btn-sm btn-info show-keywords">{{ trans('admin.keywords') }}</button>
		            				</td>
		            				<td>
		            					<button data-toggle="modal" data-target="#actions-modal" data-id="{{ $ad->id }}" class="btn change-status {{ $ad->status == DELETED_AD ? 'btn-success unblock' : 'btn-danger block' }}" >
											{{ $ad->status == DELETED_AD ? trans('admin.unblock') : trans('admin.block') }}
		            					</button>
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
	<script type="text/javascript">
		$(function(){
			var $modal 	= $('#actions-modal');
            var $link 	= $modal.find('.modal-footer a');

			$('.show-keywords').on('click', function(){
				var modalTitle 	= $(this).attr('data-name') + " {{ trans('admin.keywords') }}";	
				var modalBody  	= $(this).attr('data-keywords');

				$link.addClass('hidden');
				$modal.find('.modal-footer button').removeClass('btn-outline').addClass('btn-default');

				$modal.removeClass('modal-success modal-danger').find('.modal-title').text(modalTitle);
				$modal.find('.modal-body p').text(modalBody);
			});
			$('.change-status').on('click', function(){
				var modalTitle, modalBody, action;
				var id = $(this).attr('data-id');
				var src = "{!! url( 'ads/change-status?token=' . csrf_token() . '&id=' ) !!}" + id; 

				$modal.find('.modal-footer button').addClass('btn-outline').removeClass('btn-default');

				if( $(this).hasClass('block') ){
					modalTitle 	= "{{ trans( 'admin.sure_delete' ) . ' ' . trans( 'admin.creative_ads' ) }}";
					modalBody 	= "{{ trans('admin.delete_ads') }}";
	                action 		= "{{ trans('admin.block') }}";

	                src = src + "&s=" + "{{DELETED_AD}}";

					$modal.addClass('modal-danger').removeClass('modal-success');
				}else{
					modalTitle 	= "{{ trans('admin.run_ads') }}";
					modalBody 	= "{{ trans('admin.are_u_sure_run_ads') }}";
	                action 		= "{{ trans('admin.unblock') }}";

	                src = src + "&s=" + "{{RUNNING_AD}}";

					$modal.addClass('modal-success').removeClass('modal-danger');
				}

				$modal.find('.modal-title').text(modalTitle);
				$modal.find('.modal-body p').text(modalBody);
                $link.removeClass('hidden').text(action).attr( 'href', src );
			});


		});
	</script>
@stop