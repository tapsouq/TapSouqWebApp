@extends('home.layout.layout')

@section('content')
	<section class="resources-page container-fluid">
		<header>
			<h1>
				Resources Page
			</h1>
		</header>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<h3>
						To integrate tapsouq sdk and show ads in your app follow the following steps:
					</h3>
				</div>
				<div class="col-md-12">
					<ol type="1">
						<li>
							<b>download sdk from here:</b> <a href="{{ url('resources/assets/home/tapsouq-sdk-library.rar') }}">tap-souq-sdk-library.rar</a>
							<br>
							extract the downloaded file then you will find the library file tap-souq-sdk.aar and SampleProject to see how banners and interstitials are working.
						</li>
						<li>
							<b>Create tapsouq library in Android Studio:</b>
							<br>
							<span class="ml20">
								Follow the following steps to add tap-souq-sdk.aar file to your android studio project.
							</span>
							<ol class="ml20" type="a">
								<li>
									Click File -> New -> New Module -> Choose Import new JAR/AAR Module
									<img src="{{ url() }}/resources/assets/home/images/resources/2-a.jpg" alt="Click File -> New -> New Module -> Choose Import new JAR/AAR Module">
								</li>
								<li>
									Locate and open tap-souq-sdk.aar library file from the extracted folder. Then click finish. Note that the module will be shown in your project modules list.
									<img src="{{ url() }}/resources/assets/home/images/resources/2-b.jpg" alt="Locate and open tapsouq-sdk-v1.0.aar library file from the extracted folder. Then click finish. Note that the module will be shown in your project modules list.">
								</li>
								<li>
									Add the module to your project as follow:
									<br>
									Open File Menu-> Project Structure -> Select your app from the left pane -> Dependencies Tab -> Click + Button -> Select Module Dependency -> and Select tap-souq-sdk module, then click OK. 
									<img src="{{ url() }}/resources/assets/home/images/resources/2-c.jpg" alt="add the file to your project.">
								</li>
								<li>
									Add these lines to your build.gradle file:
									<pre>
										dependencies {
										  compile 'com.github.bumptech.glide:glide:3.7.0'
										  compile 'com.android.support:support-v4:19.1.0'
										}
									</pre>
								</li>
								<li>
									now you can start adding a few lines of code to integrate banners and interstitial.
								</li>
							</ol>
						</li>
						<li>
							<b>Banner Ad Integration:</b>
							<ol class="ml20" type="a">
								<li>
									Login to your account and Add new app, then add new Ad unit (select banner format) and keep the Ad Unit ID to be used in step c. 
								</li>
								<li>
									add xml tag of tapsouq banner.
									<pre>
										&lt;com.tapsouq.sdk.ads.TapSouqBannerAd
										    android:id="@+id/banner_view"
										    android:layout_width="match_parent"
										    android:layout_height="wrap_content"/&gt;
									</pre>
								</li>
								<li>
									In your Activity add the following line of codes:
									<pre>
										// define the string field adUnitId
										private String adUnitId = "8";
										...
										//add banner code in your activity onCreate() method
										TapSouqBannerAd banner = (TapSouqBannerAd) findViewById(R.id.banner_view);
										banner.setAdUnitID(adUnitId);
										banner.load();
									</pre>
								</li>
								<li>
									For testing purposes, you must set banner test mode before banner.load().
									<pre>
										banner.setTestMode(true); //remove this line before publishing
									</pre>
								</li>
							</ol>
						</li>
						<li>
							<b>Interstitial Ads Integration:</b>
							<pre>
								public class MainActivity extends AppCompatActivity {

								    private String interstitialAdUnit = "12";

								    @Override
								    protected void onCreate(Bundle savedInstanceState) {
								        super.onCreate(savedInstanceState);
								        setContentView(R.layout.activity_main);


								        final TapSouqInterstitialAd interstitialAd = new TapSouqInterstitialAd(this);
								        interstitialAd.setAdUnitID(interstitialAdUnit);
								        interstitialAd.setListener(new TapSouqListener() {
								            @Override
								            public void adLoaded() {
										    if(interstitialAd.isAdLoaded()){
								                 interstitialAd.showAd();
										    }
								            }
										//you can override other methods 
								 		// like adShown(), adClicked(), adClosed(), AdFailed()
								        });
								        interstitialAd.load();

								    }
								}
							</pre>
							Also you can add setTestMode(true) with interstitial ad.
						</li>
						<li>
						<b>(Optional) Add Listener: </b> 
						<br>
						If you want to add listener to track when the Ad is shown, clicked, failed, put this code before loading the banner or interstitial: 
						<pre>
							banner.setListener(new TapSouqListener() {
										 @Override
										 public void adShown() {
								
											 //do your stuff
										 }
								
								
										 @Override
										 public void adClicked() {
								
											 //do your stuff
										 }
								
										 @Override
										 public void adFailed() {
								
											 //do your stuff
										 }


								 }
									);						
						</pre>
						</li>
						<li>
							<b>(Optional) How to integrate tapsouq ads with admob:</b>
							<br>
							You can put tapsouq Ads directly in your app and/or you can integrate them with admob. When admob ads are failed to load you can simply call tapsouq ads.
							<pre>
								admobInterstitialAd = new InterstitialAd(this);
								admobInterstitialAd.setAdUnitId(admobAdUnitId);
								admobInterstitialAd.setAdListener(new AdListener() {

								    @Override
								    public void onAdFailedToLoad(int errorCode) {

								        final TapSouqInterstitialAd interstitialAd = new TapSouqInterstitialAd(this);
								        interstitialAd.setAdUnitID(tapsouqAdUnitId);
								        interstitialAd.setListener(new TapSouqListener() {
								            @Override
								            public void adLoaded() {
								                if (interstitialAd.isAdLoaded() {
								                    interstitialAd.showAd();
								                }
								            }
								            //you can override other methods, if you need 
								            // like adShown(), adClicked(), adClosed(), AdFailed()
								        });


								        super.onAdFailedToLoad(errorCode);
								    }
								});

								admobInterstitialAd.loadAd(new AdRequest.Builder().build());

							</pre>
						</li>
					</ol>
				</div>
			</div>
		</div>
	</section>
@stop