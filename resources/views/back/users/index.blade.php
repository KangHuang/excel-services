@extends('back.template')

@section('head')

<style type="text/css">
  
  .badge {
    padding: 1px 8px 1px;
    background-color: #aaa !important;
  }

</style>

@stop

@section('main')

  @include('back.partials.entete', ['title' => trans('back/users.dashboard') . link_to_route('user.create', trans('back/users.add'), [], ['class' => 'btn btn-info pull-right']), 'icone' => 'user', 'fil' => trans('back/users.users')])
 


	@if(session()->has('ok'))
            @include('partials/error', ['type' => 'success', 'message' => session('ok')])
	@endif
        
        @if(session()->has('error'))
            @include('partials/error', ['type' => 'danger', 'message' => session('ok')])
	@endif

  <div class="pull-right link">{!! $links !!}</div>

	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>{{ trans('back/users.name') }}</th>
					<th>{{ trans('back/users.role') }}</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			  @include('back.users.table')
      </tbody>
		</table>
	</div>

	<div class="pull-right link">{!! $links !!}</div>

@stop