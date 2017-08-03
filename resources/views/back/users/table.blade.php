	@foreach ($users as $user)
		<tr {!! !$user->seen? 'class="warning"' : '' !!}>
			<td class="text-primary"><strong>{{ $user->username }}</strong></td>
			<td>{{ $user->role->title }}</td>
			<td>
				{!! Form::open(['method' => 'get', 'url' => 'user/destroy/'.$user->id]) !!}
				{!! Form::destroy(trans('back/users.destroy'), trans('back/users.destroy-warning')) !!}
				{!! Form::close() !!}
			</td>
		</tr>
	@endforeach