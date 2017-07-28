@extends('app')
@section('header')
    <?php $message = $exception->getMessage() ?>
    <h3>{{ $message or "I can't find the page you want"}}</h3>
@endsection