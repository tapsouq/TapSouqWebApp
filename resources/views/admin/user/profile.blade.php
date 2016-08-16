@extends( 'admin.layout.layout' )

@section( 'head' )

@stop

@section( 'content' )
    <section class="profile-section">
        <div class="form">
            <form role="form" method="post" action="{{ url('save-profile') }}">
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
                                    <div class="form-group has-feedback {{ $errors->has( 'fname' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.fname' ) }}
                                        </label>
                                        <input type="text" class="form-control" name="fname" value="{{ $user->fname }}" >
                                        <span class="help-block">
                                            {{ $errors->has( 'fname' ) ? $errors->first( 'fname' ) : '' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'lname' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.lname' ) }}
                                        </label>
                                        <input type="text" class="form-control" name="lname" value="{{ $user->lname }}" >
                                        <span class="help-block">
                                            {{ $errors->has( 'lname' ) ? $errors->first( 'lname' ) : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label>
                                            {{ trans( 'lang.email' ) }}
                                        </label>
                                        <p class="form-static">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'country' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.country' ) }}
                                        </label>
                                        @if( sizeof( $countries = DB::table( 'countries' )->get() ) > 0 )
                                            <select class="form-control" name="country">
                                                @foreach( $countries as $key =>$value )
                                                    <option value="{{ $value->id }}" {{ $value->id == $user->country ? 'selected' : '' }}>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->has( 'country' ) ? $errors->first('country') : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'password' ) ? 'has-error' : '' }}">
                                          <label>
                                              {{ trans( 'lang.password' ) }}
                                          </label>
                                          <input type="password" class="form-control" name="password" >
                                          <span class="help-block">
                                              {{ $errors->has( 'password' ) ? $errors->first( 'password' ) : '' }}
                                          </span>
                                          {!! csrf_field() !!}
                                      </div>  
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'password' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.confirm-password' ) }}
                                        </label>
                                        <input type="password" class="form-control" name="password_confirmation" >
                                        <span class="help-block">
                                            {{ $errors->has( 'password' ) ? $errors->first( 'password' ) : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group has-feedback {{ $errors->has( 'city' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.city' ) }}
                                        </label>
                                        <input type="text" class="form-control" name="city" value="{{ $user->city }}" >
                                        <span class="help-block">
                                            {{ $errors->has( 'city' ) ? $errors->first( 'city' ) : '' }}
                                        </span>
                                    </div>  
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'address' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.address' ) }}
                                        </label>
                                        <input type="text" class="form-control" name="address" value="{{ $user->address }}" >
                                        <span class="help-block">
                                            {{ $errors->has( 'address' ) ? $errors->first( 'address' ) : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback {{ $errors->has( 'company' ) ? 'has-error' : '' }}">
                                        <label>
                                            {{ trans( 'lang.company' ) }}
                                        </label>
                                        <input type="text" class="form-control" name="company" value="{{ $user->company }}" >
                                        <span class="help-block">
                                            {{ $errors->has( 'company' ) ? $errors->first( 'company' ) : '' }}
                                        </span>
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