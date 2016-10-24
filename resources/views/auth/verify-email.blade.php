@extends('home.layout.layout')

@section('content')
	<div class="verify-section msg-section">
		<div class="msg-box container">
			<p>
				{{ $msg }}
			    @if( $status )
			        <br>
			        <p>
			            <a href="{{ url('auth/login') }}">
			                {{ trans( 'lang.login' ) }}
			            </a>
			        </p>
			    @endif
			</p>
		</div>
		
	</div>
@stop