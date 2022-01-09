<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RequestLog;
use App\Http\Requests\MassDestroyLogRequest;

class RequestLogController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $logs = RequestLog::all();

        return view('admin.logs.index', compact('logs'));
    }

    public function show(RequestLog $log)
    {
        abort_if(Gate::denies('log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.logs.show', compact('log'));
    }    

    public function destroy(RequestLog $log)
    {
        abort_if(Gate::denies('log_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $log->delete();

        return back();

    }

    public function massDestroy(MassDestroyLogRequest $request)
    {
        RequestLog::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }
}
