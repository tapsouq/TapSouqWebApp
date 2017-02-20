@extends('admin.layout.layout')

@section('content')
	<div class="container-fluid">
		<div class="box box-info">
		    <div class="box-header with-border">
		        <h3 class="box-title">
		            {{ $title }}
		        </h3>
		        <div class="pull-right">
		            <a class="btn btn-info btn-sm" href="{{ url( 'simi-cats/edit' ) }}">
		                <i class="fa fa-edit"></i>
		                {{ trans( 'admin.editSimiCats' ) }}
		            </a>
		        </div>
		    </div>
		    <div class="box-body">
		        <div class="table">
		            @if( sizeof( $similarCats ) > 0 )
		                <table class="table table-hover table-responsive table-striped table-bordered">
		                	<thead>
		                		<tr>
		                			<th>#</th>
		                			<th>{{ trans('admin.category') }}</th>
		                			<th>{{ trans('admin.similarity') }}</th>
		                		</tr>
		                	</thead>
		                	<tbody>
		                		@foreach( $similarCats as $key => $value )
		                			<tr>
		                				<td>{{ $key + 1 }}</td>
		                				<td>{{ $value->name }}</td>
		                				<td>{{ getCatNames($value->simi_cats) }}</td>
		                			</tr>
		                		@endforeach
		                	</tbody>
		                </table>
		            @else
		            	<p>
		            		{{ trans('admin.no_cat_similarity') }}
		            	</p>
		            @endif
		        </div>
		    </div>
		</div>
	</div>
@stop