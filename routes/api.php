<?php

use App\Code;
use App\Customer;
use App\Http\Resources\CodeResource;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommonExport;
use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => [\App\Http\Middleware\Localization::class, 'auth:api']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // CODES
    Route::get('/codes', 'Api\CodesController@getCodesWithCustomers');
//    Route::get('/codes', function () {
//        return CodeResource::collection(Code::with('customers', 'user')->get());
//    });
    Route::post('/upload-codes', 'Api\CodesController@uploadCodesData');
    Route::get('/check-code-exist/{code}', 'Api\CodesController@checkCodeExist');
    Route::post('/store-code', 'Api\CodesController@storeCode');
    Route::get('/codes-list', 'Api\CodesController@index');

    // STOREHOUSE
    Route::get('/store-house-data/{id}', 'Api\StorehouseDataController@getStorehouseData');

    // STOREHOUSE_DATA
    Route::post('/add-storehouse-data', 'Api\StorehouseDataController@store');
    Route::post('/update-storehouse-data', 'Api\StorehouseDataController@update');
    Route::get('/shop-names', 'Api\StorehouseDataController@getShopNames');
    Route::get('/thing-list', 'Api\StorehouseDataController@getThingsList');
    Route::post('/export-storehouse-data', 'Api\StorehouseDataController@export');
    Route::post('/destroy-storehouse-data', 'Api\StorehouseDataController@destroy');
    Route::get('/storehouse-data-history/{id}', 'Api\StorehouseDataController@getStorehouseDataHistory');
    Route::post('/get-new-storehouseData', 'Api\StorehouseDataController@getNewStorehouseData');
    Route::post('/set-transfers-storehouse-fax', 'Api\StorehouseDataController@setTransfersStorehouseFax');
    Route::post('/update-fax-combine-data', 'Api\StorehouseDataController@updateFaxCombineData');

    // CLIENTS
    Route::post('/valid-customer-data', 'Api\CustomersController@checkValidCustomerData');
    Route::post('/store-customers', 'Api\CustomersController@storeCustomers');
    Route::post('/update-customer', 'Api\CustomersController@update');
    Route::get('/destroy-customer/{id}', 'Api\CustomersController@destroy');

    // CARGO_TABLE
    Route::post('/upload-cargo-table', 'CommonController@storeCargoTable');
    Route::get('/client-data/{client}', 'CommonController@getData');

    // DEBTS_TABLE
    Route::post('/upload-debts-table', 'CommonController@storeDebtsTable');

    // SKLAD_TABLE
    Route::post('/upload-sklad-table', 'CommonController@storeSkladTable');

    // FAXES
    Route::get('/faxes', 'Api\FaxController@getFaxesList');
    Route::get('/dialog-add-fax-options', 'Api\FaxController@getOptionsData');
    Route::post('/add-fax', 'Api\FaxController@addFax');
    Route::post('/delete-faxes', 'Api\FaxController@deleteFax');
    Route::post('/update-faxes', 'Api\FaxController@updateFaxes');
    Route::post('/upload-to-cargo', 'Api\FaxController@uploadToCargo');
    Route::get('/fax-history/{id}', 'Api\FaxController@faxHistory');
    Route::post('/get-new-fax', 'Api\FaxController@getNewFax');
    Route::get('/fax/{id}', 'Api\FaxController@getFax');

    // FAX_DATA
    Route::post('/upload-fax-data-table', 'Api\FaxDataController@storeFaxData');
    Route::get('/fax-data/{id}', 'Api\FaxDataController@getFaxData');
    Route::post('/update-fax-data', 'Api\FaxDataController@updateFaxData');
    Route::post('/export-fax-data', 'Api\FaxDataController@export');
    Route::get('/update-all-price-in-fax-data/{id}', 'Api\FaxDataController@updatePrice');
    Route::post('/export-fax-moder-data', 'Api\FaxDataController@exportModer');
    Route::post('/export-fax-admin-data', 'Api\FaxDataController@exportAdmin');

    // CATEGORIES
    Route::get('/categories', 'Api\CategoryController@index');
    Route::post('/add-category', 'Api\CategoryController@store');

    // TRANSPORTERS
    Route::get('/transporters', 'Api\TransporterController@index');
    Route::post('/add-transporter', 'Api\TransporterController@store');

    // TRANSPORTS
    Route::get('/transports', 'Api\TransportController@index');

    // TRANSPORTER_PRICE
    Route::post('/update-transporter-price-data', 'Api\TransporterPriceController@updateData');
    Route::post('/update-transporter-faxes-price', 'Api\TransporterFaxesPriceController@updateData');
    Route::get('/transporter-price-data/{id}', 'Api\TransporterFaxesPriceController@getTransporterPriceData');
    Route::post('/save-categories-price', 'Api\TransporterFaxesPriceController@saveCategoriesPrice');

    // CITIES
    Route::get('/cities', function () {
        return \App\City::select('id as value', 'name as label')->orderBy('name')->get();
    });
    // THINGSLIST
    Route::get('/thingsList', function () {
        $things = \App\Thingslist::orderBy('name')->get();
        return $things->pluck('name');
    });
    // SHOPSLIST
    Route::get('/shopsList', function () {
        return \App\Shop::select('id as value', 'name as label')->orderBy('name')->get();
    });
    // TRANSFERS
    Route::get('/transfers', 'Api\TransferController@index');
    Route::post('/update-transfers', 'Api\TransferController@update');
    Route::post('/store-transfers', 'Api\TransferController@store');
    Route::post('/get-new-transfers', 'Api\TransferController@getNewTransfers');
    Route::post('/export-transfers', 'Api\TransferController@export');
    // TRANSFERS HISTORY
    Route::get('/transfers-history/{id}', 'Api\TransferController@getTransferHistory');
    // AUXILIARY REQUESTS
    // Клиенты котрые получают бренды
    Route::get('/export-brands-customers', function () {
        return Excel::download(new CommonExport([], 'brands'), 'brands.xlsx');
    });

    // Кода без информации о клиентах
    Route::get('/export-codes-without-customers', function () {
        $codes = Code::all();
        $arrCodesWithoutCustomers = [];
        $codes->each(function ($item) use (&$arrCodesWithoutCustomers) {
            $customer = Customer::where('code_id', $item->id)->first();
            if (!$customer) {
                array_push($arrCodesWithoutCustomers, $item->id);
            }
        });
//        return response(['ok' => $arrCodesWithoutCustomers]);
        return Excel::download(new CommonExport($arrCodesWithoutCustomers, 'codes'), 'codes.xlsx');
    });

    Route::post('/upload-faxes', 'CommonController@uploadFaxes');
    Route::post('/search-in-faxes', 'CommonController@searchInFaxes');
    Route::get('/areas', function () {
        $client = new Client();
        return $client->get("https://api.hh.ru/areas/5");
    });
    // Загрузка городов Украины
//    Route::post('/store-areas', function (Request $request) {
//        foreach ($request->areas as $area) {
//            $areaM = \App\Region::create(['name' => $area['name'], 'parent_id' => 1]);
//            foreach ($area['areas'] as $item) {
//                \App\City::create(['name' => $item['name'], 'parent_id' => $areaM->id]);
//            }
//        }
//        return $request->areas;
//    });
});


Route::group(['middleware' => [\App\Http\Middleware\Localization::class]], function () {
    Route::post('/login', 'Api\AuthController@login');
//    Route::post('/register', 'Api\AuthController@register');

//    Route::post('/password/email', 'Api\ForgotPasswordController@sendResetLinkEmail');
//    Route::post('/password/reset', 'Api\ResetPasswordController@reset');
});
