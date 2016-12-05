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
	            <div class="table">
	            	@if( sizeof($relevantAds) > 0 )
	            		<table class="table table-hover table-striped">
		            		<thead>
		            			<tr>                                    
		            				<th>{{ trans( 'lang.name' ) }}</th>
                                    <th>{{ trans( 'admin.categories' ) }}</th>
                                    <th>{{ trans( 'admin.view_keywords' ) }}</th>
                                    <th>{{ trans( 'admin.view_countries' ) }}</th>
		            			</tr>
		            		</thead>
		            		<tbody>
		            		@foreach($relevantAds as $key => $ad)
		            			<tr>
		            				<td>
		            					{{ $ad->name }}</td>
		            				<td></td>
		            				<td>
		            					<a href="" class="btn btn-sm btn-info">{{ trans('admin.keywords') }}</a>
		            				</td>
		            				<td>
		            					<a href="" class="btn btn-sm btn-warning">{{ trans('admin.countries') }}</a>
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
    </section>
@stop
