@extends( 'admin.layout.layout' )

@section( 'head' )
    <link rel="stylesheet" type="text/css" href="{{ url() }}/resources/assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ url() }}/resources/assets/plugins/daterangepicker/daterangepicker.css">
@stop

@section( 'content' )
    <section class="create-campaign">
        <div class="form">
            <form role='form' action="{{ isset($camp) ? url('save-campaign') : url('store-campaign') }}" method="post">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            {{ $title }}
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'name' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.name' ) }}
                                            {{ csrf_field() }}
                                            @if( isset($camp) )
                                                <input type="hidden" name="id" value="{{ $camp->id }}">
                                            @endif
                                        </label>
                                        <input type="text" class="form-control" name="name" value="{{ isset($camp) ? $camp->name : old('name') }}" required>
                                        <span class="help-block">
                                            {{ $errors->has( 'name' ) ? $errors->first( 'name' ) : '' }}
                                        </span>
                                    </div>        
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'category' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.category' ) }}
                                        </label>
                                        @if( sizeof( $categories ) > 0 )
                                            <select class="form-control cat-select" name="category[]" multiple >
                                                @foreach( $categories as $key => $category )
                                                    <option value="{{ $category->id }}" {{ isset($camp) ? ( $camp->category == $category->id ? 'selected' : '' ) : ( old('category') == $category->id ? 'selected' :'' ) }} >
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'category' ) ? $errors->first( 'category' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'start_date' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.start_date' ) }}
                                        </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="start_date" value="{{ isset($camp) ? date_create_from_format( 'Y-m-d H:i:s', $camp->start_date )->format('m/d/Y g:i A') : '' }}" required="">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        <span class="help-block">
                                            {{ $errors->has( 'start_date' ) ? $errors->first( 'start_date' ) : '' }}
                                        </span>
                                    </div>  
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'end_date' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.end_date' ) }}
                                        </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="end_date" value="{{ isset($camp) ? date_create_from_format( 'Y-m-d H:i:s', $camp->end_date )->format('m/d/Y g:i A') : '' }}" required="">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        <span class="help-block">
                                            {{ $errors->has( 'end_date' ) ? $errors->first( 'end_date' ) : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'target_platformat' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.target_platformat' ) }}
                                        </label>
                                        @if( sizeof( $platforms = config('consts.app_platforms') ) > 0 )
                                            <select name="target_platform" class="form-control" required="">
                                                @foreach( $platforms as $key => $value )
                                                    <option value="{{ $key }}" {{ isset($camp) ? ( $camp->target_platform == $key ? 'selected' : '' ) : ( old('layout') == $key ? 'selected' :'' ) }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'target_platform' ) ? $errors->first( 'target_platform' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'ad_serving_pace' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.ad_serving_pace' ) }}
                                        </label>
                                        @if( sizeof( $servingPaces = config('consts.camp_serving') ) > 0 )
                                            <select name="ad_serving_pace" class="form-control">
                                                <option value="">{{ trans( 'admin.normal' ) }}</option>
                                                @foreach( $servingPaces as $key => $value )
                                                    <option value="{{ $key }}" {{ isset($camp) ? ( $camp->ad_serving_pace == $key ? 'selected' : '' ) : ( old('layout') == $key ? 'selected' :'' ) }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'ad_serving_pace' ) ? $errors->first( 'ad_serving_pace' ) : '' }}
                                            </span>
                                        @endif
                                    </div>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'country' ) ? 'has-error' : '' }}">
                                            <label>
                                                {{ trans( 'lang.country' ) }}
                                            </label>
                                            @if( sizeof( $countries ) > 0 )
                                                <select class="form-control country-select" name="country[]" multiple="">
                                                    <option value="">{{ trans('admin.all_countries') }}</option>
                                                    @foreach( $countries as $key => $country )
                                                        <option value="{{ $country->id }}" {{ isset($camp) ? ( $camp->country == $country->id ? 'selected' : '' ) : ( old('country') == $country->id ? 'selected' :'' ) }} >
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="help-block">
                                                    {{ $errors->has( 'country' ) ? $errors->first( 'country' ) : '' }}
                                                </span>
                                            @endif
                                      </div>  
                                </div>
                                @if( Auth::user()->role == ADMIN_PRIV && isset($camp) )
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'status' ) ? 'has-error' : '' }}">
                                            <label>
                                                {{ trans( 'lang.status' ) }}
                                            </label>
                                            @if( sizeof( $states = config('consts.camp_status') ) > 0 )
                                                <select name="status" class="form-control">
                                                    @foreach( $states as $key => $value )
                                                        <option value="{{ $key }}" {{ isset($camp) ? ( $camp->status == $key ? 'selected' : '' ) : ( old('layout') == $key ? 'selected' :'' ) }}>
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
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'description' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.description' ) }}
                                        </label>
                                        <textarea class="form-control" name="description">{{ isset($camp) ? $camp->description : '' }}</textarea>
                                        <span class="help-block">
                                            {{ $errors->has( 'description' ) ? $errors->first( 'description' ) : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-info pull-right">
                            {{ isset($camp) ? trans('lang.save') : trans('create') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>          
@stop

@section( 'script' )
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ url() }}/resources/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="{{ url() }}/resources/assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js" ></script>
    <!-- Page script -->
    <script type="text/javascript">
        $(function () {
            $('.cat-select').select2({
                placeholder : "{{ trans( 'admin.select_cat' ) }}",
                maximumInputLength: 7, // only allow terms up to 20 characters long
                maximumSelectionLength: 2
            });

            $('.country-select').select2({
                placeholder : "{{ trans( 'admin.select_country' ) }}"
            });

            $('input[name=start_date]').datetimepicker({
                minDate : moment()
            });

            $('input[name=end_date]').on( 'focus', function(){
                var val = $('input[name=start_date]').val();
                $(this).datetimepicker({
                    minDate : val
                });                    
            });

        });
    </script>
@stop