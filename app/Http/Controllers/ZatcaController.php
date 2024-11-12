<?php

namespace App\Http\Controllers;

use App\Http\Requests\Zatca\renewCertificateRequest;
use App\Models\ZatcaSetting;
use App\Services\Zatca\ReportInvoiceToZatca;
use App\Services\Zatca\ZatcaService;
use Illuminate\Http\Request;
use App\Services\Zatca\OnBoarding;
use App\Models\Entities\SaleProcess;

class ZatcaController extends Controller
{

    public function getZatcaSettings(Request $request)
    {

        $zatcaSetting = ZatcaSetting::first();

        if (!$zatcaSetting) {


            $zatcaSetting = ZatcaSetting::create(
                [
                    'postal_number' => 'EX :23613',
                    'egs_serial_number' => 'EX: 1-ABC|2-ABC|3-ABC',
                    'business_category' => 'EX: Containers',
                    'common_name' => 'EX: Albadr Container',
                    'organization_unit_name' => 'EX: Albadr Container',
                    'registered_address' => 'Ex: Al Kournish Road',
                    'otp' => 'Ex: 052350',
                    'cnf' => 'Ex: DQogICAgICAgIG9pZF9zZWN0aW9uID0gT0lEcw0KICAgICAgICBbIE9JRHMgXQ0KICAgICAgICBjZXJ0aWZpY2F0ZVRlbXBsYXRlTmFtZT0gMS4zLjYuMS40LjEuMzExLjIwLjINCg0KICAgICAgICBbIHJlcSBdDQogICAgICAgIGRlZmF1bHRfYml0cyAJPSAyMDQ4DQogICAgICAgIGVtYWlsQWRkcmVzcyAJPSBtLmJhcm91ZGlAaW5rYW5ka29kZS5jb20NCiAgICAgICAgcmVxX2V4dGVuc2lvbnMJPSB2M19yZXENCiAgICAgICAgeDUwOV9leHRlbnNpb25zIAk9IHYzX2NhDQogICAgICAgIHByb21wdCA9IG5vDQogICAgICAgIGRlZmF1bHRfbWQgPSBzaGEyNTYNCiAgICAgICAgcmVxX2V4dGVuc2lvbnMgPSByZXFfZXh0DQogICAgICAgIGRpc3Rpbmd1aXNoZWRfbmFtZSA9IGRuDQoNCiAgICAgICAgWyB2M19yZXEgXQ0KICAgICAgICBiYXNpY0NvbnN0cmFpbnRzID0gQ0E6RkFMU0UNCiAgICAgICAga2V5VXNhZ2UgPSBkaWdpdGFsU2lnbmF0dXJlLCBub25SZXB1ZGlhdGlvbiwga2V5RW5jaXBoZXJtZW50DQoNCiAgICAgICAgW3JlcV9leHRdDQogICAgICAgIGNlcnRpZmljYXRlVGVtcGxhdGVOYW1lID0gQVNOMTpQUklOVEFCTEVTVFJJTkc6VFNUWkFUQ0EtQ29kZS1TaWduaW5nDQogICAgICAgIHN1YmplY3RBbHROYW1lID0gZGlyTmFtZTphbHRfbmFtZXMNCg0KICAgICAgICBbIHYzX2NhIF0NCg0KDQogICAgICAgICMgRXh0ZW5zaW9ucyBmb3IgYSB0eXBpY2FsIENBDQoNCg0KICAgICAgICAjIFBLSVggcmVjb21tZW5kYXRpb24uDQoNCiAgICAgICAgc3ViamVjdEtleUlkZW50aWZpZXI9aGFzaA0KDQogICAgICAgIGF1dGhvcml0eUtleUlkZW50aWZpZXI9a2V5aWQ6YWx3YXlzLGlzc3VlcjphbHdheXMNCiAgICAgICAgWyBkbiBdDQogICAgICAgIENOID1SZWQgU2VhIEZpbG0gICAJCQkJICAgICAgICAgICAgICAgICAgICAjIENvbW1vbiBOYW1lDQogICAgICAgIEM9U0EJCQkJCQkJICAgICAgICAgICAgIyBDb3VudHJ5IENvZGUgZS5nIFNBDQogICAgICAgIE9VPVJlZCBTZWEgRmlsbSAJCQkJCQkJIyBPcmdhbml6YXRpb24gVW5pdCBOYW1lDQogICAgICAgIE89UmVkIFNlYSBGaWxtIAkJCQkJCQkgICAgIyBPcmdhbml6YXRpb24gTmFtZQ0KDQogICAgICAgIFthbHRfbmFtZXNdDQogICAgICAgIFNOPTEtQUJDfDItQUJDfDMtQUJDCQkJCSAgICAgICAgICAgICAgICAjIEVHUyBTZXJpYWwgTnVtYmVyIDEtQUJDfDItUFFSfDMtWFlaDQogICAgICAgIFVJRD0zMTA0NjAzMzA2MDAwMDMJCQkJCQkgICAgICAgICAgICAgICAgICAgICMgT3JnYW5pemF0aW9uIElkZW50aWZpZXIgKFZBVCBOdW1iZXIpDQogICAgICAgIHRpdGxlPTExMDAJCQkJCQkJCSAgICAjIEludm9pY2UgVHlwZQ0KICAgICAgICByZWdpc3RlcmVkQWRkcmVzcz1BbCBLb3VybmlzaCBSb2FkICAJIAkJCSMgQWRkcmVzcw0KICAgICAgICBidXNpbmVzc0NhdGVnb3J5PUZpbG0gRmVzdGl2YWxzCQkJCQkjIEJ1c2luZXNzIENhdGVnb3J5',
                ]
            );
        }
        return view('zatca.zatca_settings', compact('zatcaSetting'));


    }

    public function updateZatcaSetting(Request $request)
    {


        $zatcaSetting = ZatcaSetting::first();
        $zatcaSetting->forceFill($request->except(['_token', 'zatca_setting_id']));
        $zatcaSetting->save();
        return back()->with(['success' => trans('global.saved_successfully')]);
    }

    public function zatcaSubmit(Request $request)
    {
        dd(12584);

    }



    public function submitInvoice(Request $request,$type, $targetId, ZatcaService $zatcaService)
    {


        $target = $type != 'sales' ? Rent::findOrFail($targetId) : SaleProcess::with('salesDetail','client')->findOrFail($targetId);



        if (!$target) {
            return back()->with(['error' => 'لا يوجد فاتورة']);
        }

        $zatcaSettings = $zatcaService->getZatcaSettings();
        if (!$zatcaSettings) {
            flash('please set zatca Setting First')->error();
            return back();
        }

        if ($zatcaSettings && $zatcaSettings->is_production == 0) {
            if (!$zatcaSettings->secret || !$zatcaSettings->certificate) {
                return redirect()->route('zatca.renew-certificate');
            }
        }
        if (($zatcaSettings && $zatcaSettings->is_production == 1)) {
            if (!$zatcaSettings->production_secret || !$zatcaSettings->production_certificate) {
                return redirect()->route('zatca.renew-certificate');
            }
        }
        $target->invoice_type = $type;

        $invoiceObject = $zatcaService->invoiceData($target);

//        dd($invoiceObject);
        $invoice_obj = json_decode(json_encode($invoiceObject));

        $new_obj = new ReportInvoiceToZatca($invoice_obj, $zatcaSettings);

        $response = $new_obj->ReportInvoice();
return $response;
        $zatcaService->updateZatcaResponse($response, $target, $invoiceObject);
        return back()->with(['success' => 'submitted successfully']);
    }


    public function renewCertificate()
    {

        $row = auth()->user();
        // dd($row);
        $shop_name = \DB::table('badr_shop')->where('serial_id', $row->shop_id ?? session('shop_id'))->first()->shop_name;

        return view('zatca.renew-zatca-certificate',compact('shop_name'));


    }


    public function renewCertificateStore(renewCertificateRequest $request, ZatcaService $zatcaService)
    {
        $zatcaSetting = ZatcaSetting::first();

        $zatcaSetting->update(['otp' => $request->otp]);
        $setting_obj = $zatcaService->getZatcaSettings();
        // dd($setting_obj);


        $new_obj = new OnBoarding($setting_obj);
        $response = $new_obj->generatePemsKeys()->IssueCert509();
        // dd($response);
        if ($response['success']) {
            $zatcaSetting->forceFill($response['data'])->save();

        }
        return back()->with(['success' => trans('global.saved_successfully')]);

    }


}




