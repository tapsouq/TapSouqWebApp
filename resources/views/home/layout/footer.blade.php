<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <ul class="unstyled-list">
                    <li>
                        <a href="{{ url() }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ url('resources-page') }}">Resources</a>
                    </li>
                    <li>
                        <a href="{{ url("contact-us") }}">
                            Contact Us
                        </a>
                    </li>
                    <li>
                        <a href="{{ url("terms-of-service") }}">
                            Terms of service
                        </a>
                    </li>
                    <li>
                        <a href="https://www.facebook.com/tapsouq">
                            <i class="fa fa-facebook"></i>
                            Facebook
                        </a>
                    </li>
                    <li>
                        <a href="https://twitter.com/TapSouq ">
                            <i class="fa fa-twitter"></i>
                            Twitter
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">
                    Copyright &copy; {{ date('Y') }} TAPSOUQ. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
        