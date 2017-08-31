@extends('back.service.template')

@section('form')
	{!! Form::model($service, ['route' => ['service.update', $service->id], 'method' => 'put', 'class' => 'form-horizontal panel']) !!}
        {!! Form::hidden('service_id',$service->id)!!}
        @php 
            $isEdit = 1; 
        @endphp
@stop
