<p>
	{{ trans( 'admin.verify_msg' ) }} <a href="{{ url( 'verify-email?token=' . $user->remember_token .'&email=' . $user->email ) }}" >{{ trans( 'admin.link' ) }}</a>
</p>