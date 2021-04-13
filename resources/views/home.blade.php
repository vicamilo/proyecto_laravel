@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('includes.message')
            @foreach($images as $image)
               @include('includes.image',['image'=>$image])
            @endforeach
            <!-- PAGINACION -->
            <div class="clearfix"></div>
            {{$images->links()}}
        </div>
    </div>
</div>
@endsection

<!-- @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

You are logged in! -->