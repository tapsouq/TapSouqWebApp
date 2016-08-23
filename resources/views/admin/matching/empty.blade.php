@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="empty-keywords">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ $title }}
                </h3>
            </div>
            <div class="box-body">
                <div class="table">
                    @if( sizeof( $keywords ) )
                        <table class="tabel table-hover table-striped tabel-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans( 'admin.name' ) }}</th>
                                    <th>{{ trans( 'lang.actions' ) }}</th>
                                </tr>
                            </thead>       
                        </table>
                    @else
                        <p>
                            {{ trans( 'admin.no_empty_keywords' ) }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>      
@stop

@section( 'script' )

@stop