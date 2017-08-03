          @foreach ($posts as $post)
            <tr>
              <td class="text-primary"><strong>{{ $post->title }}</strong></td>
              <td>{{ $post->created_at }}</td> 
              <td>{!! Form::checkbox('active', $post->id, $post->active) !!}</td>
               <td>
              {!! Form::open(['method' => 'post', 'url' => 'service/config/'.$post->id]) !!}
                {!! Form::submit(trans('back/service.config')) !!}
              {!! Form::close() !!}
              </td>
              <td>
              {!! Form::open(['method' => 'get', 'url' => 'service/run/'.$post->id]) !!}
                {!! Form::submit(trans('back/service.start')) !!}
              {!! Form::close() !!}
              </td>
              <td>
              {!! Form::open(['method' => 'post', 'url' => 'service/destroy/'.$post->id]) !!}
                {!! Form::destroy(trans('back/service.destroy'), trans('back/service.destroy-warning')) !!}
              {!! Form::close() !!}
              </td>
            </tr>
          @endforeach