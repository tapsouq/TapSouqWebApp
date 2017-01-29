@extends( 'admin.layout.layout' )

@section( 'head' )
    <link rel="stylesheet" type="text/css" href="{{ url() }}/resources/assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
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
                                        <input type="text" class="form-control" placeholder="{{ trans( 'lang.name' ) }}" name="name" value="{{ isset($camp) ? $camp->name : old('name') }}" required>
                                        <span class="help-block">
                                            {{ $errors->has( 'name' ) ? $errors->first( 'name' ) : '' }}
                                        </span>
                                    </div>        
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'target_platform' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.target_platformat' ) }}
                                        </label>
                                        @if( sizeof( $platforms = config('consts.app_platforms') ) > 0 )
                                            <div class="radio-list">
                                                @foreach( $platforms as $key => $value )
                                                    <input type="radio" name="target_platform" class="minimal-blue" value="{{ $key }}" {{ isset($camp) ? ( $camp->target_platform == $key ? 'checked' : '' ) : ( old('target_platform') == $key ? 'checked' :'' ) }}>
                                                    <span class="radio-label">
                                                        {{ $value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            <span class="help-block">
                                                {{ $errors->has( 'target_platform' ) ? $errors->first( 'target_platform' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row camp-date">
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
                                    <div class="form-group has-feedback {{ $errors->has( 'fcategory' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.primary_cat' ) }}
                                        </label>
                                        @if( sizeof( $categories ) > 0 )
                                            <select class="form-control category" name="fcategory" >
                                                <option value="">{{ trans('admin.select_cat') }}</option>
                                                @foreach( $categories as $key => $fcategory )
                                                    <option value="{{ $fcategory->id }}" {{ isset($camp) ? ( $fcategory->id == $camp->fcategory ? 'selected' : '' ) : ( old('fcategory') == $fcategory->id ? 'selected' :'' ) }} >
                                                        {{ $fcategory->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'fcategory' ) ? $errors->first( 'fcategory' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'scategory' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.secondary_cat' ) }}
                                        </label>
                                        @if( sizeof( $categories ) > 0 )
                                            <select class="form-control category" name="scategory" >
                                                <option value="">{{ trans('admin.select_cat') }}</option>
                                                @foreach( $categories as $key => $scategory )
                                                    <option value="{{ $scategory->id }}" {{ isset($camp) ? ( $scategory->id == $camp->scategory ? 'selected' : '' ) : ( old('scategory') == $scategory->id ? 'selected' :'' ) }} >
                                                        {{ $scategory->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'scategory' ) ? $errors->first( 'scategory' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'country' ) ? 'has-error' : '' }}">
                                        <label>
                                            {!! trans( 'admin.targeted_country' ) !!}
                                        </label>
                                        @if( sizeof( $countries ) > 0 )
                                            <select class="form-control country-select" name="country[]" multiple="">
                                                <option value="">{{ trans('admin.all_countries') }}</option>
                                                @foreach( $countries as $key => $country )
                                                    <option value="{{ $country->id }}" {{ isset($camp) ? ( in_array($country->id, explode(',', $camp->countries) ) ? 'selected' : '' ) : ( old('country') ? ( in_array($country->id, old('country')) ? 'selected' :'' ) : '' ) }} >
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
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'ad_serving_pace' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.ad_serving_pace' ) }}
                                        </label>
                                        @if( sizeof( $servingPaces = config('consts.camp_serving') ) > 0 )
                                            <select name="ad_serving_pace" class="form-control">
                                                <option value="">{{ trans( 'admin.normal' ) }}</option>
                                                @foreach( $servingPaces as $key => $value )
                                                    <option value="{{ $key }}" {{ isset($camp) ? ( $camp->ad_serving_pace == $key ? 'selected' : '' ) : ( old('ad_serving_pace') == $key ? 'selected' :'' ) }}>
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
                                    <div class="form-group has-feedback {{ $errors->has( 'keyword' ) ? 'has-error' : '' }}">
                                        <label>
                                            {!! trans( 'admin.targeted_keywords' ) !!}
                                        </label>
                                        @if( sizeof( $keywords ) > 0 )
                                            <select class="form-control keyword-select" name="keyword[]" multiple="">
                                                @foreach( $keywords as $value  )
                                                    <option value="{{ $value->id }}" {{ isset($camp) ? ( in_array($value->id, $selectedKeys) ? 'selected' : '' ) : ( old('keyword') ?  ( in_array($value->id, old('keyword')) ? 'selected' : '' ) :'' ) }}>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <a class="new-keyword">
                                                {{ trans( 'admin.add_new_keyword' ) }}
                                            </a>
                                            <select name="new_keywords[]" class="form-control hidden" multiple=""></select>
                                            <span class="help-block">
                                                {{ $errors->has( 'keyword' ) ? $errors->first( 'keyword' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'description' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.description' ) }}
                                        </label>
                                        <textarea class="form-control" name="description">{{ isset($camp) ? $camp->description : old('description') }}</textarea>
                                        <span class="help-block">
                                            {{ $errors->has( 'description' ) ? $errors->first( 'description' ) : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'imp_per_day' ) ? 'has-error' : '' }}">
                                        <label class="mr20">
                                            {{ trans( 'admin.imp_per_day' ) }}
                                        </label>
                                        <input type="checkbox" value="1" name="imp_per_day_checkbox" class="minimal-blue toggle-target" data-toggle="imp-per-day" {{ isset($camp)? ( $camp->imp_per_day ? 'checked' : ''): (old('checkbox')? 'checked' : '')}}>
                                        <input type="number" placeholder="{{ trans( 'admin.imp_per_day' ) }}" class="form-control imp-per-day {{ isset($camp)? ( $camp->imp_per_day ? '' : 'hidden'): (old('checkbox')? '' : 'hidden') }}" name="imp_per_day" value="{{ isset($camp) ? $camp->imp_per_day : old('imp_per_day') }}" >
                                        <span class="help-block">
                                            {{ $errors->has( 'imp_per_day' ) ? $errors->first( 'imp_per_day' ) : '' }}
                                        </span>
                                    </div>        
                                </div>
                                <div class="col-md-6">
                                    @if( Auth::user()->role == ADMIN_PRIV && isset($camp) )
                                        <div class="form-group has-feedback {{ $errors->has( 'status' ) ? 'has-error' : '' }}">
                                            <label>
                                                {{ trans( 'lang.status' ) }}
                                            </label>
                                            @if( sizeof( $states = config('consts.camp_status') ) > 0 )
                                                <select name="status" class="form-control">
                                                    @foreach( $states as $key => $value )
                                                        <option value="{{ $key }}" {{ isset($camp) ? ( $camp->status == $key ? 'selected' : '' ) : ( old('status') == $key ? 'selected' :'' ) }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="help-block">
                                                    {{ $errors->has( 'status' ) ? $errors->first( 'status' ) : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
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
    <script type="text/javascript" src="{{ url() }}/resources/assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js" ></script>
    <!-- Page script -->
    <script type="text/javascript">
        $(function () {
            $('.toggle-target').on('ifChanged', function(){
                $target = $(this).attr('data-toggle');
                $('.' + $target).toggleClass('hidden');
            });
            $('.country-select').select2({
                placeholder : "{{ trans( 'admin.select_country' ) }}"
            });
            $('.keyword-select').select2({
                placeholder : "{{ trans( 'admin.select_keyword' ) }}"
            });

            $('input[name=start_date], input[name=end_date]').datetimepicker();

            @if( isset($camp) )
                $('input[name=start_date]').data('DateTimePicker').minDate(moment( '{{ $camp->start_date }}' )).date( moment( '{{ $camp->start_date }}' ) );
                $('input[name=end_date]').data('DateTimePicker').minDate(moment( '{{ $camp->start_date }}' )).date( moment( '{{ $camp->end_date }}' ) );
            @else
                $('input[name=start_date]').data('DateTimePicker').minDate(moment());
                $('input[name=end_date]').data('DateTimePicker').minDate(moment().add(1, 'week'));
            @endif

            $('input[name=start_date]').on( 'dp.change', function(e){
                $('input[name=end_date]').data('DateTimePicker').minDate( e.date ).date(e.date.add(1, 'week'));                    
            });


            $("select.category").select2();

            $("select.category").on('change', function(){
                var catVal  = $(this).val();
                var $secCat = $('select.category').not(this);

                $secCat.find('option').not('.disabled').prop('disabled', false);
                $secCat.find('option[value="' + catVal + '"]').prop( 'disabled', true );
                $secCat.select2();
            });

            $('.new-keyword').on( 'click', function(){
                $('select[name="new_keywords[]"]').removeClass('hidden')
                                                .select2({
                                                    tags :true,
                                                    placeholder : "{{ trans( 'admin.add_new_keyword' ) }}"
                                                });

            });
        });
    </script>
@stop