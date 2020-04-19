<?php

use App\Code;
use App\Customer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommonExport;
use GuzzleHttp\Client;
use App\Traits\UserSetAccessData;

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
        class Access
        {
            use UserSetAccessData;
        }

        $obj = new Access();
        return $obj->setAccessData();
    });

    // PERMISSION AND ROLES
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/users-with-roles', 'Api\PermissionController@index');
        Route::get('/roles-list', 'Api\PermissionController@getAllRoles');
        Route::post('/add-role', 'Api\PermissionController@addRole');
        Route::post('/assign-role-to-users', 'Api\PermissionController@assignRoleToUsers');
        Route::post('/delete-role-from-user', 'Api\PermissionController@deleteRoleFromUser');
        Route::get('/delete-role/{id}', 'Api\PermissionController@deleteRole');
        Route::post('/delete-role', 'Api\PermissionController@deleteRole');
        Route::get('/permission-list', 'Api\PermissionController@getAllPermissions');
        Route::post('/add-permission', 'Api\PermissionController@addPermission');
        Route::post('/assign-permission-to-role-and-user', 'Api\PermissionController@assignPermissionToRoleAndUser');
        Route::get('/delete-permission/{id}', 'Api\PermissionController@deletePermission');
        Route::post('/delete-permission-from-role-or-user', 'Api\PermissionController@deletePermissionFromRoleOrUser');
    });

    // CODES
    Route::get('/codes', 'Api\CodesController@getCodesWithCustomers')->name('view codes list')->middleware(['role_or_permission:admin|codes|view codes list']);
    Route::get('/get-code-history/{id}', 'Api\CodesController@getCodeHistory')->name('get-code-history')->middleware(['role_or_permission:admin|codes|get-code-history']);
//    Route::post('/get-new-codes', 'Api\CodesController@getNewCodes')->name('get-new-codes')->middleware(['role_or_permission:admin|codes|get-new-codes']);
//    Route::get('/codes', function () {
//        return CodeResource::collection(Code::with('customers', 'user')->get());
//    });
    Route::post('/upload-codes', 'Api\CodesController@uploadCodesData');
    Route::get('/check-code-exist/{code}', 'Api\CodesController@checkCodeExist');
    Route::post('/store-code', 'Api\CodesController@storeCode');
    Route::get('/codes-list', 'Api\CodesController@index');

    // STOREHOUSE
    Route::get('/store-house-data/{id}', 'Api\StorehouseDataController@getStorehouseData')->name('view storehouse data')->middleware(['role_or_permission:admin|storehouse|view storehouse data']);

    // STOREHOUSE_DATA
    Route::post('/add-storehouse-data', 'Api\StorehouseDataController@store')->name('add entry to storehouse data')->middleware(['role_or_permission:admin|storehouse|add entry to storehouse data']);
    Route::post('/update-storehouse-data', 'Api\StorehouseDataController@update')->name('update entry on storehouse data')->middleware(['role_or_permission:admin|storehouse|update entry on storehouse data']);
    Route::get('/shop-names', 'Api\StorehouseDataController@getShopNames');
    Route::get('/thing-list', 'Api\StorehouseDataController@getThingsList');
    Route::post('/export-storehouse-data', 'Api\StorehouseDataController@export')->name('export storehouse data')->middleware(['role_or_permission:admin|storehouse|export storehouse data']);
    Route::post('/destroy-storehouse-data', 'Api\StorehouseDataController@destroy')->name('delete entry on storehouse data')->middleware(['role_or_permission:admin|storehouse|delete entry on storehouse data']);
    Route::get('/storehouse-data-history/{id}', 'Api\StorehouseDataController@getStorehouseDataHistory')->name('get history of entry in storehouse data')->middleware(['role_or_permission:admin|storehouse|get history of entry in storehouse data']);
    Route::post('/get-new-storehouseData', 'Api\StorehouseDataController@getNewStorehouseData')->name('get new storehouse data entries')->middleware(['role_or_permission:admin|storehouse|get new storehouse data entries']);
    Route::post('/set-transfers-storehouse-fax', 'Api\StorehouseDataController@setTransfersStorehouseFax')->name('set transfers storehouse fax and reverse')->middleware(['role_or_permission:admin|storehouse|set transfers storehouse fax and reverse']);
    Route::post('/update-fax-combine-data', 'Api\StorehouseDataController@updateFaxCombineData')->name('update fax combine data')->middleware(['role_or_permission:admin|fax|update fax combine data']);
    Route::post('/move-from-storehouse-to-fax', 'Api\StorehouseDataController@moveFromStorehouseToFax')->name('move entries from storehouse to fax')->middleware(['role_or_permission:admin|storehouse|move entries from storehouse to fax']);

    // CLIENTS
    Route::post('/valid-customer-data', 'Api\CustomersController@checkValidCustomerData');
    Route::post('/store-customers', 'Api\CustomersController@storeCustomers');
    Route::post('/update-customer', 'Api\CustomersController@update');
    Route::get('/destroy-customer/{id}', 'Api\CustomersController@destroy');
    Route::get('/get-customer-history/{id}', 'Api\CustomersController@getCustomerHistory');

    // CARGO_TABLE
    Route::post('/upload-cargo-table', 'CommonController@storeCargoTable');
    Route::get('/client-data/{client}', 'CommonController@getData');

    // DEBTS_TABLE
    Route::post('/upload-debts-table', 'CommonController@storeDebtsTable');

    // SKLAD_TABLE
    Route::post('/upload-sklad-table', 'CommonController@storeSkladTable');

    // FAXES
    Route::get('/faxes', 'Api\FaxController@getFaxesList')->name('view faxes list')->middleware(['role_or_permission:admin|fax|view faxes list']);
    Route::get('/dialog-add-fax-options', 'Api\FaxController@getOptionsData');
    Route::post('/add-fax', 'Api\FaxController@addFax')->name('add new fax')->middleware(['role_or_permission:admin|fax|add new fax']);
    Route::post('/delete-faxes', 'Api\FaxController@deleteFax')->name('delete faxes')->middleware(['role_or_permission:admin|fax|delete faxes']);
    Route::post('/update-faxes', 'Api\FaxController@updateFaxes')->name('update fax')->middleware(['role_or_permission:admin|fax|update fax']);
    Route::post('/upload-to-cargo', 'Api\FaxController@uploadToCargo')->name('upload fax to cargo')->middleware(['role_or_permission:admin|fax|upload fax to cargo']);
    Route::get('/fax-history/{id}', 'Api\FaxController@faxHistory')->name('get fax history')->middleware(['role_or_permission:admin|fax|get fax history']);
    Route::post('/get-new-fax', 'Api\FaxController@getNewFax')->name('get new fax list')->middleware(['role_or_permission:admin|fax|get new fax list']);
    Route::get('/fax/{id}', 'Api\FaxController@getFax')->name('view fax data')->middleware(['role_or_permission:admin|fax|view fax data']);
    Route::post('/combine-faxes', 'Api\FaxController@combineFaxes')->name('combine-faxes')->middleware(['role_or_permission:admin|fax|combine-faxes']);

    // FAX_DATA
    Route::post('/upload-fax-data-table', 'Api\FaxDataController@storeFaxData');
    Route::get('/fax-data/{id}', 'Api\FaxDataController@getFaxData');
    Route::post('/update-fax-data', 'Api\FaxDataController@updateFaxData');
    Route::post('/export-fax-data', 'Api\FaxDataController@export');
//    Route::get('/update-all-price-in-fax-data/{id}', 'Api\FaxDataController@updatePrice');
    Route::get('/update-prices-in-fax/{id}', 'Api\FaxDataController@updatePricesInFax')->name('update-prices-in-fax')->middleware(['role_or_permission:admin|fax|update-prices-in-fax']);
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
    Route::get('/transfers', 'Api\TransferController@index')->name('view transfers list')->middleware(['role_or_permission:admin|transfers|view transfers list']);
    Route::post('/update-transfers', 'Api\TransferController@update')->name('update transfers data')->middleware(['role_or_permission:admin|transfers|update transfers data']);
    Route::post('/store-transfers', 'Api\TransferController@store')->name('store transfers data')->middleware(['role_or_permission:admin|transfers|store transfers data']);
    Route::post('/get-new-transfers', 'Api\TransferController@getNewTransfers')->name('get new transfers')->middleware(['role_or_permission:admin|transfers|get new transfers']);
    Route::post('/export-transfers', 'Api\TransferController@export')->name('export transfers')->middleware(['role_or_permission:admin|transfers|export transfers']);
    // TRANSFERS HISTORY
    Route::get('/transfers-history/{id}', 'Api\TransferController@getTransferHistory')->name('get transfer history')->middleware(['role_or_permission:admin|transfers|get transfer history']);

    // CLIENT PRICE
    Route::get('/get-codes-prices', 'Api\CodesPriceController@getCodesPrices')->name('get-codes-prices')->middleware(['role_or_permission:admin|codes-prices|get-codes-prices']);
    Route::post('/add-code-price', 'Api\CodesPriceController@store')->name('add-code-price')->middleware(['role_or_permission:admin|codes-prices|add-code-price']);
    Route::delete('/delete-code-price/{id}', 'Api\CodesPriceController@delete')->name('delete-code-price')->middleware(['role_or_permission:admin|codes-prices|delete-code-price']);
    Route::post('/update-code-price', 'Api\CodesPriceController@update')->name('update-code-price')->middleware(['role_or_permission:admin|codes-prices|update-code-price']);
    Route::get('/get-code-price-history/{id}', 'Api\CodesPriceController@getCodePriceHistory')->name('get-code-price-history')->middleware(['role_or_permission:admin|codes-prices|get-code-price-history']);
    Route::post('/get-new-codes-prices', 'Api\CodesPriceController@getNewCodesPrices')->name('get-new-codes-prices')->middleware(['role_or_permission:admin|codes-prices|get-new-codes-prices']);

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
