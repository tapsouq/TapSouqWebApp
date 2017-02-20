@extends('admin.layout.layout')

@section('content')
	<div class="container-fluid">
		<div class="box box-info">
		    <div class="box-header with-border">
		        <h3 class="box-title">
		            {{ $title }}
		        </h3>
		    </div>
		    <div class="box-body">
		    	<form class="form-horizontal" action="{{ url('save-simi-cats') }}" method="post">
		    		<div class="form">
		    			<div class="form-body">
		    				{{ csrf_field() }}
		    				@foreach( $similarCats as $value )
		    					<?php $simiCats = explode(',', $value->simi_cats); ?>
			    				<div class="form-group">
			    					<div class="col-md-4">
			    						{{ $value->name }}
			    					</div>
			    					<div class="col-md-8">
			    						<select class="form-control select-simi-cats" name="cat_{{$value->id}}[]" multiple="">
			    							@foreach($similarCats as $_value)
			    								@if( $_value->id != $value->id )
				    								<option value="{{ $_value->id }}" {{ in_array($_value->id, $simiCats) ? 'selected' : '' }}>
				    									{{ $_value->name }}
				    								</option>
			    								@endif
			    							@endforeach
			    						</select>
			    					</div>
			    				</div>
			    			@endforeach
		    			</div>
		    			<div class="form-actions">
		    				<button class="btn btn-success pull-right" type="submit">{{ trans("lang.save") }}</button>
		    			</div>
		    		</div>
		    	</form>
		    </div>
		</div>
	</div>
@stop

@section('script')
	<script type="text/javascript">
		$(document).ready(function(){
			$('.select-simi-cats').select2({
				placeholder: "Select Similar Categories"
			});
		});
	</script>
@stop