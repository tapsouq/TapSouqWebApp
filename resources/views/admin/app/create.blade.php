@extends( 'admin.layout.layout' )

@section( 'head' )
    <link rel="stylesheet" type="text/css" href="{{ url() }}/resources/assets/plugins/bootsnipp-file-input/bootsnipp-file-input.css">
@stop

@section( 'content' )
    <section class="create-app-section">
        <div class="form">
            <form role="form" action="{{ isset($_app) ? url('save-app') : url('store-app') }}" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            {{ $title }}
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="form-body">
                            @if( isset( $_app ) )
                                <input type="hidden" name="id" value="{{$_app->id}}" />
                            @endif
                            <div class="form-group has-feedback {{ $errors->has( 'name' ) ? 'has-error' : '' }}">
                                <label>
                                    {{ trans( 'lang.name' ) }}
                                    {!! csrf_field() !!}
                                </label>
                                <input type="text" class="form-control" name="name" value="{{ isset($_app) ? $_app->name : old('name') }}" required>
                                <span class="help-block">
                                    {{ $errors->has( 'name' ) ? $errors->first( 'name' ) : '' }}
                                </span>
                            </div>
                            <div class="form-group has-feedback {{ $errors->has( 'platform' ) ? 'has-error' : '' }}">
                                <label>
                                    {{ trans( 'admin.platform' ) }}
                                </label>
                                @if( sizeof( $platforms = config( 'consts.app_platforms' ) ) > 0 )
                                    <select name="platform" class="form-control" required>
                                        @foreach( $platforms as $key => $value )
                                            <option value="{{ $key }}" {{ isset($_app)? ( $_app->platform == $key ? 'selected' : '' ) : ( old('platform') == $key ? 'selected' : '' ) }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block">
                                        {{ $errors->has( 'platform' ) ? $errors->first( 'platform' ) : '' }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group has-feedback {{ $errors->has( 'package_id' ) ? 'has-error' : '' }}">
                                <label>
                                    {{ trans( 'admin.package_id' ) }}
                                </label>
                                <input type="text" class="form-control" name="package_id" value="{{ isset($_app)? $_app->package_id : old('package_id') }}" required>
                                <span class="help-block">
                                    {{ $errors->has( 'package_id' ) ? $errors->first( 'package_id' ) : '' }}
                                </span>
                            </div>
                            <div class="form-group has-feedback {{ $errors->has( 'icon' ) ? 'has-error' : '' }}">
                                <label>
                                    {{ trans( 'admin.icon' ) }}
                                </label>
                                @if( isset( $_app ) )
                                    <div class="icon-container">
                                        <div class="col-md-4">
                                            <a href="#" class="thumbnail">
                                                <img src="{{ url('public/uploads/app-icons/' . $_app->icon ) }}" alt="{{ trans( 'admin.app_icon' ) }}">
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                <!-- image-preview-filename input [CUT FROM HERE]-->
                                <div class="input-group image-preview">
                                    <input type="text" class="form-control image-preview-filename" disabled="disabled"> <!-- don't give a name === doesn't send on POST/GET -->
                                    <span class="input-group-btn">
                                        <!-- image-preview-clear button -->
                                        <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                            <span class="glyphicon glyphicon-remove"></span> {{ trans( 'lang.clear' ) }}
                                        </button>
                                        <!-- image-preview-input -->
                                        <div class="btn btn-default image-preview-input">
                                            <span class="glyphicon glyphicon-folder-open"></span>
                                            <span class="image-preview-input-title">{{ trans( 'lang.browse' ) }}</span>
                                            <input type="file" accept="image/png, image/jpeg, image/gif" name="icon" {{ isset($_app) ? '' : 'required' }} /> <!-- rename it -->
                                        </div>
                                    </span>
                                </div><!-- /input-group image-preview [TO HERE]--> 
                                <span class="help-block">
                                    {{ $errors->has( 'icon' ) ? $errors->first( 'icon' ) : '' }}
                                </span>
                            </div>
                            <div class="form-group has-feedback {{ $errors->has( 'category' ) ? 'has-error' : '' }}">
                                <label>
                                    {{ trans( 'admin.category' ) }}
                                </label>
                                @if( sizeof( $categories ) > 0 )
                                    <select class="form-control category" name="category[]" multiple required>
                                        @foreach( $categories as $key => $value )
                                            <option value="{{ $value->id }}" {{ isset($app_cats) ? ( in_array( $value->id, $app_cats ) ? 'selected' : '' ) :  ( old( 'category' ) ? ( in_array($value->id, old( 'category' ) ) ? 'selected' : '' ) : '' )  }} >
                                                {{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block">
                                        {{ $errors->has( 'category' ) ? $errors->first( 'category' ) : '' }}
                                    </span>
                                @endif
                            </div>
                            @if( Auth::user()->role == ADMIN_PRIV )
                            <div class="form-group has-feedback {{ $errors->has( 'status' ) ? 'has-error' : '' }}">
                                <label>
                                    {{ trans( 'admin.status' ) }}
                                </label>
                                @if( sizeof( $states = config( 'consts.app_status' ) ) > 0 )
                                    <select name="status" class="form-control">
                                        @foreach( $states as $key => $value )
                                            <option value="{{$key}}" {{ isset($_app) ? ( $key == $_app->status ? 'selected' : '' ) : ( old('status') == $key ? 'selected' : '' ) }}>
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
                    <div class="box-footer">
                        <button type="submit" class="btn btn-info pull-right">
                            {{ isset($_app) ?  trans( 'lang.save' ) : trans( 'lang.create' ) }}
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </section>
@stop

@section( 'script' )
    <script type="text/javascript" src="{{ url() }}/resources/assets/plugins/bootsnipp-file-input/bootsnipp-file-input.js" ></script>
    <script type="text/javascript">
        $(document).ready( function(){

            $("select.category").select2({
                placeholder : "{{ trans( 'admin.select_cat' ) }}",
                maximumInputLength: 7, // only allow terms up to 20 characters long
                maximumSelectionLength: 2
            });
        });

    </script>
@stop