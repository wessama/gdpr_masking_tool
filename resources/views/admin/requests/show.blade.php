@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">
            {{ trans('global.show') }} {{ trans('cruds.request.title') }}
        </h4>
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.request.fields.id') }}
                        </th>
                        <td>
                            {{ substr($request->id, -12) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.request.fields.title') }}
                        </th>
                        <td>
                            {{ $request->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.request.fields.instance') }}
                        </th>
                        <td>
                            {{ $request->Instance->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.request.fields.latest_log') }}
                        </th>
                        <td>
                            @if($request->RequestLog)
                                <a href="{{ route('admin.logs.show', $request->RequestLog->first()->id) }}">
                                    {{ Carbon\Carbon::parse($request->RequestLog->first()->updated_at)->isoFormat('MMMM Do YYYY, h:mm:ss a') }}
                                </a>
                            @endif
                        </td>
                    </tr>                    
                    <tr>
                        <th>
                            {{ trans('cruds.request.fields.created_at') }}
                        </th>
                        <td>
                            {{ Carbon\Carbon::parse($request->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a') }}
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
