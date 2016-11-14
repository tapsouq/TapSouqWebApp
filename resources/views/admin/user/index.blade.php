@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
	<section class="all-users-section">
		<div class="box box-info">
			<div class="box-header with-border">
				{{ $title }}
				<span class="pull-right">
					<div class="btn-toolbar">
						<a href="{{ url('user/all?adv=1') }}" class="btn btn-info {{ Request::input('adv') ? 'disabled' : '' }}">
							{{ trans( 'admin.advertisers' ) }}
						</a>
						<a  href="{{ url('user/all') }}" class="btn btn-success {{ Request::input('adv') ? '' : 'disabled' }}" >
							{{ trans('admin.publishers') }}
						</a>
					</div>
				</span>
			</div>
			<div class="box-body">
				@include('admin.partial.filterTimePeriod')
				<div class="table">
					@if( sizeof( $allUsers ) > 0 )
					<div id="chart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
					<table class="table table-bordred table-hover table-striped">
						<thead>
							<tr>
								<td>
									{{ trans( 'lang.name' ) }}
								</td>
								<td>{{ trans( 'admin.requests' ) }}</td>
								<td>{{ trans( 'admin.impressions' ) }}</td>
								<td>{{ trans( 'admin.clicks' ) }}</td>
								<td>{{ trans( 'admin.ctr' ) }}</td>
								<td>{{ trans( 'admin.convs' ) }}</td>
								<td>{{ trans( 'lang.status' ) }}</td>
								<td>{{ trans( 'lang.actions' ) }}</td>
							</tr>
						</thead>
						<tbody>
							<?php $css = [ PENDING_USER => 'label-info', ACTIVE_USER => 'label-success', SUSPEND_USER => 'label-warning' ] ?>
							<?php $ids = [];?>
							@if($tableItems)
								@foreach( $tableItems as $key => $item )
									<?php $ids[] = $item->id; ?>
									<tr>
										<td>
											<a href="{{ ( Request::input('adv') ? url('campaign/all/' . $item->id)  : url('app/all/' . $item->id) )}}">
												{{ $item->fname . " " . $item->lname }}
											</a>
										</td>
										<td>{{ $item->requests ?: 0 }}</td>
										<td>{{ $item->impressions ?: 0 }}</td>
										<td>{{ $item->clicks ?: 0 }}</td>
										<td>{{ ($item->requests != 0 ) ? round( $item->clicks / $item->requests, 2) * 100 : 0 }}%</td>
										<td>{{ ($item->clicks != 0 ) ? round( $item->installed / $item->clicks, 2) * 100 : 0 }}%</td>
										<td>
											<div class="label {{ $css[ $item->status ] }}">
												{{ config( 'consts.user_status' )[ $item->status ] }}
											</div>
										</td>
										<td>
											<div class="btn-group">
												<a href="{{ url('user/edit/' . $item->id ) }}" class="btn btn-sm btn-info">
													<i class="fa fa-edit"></i>
												</a>
												@if( $item->status != SUSPEND_USER )
												<a data-toggle="modal" data-target="#deactivate-user-modal" data-id="{{ $item->id }}" class="btn btn-sm btn-danger deactivate-user">
													<i class="fa fa-trash"></i>
												</a>
												@endif
											</div>
										</td>
									</tr>
								@endforeach
							@endif
							@foreach($allUsers as $_key => $_value)
								@if( ! in_array($_value->id, $ids) )
									<tr>
										<td>
											<a href="{{ ( Request::input('adv') ? url('campaign/all/' . $_value->id)  : url('app/all/' . $_value->id) )}}">
												{{ $_value->fname . " " . $_value->lname }}
											</a>
										</td>
										<td> 0 </td>
										<td> 0 </td>
										<td> 0 </td>
										<td>0%</td>
										<td>0%</td>
										<td>
											<div class="label {{ $css[ $_value->status ] }}">
												{{ config( 'consts.user_status' )[ $_value->status ] }}
											</div>
										</td>
										<td>
											<div class="btn-group">
												<a href="{{ url('user/edit/' . $_value->id ) }}" class="btn btn-sm btn-info">
													<i class="fa fa-edit"></i>
												</a>
												@if( $_value->status != SUSPEND_USER )
												<a data-toggle="modal" data-target="#deactivate-user-modal" data-id="{{ $_value->id }}" class="btn btn-sm btn-danger deactivate-user">
													<i class="fa fa-trash"></i>
												</a>
												@endif
											</div>
										</td>
									</tr>
								@endif
							@endforeach
						</tbody>
					</table>
					@else
						<p>
							{{ trans( 'admin.no_users_yet' ) }}
						</p>
					@endif
				</div>
			</div>
		</div>
		<div class="modal modal-danger" id="deactivate-user-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          	<div class="modal-dialog">
            	<div class="modal-content">
              		<div class="modal-header">
                		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  			<span aria-hidden="true">×</span>
                  		</button>
                		<h4 class="modal-title">{{ trans( 'admin.deactivate_user' ) }}</h4>
              		</div>
              		<div class="modal-body">
                		<p>{{ trans( 'admin.sure_deactivate_user' ) }}</p>
              		</div>
	              	<div class="modal-footer">
	                	<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">{{ trans( 'lang.close' ) }}</button>
	                	<a  class="btn btn-outline">
							{{ trans( 'lang.deactivate' ) }}
						</a>
	              	</div>
            	</div>
            	<!-- /.modal-content -->
          	</div>
          	<!-- /.modal-dialog -->
        </div>
	</section>
@stop

@section( 'script' )
	<script type="text/javascript">
		$(document).ready(function(){
			$('.deactivate-user').on('click', function(){
				var id = $(this).attr('data-id');
				var $link = $('#deactivate-user-modal .modal-footer a');
				var src = "{!! url( 'user/delete?token=' . csrf_token() . '&id=' ) !!}" + id;
				$link.attr( 'href', src );
			});
		});
	</script>
@stop