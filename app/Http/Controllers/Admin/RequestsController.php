<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\JoomlaAppHelper;
use App\Helpers\MoodleAppHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRTBFRequest;
use App\Http\Requests\StoreMassRTBFRequest;
use App\Instance;
use App\Request as Model;
use App\RequestLog;
use App\RequestStatus;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RequestsController extends Controller
{
    /**
     * Resolves to the relevant API helper
     * @var IWebservice
     */
    public $app;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $requests = Model::all();

        return view('admin.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Instance $model)
    {
        abort_if(Gate::denies('request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.requests.create', ['instances' => $model->all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_alt(Instance $model)
    {
        abort_if(Gate::denies('request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.requests.create_alt', ['instances' => $model->all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMassRTBFRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMassRTBFRequest $request)
    {
        abort_if(Gate::denies('request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $messages['status'] = trans('global.successMessage', ['request' => trans('global.bulk_rtbf')]);

        $instance = Instance::find($request->instance_id);

        if ($instance->InstanceType->name == "CLMS") {
            $this->app = new MoodleAppHelper();
        } else {
            $this->app = new JoomlaAppHelper();
        }

        if ($request->hasFile('file') && empty($request->mass_request)) {

            $file = $request->file('file');

            // Import the file. Further operations are executed within
            $import_result = $this->massImport($file, $instance);

            if (!$import_result['status']) {

                $messages['warning'] = $import_result['message'] ?? '';

                unset($messages['status']);
            }

        } elseif ($request->mass_request) {

            $massRequest = $this->handleMassRequest($instance);

            if ($massRequest->code != Response::HTTP_OK) {
                $messages['warning'] = trans('global.mass_request_error', ['ex' => $massRequest->message]);

                unset($messages['status']);
            }
        } else {

            $bulkEmails   = [];
            $bulkEmails[] = $request->email;

            $requestProcess = $this->handleRequest($bulkEmails, $instance);

            if ($requestProcess == false) {
                $messages['warning'] = trans('global.warningMessage', ['reason' => trans('global.error_servicing_req'), 'count' => count($bulkEmails)]);

                unset($messages['status']);
            }
        }

        return redirect()->route('admin.requests.create')->with($messages);
    }

    /**
     * Asynchronous AJAX retry request
     * @param  Illuminate\Http\Request $request Request object
     * @return JSON                             Result of the operation
     */
    public function retry(Request $request)
    {
        abort_if(Gate::denies('request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validator = Validator::make($request->input(), array(
            'id' => 'required',
        ));

        if ($validator->fails()) {
            return response()->json([
                'error'    => true,
                'messages' => $validator->errors(),
            ], Response::HTTP_OK);
        }

        $model = Model::find($request->id);

        $instance = $model->Instance;

        if ($instance->InstanceType->name == "CLMS") {
            $this->app = new MoodleAppHelper();
        } else {
            $this->app = new JoomlaAppHelper();
        }

        $bulkEmails   = [];
        $bulkEmails[] = $model->email;

        $requestProcess = $this->handleRequest($bulkEmails, $instance);

        $updatedModel = Model::find($request->id);

        $updatedStatus = $updatedModel->is_processed->value();

        if ($requestProcess == false || $updatedStatus !== RequestStatus::COMPLETED) {
            return response()->json([
                'success'  => false,
                'messages' => trans('global.warningMessage', ['reason' => trans('global.error_servicing_req'), 'count' => count($bulkEmails)]),
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success'  => true,
                'messages' => trans('global.successMessage', ['request' => trans('global.bulk_rtbf')]),
            ], Response::HTTP_OK);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Model $request)
    {
        abort_if(Gate::denies('request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.requests.show', compact('request'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $request)
    {
        abort_if(Gate::denies('request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->delete();

        return back();
    }

    public function massDestroy(MassDestroyRTBFRequest $request)
    {
        Model::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }

    /**
     * Import CSV file data
     * @param  file                                     $file           File to be imported
     * @param  Illuminate\Database\Eloquent\Model       $instance       Instance object
     * @return array                                    $import_result An array holding results of the import operation
     */
    public function massImport($file, Instance $instance)
    {
        $import_error = 0;
        // Data to be imported
        $importData = $this->processFile($file);
        // Check for correct format; headers contain one entry for e-mail address
        $columnIndex = $this->validateData($importData);

        // If no corresponding columns are found, return with validation error
        if (!$columnIndex) {
            $import_result['status']  = false;
            $import_result['message'] = trans('global.errorMessage', ['operation' => 'importing CSV', 'reason' => 'incorrect format']);
            return $import_result;
        }

        if (count($importData) > 100) {
            $import_result['status']  = false;
            $import_result['message'] = trans('global.errorMessage', ['operation' => 'importing CSV', 'reason' => 'cannot send more than 100 requests at once']);
            return $import_result;
        }

        // Remove headers
        array_shift($importData);

        $bulkEmails = [];

        foreach ($importData as $data) {

            $bulkEmails[] = $data[$columnIndex];
        }

        $requestProcess = $this->handleRequest($bulkEmails, $instance);

        $import_result['status']  = $requestProcess == true ? true : false;
        $import_result['message'] = trans('global.warningMessage', ['reason' => trans('global.error_servicing_req'), 'count' => 'one or more']);

        return $import_result;
    }

    /**
     * Import data from CSV and convert to array
     *
     * @param  file $file
     * @return array
     */
    public function processFile($file)
    {
        // File Details
        $filename = time() . $file->getClientOriginalName();

        // File upload location - must use symbolic link in production
        $location = 'public';

        // Upload file
        $file->storeAs($location, $filename);

        $filepath = public_path("storage/" . $filename);

        // Reading file
        $file = fopen($filepath, "r");

        $importData_arr = array();
        $i              = 0;

        while (($filedata = fgetcsv($file, 1000, ",")) !== false) {
            $num = count($filedata);
            for ($c = 0; $c < $num; $c++) {
                $importData_arr[$i][] = $filedata[$c];
            }
            $i++;
        }
        fclose($file);

        // Delete the file
        unlink($filepath);

        return $importData_arr;
    }

    /**
     * Check if headers contain at least one entry for e-mail address
     *
     * @param  array $importData
     * @return int
     */
    public function validateData($importData)
    {
        $columnIndex = false;
        foreach ($importData[0] as $key => $value) {
            $value = strtolower($value);

            if (strpos($value, 'email') !== false || strpos($value, 'e-mail') !== false) {
                $columnIndex = $key;
            }
        }

        if (!$columnIndex) {
            return false;
        }

        return $columnIndex;
    }

    /**
     * Handle an RTBF for each Request and Instance
     * @param  array                                    $bulkEmails  Emails to be masked
     * @param  Illuminate\Database\Eloquent\Model       $instance    Current Instance object
     * @return boolean
     */
    public function handleRequest($bulkEmails, Instance $instance)
    {
        $payload = $this->app->prepareAPIRequest($instance, $bulkEmails);

        if ($payload->code == Response::HTTP_BAD_REQUEST) {

            return false;

        } else {

            foreach ($payload->request_results as $result) {

                $request              = new Model;
                $request->instance_id = $instance->id;

                if ($result->code == RESPONSE::HTTP_OK) {

                    $request->is_processed = RequestStatus::COMPLETED;

                    $request = Model::updateOrCreate(['email' => $result->email], $request->toArray());

                    RequestLog::Log($request->id, $result->code, $result->message);
                } else {

                    $request->is_processed = RequestStatus::FAILED;

                    $request = Model::updateOrCreate(['email' => $result->email], $request->toArray());

                    RequestLog::Log($request->id, $result->code, $result->message);
                }
            }

            return true;
        }
    }

    /**
     * Handle a mass data masking request
     * @param  Illuminate\Database\Eloquent\Model $instance Instance model
     * @return stdClass
     */
    public function handleMassRequest($instance)
    {
        $payload = $this->app->prepareAPIRequest($instance, "", true);

        $model              = new Model;
        $model->email       = "MASS_REQUEST";
        $model->instance_id = $instance->id;

        if ($payload->code != Response::HTTP_OK) {

            $model->is_processed = RequestStatus::FAILED;
        }

        $model->is_processed = RequestStatus::COMPLETED;

        $entity = Model::create($model->toArray());

        if ($payload->request_results) {
            $log_message = json_encode($payload->request_results);
        } else {
            $log_message = $payload->message;
        }

        RequestLog::Log($entity->id, $payload->code, $log_message);

        return $payload;
    }
}
