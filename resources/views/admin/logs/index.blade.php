@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">
            {{ trans('cruds.log.title_singular') }} {{ trans('global.list') }}
        </h4>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-striped table-hover datatable datatable-log">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.log.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.log.fields.instance_title') }}
                        </th>                        
                        <th>
                            {{ trans('cruds.log.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.log.fields.status') }}
                        </th>                        
                        <th>
                            {{ trans('cruds.log.fields.created_at') }}
                        </th>                        
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $key => $log)
                        <tr data-entry-id="{{ $log->id }}">
                            <td>

                            </td>
                            <td>
                                {{ substr($log->id, -12) ?? '' }}
                            </td>
                            <td>
                                {{ $log->request->instance->name ?? '' }}
                            </td>                            
                            <td>
                                <a href="{{ route('admin.logs.show', $log->id) }}">
                                    {{ __('Show') }}
                                </a>
                            </td>
                            <td>
                                @if($log->Request->is_processed->value() == App\RequestStatus::FAILED)
                                    <a href="{{ route('admin.requests.show', $log->Request->id) }}">
                                        <i class="fa-fw fas fa-exclamation-triangle error">

                                        </i>
                                    </a>
                                @else
                                    <a href="{{ route('admin.requests.show', $log->Request->id) }}">
                                        <i class="fa-fw fas fa-check-circle completed">

                                        </i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                {{ Carbon\Carbon::parse($log->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a') }}
                            </td>                            
                            <td>
                                @can('log_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.logs.show', $log->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('log_delete')
                                    <form action="{{ route('admin.logs.destroy', $log->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('log_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.logs.massDestroy') }}",
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
  $('.datatable-log:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
