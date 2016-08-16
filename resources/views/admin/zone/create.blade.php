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
                    @if( sizeof( $applications ) > 0 )
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'name' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.name' ) }}
                                            {!! csrf_field() !!}
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
                                    <div class="form-group has-feedback {{ $errors->has( 'application' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.application' ) }}
                                        </label>
                                        @if( sizeof( $applications ) > 0 )
                                            <select class="form-control select2" name="application" required>
                                                @foreach( $applications as $key => $value )
                                                    <option value="{{ $value->id }}" {{ isset($zone) ? ( $zone->app_id == $value->id ? 'selected' : '' ) : ( old('application') == $value->id ? 'selected' :'' ) }}>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'application' ) ? $errors->first( 'application' ) : '' }}
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
                                                    <option value="{{ $key }}" {{ isset($zone) ? ( $zone->format == $key ? 'selected' : '' ) : ( old('application') == $key ? 'selected' :'' ) }} >
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
                                    <div class="form-group has-feedback {{ $errors->has( 'daily_freq_cap' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.daily_freq_cap' ) }}
                                        </label>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="checkbox" class="minimal-blue freq-cap" value="1" name="daily_freq" {{ isset($zone) ? ( $zone->daily_freq_cap ? 'checked' : '' ) : ( old('daily_freq') ? 'checked' : '' )  }} >
                                            </div>
                                            <div class="col-md-10 freq-input {{ isset($zone) ? ( $zone->daily_freq_cap ? '' : 'hidden' ) : ( old('daily_freq') ? '' : 'hidden' ) }} ">
                                                <div class="input-group input-group-sm">
                                                    <input class="form-control" type="number" min="1" name="daily_freq_cap" value="{{ isset($zone) ? $zone->daily_freq_cap : old('daily_freq_cap') }}">
                                                    <span class="input-group-addon">
                                                        {{ trans( 'admin.times_per_day' ) }}
                                                    </span>
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->has( 'daily_freq_cap' ) ? $errors->first( 'daily_freq_cap' ) : '' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'hourly_freq_cap' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.hourly_freq_cap' ) }}
                                        </label>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="checkbox" class="minimal-blue freq-cap" value="1" name="hourly_freq" {{ isset($zone) ? ( $zone->hourly_freq_cap ? 'checked' : '' ) : ( old('hourly_freq') ? 'checked' : '' )  }} >
                                            </div>
                                            <div class="col-md-10 freq-input {{ isset($zone) ? ( $zone->hourly_freq_cap ? '' : 'hidden' ) : ( old('hourly_freq') ? '' : 'hidden' ) }} ">
                                                <div class="input-group input-group-sm">
                                                    <input class="form-control" type="number" min="1" name="hourly_freq_cap" value="{{ isset($zone) ? $zone->hourly_freq_cap : old('hourly_freq_cap') }}">
                                                    <span class="input-group-addon">
                                                        {{ trans( 'admin.times_per_hour' ) }}
                                                    </span>
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->has( 'hourly_freq_cap' ) ? $errors->first( 'hourly_freq_cap' ) : '' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group interstitial-layout has-feedback {{ $errors->has( 'layout' ) ? 'has-error' : '' }} {{ isset($zone) ? ( $zone->device_type == BANNER ? 'hidden' : '' ) : ( old('device_type') == BANNER ? 'hidden' : '' ) }} ">
                                        <label>
                                              {{ trans( 'admin.layout' ) }}
                                        </label>
                                        @if( sizeof( $layouts = config('consts.zone_layouts') ) > 0 )
                                            <select name="layout" class="form-control">
                                                @foreach( $layouts as $key => $value )
                                                    <option value="{{ $key }}" {{ isset($zone) ? ( $zone->layout == $key ? 'selected' : '' ) : ( old('layout') == $key ? 'selected' :'' ) }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'layout' ) ? $errors->first( 'layout' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group banner-refresh has-feedback {{ $errors->has( 'refresh_interval' ) ? 'has-error' : '' }} {{ isset($zone) ? ( $zone->device_type != BANNER ? 'hidden' : '' ) : ( old('device_type') != BANNER ? 'hidden' : '' ) }} ">
                                        <label>
                                              {{ trans( 'admin.refresh_interval' ) }}
                                        </label>
                                        <input type="number" class="form-control" name="refresh_interval" min="1" value="{{ isset($zone) ? $zone->refresh_interval : old('refresh_interval') }}">
                                        <span class="help-block">
                                            {{ $errors->has( 'refresh_interval' ) ? $errors->first( 'refresh_interval' ) : '' }}
                                        </span>
                                    </div>  
                                </div>
                                @if( Auth::user()->role == ADMIN_PRIV )
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'status' ) ? 'has-error' : '' }}">
                                            <label>
                                                {{ trans( 'lang.status' ) }}
                                            </label>
                                            @if( sizeof( $zone_states = config('consts.zone_status') ) > 0 )
                                                <select name="status" class="form-control">
                                                    @foreach( $zone_states as $key => $value )
                                                        <option value="{{ $key }}" {{ isset($zone) ? ( $zone->status == $key ? 'selected' : '' ) : ( old('layout') == $key ? 'selected' :'' ) }}>
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
                                @endif
                            </div>
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
                        $('.banner-refresh').removeClass('hidden');
                        console.log( 'banner {{ BANNER }}' );
                        break;
                    case "{{ INTERSTITIAL }}":
                        $('.interstitial-layout').removeClass('hidden');
                        console.log( 'intersti {{ INTERSTITIAL }}' );
                        break;
                }
            } );

            // To handle toggle capping
            $('.create-zone').on( 'ifChanged', '.freq-cap', function(){
                console.log( 'test' );
                if( $(this).prop('checked') ){
                    $(this).parents('.form-group').find('.freq-input').removeClass( 'hidden' );
                }else{
                    $(this).parents('.form-group').find('.freq-input').addClass( 'hidden' );
                }
            });
        });
    </script>
@stop