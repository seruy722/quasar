<?php

use App\Code;
use App\Customer;
use App\Http\Resources\CodeResource;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommonExport;

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
    Route::get('/codes', function () {
        return CodeResource::collection(Code::with('customers', 'user')->get());
    });
    Route::get('/codes-list', function () {
        return Code::select('id as value', 'code as label')->orderBy('label')->get();
    });
    Route::post('/upload-codes', 'Api\CodesController@uploadCodesData');
    Route::get('/check-code-exist/{code}', 'Api\CodesController@checkCodeExist');
    Route::post('/store-code', 'Api\CodesController@storeCode');

    // CUSTOMERS
    Route::post('/valid-customer-data', 'Api\CustomersController@checkValidCustomerData');
    Route::post('/store-customers', 'Api\CustomersController@storeCustomers');

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

    // FAX_DATA
    Route::post('/upload-fax-data-table', 'Api\FaxDataController@storeFaxData');
    Route::get('/fax-data/{id}', 'Api\FaxDataController@getFaxData');
    Route::post('/update-fax-data', 'Api\FaxDataController@updateFaxData');
    Route::post('/export-fax-data', 'Api\FaxDataController@export');
    Route::get('/update-all-price-in-fax-data/{id}', 'Api\FaxDataController@updatePrice');

    // CATEGORIES
    Route::get('/categories', 'Api\CategoryController@getCategories');

    // TRANSPORTERS
    Route::get('/transporters', 'Api\TransporterController@index');

    // TRANSPORTS
    Route::get('/transports', 'Api\TransportController@index');

    // TRANSPORTER_PRICE
    Route::post('/update-transporter-price-data', 'Api\TransporterPriceController@updateData');
    // AUXILIARY REQUESTS
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
});


Route::group(['middleware' => [\App\Http\Middleware\Localization::class]], function () {
    Route::post('/login', 'Api\AuthController@login');
    Route::post('/register', 'Api\AuthController@register');

    Route::post('/password/email', 'Api\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'Api\ResetPasswordController@reset');
});
