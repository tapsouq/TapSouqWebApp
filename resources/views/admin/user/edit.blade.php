@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="edit-user">
        <div class="form" >
            <form role="form" method="post" action="{{ url('save-user') }}">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            {{ $title }}
                            {!! csrf_field() !!}
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            {{ trans( 'lang.fname' ) }}  
                                        </label>
                                        <p class="form-static">
                                            {{ $user->fname }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            {{ trans( 'lang.lname' ) }}  
                                        </label>
                                        <p class="form-static">
                                            {{ $user->lname }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            {{ trans( 'lang.email' ) }}  
                                        </label>
                                        <p class="form-static">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            {{ trans( 'lang.address' ) }}  
                                        </label>
                                        <p class="form-static">
                                            {{ $user->address }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            {{ trans( 'lang.country' ) }}  
                                        </label>
                                        <p class="form-static">
                                            {{ $user->country_name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            {{ trans( 'lang.city' ) }}  
                                        </label>
                                        <p class="form-static">
                                            {{ $user->city }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            {{ trans( 'lang.role' ) }}
                                            <input name="id" type="hidden" value="{{ $user->id }}" />  
                                        </label>
                                        @if( sizeof( $roles = config( 'consts.user_roles' ) ) > 0 )
                                            <select name="role" class="form-control has-feedback {{ $errors->has( 'role' ) ? 'has-error' : '' }}">
                                                @foreach( $roles as $key => $value )
                                                    <option value="{{ $key }}" {{ $key == $user->role ? 'selected' : '' }} >{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'role' ) ? $errors->first( 'role' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'status' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.status' ) }}  
                                        </label>
                                        @if( sizeof( $states = config( 'consts.user_status' ) ) > 0 )
                                            <select class="form-control" name="status">
                                                @foreach( $states as $key => $value )
                                                    <option value="{{ $key }}" {{ $user->status == $key ? 'selected' : '' }} >{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'status' ) ? $errors->first( 'status' ) : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-info pull-right">
                            {{ trans( 'lang.save' ) }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@stop

@section( 'script' )

@stop