@extends('back.service.template')

@section('form')
	{!! Form::open(['url' => 'service/create', 'method' => 'post', 'class' => 'form-horizontal panel', 'files' => true]) !!}

@stop
