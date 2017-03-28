<div>
	<style type="text/css">
		p, li{
			line-height: 1.7;
		}
		ol li span{
			display: block;
			margin: 5px;
		}
	</style>
	<p>
		<span class="hello-span">Hello,</span>
	</p>
	<p>
		Your account is created successfully in <a href="{{ url() }}">tapsouq.com</a>. And you have 100,000 impressions (signup bonus) that will be served after you integrate sdk in your apps.
		<br>
		Please activate your account by clicking on the <a href="{{ url( 'verify-email?token=' . $user->verify_token .'&email=' . $user->email ) }}" >link</a>.<br>
	</p>

	<p>
		Then do the following steps to start ad exchange:
	</p>
	
	<ol>
		<li>
			<b>Create App and AdPlacements</b> and integrate the sdk library in your app to start serving ads of other developers and get credit. 
			<br>
			<span class="follow-steps">
				Follow this guide to integrate sdk: <a href="{{ url('resources-page') }}">Resources page</a>
			</span>
		</li>
		<li>
			<b>Create Campaign and AdCreatives</b> to start showing your ads in other developers apps.
		</li>
	</ol>
	For more support contact us by:
	
	<p class="support">
		email: <a href="mailto:info@tapsouq.com" target="_blank">info@tapsouq.com</a><br />
		skype: a.dhaiban
	</p>
	
	<p>
		Regards,<br />
		TapSouq Team<br />
		<a href="{{ url() }}">www.tapsouq.com</a>
	</p>	
</div>