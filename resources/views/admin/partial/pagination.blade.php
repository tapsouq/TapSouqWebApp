<div class="table-footer">
	<div class="pull-left">
		<form class="per-page-form form-inline">
			<label>
				{{ trans('admin.per_page') }}
			</label>
			@if( sizeof( $pageSizes = config('consts.page_sizes') ) > 0 )
			<select class="form-control" name="per-page">
				@foreach( $pageSizes as $pageSize )
					<option value="{{ $pageSize }}" {{ Request::has('per-page') ? (Request::input('per-page') == $pageSize ? 'selected' : '' ): '' }} >{{ $pageSize }}</option>
				@endforeach
			</select>
			@endif
		</form>
	</div>
	@if( sizeof($items) )
	<div class="pull-right">
		{!! $items->appends(Request::query())->render() !!}
	</div>	
	@endif
</div>