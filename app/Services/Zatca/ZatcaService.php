<?php

namespace App\Services\Zatca;

use App\Models\OrganizationSetting;
use App\Models\ZatcaSetting;
use App\Models\BillSettings;
use Carbon\Carbon;


class ZatcaService
{
    public function getZatcaSettings()
    {
        $mainSetting = OrganizationSetting::first();
        $zatcaSetting = ZatcaSetting::first();
        $BillSettings = BillSettings::first();
        if (!$mainSetting || !$zatcaSetting) {
            return false;
        }


        $setting_obj = (object) [
            'id' => $mainSetting->serial_id,
            'name' => $mainSetting->shop_name,
            'mobile' => $mainSetting->telephone,
            'trn' => $BillSettings->vat_number,//shop
            'crn' => $BillSettings->commercial_register,
            'street_name' => $mainSetting->street_name,
            'building_number' => $mainSetting->building_number,
            'plot_identification' => $mainSetting->plot_identification,
            'region' => $mainSetting->governorate,
            'city' => $mainSetting->city,
            'postal_number' => $zatcaSetting->postal_number,
            'egs_serial_number' => $zatcaSetting->egs_serial_number,
            'business_category' => $zatcaSetting->business_category,
            'common_name' => $zatcaSetting->common_name,
            'organization_unit_name' => $zatcaSetting->organization_unit_name,
            
            // 'organization_name' => $mainSetting->name,
            // 'country_name' => $mainSetting->country_name,
            
            'organization_name' => "albadr" . rand(),
            'country_name' => "SA",

            'registered_address' => $zatcaSetting->registered_address,
            'otp' => $zatcaSetting->otp,
            'email_address' => $mainSetting->email,
            'invoice_type' => $zatcaSetting->invoice_type,
            'is_production' => $zatcaSetting->is_production,
            'cnf' => $zatcaSetting->cnf,
            'private_key' => $zatcaSetting->private_key,
            'public_key' => $zatcaSetting->public_key,
            'csr_request' => $zatcaSetting->csr_request,
            'certificate' => $zatcaSetting->certificate,
            'secret' => $zatcaSetting->secret,
            'csid' => $zatcaSetting->csid,
            'production_certificate' => $zatcaSetting->production_certificate,
            'production_secret' => $zatcaSetting->production_secret,
            'production_csid' => $zatcaSetting->production_csid
        ];

        return $setting_obj;
    }

    public function  invoiceData($invoice)
    {

        // $taxes = [];
/*
        foreach ($invoice->taxesTransaction as $taxTransaction) {
            array_push($taxes, [
                'percentage' => $taxTransaction->amount == 0 ? 0: $taxTransaction->tax_value,
                'category'   => $taxTransaction->amount == 0 ? 'E': 'S',
                'type'       => '',
                'reason'     => '',

            ]);

        }
 */

        $taxes = [];
        $items = [];



        foreach ($invoice->salesDetail as $key => $item) {
            // dd(number_format($item->vat_value,0));
            
                        $taxes = [];
                        array_push($taxes, [
                            'percentage' => $item->vat_mony == 0 ? 0 : number_format($item->vat_value,0),
                            'category' => $item->vat_mony == 0 ? 'E' : 'S',
                            'type' => '',
                            'reason' =>  '',

                        ]);
                        array_push($items,
                            [
                                'id'         => $key +1,
                                'qty'        => $item->quantity,
                                'sell_price' => $item->price,
                                'name'       => $item->item_name,
                                'taxes'      =>
                                    $taxes
                                ,
                                'discounts' => [

                                    [
                                        'amount' => $item->discount_money ?? 0,
                                        'reason' => '',
                                    ]

                                ]
                            ]

                        );

                    }


        $invoice_obj
            = [
            'invoice_counter' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'uuid' => $invoice->uuid, // alter table sale_process add uuid varchar(36) null;

            'document_type' =>  'simplified',
            // simplified or standard
            'invoice_type' => in_array($invoice->invoice_type,
                ['payment', 'rent']) ? 388 : ((in_array($invoice->invoice_type, [ 'back','back_payment'])) ? 383 : 381),
            //  "388" NORMAL INVOICE , "383"  DEBIT_NOTE , "381" CREDIT_NOTE
            'issue_date'    =>Carbon::now()->format('Y-m-d'),
            'issue_time'    => Carbon::now()->format('H:i:s'),
            'parentInvoice' => in_array($invoice->invoice_type,
                ['payment', 'rent']) ? null : $invoice->invoiceable->id ?? null,
            'invoice_identifier' => 'id',
            // this identifier for get invoice to update required fields ====> important
            'hash' => 'hash', // this identifier for get invoice to update required fields ====> important
            'sent_to_zatca' => 'sent_to_zatca',
            // this identifier for get invoice to update required fields ====> important
            'sent_to_zatca_status' => 'sent_to_zatca_status',
            // this identifier for get invoice to update required fields ====> important
            'signing_time' => 'signing_time',
            // this identifier for get invoice to update required fields ====> important
            'xml' => 'xml', // this identifier for get invoice to update required fields ====> important
            'items' => $items
        ];
        $invoice_obj['client'] = [
            'trn' =>  '',
            'street_name' => '',
            'building_number' => '',
            'plot_identification' => '',
            'city' => '',
            'postal_number' => '00000',
            'name' => 'client',
            'country' => 'SA',
        ];
        return $invoice_obj;
    }

    public function updateZatcaResponse($response, $invoice, $invoiceObject)
    {
        if ($response['success']) {
            $invoice->zatcaResult()->updateOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'qr_code' => $response['qr_code'],
                    'hash' => $response['data']['hash'],
                    'xml' => $response['data']['xml'],
                    'status' => $response['data']['sent_to_zatca_status'],
                    'invoice_type' => $invoiceObject['invoice_type'],
                    'document_type' => $invoiceObject['document_type'],
                ]
            );

        } else {
            $invoice->zatcaResult()->updateOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'status' => $response['message']->validationResults->status,
                    'code' => $response['message']->validationResults->errorMessages[0]->code,
                    'message' => $response['message']->validationResults->errorMessages[0]->message,
                ]
            );

        }
    }


    public function checkCertificate($zatcaSettings)
    {
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

        return true;
    }

}
