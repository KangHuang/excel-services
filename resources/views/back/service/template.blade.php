    @extends('back.template')

@section('head')

	{!! HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/default.css') !!}

@stop

@section('main')

	@include('back.partials.entete', ['title' => trans('back/service.dashboard'), 'icone' => 'pencil', 'fil' => link_to('service', trans('back/service.services')) . ' / ' . trans('back/service.creation')])

	<div class="col-sm-12">
		@yield('form')

		<div class="form-group checkbox pull-right">
			<label>
				{!! Form::checkbox('active') !!}
				{{ trans('back/service.published') }}
			</label>
		</div>

		{!! Form::control('text', 0, 'title', $errors, trans('back/service.title')) !!}                
		{!! Form::control('file', 0, 'filename', $errors, trans('back/service.file')) !!}                
		{!! Form::control('textarea', 0, 'description', $errors, trans('back/service.description')) !!}
                {!! Form::control('text', 0, 'price', $errors, trans('back/service.price')) !!}                
		{!! Form::control('text', 0, 'hid_fin', $errors, trans('back/service.hid_fin')) !!}                
		{!! Form::control('text', 0, 'hid_tec', $errors, trans('back/service.hid_tec')) !!}                

		{!! Form::submit(trans('front/form.send')) !!}

		{!! Form::close() !!}
	</div>

@stop

@section('scripts')

	{!! HTML::script('ckeditor/ckeditor.js') !!}
	
	<script>

	var config = {
		codeSnippet_theme: 'Monokai',
		language: '{{ config('app.locale') }}',
		height: 100,
		toolbarGroups: [
			{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
			{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
			{ name: 'links' },
			{ name: 'insert' },
			{ name: 'forms' },
			{ name: 'tools' },
			{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
			{ name: 'others' },
			//'/',
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
			{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
			{ name: 'styles' },
			{ name: 'colors' }
		]
	};


        config['height'] = 400;	
	CKEDITOR.replace( 'description', config);

	$("#title").keyup(function(){
			var str = sansAccent($(this).val());
			str = str.replace(/[^a-zA-Z0-9\s]/g,"");
			str = str.toLowerCase();
			str = str.replace(/\s/g,'-');
			$("#permalien").val(str);        
		});

  </script>

@stop