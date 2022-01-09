@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">
            {{ trans('global.create') }} {{ trans('cruds.request.title') }}
        </h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.requests.store') }}" autocomplete="off" class="form-horizontal" enctype="multipart/form-data" method="post">
            @csrf
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-success">
                                <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                    <i class="material-icons">
                                        close
                                    </i>
                                </button>
                                <span>
                                    {{ session('status') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                @if (session('warning'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-warning">
                                <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                    <i class="material-icons">
                                        close
                                    </i>
                                </button>
                                <span>
                                    {{ session('warning') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <label class="col-sm-2 col-form-label">
                            {{ trans('cruds.request.fields.instance') }}
                        </label>
                        <div class="col-sm-7">
                            <div class="form-group{{ $errors->has('instance') ? ' has-danger' : '' }}">
                                <select class="form-control form-control-md" name="instance_id">
                                    <option>
                                        Choose...
                                    </option>
                                    @foreach($instances as $instance)
                                    <option value="{{ $instance->id }}">
                                        {{ $instance->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('instance'))
                                <span class="error text-danger" for="input-name" id="name-error">
                                    {{ $errors->first('instance') }}
                                </span>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.request.fields.instance_helper') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">
                            {{ trans('cruds.request.fields.mass_request') }}
                        </label>
                        <div class="col-sm-7">
                            <div class="form-group{{ $errors->has('mass_request') ? ' has-danger' : '' }}">
                                <div class="form-check mr-auto mt-3">
                                    <label class="form-check-label">
                                        <input class="form-check-input" id="mass_request" name="mass_request" type="checkbox">
                                            Yes
                                            <span class="form-check-sign">
                                                <span class="check">
                                                </span>
                                            </span>
                                        </input>
                                    </label>
                                </div>
                                @if ($errors->has('mass_request'))
                                <span class="error text-danger" for="input-name" id="name-error">
                                    {{ $errors->first('mass_request') }}
                                </span>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.request.fields.mass_request_helper') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">
                            {{ trans('cruds.request.fields.file') }}
                        </label>
                        <div class="col-sm-7">
                            <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                <div class="fileinput-preview fileinput-exists thumbnail img-circle img-raised">
                                </div>
                                <div>
                                    <span class="btn btn-raised btn-round btn-default btn-file">
                                        <input id="fileinput" name="file" type="file"/>
                                    </span>
                                    <br/>
                                </div>
                            </div>
                            @if ($errors->has('file'))
                            <span class="error text-danger" for="input-name" id="name-error">
                                {{ $errors->first('file') }}
                            </span>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.request.fields.file_helper') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ml-auto mr-auto">
                <button class="btn btn-primary" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('mass_request').onchange = function() {
    document.getElementById('fileinput').disabled = this.checked;
};
</script>
@endsection
