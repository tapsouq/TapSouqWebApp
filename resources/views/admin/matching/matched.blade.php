@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="matched-keywords">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                </h3>
            </div>
            <div class="box-body">
                @if( sizeof($keywords) > 0 )
                    <div class="table">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans( 'admin.application' ) }}</th>
                                    <th>{{ trans( 'admin.keyword' ) }}</th>
                                    <th>{{ trans( 'admin.priority' ) }}</th>
                                    <th>{{ trans( 'admin.app_desc' ) }}</th>
                                    <th>{{ trans( 'lang.actions' ) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($keywords as $key => $keyword)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <a href="{{ url('app/edit/' . $keyword->app_id ) }}">
                                                {{ $keyword->title }}
                                            </a>
                                        </td>
                                        <td>{{ $keyword->keyword }}</td>
                                        <td>
                                            {{ $keyword->priority }}
                                        </td>
                                        <td>
                                            <a data-toggle="modal" data-target="#keywords-modal" title="{{ trans('admin.show_desc') }}" class="btn btn-sm btn-primary view-description">
                                                <i class="fa fa-search"></i>
                                                <input type="hidden" value="{{ $keyword->description }}" />
                                            </a>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a data-toggle="modal" data-target="#keywords-modal" data-href="{!! url('change-priority?s=p&token=' . csrf_token() . '&keyword_id=' . $keyword->keyword_id . '&app_id=' . $keyword->app_id . '&priority=' . $keyword->priority ) !!}" title="{{ trans('admin.priority++') }}" class="change-priority plus btn btn-sm btn-success">
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                                @if( $keyword->priority > 1 )
                                                <a data-toggle="modal" data-target="#keywords-modal" data-href="{!! url('change-priority?s=m&token=' . csrf_token() . '&keyword_id=' . $keyword->keyword_id . '&app_id=' . $keyword->app_id . '&priority=' . $keyword->priority ) !!}" title="{{ trans('admin.priority--') }}" class="change-priority minus btn btn-sm btn-warning">
                                                    <i class="fa fa-minus"></i>
                                                </a>
                                                @endif
                                                <a data-toggle="modal" data-target="#keywords-modal" data-keyword="{{ $keyword->keyword_id }}" data-app="{{ $keyword->app_id }}" class="delete-match btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $keywords->render() !!}
                    </div>
                @else
                    <p>
                        {{ trans( 'admin.no_keywords' ) }}
                    </p>
                @endif
            </div>
        </div>

        <div class="modal modal-danger" id="keywords-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">{{ trans( 'lang.delete' ) }} {{ trans( 'admin.application' ) }} </h4>
                    </div>
                    <div class="modal-body">
                        <p> </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">{{ trans( 'lang.close' ) }}</button>
                        <a  class="btn btn-outline action"></a>
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
            $('.delete-match').on('click', function(){
                var keyword_id  = $(this).attr('data-keyword');
                var app_id      = $(this).attr('data-app');
                $('#keywords-modal').removeClass('modal-warning modal-success')
                                        .addClass('modal-danger')
                                        .find('.modal-body p')
                                        .text("{{ trans( 'admin.sure_delete' ) . ' ' . trans( 'admin.matched_keyword' ) }}")
                                        .parents('.modal')
                                        .find('.modal-footer .action').show()
                                        .text("{{ trans( 'lang.delete' ) }}")
                                        .parents('.modal')
                                        .find('.modal-header .modal-title')
                                        .text( "{{ trans('admin.delete_matched_keyword') }}" );
                var $link = $('#keywords-modal .modal-footer a');
                var src = "{!! url( 'delete-matching?&token=' . csrf_token() . '&keyword_id=' ) !!}" + keyword_id + "&app_id=" + app_id;
                $link.attr( 'href', src );
            });

            $('.change-priority').on('click', function(){
                var href = $(this).attr('data-href');
                if( $(this).hasClass('plus')){
                    $('#keywords-modal').removeClass('modal-danger modal-primary modal-warning')
                                            .addClass('modal-success')
                                            .find('.modal-body p')
                                            .text("{{ trans('admin.are_u_sure_increase') }}")
                                            .parents('.modal')
                                            .find('.modal-footer .action').show()
                                            .text("{{ trans( 'lang.increase' ) }}")
                                            .parents('.modal')
                                            .find('.modal-header .modal-title')
                                            .text( "{{ trans('admin.increase_priority') }}" );
                }else{
                    $('#keywords-modal').removeClass('modal-success modal-danger modal-primary')
                                            .addClass('modal-warning')
                                            .find('.modal-body p')
                                            .text("{{ trans('admin.are_u_sure_decrease') }}")
                                            .parents('.modal')
                                            .find('.modal-footer .action').show()
                                            .text("{{ trans( 'lang.decrease' ) }}")
                                            .parents('.modal')
                                            .find('.modal-header .modal-title')
                                            .text( "{{ trans('admin.decrease_priority') }}" );
                }
                var $link = $('#keywords-modal .modal-footer a');
                $link.attr( 'href', href );
            });

            $('.view-description').on('click', function(){
                var desc = $(this).find('input[type=hidden]').val();
                $('#keywords-modal').removeClass('modal-danger modal-warning modal-success')
                                    .addClass('modal-primary')
                                    .find('.modal-body p')
                                    .text(desc)
                                    .parents('.modal')
                                    .find('.modal-footer .action') .hide()
                                    .parents('.modal')
                                    .find('.modal-header .modal-title')
                                    .text( "{{ trans('admin.app_desc') }}" );                   
            });
        });
    </script>
@stop