@extends( 'home.layout.layout' )

@section( 'head' )
    
@stop

@section( 'content' )
    <section class="slider"> 
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            <div class="item active">
                                <img src="{{ url('resources/assets/home') }}/images/img1.png" alt="...">
                                <div class="slider-caption">
                                    <article>
                                        <h2>From Developers to Developers</h2>
                                        <p>
                                            We Know that the biggest problem of mobile application developers is getting new users for their apps, so we made this solution to help developers in utilizing their apps to get new users in new countries.
                                        </p>
                                    </article>  
                                </div>
                            </div>
                            <div class="item">
                                <img src="{{ url('resources/assets/home') }}/images/img2.png" alt="...">
                                <div class="slider-caption">
                                    <article>
                                        <h2>Give and Take</h2>
                                        <p>
                                            Simply you will show ads of other developers in your apps. And your ads will be shown in other developers' apps and get new installs accordingly.
                                        </p>
                                    </article>
                                </div>
                            </div>
                            <div class="item">
                                <img class="pig-img" src="{{ url('resources/assets/home') }}/images/img3.png" alt="...">
                                <div class="slider-caption">
                                    <article> 
                                        <h2>Main Benefits</h2>
                                        <ul>
                                            <li>About 90% of monetization ad requests are not filled. So, tapsouq can utilize your ad inventory.</li>
                                            <li>Gather ads in your account and use them in the big launch of new application.</li>
                                            <li>Or, spend your ads day by day to get new users for the current applications.</li>
                                            <li>Filter ads and apps that are being promoted in your apps.</li>
                                        </ul>
                                    </article>
                                </div>
                            </div>
                        </div>

                        <!-- Controls -->
                        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="video">
        <div class="overlay outer-overlay">
            <div class="inner-overlay"></div>
        </div>
        <img src="{{ url('resources/assets/home/images/play.png') }}">
        <video id="siteVideo" width="auto" height="auto">
          <source src="{{ url("resources/assets/custom")}}/video.mp4" type="video/mp4">
          <source src="{{ url("resources/assets/custom")}}/video.ogg" type="video/ogg">
          Your browser does not support the video tag.
        </video>
    </section>
    <section class="cols-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <article class="text-center">
                        <header>
                            <h3>
                                Ads Exchange
                            </h3>
                        </header>
                        <div>
                            <p>
                                <img src="{{ url('resources/assets/home') }}/images/ads-exchange.png" alt="Ads Exchange">
                            </p>
                            <p>
                                Exchange your extra ads with other developers and reach new countries.
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-3">
                    <article class="text-center">
                        <header>
                            <h3>
                                Cross Promotion
                            </h3>
                        </header>
                        <div>
                            <p>
                                <img src="{{ url('resources/assets/home') }}/images/gross-promotion.png" alt="Gross Promotion">
                            </p>
                            <p>
                                Utilize the network of your Apps and promote the new app. 
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-3">
                    <article class="text-center">
                        <header>
                            <h3>
                                Reports &amp; Matrices
                            </h3>
                        </header>
                        <div>
                            <p>
                                <img src="{{ url('resources/assets/home') }}/images/reports-matrices.png" alt="Reports &amp; Matrices">
                            </p>
                            <p>
                                Our dashboard reports make it easy for you to monitor your performance.
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-3">
                    <article class="text-center">
                        <header>
                            <h3>
                                Easy Integration
                            </h3>
                        </header>
                        <div>
                            <p>
                                <img src="{{ url('resources/assets/home') }}/images/easy-integration.png" alt="Easy Integration">
                            </p>
                            <p>
                                Integrate our SDK in a few lines of code.
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
    <section class="steps-section">
        <header class="text-center">
            Only 2 Steps
        </header>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <header>
                        <h3>
                            <span class="circle">1</span>
                            Integrate SDK
                        </h3>
                    </header>
                    <p>
                        <img class="pull-left" src="{{ url('resources/assets/home') }}/images/integrate-sdk.png" alt="Integrate SDK">
                        You can add apps from your account page and integrate SDK in two lines of code.
                    </p>
                </div>
                <div class="col-md-6 clear-both">
                    <header>
                        <h3>
                            <span class="circle">2</span>
                            Create Ad Campaign
                        </h3>
                    </header>
                    <p>
                        <img class="pull-left" src="{{ url('resources/assets/home') }}/images/create-campaign.png" alt="Create Ad Campaign">
                        create your ad campaign and target countries and create your creative to start getting installs.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <a href="{{ url('auth/register') }}" class="btn btn-info btn-block">
                        Join The Developer Community
                    </a>
                </div>
            </div>
        </div>
    </section>
@stop

@section( 'script' )
    <script type="text/javascript">
        var vid = document.getElementById("siteVideo");
        vid.volume = 0.2;
        $('.video img').on('click', function toggleControls() {
            vid.setAttribute("controls", "controls");
            vid.play();
            $('.overlay, .video img').addClass('hidden');
        })
    </script>    
@stop