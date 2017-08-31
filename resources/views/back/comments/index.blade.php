@extends('back.template')

@section('head')

	<style type="text/css">
		.table { margin-bottom: 0; }
		.panel-heading { padding: 0 15px; }
		.border-red {
			border-style: solid;
			border-width: 5px;
			border-color: red !important;
		}
	</style>

@stop

@section('main')

  @include('back.partials.entete', ['title' => trans('back/comments.dashboard'), 'icone' => 'comment', 'fil' => trans('back/comments.comments')])

	<div class="row col-lg-12">
		<div class="pull-right">{!! $links !!}</div>
	</div>

	<div class="row col-lg-12">
		@foreach ($comments as $comment)
			<div class="panel {!! $comment->seen? 'panel-default' : 'panel-warning' !!}">
				<div class="panel-heading">
					<table class="table">
						<thead>
							<tr>
								<th class="col-lg-3">{{ trans('back/comments.author') }}</th>
								<th class="col-lg-3">{{ trans('back/comments.date') }}</th>
								<th class="col-lg-3">{{ trans('back/comments.service') }}</th>
								<th class="col-lg-1"></th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td class="text-primary"><strong>{{ $comment->user->username }}</strong></td>
							<td>{{ $comment->created_at }}</td>
							<td>{{ $comment->service->title }}</td>
							<td>
									{!! Form::open(['method' => 'DELETE', 'route' => ['comment.destroy', $comment->id]]) !!}
										{!! Form::destroy(trans('back/comments.destroy'), trans('back/comments.destroy-warning'), 'btn-xs') !!}
									{!! Form::close() !!}
							</td>
						</tr>
			  		</tbody>
					</table>	
				</div>
				<div class="panel-body">
					{!! $comment->content !!}
				</div> 
			</div>
		@endforeach
	</div>

  <div class="row col-lg-12">
    <div class="pull-right">{!! $links !!}</div>
  </div>

@stop
