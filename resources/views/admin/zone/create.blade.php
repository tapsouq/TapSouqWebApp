@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="create-zone">
        <div class="form">
            <form role="form" method="post" action="{{ isset($zone) ? url('save-zone') : url('store-zone') }}">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            {{ $title }}
                        </h3>
                    </div>
                    @if( isset($app_id ) )
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'name' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.name' ) }}
                                            {!! csrf_field() !!}
                                                <input type="hidden" name="application" value="{{ $app_id }}">
                                            @if( isset($zone) )
                                                <input type="hidden" name="id" value="{{ $zone->id }}" >
                                            @endif
                                        </label>
                                        <input type="text" class="form-control" name="name" value="{{ isset($zone) ? $zone->name : old('name') }}" required>
                                        <span class="help-block">
                                            {{ $errors->has( 'name' ) ? $errors->first( 'name' ) : '' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'device_type' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.device_type' ) }}
                                        </label>
                                        @if( sizeof( $devices = config( 'consts.zone_devices' ) ) > 0 )
                                            <select name="device_type" class="form-control" required>
                                                @foreach( $devices as $key => $value )
                                                    <option value="{{ $key }}" {{ isset($zone) ? ( $zone->device_type == $key ? 'selected' : '' ) : ( old('device_type') == $key ? 'selected' :'' ) }} >
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'device_type' ) ? $errors->first( 'device_type' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'format' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.format' ) }}
                                        </label>
                                        @if( sizeof( $formats = config( 'consts.zone_formats' ) ) > 0 )
                                            <select name="format" class="form-control">
                                                @foreach( $formats as $key => $value )
                                                    <option value="{{ $key }}" {{ isset($zone) ? ( $zone->format == $key ? 'selected' : '' ) : ( old('format') == $key ? 'selected' :'' ) }} >
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'format' ) ? $errors->first( 'format' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group interstitial-layout has-feedback {{ $errors->has( 'layout' ) ? 'has-error' : '' }} {{ isset($zone) ? ( $zone->format == BANNER ? 'hidden' : '' ) : ( old('format') == BANNER ? 'hidden' : '' ) }} ">
                                        <label>
                                              {{ trans( 'admin.layout' )  }}
                                        </label>
                                        @if( sizeof( $layouts = config('consts.zone_layouts') ) > 0 )
                                            <select name="layout" class="form-control">
                                                @foreach( $layouts as $key => $value )
                                                    <option value="{{ $key }}" {{ isset($zone) ? ( $zone->layout == $key ? 'selected' : '' ) : ( old('layout') == $key ? 'selected' : '' ) }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'layout' ) ? $errors->first( 'layout' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group banner-refresh has-feedback {{ $errors->has( 'refresh_interval' ) ? 'has-error' : '' }} {{ isset($zone) ? ( $zone->format != BANNER ? 'hidden' : '' ) : ( old('format') != BANNER ? 'hidden' : '' ) }} ">
                                        <label>
                                              {!! trans( 'admin.refresh_interval' ) !!}
                                        </label>
                                        <input type="number" class="form-control" name="refresh_interval" min="1" value="{{ isset($zone) ? $zone->refresh_interval : (old('refresh_interval') ?: 60 ) }}">
                                        <span class="help-block">
                                            {{ $errors->has( 'refresh_interval' ) ? $errors->first( 'refresh_interval' ) : '' }}
                                        </span>
                                    </div>  
                                </div>
                            </div>
                            
                            @if( Auth::user()->role == ADMIN_PRIV )
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'status' ) ? 'has-error' : '' }}">
                                            <label>
                                                {{ trans( 'lang.status' ) }}
                                            </label>
                                            @if( sizeof( $zone_states = config('consts.zone_status') ) > 0 )
                                                <select name="status" class="form-control">
                                                    @foreach( $zone_states as $key => $value )
                                                        <option value="{{ $key }}" {{ isset($zone) ? ( $zone->status == $key ? 'selected' : '' ) : ( old('status') == $key ? 'selected' :'' ) }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="help-block">
                                                    {{ $errors->has( 'status' ) ? $errors->first( 'status' ) : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">
                                {{ isset($zone) ? trans('lang.save') : trans('lang.create') }}
                            </button>
                        </div>
                    @else
                        <div class="box-body">
                            <p>
                                <a href="{{ url('app/create') }}">
                                    {{ trans( 'admin.create_app_first' ) }}
                                </a>
                            </p>
                        </div>
                    @endif         
                </div>
            </form>
        </div>
    </section>
@stop

@section( 'script' )
    <script type="text/javascript">
        $(document).ready(function(){

            // To handle select ad format to show and hide layout and refresh interval
            $('.create-zone').on( 'change', 'select[name=format]', function(){
                var format = $(this).val();
                $('.banner-refresh, .interstitial-layout').addClass( 'hidden' );
                switch (format) {
                    case "{{ BANNER }}":
                        $('.banner-refresh').removeClass('hidden').find('input[name=refresh_interval]').val('60');
                        break;
                    case "{{ INTERSTITIAL }}":
                        $('.interstitial-layout').removeClass('hidden');
                        break;
                }
            } );

            // To handle toggle capping
            $('.create-zone').on( 'ifChanged', '.freq-cap', function(){
                if( $(this).prop('checked') ){
                    $(this).parents('.form-group').find('.freq-input').removeClass( 'hidden' );
                }else{
                    $(this).parents('.form-group').find('.freq-input').addClass( 'hidden' );
                }
            });
        });
    </script>
@stop