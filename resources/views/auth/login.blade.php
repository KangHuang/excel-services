@extends('front.template')

@section('main')
	<div class="row">
		<div class="box">
			<div class="col-lg-12">
				@if(session()->has('error'))
					@include('partials/error', ['type' => 'danger', 'message' => session('error')])
				@endif	
				<hr>	
				<h2 class="intro-text text-center">{{ trans('front/login.connection') }}</h2>
				<hr>
				<p>{{ trans('front/login.text') }}</p>				
				
				{!! Form::open(['url' => 'auth/login', 'method' => 'post', 'role' => 'form']) !!}	
				
				<div class="row">
                                    {!! Form::control('text', 6, 'log', $errors, trans('front/login.log')) !!}
                                    {!! Form::control('password', 6, 'password', $errors, trans('front/login.password')) !!}
                                </div>
                                
                                <div class="row">
                                  {!! Form::label('email', trans('front/register.role'), ['class'=>'col-lg-12'])!!}
                                </div>
                                 <div class="row col-lg-12">
                                           {!! Form::select('role', array('use'=>'Service user','pro'=>'Service provider'))!!}
                                 </div>
				{!! Form::check('memory', trans('front/login.remind')) !!}
                                <center>{!! Form::submit(trans('front/form.send'), ['col-lg-12']) !!}</center>
					{!! Form::text('address', '', ['class' => 'hpet']) !!}		  
                                <div class="col-lg-12">					
                                    {!! link_to('password/email', trans('front/login.forget')) !!}
                                </div>

			
				
				{!! Form::close() !!}

				<div class="text-center">
					<hr>
						<h2 class="intro-text text-center">{{ trans('front/login.register') }}</h2>
					<hr>	
					<p>{{ trans('front/login.register-info') }}</p>
					{!! link_to('auth/register', trans('front/login.registering'), ['class' => 'btn btn-default']) !!}
				</div>

			</div>
		</div>
	</div>
@stop

