@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="all-apps">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                </h3>
            </div>
            <div class="box-body">
                <div class="table">
                    @if( sizeof( $apps ) > 0 )
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans( 'lang.name' ) }}</th>
                                    <th>{{ trans( 'admin.platform' ) }}</th>
                                    <th>{{ trans( 'admin.package_id' ) }}</th>
                                    @if( Auth::user()->role == ADMIN_PRIV ) <!-- role = 1 => admin priveleg -->
                                        <th>{{ trans( 'admin.developer' ) }}</th>
                                    @endif
                                    <th>{{ trans( 'lang.status' ) }}</th>
                                    <th>{{ trans( 'lang.actions' ) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $css = [ PENDING_APP => 'label-info', ACTIVE_APP => 'label-success', DELETED_APP => 'label-warning' ]; ?>
                                @foreach( $apps as $key => $value )
                                    <tr>
                                        <td>
                                            {{ $value->id }}
                                        </td>
                                        <td>
                                            <a href="{{ url( 'zone/all#app_' . $value->id ) }}" title="{{ trans('admin.show_app_ads') }}" >
                                                {{ $value->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ config('consts.app_platforms')[$value->platform] }}
                                        </td>
                                        <td>{{ $value->package_id }}</td>
                                        @if( Auth::user()->role == ADMIN_PRIV )
                                            <td>{{ $value->fname ." " . $value->lname }}</td>
                                        @endif
                                        <td>
                                            <div class="label {{ $css[$value->status] }}">
                                                {{ config('consts.app_status')[$value->status] }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ url('app/edit/' . $value->id ) }}" class="btn btn-sm btn-info">
                                                    {{ trans( 'lang.edit' ) }}
                                                </a>
                                                @if( $value->status != DELETED_APP )
                                                <a data-toggle="modal" data-target="#deactivate-app-modal" data-id="{{ $value->id }}" class="btn btn-sm btn-danger deactivate-app">
                                                    {{ trans( 'lang.delete' ) }}
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
                            {{ trans( 'admin.no_apps' ) }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal modal-danger" id="deactivate-app-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">{{ trans( 'lang.delete' ) }} {{ trans( 'admin.application' ) }} </h4>
                    </div>
                    <div class="modal-body">
                        <p>{{ trans( 'admin.sure_delete' ) }} {{ trans( 'admin.application' ) }} </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">{{ trans( 'lang.close' ) }}</button>
                        <a  class="btn btn-outline">
                            {{ trans( 'lang.delete' ) }}
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
            $('.deactivate-app').on('click', function(){
                var id = $(this).attr('data-id');
                var $link = $('#deactivate-app-modal .modal-footer a');
                var src = "{!! url( 'delete-app?token=' . csrf_token() . '&id=' ) !!}" + id;
                $link.attr( 'href', src );
            });
        });
    </script>
@stop