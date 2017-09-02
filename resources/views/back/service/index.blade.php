@extends('back.template')

@section('main')

  @include('back.partials.entete', ['title' => trans('back/service.dashboard') . link_to_route('service.create', trans('back/service.add'), [], ['class' => 'btn btn-info pull-right']), 'icone' => 'pencil', 'fil' => trans('back/service.services')])

	@if(session()->has('ok'))
            @include('partials/error', ['type' => 'success', 'message' => session('ok')])
	@endif
        
        @if(session()->has('error'))
             @include('partials/error', ['type' => 'danger', 'message' => session('error')])
	@endif

  <div class="row col-lg-12">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>
              {{ trans('back/service.title') }} 
            </th>
            <th>
              {{ trans('back/service.date') }}
            </th>
            <th>
              {{ trans('back/service.published') }}
            </th>
            <th>
              {{ trans('back/service.details') }}
            </th> 
              <th>
                {{ trans('back/service.permission') }}
              </th>            
              <th>
                {{ trans('back/service.preview') }}
              </th>
          </tr>
        </thead>
        <tbody>
          @include('back.service.table')
        </tbody>
      </table>
    </div>
  </div>

@stop

@section('scripts')

  <script>
    
    $(function() {
      // Active gestion
      $(document).on('change', ':checkbox[name="active"]', function() {
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var token = $('input[name="_token"]').val();
        $.ajax({
          url: '{{ url('postactive') }}' + '/' + this.value,
          type: 'PUT',
          data: "active=" + this.checked + "&_token=" + token
        })
        .done(function() {
          $('.fa-spin').remove();
          $('input:checkbox[name="active"]:hidden').show();
        })
        .fail(function() {
          $('.fa-spin').remove();
          chk = $('input:checkbox[name="active"]:hidden');
          chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
          alert('{{ trans('back/service.fail') }}');
        });
      });
     })

  </script>

@stop
