@extends('app')
@section('header')
    <section class="features" id="features">
        <div class="container">
            <div class="section-heading text-center">
                <h2>Upload a doc to test</h2>
                <form action="{{ url('/test') }}" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="input-group">
                        <label>File</label>
                        <div class="col-md-6">
                            <input type="file" name="page">
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="file" name="stylesheets[]" value="Stylesheets">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-outline btn-xl btn-danger">Convert</button>
                    </div>
                </form>
                <hr>
            </div>
        </div>
    </section>
@endsection
@section('body')
@endsection