@extends( 'admin.layout.layout' )

@section( 'head' )
    <link rel="stylesheet" type="text/css" href="{{ url() }}/resources/assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ url() }}/resources/assets/plugins/daterangepicker/daterangepicker.css">
    <style type="text/css">
        .wrapper{
            overflow-x: visible;
        }
    </style>
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
                                                    <option value="{{ $category->id }}" {{ isset($camp) ? ( in_array($category->id, $selected_cats) ? 'selected' : '' ) : ( old('category') ? ( in_array($category->id, old('category')) ? 'selected' :'' ) : '' ) }} >
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
                                                    <option value="{{ $country->id }}" {{ isset($camp) ? ( in_array($country->id, $selected_countries) ? 'selected' : '' ) : ( old('country') ? ( in_array($country->id, old('country')) ? 'selected' :'' ) : '' ) }} >
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'country' ) ? $errors->first( 'country' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has( 'keyword' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'admin.keywords' ) }}
                                        </label>
                                        <div class="selected-keywords">
                                            @if( isset($camp) )
                                                @if( sizeof( $keywords ) > 0 )
                                                    @foreach( $keywords as $keyword  )
                                                        <span class='label label-primary'>
                                                            {{ $keyword->name }}
                                                            <i data-id='{{ $keyword->keyword_id }}' class='remove-keyword fa fa-times' ></i>
                                                            <input type="hidden" name="keywords[]" value="{{ $keyword->id }}">
                                                        </span>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </div>
                                        <input type="text" class="form-control keyword" placeholder="{{ trans('admin.type_keywords') }}" >
                                        <ul class="list-unstyled keywords-list"></ul>
                                        <span class="help-block">
                                            {{ $errors->has( 'keyword' ) ? $errors->first( 'keyword' ) : '' }}
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
                                    @endif
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

            // To handle search keywords.
            $(".keyword").on( 'keyup', function(e){
                $this = $(this);
                e.preventDefault();

                // variable to delay sent ajax request not to overlap.
                if ( $this.data('requestRunning') || $(this).val() == '' ) {
                    $('.keywords-list li').remove();
                    return;
                }
                $(this).data('requestRunning', true);

                $('.keywords-list li').remove();
                
                ids = [];
                $("input[name='keywords[]']").each(function( i, val ){
                    ids[i] = $(this).val();
                });
                $('.keywords-list').append( "<li class='spinner'><i class='fa fa-spinner fa-spin' ></i>{{ trans('lang.loading') }}</li>" );
                $.ajax({
                    url     : '{{ url('get-keywords') }}',
                    type    : 'post',
                    data    : {
                        key : $(this).val(),
                        present : ids
                    },
                    success : function(data){
                        $('.keywords-list li').remove();
                        if( data.length > 0 ){
                            for (var i = data.length - 1; i >= 0; i--) {
                                var id = data[i]['id'];
                                var name = data[i]['name'];
                                var listItem = "<li data-id='" + id + "' >" + name + "</li>";
                                $('.keywords-list').append( listItem )
                                                .css('display', 'none')
                                                .fadeIn(200);
                            }
                        }else{
                            $('.keywords-list').append( "<li data-id='new'>{{ trans('admin.add_new_keyword') }} <em>" + $('.keyword').val() + "</em></li>")
                                                .css('display', 'none')
                                                .slideDown();
                        }
                    },
                    complete : function(){
                        $this.data('requestRunning', false);
                    }
                });
            });

            $('.keywords-list').on( 'click', 'li', function(){
                
                // Rest the shown keywords list
                $('input.keyword').val('');
                $('.keywords-list li').remove();

                var id = $(this).attr('data-id');
                var newKeyword = '';

                if( id == 'new' ){
                    var name = $(this).find('em').text();
                    var input = "<input name='new_keywords[]' type='hidden' value='" + name + "' />";
                    id = name;
                    newKeyword = 'new';
                }else{
                    var name = $(this).text();
                    var input ="<input name='keywords[]' type='hidden' value='" + id + "' />";
                }
                $(this).remove();
                $('.keywords-list').append( input ); 
                var keyword = "<span class='label label-primary'>" + name + " <i data-id='" + id + "' class='" + newKeyword + " remove-keyword fa fa-times' ></i></span>";
                $('.selected-keywords').append( keyword );
            });

            $('.selected-keywords').on( 'click', '.remove-keyword', function(){
                var id = $(this).attr('data-id');
                if( $(this).hasClass('new') ){
                    $('input[name="new_keywords[]"][value="' + id + '""]').remove();
                }else{
                    $('input[name="keywords[]"][value="' + id + '"]').remove();
                }
                $(this).parents('span').remove();
            });
        });
    </script>
@stop