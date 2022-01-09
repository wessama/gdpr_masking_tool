@extends('layouts.admin')
@section('content')
@can('request_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.requests.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.request.title_plural') }}
            </a>
            <a class="btn btn-success" href="{{ route("admin.requests.createAlt") }}">
                {{ trans('global.add') }} {{ trans('cruds.request.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">
            {{ trans('cruds.request.title_singular') }} {{ trans('global.list') }}
        </h4>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-striped table-hover datatable datatable-request">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.request.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.request.fields.instance') }}
                        </th>
                        <th>
                            {{ trans('cruds.request.fields.email') }}
                        </th>
                        <th>
                            {{ trans('cruds.request.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.request.fields.updated_at') }}
                        </th>                                                                                                
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $key => $request)
                        <tr data-entry-id="{{ $request->id }}">
                            <td>

                            </td>
                            <td>
                                {{ substr($request->id, -12) ?? '' }}
                            </td>
                            <td>
                                {{ $request->instance->name ?? '' }}
                            </td>
                            <td>
                                {{ $request->email ?? '' }}
                            </td>
                            <td>
                                @can('request_edit')
                                    @if($request->is_processed->value() == App\RequestStatus::COMPLETED)
                                        <i class="fa-fw fas fa-check-circle completed">

                                        </i>
                                    @elseif($request->is_processed->value() == App\RequestStatus::PENDING)
                                        <i class="fa-fw fas fa-exclamation-triangle failed">
                                            
                                        </i>                                        
                                    @else
                                        <a href="{{ route('admin.logs.show', $request->RequestLog->first()->id) }}">
                                            <i class="fa-fw fas fa-exclamation-triangle error">

                                            </i>
                                        </a>
                                    @endif
                                @endcan
                            </td>  
                            <td>
                                {{ $request->updated_at->diffForHumans() }}
                            </td>                                                       
                            <td>
                                @can('request_edit')
                                    @if($request->is_processed->value() == App\RequestStatus::FAILED)
                                        <a class="btn btn-xs btn-warning btn-retry">
                                            {{ trans('global.retry') }}
                                        </a>
                                    @endif
                                @endcan 

                                @can('request_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.requests.show', $request->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('request_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.requests.edit', $request->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan                               

                                @can('request_delete')
                                    <form action="{{ route('admin.requests.destroy', $request->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('request_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.requests.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  $('.datatable-request:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

@can('request_edit')
    $(".btn-retry").click(function() {
        var row = $(this).closest('tr');
        var requestId = row.attr('data-entry-id');
        var icon = row.find('.error');
        icon.removeClass('fa-fw fas fa-exclamation-triangle error');
        icon.addClass('fa fa-spinner fa-spin');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/admin/requests/retry',
            type: 'POST',
            data: {
                id: requestId,
            },            
            success: function(data) {
                    if(data.success == false) {
                        // show this error message somewhere on screen
                        icon.removeClass('fa fa-spinner fa-spin');
                        icon.addClass('fa-fw fas fa-exclamation-triangle error');
                    } else {
                        (row.find('.btn-retry')).hide();
                        icon.removeClass('fa fa-spinner fa-spin');
                        icon.addClass('fa-fw fas fa-check-circle completed');
                    }
                }
            });
        });
@endcan

</script>
@endsection
