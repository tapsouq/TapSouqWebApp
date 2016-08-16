<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> {{ trans( 'lang.register' ) }} </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{ url('resources/assets') }}/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('resources/assets') }}/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ url('resources/assets') }}/plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style type="text/css">
      .login-box, .register-box{
        width: 600px;
      }
  </style>
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="{{ url('') }}"><b>Tap</b>Souq</a>
  </div>

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
  <!-- /.form-box -->
</div>
<!-- /.register-box -->

<!-- jQuery 2.2.3 -->
<script src="{{ url('resources/assets') }}/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ url('resources/assets') }}/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="{{ url('resources/assets') }}/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>
