@extends('app')
@section('header')       <h2>Convert your HTML documents to PDF easily</h2>
<a href="{{ url('/') }}" class="btn btn-outline btn-xl">Start Now!</a>
@endsection
@section('body')
    <section class="features" id="features">
        <div class="container">
            <div class="section-heading text-center">
                <h2>Unlimited Features, Unlimited Fun</h2>
                <p class="text-muted">Check out what you can do with this service!</p>
                <hr>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="feature-item">
                                    <i class="icon-camera text-primary"></i>
                                    <h3>Flexible Use</h3>
                                    <p class="text-muted">Put an image or anything else in pdf!</p>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="feature-item">
                                    <i class="icon-present text-primary"></i>
                                    <h3>Free to Use</h3>
                                    <p class="text-muted">As always, this service will be affordable to use for any
                                        purpose!</p>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="feature-item">
                                    <i class="icon-lock-open text-primary"></i>
                                    <h3>Open Source</h3>
                                    <p class="text-muted">MIT licensed, you can use it commercially!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta pricing" id="pricing">
        <div class="container text-white">
            <div class="section-heading text-center">
                <h2>Limited money, No problem</h2>
                <p class="">Check out what plans are available!</p>
                <hr>
            </div>
            <div class="row pricing">
                <div class="col-lg-4">
                    <a class="btn btn-outline btn-xl" href="{{url('/')}}">
                        Free
                    </a>
                </div>
                <div class="col-lg-4">
                    <a class="btn btn-outline btn-xl text-center" href="{{url('/')}}">
                        Free
                    </a>
                </div>
                <div class="col-lg-4">
                    <a class="btn btn-outline btn-xl" href="{{url('/')}}">
                        Free
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="contact bg-primary" id="contact">
        <div class="container">
            <h2>We <i class="fa fa-heart"></i> new friends!</h2>
            <ul class="list-inline list-social">
                <li class="list-inline-item">
                    <a href="#"><i class="fa fa-github"></i></a>
                </li>
                <li class="list-inline-item">
                    <a href="#"><i class="fa fa-twitter"></i></a>
                </li>
                <li class="list-inline-item">
                    <a href="#"><i class="fa fa-google-plus"></i></a>
                </li>
            </ul>
        </div>
    </section>
@endsection