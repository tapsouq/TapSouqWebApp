@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
	<section class="all-users-section">
		<div class="box box-info">
			<div class="box-header with-border">
				{{ $title }}
			</div>
			<div class="box-body">
				<div class="table">
					@if( sizeof( $users ) > 0 )
					<table class="table table-bordred table-hover table-striped">
						<thead>
							<tr>
								<td>{{ trans( 'lang.name' ) }}</td>
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
							@foreach( $users as $key => $_user )
								<tr>
									<td>{{ $_user->fname . " " . $_user->lname }}</td>
									<td>{{ $_user->email }}</td>
									<td>{{ $_user->company }}</td>
									<td>{{ $_user->country_name }}</td>
									<td>{{ $_user->city }}</td>
									<td>
										<div class="label {{ $css[ $_user->status ] }}">
											{{ config( 'consts.user_status' )[ $_user->status ] }}
										</div>
									</td>
									<td>
										<div class="btn-group">
											<a href="{{ url('user/edit/' . $_user->id ) }}" class="btn btn-sm btn-info">
												<i class="fa fa-edit"></i>
											</a>
											@if( $_user->status != SUSPEND_USER )
											<a data-toggle="modal" data-target="#deactivate-user-modal" data-id="{{ $_user->id }}" class="btn btn-sm btn-danger deactivate-user">
												<i class="fa fa-trash"></i>
											</a>
											@endif
										</div>
									</td>
								</tr>
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