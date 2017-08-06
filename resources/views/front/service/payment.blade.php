@extends('front.template')
@section('main')
@if(session()->has('error'))
<div class="row">
	@include('partials/error', ['type' => 'danger', 'message' => session('error')])
</div>
@endif	

<div class="box">
    <a href={{$approvalUrl}}>click to proceed payment</a>
</div>

@stop

