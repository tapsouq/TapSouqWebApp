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