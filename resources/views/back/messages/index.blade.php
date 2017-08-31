@extends('back.template')

@section('head')

	<style type="text/css">
		.table { margin-bottom: 0; }
		.panel-heading { padding: 0 15px; }
	</style>

@stop

@section('main')

 <!-- Entête de page -->
  @include('back.partials.entete', ['title' => trans('back/messages.dashboard'), 'icone' => 'envelope', 'fil' => trans('back/messages.messages')])

  @foreach ($messages as $message)
		<div class="panel {!! $message->seen? 'panel-default' : 'panel-warning' !!}">
		  <div class="panel-heading">
				<table class="table">
					<thead>
						<tr>
							<th class="col-lg-1">{{ trans('back/messages.name') }}</th>
							<th class="col-lg-1">{{ trans('back/messages.email') }}</th>
							<th class="col-lg-1">{{ trans('back/messages.date') }}</th>
							<th class="col-lg-1"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="text-primary"><strong>{{ $message->name }}</strong></td>
							<td>{!! HTML::mailto($message->email, $message->email) !!}</a></td>
							<td>{{ $message->created_at }}</td>
							<td>
							{!! Form::open(['method' => 'DELETE', 'route' => ['contact.destroy', $message->id]]) !!}
								{!! Form::destroy(trans('back/messages.destroy'), trans('back/messages.destroy-warning'), 'btn-xs') !!}
							{!! Form::close() !!}
							</td>
						</tr>
					</tbody>
				</table>	
			</div>
			<div class="panel-body">
				{{ $message->text }}
			</div>
		</div>
	@endforeach

@stop

@section('scripts')

@stop