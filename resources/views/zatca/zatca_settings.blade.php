@extends('main.main')
@section('address')
    <h1>{{ trans('global.organizationSettings') }} </h1>
@stop
@section('container')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('flash::message')

    {{--    @if (session()->has('success')) --}}

    {{--        <p class="alert alert-success">{{ session('success') }}</p> --}}

    {{--    @endif --}}

    {{--    @if (session()->has('error')) --}}
    {{--        <p class="alert alert-danger">{{ session('error') }}</p> --}}

    {{--    @endif --}}

    <div class="ibox ibox-content">

        <div class="row">


            <div class="col-lg-8">

                <form method="post" enctype="multipart/form-data" action="{{ route('zatca.zatca-settings.update') }}">

                    {{ csrf_field() }}

                    <input type="hidden" name="zatca_setting_id" value="{{ $zatcaSetting->id }}">

                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.postal_number') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <input id="postal_number" class="form-control" type="text" name="postal_number"
                                   value="{{ $zatcaSetting->postal_number }}">
                        </div>
                    </div>


                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.egs_serial_number') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <input id="egs_serial_number" class="form-control" type="text" name="egs_serial_number"
                                   value="{{ $zatcaSetting->egs_serial_number }}">
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.business_category') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <input id="business_category" class="form-control" type="text" name="business_category"
                                   value="{{ $zatcaSetting->business_category }}">
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.common_name') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <input id="common_name" class="form-control" type="text" name="common_name"
                                   value="{{ $zatcaSetting->common_name }}">
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.organization_unit_name') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <input id="organization_unit_name" class="form-control" type="text"
                                   name="organization_unit_name"
                                   value="{{ $zatcaSetting->organization_unit_name }}">
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.registered_address') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <input id="registered_address" class="form-control" type="text" name="registered_address"
                                   value="{{ $zatcaSetting->registered_address }}">
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.otp') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <input id="otp" class="form-control" type="text" name="otp"
                                   value="{{ $zatcaSetting->otp }}">
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.invoice_type') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <select id="invoice_type" name="invoice_type" class="form-control chosen-select"
                                    required>
                                <option value="1100" @if ($zatcaSetting->invoice_type == '1100') selected @endif>
                                    1100
                                </option>
                                <option value="1000" @if ($zatcaSetting->invoice_type == '1000') selected @endif>
                                    1000
                                </option>
                                <option value="0100" @if ($zatcaSetting->invoice_type == '0100') selected @endif>
                                    0100
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.is_production') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <select id="is_production" name="is_production" class="form-control chosen-select"
                                    required>
                                <option value="0" @if ($zatcaSetting->is_production == 0) selected @endif>
                                    No
                                </option>
                                <option value="1" @if ($zatcaSetting->is_production == 2) selected @endif>
                                    Yes
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.cnf') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <textarea rows="8" id="cnf" class="form-control" type="text"
                                      name="cnf">{{ $zatcaSetting->cnf }}</textarea>
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.private_key') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <textarea rows="8" id="private_key" class="form-control" type="text"
                                      name="private_key">{{ $zatcaSetting->private_key }}</textarea>
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.public_key') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <textarea rows="8" id="public_key" class="form-control" type="text"
                                      name="public_key">{{ $zatcaSetting->public_key }}</textarea>
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.csr_request') }}</h4>
                        </label>
                        <div class="col-md-6">
                             <textarea rows="8" id="csr_request" class="form-control" type="text"
                                       name="csr_request">{{ $zatcaSetting->csr_request }}</textarea>
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.certificate') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <textarea rows="8" id="certificate" class="form-control" type="text"
                                      name="certificate">{{ $zatcaSetting->certificate }}</textarea>
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.secret') }}</h4>
                        </label>
                        <div class="col-md-6">

                            <textarea rows="8" id="secret" class="form-control" type="text"
                                      name="secret">{{ $zatcaSetting->secret }}</textarea>
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.csid') }}</h4>
                        </label>
                        <div class="col-md-6">
                             <textarea rows="8" id="csid" class="form-control" type="text"
                                       name="csid">{{ $zatcaSetting->csid }}</textarea>
                        </div>
                    </div>

                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.production_certificate') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <textarea rows="8" id="production_certificate" class="form-control" type="text"
                                      name="production_certificate">{{ $zatcaSetting->production_certificate }}</textarea>
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.production_secret') }}</h4>
                        </label>
                        <div class="col-md-6">
                            <textarea rows="8" id="production_secret" class="form-control" type="text"
                                      name="production_secret">{{ $zatcaSetting->production_secret }}</textarea>
                        </div>
                    </div>
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-4">
                            <h4>{{ trans('global.production_csid') }}</h4>
                        </label>
                        <div class="col-md-6">
                             <textarea rows="8" id="production_csid" class="form-control" type="text"
                                       name="production_csid">{{ $zatcaSetting->production_csid }}</textarea>
                        </div>
                    </div>

                    <button class="col-md-2 btn btn-success" style="" id="save_print">{{ trans('global.save') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@stop
@section('scripts')

    <link href="{{ url('public/summernote/summernote-bs4.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ url('public/summernote/summernote-bs4.js') }}"></script>

    <script type="text/javascript">
        $('.summernote').summernote({
            height: 400
        });
    </script>
    <script>
        function resetAccount(target, title, text) {
            Swal.fire({
                title,
                text,
                showCancelButton: true,
                // confirmButtonColor: '#3085d6',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '@lang('global.delete')',
                cancelButtonText: '@lang('global.cancel')'
            }).then((result) => {
                if (result.value) {
                    document.getElementById(target).submit();
                }
            })
        }

        function upgradeAccount(target, title, text) {
            Swal.fire({
                title,
                text,
                showCancelButton: true,
                // confirmButtonColor: '#3085d6',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '@lang('global.ok')',
                cancelButtonText: '@lang('global.cancel')'
            }).then((result) => {
                if (result.value) {
                    document.getElementById(target).submit();
                }
            })
        }
    </script>
@endsection
