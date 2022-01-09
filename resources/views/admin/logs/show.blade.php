@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">
            {{ trans('global.show') }} {{ trans('cruds.log.title') }}
        </h4>
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.log.fields.id') }}
                        </th>
                        <td>
                            {{ substr($log->id, -12) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.log.fields.title') }}
                        </th>
                        <td>
                            {{ $log->log }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.log.fields.instance_title') }}
                        </th>
                        <td>
                            {{ $log->request->instance->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.log.fields.updated_at') }}
                        </th>
                        <td>
                            {{ Carbon\Carbon::parse($log->updated_at)->isoFormat('MMMM Do YYYY, h:mm:ss a') }}
                        </td>
                    </tr>                     
                    <tr>
                        <th>
                            {{ trans('cruds.log.fields.created_at') }}
                        </th>
                        <td>
                            {{ Carbon\Carbon::parse($log->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a') }}
                        </td>
                    </tr> 
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>

        <nav class="mb-3">
            <div class="nav nav-tabs">

            </div>
        </nav>
        <div class="tab-content">

        </div>
    </div>
</div>
@endsection
