<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h4>
                    Latest Article from Blog
                </h4>
                <p>
                    You can add apps from your account pageand integrate SDK in two lines of code.
                </p>
            </div>
            <div class="col-md-6">
                <h4>
                    Menu
                </h4>
                <ul class="unstyled-list">
                    <li>
                        <a href="{{ url() }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ url('blog') }}">Blog</a>
                    </li>
                    <li>
                        <a href="{{ url('resources') }}">Resources</a>
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
                    <li>
                        <a href="{{ url("contact-us") }}">
                            Contact Us
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
        