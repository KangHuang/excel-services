@extends('front.template')

@section('main')
@if(session()->has('error'))
<div class="row">
	@include('partials/error', ['type' => 'danger', 'message' => session('error')])
</div>
@endif	

    <div class="row">

        @foreach($posts as $post)
            <div class="box">
                <div class="col-lg-12 text-center">
                    <h2>{{ $post->title }}
                    <br>
                    <small>{{ $post->provider->username }} {{ trans('front/blog.on') }} {!! $post->created_at . ($post->created_at != $post->updated_at ? trans('front/blog.updated') . $post->updated_at : '') !!}</small>
                    </h2>
                </div>
                <div class="col-lg-12">
                    <p>{!! $post->description !!}</p>
                </div>
                <div class="col-lg-12 text-center">
                    {!! link_to('service/run/'.$post->id, trans('front/blog.button'), ['class' => 'btn btn-default btn-lg']) !!}

                    <hr>
                </div>
            </div>
        @endforeach
     
        <div class="col-lg-12 text-center">
            {!! $links !!}
        </div>

    </div>

@stop

