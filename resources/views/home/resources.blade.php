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
						To integrate ads follow the following steps
					</h3>
				</div>
				<div class="col-md-12">
					<ol>
						<li>
							download sdk from here: <a href="">tapsouq-sdk-library.zip</a>
							<br>
							extract the downloaded file then you will find the library file tapsouq-sdk.aar and SampleProject to see how banners and interstitial working.
						</li>
						<li>
							Adding tapsouq library.
							<br>
							<span class="ml20">
								Follow the following steps to add tapsouq-sdk.aar file to your android studio project.
							</span>
							<ol class="ml20">
								<li>
									open project structure.
									<img src="{{ url() }}/resources/assets/home/images/resources/2-a.jpg" alt="open project structure.">
								</li>
								<li>
									locate .aar library file.
									<img src="{{ url() }}/resources/assets/home/images/resources/2-b.jpg" alt="locate .aar library file.">
								</li>
								<li>
									add the file to your project.
									<img src="{{ url() }}/resources/assets/home/images/resources/2-c.jpg" alt="add the file to your project.">
								</li>
								<li>
									now you can start adding a few lines of code to integrate banners and interstitial. 
								</li>
							</ol>
						</li>
						<li>
							Banner Ads Integration.
							<ol class="ml20">
								<li>
									add xml tag of tapsouq sdk.
									<pre>
									&lt;sdk.tapsouq.com.tapsouqsdk.ads.TapSouqBannerAd
										android:id="@+id/banner_view"
										android:layout_width="match_parent"
										android:layout_height="wrap_content"
										android:scaleType="fitXY" /&gt;
									</pre>
								</li>
								<li>
									in your main activity add the following line of codes:
									<pre>TapSouqBannerAd bottomBanner = (TapSouqBannerAd) findViewById(R.id.banner_view);
										bottomBanner.setAdUnitID("8");
										bottomBanner.load();</pre>
								</li>
								<li>
									If you want to add listener to track when the ads shown, clicked or closed, add the listener like the following steps:
									<pre>
										TapSouqBannerAd bannerAd = (TapSouqBannerAd) findViewById(R.id.banner_view);
										bannerAd.setAdUnitID(bannerAdUnit);
										bannerAd.setListener(new TapSouqBannerAd.BannerAdListener() {
										    @Override
										    public void adLoaded(TapSouqBannerAd banner) {
										        //do your stuff
										    }

										    @Override
										    public void adShown() {

										        //do your stuff
										    }


										    @Override
										    public void adClicked() {

										        //do your stuff
										    }

										    @Override
										    public void adClosed() {

										        //do your stuff
										    }

										    @Override
										    public void appInstalled() {

										        //do your stuff
										    }
										}
										);
										bannerAd.load();
									</pre>
								</li>
							</ol>
						</li>
						<li>
							Interstitial Ads Integration.
							<pre>
								public class MainActivity extends AppCompatActivity {

								    private String interstitialAdUnit = "12";

								    @Override
								    protected void onCreate(Bundle savedInstanceState) {
								        super.onCreate(savedInstanceState);
								        setContentView(R.layout.activity_main);


								        TapSouqInterstitialAd interstitialAd = new TapSouqInterstitialAd(this);
								        interstitialAd.setAdUnitID(interstitialAdUnit);
								        interstitialAd.setListener(new TapSouqInterstitialAd.InterstitialListener() {
								            @Override
								            public void adLoaded(TapSouqInterstitialAd loadedInterstitialAd) {
								                loadedInterstitialAd.showAd();
								            }

								            @Override
								            public void adShown() {

								            }

								            @Override
								            public void adClicked() {

								            }

								            @Override
								            public void adClosed() {

								            }

								            @Override
								            public void appInstalled() {

								            }
								        });
								        interstitialAd.load();

								    }
								}

							</pre>
						</li>
					</ol>
				</div>
			</div>
		</div>
	</section>
@stop