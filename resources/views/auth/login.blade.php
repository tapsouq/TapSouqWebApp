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

    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12 col-xs-offset-0">
            <div class="login-box">
                <div class="login-box-body container-fluid">
                    <p class="login-box-msg">{{ trans( 'lang.sign_in_start_session' ) }}</p>

                    <form action="{{ url( 'auth/login' ) }}" method="post">
                      @if( session( 'danger' ) )
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-ban"></i> {{ trans( 'lang.alert' ) }}</h4>
                            {{ session( 'danger' ) }}
                        </div>
                      @endif
                      @if( session( 'success' ) )
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-check"></i> {{ trans( 'lang.alert' ) }}</h4>
                            {{ session( 'success' ) }}
                        </div>
                      @endif  
                      <div class="form-group has-feedback {{ $errors->has( 'email' ) ? 'has-error' : '' }}">
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="{{ trans( 'lang.email' ) }}">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <span class="help-block">
                            {{ $errors->has( 'email' ) ? $errors->first( 'email' ) : '' }}
                        </span>
                        {!! csrf_field() !!}
                      </div>
                      <div class="form-group has-feedback {{ $errors->has( 'password' ) ? 'has-error' : '' }}">
                        <input type="password" name="password" class="form-control" placeholder="{{ trans( 'lang.password' ) }}">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        <span class="help-block">
                            {{ $errors->has( 'password' ) ? $errors->first( 'password' ) : '' }}
                        </span>
                      </div>
                      <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox icheck">
                                <label>
                                    <input type="checkbox" name="remember"> {{ trans( 'lang.remember_me' ) }}
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans( 'lang.login' ) }}</button>
                        </div>
                        <!-- /.col -->
                      </div>
                    </form>

                    @if( 1 == 0 )
                        <a href="#">{{ trans( 'lang.forgot_password' ) }}</a><br>
                    @endif
                    <a href="{{ url( 'auth/register' ) }}" class="text-center">{{ trans( 'lang.register_membership' ) }}</a>
                </div>
            </div>
        </div>
        <!-- /.login-box-body -->
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