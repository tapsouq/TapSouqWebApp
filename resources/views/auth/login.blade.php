<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ getSiteInfo()->site_title }} | {{ trans( 'lang.login' ) }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{ url( 'resources/assets' ) }}/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url( 'resources/assets' ) }}/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ url( 'resources/assets' ) }}/plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="{{ url( '' ) }}"><b>{{ trans( 'lang.tab' ) }}</b>{{ trans( 'lang.souq' ) }}</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">{{ trans( 'lang.sign_in_start_session' ) }}</p>

    <form action="{{ url( 'auth/login' ) }}" method="post">
      @if( session( 'danger' ) )
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> {{ trans( 'lang.alert' ) }}</h4>
            {{ session( 'danger' ) }}
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
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="{{ url( 'resources/assets' ) }}/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ url( 'resources/assets' ) }}/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="{{ url( 'resources/assets' ) }}/plugins/iCheck/icheck.min.js"></script>
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
