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
                            {{ trans('cruds.request.fields.email') }}
                        </label>
                        <div class="col-sm-7">
                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                <input aria-required="true" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="input-name" name="email" placeholder="{{ trans('cruds.request.fields.email_placeholder') }}" required="true" type="text"/>
                                @if ($errors->has('email'))
                                <span class="error text-danger" for="input-name" id="name-error">
                                    {{ $errors->first('email') }}
                                </span>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.request.fields.email_helper') }}
                                </p>
                            </div>
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
