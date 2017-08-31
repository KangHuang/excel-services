@extends('back.template')

@section('main')

@include('back.partials.entete', ['title' => trans('back/service.dashboard'), 'icone' => 'pencil', 'fil' => link_to('service/order', trans('back/service.services')) . ' / ' . trans('back/service.creation')])


<h3>Permission configuration for service: <strong>{{ $post->title }}</strong></h3>

<table class="table">
    <thead>
        <tr>
            <th>
                Users
            </th>
            <th>
                Authorized
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
              <td class="text-primary"><strong>{{ $user->username }}</strong></td>
              <td>{!! Form::checkbox('relation', $user->id, $usersPermit->contains($user->id)!=false) !!}</td>
            </tr>
          @endforeach
    </tbody>
</table>

@stop


@section('scripts')

  <script>
    $(function() {
      // Relation gestion
      $(document).on('change', ':checkbox[name="relation"]', function() {
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var service_id = {{$post->id}};
        var token = '{{csrf_token()}}';
        $.ajax({
          url: '{{ url('postrelation') }}' + '/' + this.value,
          type: 'POST',
          data: "active=" + this.checked + "&service_id=" + service_id + "&_token=" + token,
        })
        .done(function() {
          $('.fa-spin').remove();
          $('input:checkbox[name="relation"]:hidden').show();
        })
        .fail(function() {
          $('.fa-spin').remove();
          chk = $('input:checkbox[name="relation"]:hidden');
          chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
          alert('{{ trans('back/service.fail') }}');
        });
      })
   });
  </script>

@stop