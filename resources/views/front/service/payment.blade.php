@extends('front.template')
@section('main')
@if(session()->has('error'))
<div class="row">
	@include('partials/error', ['type' => 'danger', 'message' => session('error')])
</div>
@endif	

<div class="box">
    <p>
        This service costs <strong>&pound; {{$service->price}}</strong>. Now Paypal is accepted for payment.<br>
    </p>
    <a href={{$approvalUrl}}><img src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif"></a>
</div>

<div class="box">
    <center><p>COMMENTS</p></center>
    <hr>

@foreach($comments as $comment)
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h4>{{ $comment->user->username }}
                    <br>
                    <small>{{ trans('front/blog.on') }} {{ $comment->created_at }}</small>
                    </h4>
                </div>
                <div class="col-lg-12">
                    <p>{!! $comment->content !!}</p>
                </div>
                    <hr>
                    
            </div>
 <hr>
@endforeach
</div>

@stop

