@extends( 'admin.layout.layout' )

@section( 'head' )
    <link rel="stylesheet" type="text/css" href="{{ url() }}/resources/assets/plugins/bootsnipp-file-input/bootsnipp-file-input.css">
@stop

@section( 'content' )
    <section class="create-blade">
        <div class="form">
            <form role="form" action="{{ isset($ad) ? url('save-ads') : url('store-ads') }}" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            {{ $title }}
                        </h3>
                    </div>
                    <div class="box-body">
                        @if( sizeof( $camps ) > 0 )
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'name' ) ? 'has-error' : '' }}">
                                            <label>
                                                {{ trans( 'lang.name' ) }}
                                                {!! csrf_field() !!}
                                                @if( isset($ad) )
                                                    <input type="hidden" name="id" value="{{ $ad->id }}">
                                                @endif
                                            </label>
                                            <input type="text" class="form-control" name="name" value="{{ isset($ad) ? $ad->name : old('name') }}" required>
                                            <span class="help-block">
                                                {{ $errors->has( 'name' ) ? $errors->first( 'name' ) : '' }}
                                            </span>
                                        </div>              
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'format' ) ? 'has-error' : '' }}">
                                            <label>
                                                 {{ trans( 'admin.format' ) }}
                                            </label>
                                            @if( sizeof( $formats = config('consts.all_formats') ) > 0 )
                                                 <select name="format" class="form-control" required>
                                                     @foreach( $formats as $key => $value )
                                                         <option value="{{ $key }}" {{ isset($ad) ? ( $ad->format == $key ? 'selected' : '' ) : ( old('layout') == $key ? 'selected' :'' ) }}>
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
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'type' ) ? 'has-error' : '' }}">
                                              <label>
                                                  {{ trans( 'lang.type' ) }}
                                              </label>
                                              @if( sizeof( $types = config('consts.ads_types') ) > 0 )
                                                  <select name="type" class="form-control" required>
                                                      @foreach( $types as $key => $value )
                                                          <option value="{{ $key }}" {{ isset($ad) ? ( $ad->type == $key ? 'selected' : '' ) : ( old('layout') == $key ? 'selected' :'' ) }}>
                                                              {{ $value }}
                                                          </option>
                                                      @endforeach
                                                  </select>
                                                  <span class="help-block">
                                                      {{ $errors->has( 'type' ) ? $errors->first( 'type' ) : '' }}
                                                  </span>
                                              @endif
                                          </div>  
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'click_url' ) ? 'has-error' : '' }}">
                                            <label>
                                                {{ trans( 'admin.click_url' ) }}
                                            </label>
                                            <input type="text" class="form-control" name="click_url" value="{{ isset($ad) ? $ad->click_url : old('click_url') }}" required>
                                            <span class="help-block">
                                                {{ $errors->has( 'click_url' ) ? $errors->first( 'click_url' ) : '' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'title' ) ? 'has-error' : '' }} {{ isset($ad) ? ( $ad->type == TEXT_AD ? '' : 'hidden' ) : '' }} ">
                                            <label>
                                                {{ trans( 'lang.title' ) }}
                                            </label>
                                            <input type="text" class="form-control" name="title" value="{{ isset($ad) ? $ad->title : old('title') }}" >
                                            <span class="help-block">
                                                {{ $errors->has( 'title' ) ? $errors->first( 'title' ) : '' }}
                                            </span>
                                          </div>  
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'description' ) ? 'has-error' : '' }} {{ isset($ad) ? ( $ad->type == TEXT_AD ? '' : 'hidden' ) : '' }}">
                                            <label>
                                                {{ trans( 'lang.description' ) }}
                                            </label>
                                            <input type="text" class="form-control" name="description" value="{{ isset($ad) ? $ad->description : '' }}" >
                                            <span class="help-block">
                                                {{ $errors->has( 'description' ) ? $errors->first( 'description' ) : '' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'image_file' ) ? 'has-error' : '' }}">
                                            <label>
                                                {{ trans( 'admin.image_file' ) }}
                                            </label>
                                            @if( isset( $ad ) )
                                                <div class="icon-container">
                                                    <div class="col-md-4">
                                                        <a href="#" class="thumbnail">
                                                            <img src="{{ url('public/uploads/ad-images/' . $ad->image_file ) }}" alt="{{ trans( 'admin.image_file' ) }}">
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
                                                        <input type="file" accept="image/png, image/jpeg, image/gif" name="image_file" {{ isset($ad) ? '' : 'required' }} /> <!-- rename it -->
                                                    </div>
                                                </span>
                                            </div><!-- /input-group image-preview [TO HERE]--> 
                                            <span class="help-block">
                                                {{ $errors->has( 'image_file' ) ? $errors->first( 'image_file' ) : '' }}
                                            </span>
                                          </div>  
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback {{ $errors->has( 'campaign' ) ? 'has-error' : '' }}">
                                            <label>
                                                {{ trans( 'admin.campaign' ) }}
                                            </label>
                                            @if( sizeof( $camps ) > 0 )
                                                <select class="form-control" name="campaign" required>
                                                    @foreach( $camps as $key => $value )
                                                        <option value="{{ $value->id }}" {{ isset($ad) ? ( $ad->camp_id == $value->id ? 'selected' : '' ) : ( old('campaign') == $value->id ? 'selected' :'' ) }}>
                                                            {{ $value->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="help-block">
                                                    {{ $errors->has( 'campaign' ) ? $errors->first( 'campaign' ) : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if( Auth::user()->role == ADMIN_PRIV )
                                        <div class="col-md-6">
                                            <div class="form-group has-feedback {{ $errors->has( 'status' ) ? 'has-error' : '' }}">
                                                <label>
                                                    {{ trans( 'lang.status' ) }}
                                                </label>
                                                @if( sizeof( $states = config('consts.ads_status') ) > 0 )
                                                    <select name="status" class="form-control">
                                                        @foreach( $states as $key => $value )
                                                            <option value="{{ $key }}" {{ isset($ad) ? ( $ad->status == $key ? 'selected' : '' ) : ( old('layout') == $key ? 'selected' :'' ) }}>
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
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-info pull-right">
                                {{ isset($ad) ? trans('lang.save') : trans('lang.create') }}
                            </button>
                        </div>
                    @else
                        <div class="box-body">
                            <p>
                                {{ trans( 'admin.no_active_camps' ) }}
                                <a href="{{ url( 'camp/create' ) }}">
                                     {{ trans( 'admin.add_new_campaign' ) }}
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
    <script type="text/javascript" src="{{ url() }}/resources/assets/plugins/bootsnipp-file-input/bootsnipp-file-input.js" ></script>
@stop