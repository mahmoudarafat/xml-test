
        <div class="row">
            <div class="col-lg-12 ibox float-e-margins ibox-content text-center p-md">

                <form role="form" method="post" enctype="multipart/form-data" action="{{ route('zatca.renew-certificate.store')  }}">

                    {{ csrf_field() }}
                    <div class=" col-md-12 form-group float-e-margins">
                        <label class="font-normal col-md-2"><h4>{{trans('global.OTP')}}</h4></label>
                        <div class="col-md-6">
                            <input id="name" class="form-control" type="text"
                                   name="otp">
                        </div>
                    </div>





                    <button class="col-md-2 btn  btn-success" type="submit" style=""
                            id="save_print">{{trans('global.renew')}}
                    </button>
                </form>

            </div>

        </div>
