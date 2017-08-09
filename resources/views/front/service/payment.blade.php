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

@stop

