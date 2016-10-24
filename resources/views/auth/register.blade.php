@extends('home.layout.layout')

@section('head')
    <!-- iCheck -->
  <link rel="stylesheet" href="{{ url( 'resources/assets' ) }}/plugins/iCheck/square/blue.css">
  <style type="text/css">
      .checkbox label{
        padding-left: 0;
      }
      .icheckbox_square-blue{
        margin-right: 10px;
      }
  </style>
@stop

@section('content')
    <div class="register-section">
        <div class="register-box container">
            <div class="register-box-body">
                <p class="login-box-msg">{{ trans( 'lang.register_new_membership' ) }}</p>

                <form action="{{ url('auth/register') }}" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-feedback {{ $errors->has( 'fname' ) ? 'has-error' : '' }} ">
                                <input type="text" class="form-control" name="fname" placeholder="{{ trans( 'lang.fname' ) }}" value="{{ old('fname') }}" required>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="help-block">
                                    {{ $errors->has( 'fname' ) ? $errors->first( 'fname' ) : '' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group has-feedback {{ $errors->has( 'lname' ) ? 'has-error' : '' }} ">
                                <input type="text" class="form-control" name="lname" placeholder="{{ trans( 'lang.lname' ) }}" value="{{ old( 'lname' ) }}" required>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="help-block">
                                    {{ $errors->has( 'lname' ) ? $errors->first( 'lname' ) : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-feedback {{ $errors->has( 'email' ) ? 'has-error' : '' }} ">
                                <input type="email" class="form-control" name="email" placeholder="{{ trans( 'lang.email' ) }}" value="{{ old('email') }}" required>
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                <span class="help-block">
                                    {{ $errors->has( 'email' ) ? $errors->first( 'email' ) : '' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group has-feedback {{ $errors->has( 'company' ) ? 'has-error' : '' }} ">
                                <input type="text" class="form-control" name="company" placeholder="{{ trans( 'lang.company' ) }}" value="{{ old('company') }}" required>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <span class="help-block">
                                    {{ $errors->has( 'company' ) ? $errors->first( 'company' ) : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-feedback {{ $errors->has( 'password' ) ? 'has-error' : '' }} ">
                                <input type="password" class="form-control" name="password" placeholder="{{ trans( 'lang.password' ) }}" required>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                <span class="help-block">
                                    {{ $errors->has( 'password' ) ? $errors->first( 'password' ) : '' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group has-feedback {{ $errors->has( 'password' ) ? 'has-error' : '' }} ">
                                <input type="password" class="form-control" name="password_confirmation" placeholder="{{ trans( 'lang.confirm-password' ) }}" required>
                                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                                <span class="help-block">
                                    {{ $errors->has( 'password' ) ? $errors->first( 'password' ) : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-feedback {{ $errors->has( 'country' ) ? 'has-error' : '' }} ">
                                @if( sizeof( $countries = DB::table( 'countries' )->get() ) > 0 )
                                    <select name="country" class="form-control" required>
                                        @foreach( $countries as $key => $value )
                                            <option value="{{ $value->id }}" {{ old('country') == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                <span class="help-block">
                                    {{ $errors->has( 'country' ) ? $errors->first( 'country' ) : '' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group has-feedback {{ $errors->has( 'city' ) ? 'has-error' : '' }} ">
                                <input type="text" class="form-control" name="city" value="{{ old( 'city' ) }}" placeholder="{{ trans( 'lang.city' ) }}" required >
                                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                                <span class="help-block">
                                    {{ $errors->has( 'city' ) ? $errors->first( 'city' ) : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-feedback  {{ $errors->has( 'address' ) ? 'has-error' : '' }}">
                        <input type="text" class="form-control" name="address" value="{{ old( 'address' ) }}" placeholder="{{ trans( 'lang.address' ) }}" required>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                           <span class="help-block">
                                {{ $errors->has( 'address' ) ? $errors->first( 'address' ) : '' }}
                            </span>
                    </div>
                    <div class="row has-feedback {{ $errors->has( 'agree' ) ? 'has-error' : '' }}">
                        <div class="col-xs-8">
                            <div class="checkbox icheck">
                                <label>
                                    {!! csrf_field() !!}
                                    <input type="checkbox" name="agree" required> {{ trans( 'lang.agree_the' ) }} <a href="#">{{ trans( 'lang.terms' ) }}</a>
                                    <span class="help-block">
                                        {{ $errors->has( 'agree' ) ? $errors->first( 'agree' ) : '' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans( 'lang.register' ) }}</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <a href="{{ url( 'auth/login' ) }}" class="text-center">{{ trans( 'lang.already_have_membership' ) }}</a>
            </div>
        </div>
    </div>
@stop

@section('script')
    <!-- iCheck -->
    <script src="{{ url( 'resources/assets' ) }}/plugins/iCheck/icheck.min.js"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '5%' // optional
            });
        });
    </script>
@stop

    