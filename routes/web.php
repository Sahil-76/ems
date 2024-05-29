<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ems\RoleController;
use App\Http\Controllers\ems\UserController;
use App\Http\Controllers\ems\ModuleController;
use App\Http\Controllers\ems\PermissionController;

// Route::get('/', function () {
//     return view('welcome');
// });

    // \Debugbar::disable();



    // Dashboard
    Route::group(['middleware'=>['auth', '2fa']],function()
    {
        
    Route::get('/test','TestController@index');
    Route::get('/',                                     'DashboardController@index')->name('dashboard');
    Route::get('/dashboard',                            'DashboardController@index');
    // Route::get('common/download/documents',             'DashboardController@download')->name('downloadCommonDocument');
    // Route::get('/test',                                 'TestController@ticketUpdate');
    // Route::get('/test/image',                           'TestController@image');
    Route::get('/check-second-halves', 'TestController@checkForUnloggedSecondHalves')->name('check-for-unlogged-second-halves');

    Route::get('get/barcode',                           'EmployeeController@getBarCodeImage')->name('barCodeImage');

    // user Routes

    Route::resource('user',                          UserController::class);

    //Role routes
    Route::resource('role',                            RoleController::class);
   
    Route::prefix('role')->group(function () {
        Route::post('/assign',                          'UserController@assignRoles')->name('assignRole');
    });

    //Permission routes
    Route::resource('permission',                            PermissionController::class);
    Route::put('permissions/{id}/restore', 'PermissionController@restore')->name('permission.restore');

    // deaprtment routes
    Route::get('/department-view',                      'DepartmentController@view')->name('departmentView');

    Route::resource('/designation',                     'DesignationController');

    //qualification route
    Route::get('/qualification',                        'QualificationController@view')->name('qualificationView');

    Route::resource('shift-type',                       'ShiftTypeController');
    Route::resource('departments',                     'DepartmentController');
    // hr routes
    Route::group(['prefix'=>'hr','as'=>'hr.'], function(){
        Route::get('/departments',                      'DepartmentController@departmentEmployees')->name('departmentList');
        Route::get('/admin/list',                       'UserController@adminList')->name('adminList');
        // Route::get('/it-stocks',                        'ItController@hrStockList')->name('stockList');
        // Route::get('/quotation',                        'ItController@hrQuotationList')->name('quotationList');
        // Route::get('stock/details/{item_id}',           'ItController@hrStockDetailList')->name('stockDetailList');
        Route::post('/department/update',               'DepartmentController@managerUpdate')->name('managerUpdate');
    });
    
    Route::prefix('hr-employee')->group(function(){
        Route::get('/',                                 'EmployeeController@view')->name('employeeView');
        Route::get('/create',                           'EmployeeController@create')->name('createEmployee');
        Route::get('/pending/profiles',                 'EmployeeController@pendingEmployeeProfile')->name('pendingProfile');
        Route::get('/profile/draft/{employee}',         'EmployeeController@draft_view')->name('draftView');
        Route::post('/profile/draft',                   'EmployeeController@draft_action')->name('draftAction');
        Route::get('/download/{employee}/{reference}',  'EmployeeController@download_document')->name('downloadDocument');
        Route::get('/profile/draft',                    'EmployeeController@draft')->name('draftList');
        Route::post('/insert',                          'EmployeeController@insert')->name('insertEmployee');
        Route::get('/update/{employee}',                'EmployeeController@edit')->name('editEmployee');
        Route::post('/update',                          'EmployeeController@update')->name('updateEmployee');
        Route::get('/change-department',                'EmployeeController@updateDepartment')->name('updateEmployeeDepartment');
        Route::post('/update/department',               'EmployeeController@changeDepartment')->name('changeDepartment');
        Route::post('/assign/department',               'EmployeeController@assignDepartment')->name('assignDepartment');
        Route::get('/exit',                             'EmployeeController@exitList')->name('exitList');
        Route::get('/exit/insert',                      'EmployeeController@exitForm')->name('exitForm');
        Route::post('/initiate/no-dues',                'EmployeeController@noDuesInitiate')->name('noDuesInitiate');
        Route::post('/upload/experience',               'EmployeeController@uploadExperience')->name('uploadExperience');
        Route::get('/export-exit',                      'EmployeeController@exportExit')->name('exportExit');
        Route::get('/forwarded/leaves',                 'LeaveController@hrLeaveList')->name('forwardedLeaveList');
        Route::get('/leaves',                           'LeaveController@managerLeave')->name('hrLeaveList');
        Route::post('bulk/leave/action',                'LeaveController@bulkLeaveAction')->name('bulkLeaveAction');
        Route::get('/detailed/leaves-history',          'LeaveController@hrLeaveHistoryList')->name('hrLeaveHistory');
        Route::get('/leave/status/update-list',         'LeaveController@hrLeaveStatusList')->name('hrLeaveStatusUpdateList');
        Route::post('update/leave/status',              'LeaveController@updateLeaveStatus')->name('updateLeave');
        Route::get('/export',                           'LeaveController@export')->name('exportLeave');
        Route::get('/leave/balance',                    'LeaveBalanceController@dashboard')->name('leaveBalanceDashboard');
        Route::get('/performance/dashboard',            'PerformanceDashboardController@dashboard')->name('performanceDashboard');
        Route::get('/balance/edit/{id}',                'LeaveBalanceController@edit')->name('leaveBalanceEdit');
        
        Route::post('/balance/update/{id}',             'LeaveBalanceController@update')->name('leaveBalanceUpdate');

        Route::post('balance/complaint/raise',          'LeaveBalanceController@leaveBalanceRaise')->name('leaveBalanceComplaintRaise');
        Route::get('/balance/export',                   'LeaveBalanceController@export')->name('leaveBalanceExport');
    });
    Route::resource('manual-leave',                      ManualLeaveController::class);
    //employee routes
    Route::prefix('employee')->group(function () {
        // Route::get('barcode/display',                   'EmployeeController@barcode')->name('displayBarcode');
        // Route::get('/onboard/dashboard',                'EmployeeController@onboardDashboard')->name('onboardDashboard');
        // Route::get('/load/dashboard',                   'EmployeeController@onboardDashboard')->name('loadEmployees');
        // Route::get('/onboard/status/update/form',       'EmployeeController@onboardStatusUpdateForm')->name('onboardStatusUpdateForm');
        // Route::post('/onboard/status/update',           'EmployeeController@onboardStatusUpdate')->name('onboardStatusUpdate');
        // Route::get('send/document/link',                'EmployeeController@sendDocumentLink')->name('sendDocumentLink');
        // Route::get('send/document/reminder',            'EmployeeController@sendDocumentReminder')->name('sendDocumentReminder');
        // Route::get('/onboard/form',                     'EmployeeController@onboardForm')->name('onboardForm');
        // Route::post('/onboard/submit',                  'EmployeeController@onboardSubmit')->name('onboardSubmit');
        // Route::get('/view',                             'DepartmentController@employee')->name('departmentEmployeeView');
        Route::get('/profile/{employee}',               'EmployeeController@detail')->name('employeeDetail');
        Route::get('/export',                           'EmployeeController@export')->name('exportEmployee');
        Route::post('/import',                          'EmployeeController@import')->name('importEmployee');
        Route::get('/no-dues/requests',                 'EmployeeController@noDuesRequests')->name('noDuesRequests');
        Route::post('/no-dues/submit/{employee}',       'EmployeeController@noDuesSubmit')->name('noDuesSubmit');
        Route::get('/picture/{fileName}',               'EmployeeController@getImage');
        Route::get('/my/balance',                       'LeaveBalanceController@myBalance')->name('myBalance');
        Route::get('/get/balance',                       'LeaveBalanceController@getBalance')->name('getBalance');
        //employee pre details
        // Route::resource('/predetails',                  'EmployeePreDetailsController');

        // document download
        Route::get('/dashboard',                        'EmployeeDashboardController@index')->name('employeeDashboard');
        Route::get('manager/dashboard',                 'EmployeeDashboardController@managerEmployeeDashboard')->name('employeeManagerDashboard');

        //edit routes
        Route::prefix('edit')->group(function () {
            Route::get('/profile/{employee}',           'EmployeeController@editProfile')->name('editProfile');
            Route::post('/profile/{employee}',          'EmployeeController@updateProfile')->name('updateProfile');
            // Route::get('/document/{document}',          'EmployeeController@editDocument')->name('editDocument');
            // Route::post('/document',                    'EmployeeController@updateDocument')->name('updateDocument');
        });

        //delete document
        Route::get('document/delete/{employee_id}/{reference}','EmployeeController@deleteEmployeeDocument')->name('deleteEmployeeDocument');
    });

    // Ticket form
    // Route::get('/ticket/raise-form/{type?}',            'TicketController@ticketRaiseForm')->name('ticketRaiseForm');
    // Route::post('/ticket/raise',                        'TicketController@ticketRaise')->name('ticketRaise');
    // Ticket list
    // Route::get('/my/tickets',                           'TicketController@myTickets')->name('myTickets');
    // Route::get('/ticket-dashboard',                     'TicketController@dashboard')->name('ticketDashboard');

    // log views
    Route::get('laravel/logs',                          'UserController@laravelLogs')->name('laravelLogs');

    //Leave routes
    Route::prefix('my-leave')->group(function () {
        Route::get('/',                                 'LeaveController@leaveList')->name('leaveList');
        Route::get('/apply',                            'LeaveController@leaveForm')->name('createLeave');
        Route::post('/insert',                          'LeaveController@insert')->name('submitLeave');
        Route::get('view/file',                         'LeaveController@viewFile')->name('viewFile');
        // new routes
    });

    Route::post('/alter',                               'LeaveController@alterLeave')->name('leaveAlter');
    Route::post('hr/leave-history/cancel',              'LeaveController@hrLeaveHistoryCancel')->name('hrLeaveHistoryCancel');
    //Attendance Routes
    Route::prefix('attendance')->group(function () {

        Route::get('/dashboard',                        'LiveAttendanceController@attendanceDashboard')->name('attendanceDashboard');
        Route::get('/late/dashboard',                   'LiveAttendanceController@lateAttendanceDashboard')->name('lateAttendanceDashboard');
        // Route::get('/view',                             'AttendanceController@list')->name('attendanceView');
        // Route::get('/form',                             'AttendanceController@form')->name('attendanceForm');
        // Route::post('/form/submit/',                    'AttendanceController@submitAttendance')->name('submitAttendance');
        // Route::get('/export',                           'AttendanceController@export')->name('exportRemoteAttendance');
    });

    //Office Routes
    // Route::prefix('office')->group(function () {
        // Route::get('/email',                            'EmployeeController@viewOfficeEmail')->name('officeEmailView');
        // Route::get('/email/list',                       'EmployeeController@emailList')->name('officeEmailList');
        // Route::post('update/email',                     'EmployeeController@updateofficeEmail')->name('updateOfficeEmail');
    // });
    // Route::get('alloted/office/email',                  'EmployeeController@officeEmailAlloted')->name('AllotedOfficeEmail');


    // Offer Letter
    // Route::get('download/offer/letter/{offer_letter}',  'IntervieweeController@downloadOfferLetter')->name('downloadOfferLetter');

    //equipment routes
    // Route::prefix('equipment')->group(function () {
        // Route::get('/list',                             'EquipmentController@view')->name('equipmentView');
        // Route::get('/add',                              'EquipmentController@addEquipment')->name('createEquipment');
        // Route::get('/edit/{equipment}',                 'EquipmentController@editEquipment')->name('editEquipment');
        // Route::post('/insert',                          'EquipmentController@insertEquipment')->name('insertEquipment');
        // Route::post('/update/{equipment}',              'EquipmentController@updateEquipment')->name('updateEquipment');
        // Route::get('/import',                           'EquipmentController@importView')->name('equipmentImportView');
        // Route::post('/import',                          'EquipmentController@import')->name('equipmentImport');
        // Route::get('/export',                           'EquipmentController@export')->name('exportEquipment');
    //     Route::get('/details/{entity}',                 'EquipmentRequestController@equipmentDetails')->name('equipmentDetails');
    //     Route::get('/request/view',                     'EquipmentRequestController@view')->name('viewEntityRequest');
    //     Route::get('/request/alot',                     'EquipmentRequestController@requestEquipmentAlot')->name('alotEquipmentByRequest');
    //     Route::post('/request/insert',                  'EquipmentRequestController@insert')->name('insertEntityRequest');
    //     Route::get('/request/{request_id?}',            'EquipmentRequestController@request')->name('requestEquipments');
    //     Route::get('/request/view/all',                 'EquipmentRequestController@viewAll')->name('allEntityRequest');
    // });
    // Route::get('/request/equipment',                    'EquipmentRequestController@view')->name('requestEquipment');

    // update password
    Route::prefix('update')->group(function () {
        Route::get('change/password',                   'UserController@updatePasswordView')->name('changePassword');
        Route::post('password',                         'UserController@updatePassword')->name('updatePassword');
    });

    //Module routes
    Route::resource('modules',                              ModuleController::class);
    Route::put('modules/{id}/restore',                     'ModuleController@restore')->name('modules.restore');
    //  Route::get('/module',                               'ModuleController@moduleView')->name('moduleView');

    //activity log route
    Route::get('/logs',                                 'ActivityController@view')->name('activityLogView');

    //entity request routes
    // Route::prefix('entity')->group(function () {
    //     Route::get('/list',                             'EntityController@view')->name('entityView');
    // });

    //manager routes
    Route::prefix('manager')->group(function () {
        Route::get('/employees',                        'ManagerController@view')->name('managerEmployeeView');
        // Route::get('/department/equipment',             'ManagerController@views')->name('managerDepartmentEquipment');
        // Route::get('/attendance',                       'ManagerController@attendance')->name('managerAttendanceView');
    Route::get('/department/leave-request',         'LeaveController@managerLeaveList')->name('managerLeaveList');
        Route::get('/department/leave-history',         'LeaveController@managerLeaveHistory')->name('managerLeaveHistory');
    });

    //trash routes
    // Route::prefix('trash')->group(function () {
        // Route::get('equipment/view',                    'EquipmentController@viewTrash')->name('viewTrashEquipment');
        // Route::get('department/view',                   'DepartmentController@viewTrash')->name('viewTrashDepartment');
        // Route::get('role/view',                         'AuthorizeController@viewTrashRole')->name('viewTrashRole');
        // Route::get('permission/view',                   'AuthorizeController@viewTrashPermission')->name('viewTrashPermission');
        // Route::get('employee/view',                     'EmployeeController@viewTrash')->name('viewTrashEmployee');
        // Route::get('module/view',                       'ModuleController@viewTrash')->name('viewTrashModule');
    // });



    // IT Module Routes

    // Route::prefix('it')->group(function(){
        // quotation routes
        // Route::get('quotation',                         'ItController@quotationList')->name('quotationList');
        // Route::get('quotation/create',                  'ItController@quotationCreate')->name('quotationCreate');
        // Route::get('quotation/details/edit/{quotation}','ITController@quotationDetailList')->name('quotationDetails');
        // Route::get('quotation/edit/{quotationDetail}',  'ITController@quotationDetailEdit')->name('quotationDetailEdit');
        // Route::post('quotation/submit',                 'ITController@quotationSubmit')->name('quotationSubmit');
        // Route::post('quotation/detail/submit',          'ITController@quotationDetailSubmit')->name('quotationDetailSubmit');
        // item routes
        // Route::get('item',                              'ItController@itemList')->name('itemList');
        // Route::get('item/add',                          'ItController@itemCreate')->name('itemCreate');
        // Route::get('item/edit/{item}',                  'ItController@itemEdit')->name('itemEdit');
        // Route::post('item/submit',                      'ItController@itemSubmit')->name('itemSubmit');
        // employee equipment functions
        // Route::get('employee/equipments',               'ItController@employeeEquipmentList')->name('employeeEquipmentList');
        // Route::get('employee/equipment-details/{employee_id}','ItController@employeeEquipmentDetailList')->name('employeeEquipmentDetailList');
        // Route::post('employee/equipment/update',        'ItController@employeeEquipmentUpdate')->name('employeeEquipmentAction');
        // Route::get('employee/equipment/delete',         'ItController@employeeEquipmentRemove')->name('employeeEquipmentRemove');
        // stock routes
        // Route::get('stocks',                            'ItController@stockList')->name('stockList');
        // Route::get('stock/create',                      'ItController@stockCreate')->name('stockCreate');
        // Route::get('stock/edit/{stock}',                'ItController@stockEdit')->name('stockEdit');
        // Route::post('stock/submit',                     'ItController@stockSubmit')->name('stockSubmit');
        // stock detail routes
        // Route::get('stock/details/{stock_id}',          'ItController@stockDetails')->name('stockDetailList');
        // Route::get('stock/detail/form',                 'ItController@stockItemForm')->name('stockDetailForm');
        // Route::post('stock/detail/submit',              'ItController@stockDetailSubmit')->name('stockDetailSubmit');
        // view bill
        // Route::get('bill/view/{bill}',                  'ItController@viewBill')->name('viewBill');
        //view software
        // Route::get('software/view',                     'ItController@list')->name('softwareView');

        // Route::get('edit/employee/email',               'ItController@editEmail')->name('editEmail');
        // Route::post('update/email',                     'ItController@updateEmail')->name('updateEmail');
    // });

    Route::group(['prefix' => '/daily-report', 'as' => 'dailyReport.'],function(){
        Route::get('/',                                 'DailyReportsController@myList')->name('myList');
        Route::get('/department-list',                  'DailyReportsController@departmentReports')->name('departmentReports');
        Route::get('/submit',                           'DailyReportsController@form')->name('form');
        Route::post('/submit',                          'DailyReportsController@submit')->name('submit');
        Route::get('/export',                           'DailyReportsController@export')->name('exportDailyReport');
    });

    // Asset Routes
    Route::resource('asset',                            'AssetController');
    Route::get('get/types',                             'AssetController@getTypes')->name('getTypes');
    Route::get('get/sub/types',                         'AssetController@getSubTypes')->name('getSubTypes');
    Route::get('get/company/types',                     'AssetController@getCompanyTypes')->name('getCompanyTypes');
    Route::resource('asset-category' ,                  'AssetCategoryController');
    Route::resource('asset-type',                       'AssetTypeController');
    Route::get('user/asset/assignments',                'AssetController@assignEquipments')->name('assignEquipments');
    Route::get('user/asset/assignments/list',           'AssetController@assignmentList')->name('assignmentList');
    Route::get('user/assign/asset',                     'AssetController@assignAsset')->name('assetAssign');
    Route::resource('asset-subtype',                    'AssetSubTypeController');
    Route::get('dashboard/asset',                       'AssetController@dashboard')->name('assetDashboard');

    // Route::get('raised/ticket-list',                    'TicketController@hrRaiseTicket')->name('hrRaiseTicket');
    // Route::post('raised/ticket/action',                 'TicketController@raiseTicketAction')->name('raiseTicketAction');
    // Route::get('ticket-history',                        'TicketController@ticketHistory')->name('ticketHistory');
    // Route::get('assigned/tickets',                      'TicketController@assignedTickets')->name('assignedTicket');
    // Route::get('ticket/detail-list/{id}',               'TicketController@ticketDetail')->name('ticketDetail');
    //view ticket category view
    // Route::get('ticket/category',                       'TicketController@categoryView')->name('categoryView');

Route::get('switch-user',                               'UserController@switchUser')->name('switchUser');
Route::post('/switch-user/account',                     'UserController@loginWithAnotherUser')->name('switchlogin');
Route::get('/switch/user/back',                         'UserController@switchUserLogout')->name('swithLogout');

Route::get('/recent/joined/employees',                  'EmployeeController@showRecentJoinedUser')->name('recent-joined');

//Interview
// Route::resource('/interview',                           'InterviewController');

Route::get('attendance/excel',                          'LiveAttendanceController@attendanceExport')->name('attendanceExport');
Route::get('employee/attendance',                       'LiveAttendanceController@myAttendance')->name('myAttendance');
//Assign the role
Route::get('assign/role',                               'AuthorizeController@assignRoles')->name('assignRoles');
// Route::get('create/role',                               'AuthorizeController@createRole')->name('createRole');
// Route::post('store/role',                               'AuthorizeController@storeRole')->name('storeRole');

//Asset detail controllers
Route::resource('asset-detail',                         'AssetDetailController');

//Leave Type
Route::resource('leave-type',                           'LeaveTypeController');

Route::resource('electricity',                          'ElectricityController');
Route::get('electricity-dashboard',                     'ElectricityController@dashboard')->name('electricity.dashboard');
Route::get('/export/electricity',                       'ElectricityController@export')->name('electricity.export');
//Manual Attendance
Route::resource('manual-attendance',                    'ManualAttendanceController');

//Badges
Route::resource('badge',                                'BadgeController');
Route::get('/download/{reference}',                     'BadgeController@download_image')->name('downloadImage');

//Announcement
Route::resource('announcement',                         'AnnouncementController');
Route::get('/announcement/{reference}',                 'AnnouncementController@downloadAnnouncement')->name('downloadAnnouncement');

// Team module
Route::resource('team',                                'TeamController');
Route::get('teams/dashboard',                          'TeamController@dashboard')->name('teamDashboard');

// Training module
Route::resource('training',                            'TrainingController');
Route::resource('task',                                'TaskController');

// Barcode List
Route::get('/barcodeList',                              'EmployeeController@barcodeList')->name('barcodeList');
Route::post('/store-id',                                'EmployeeController@storeIdCard')->name('uploadIdCard');

//testing
// Route::get('/testing',                                  'TestController@ticketUpdate');
// Route::get('/ticket',                                   'TestController@copyId');

// Power User Department tickets
// Route::get('/department-ticket',                        'TicketController@departmentTickets')->name('departmentTickets');

// Bulk Assign
Route::get('bulk-assign/role',                          'AuthorizeController@bulkAssignRole')->name('bulkAssignRole');
Route::post('bulk-store/role',                          'AuthorizeController@bulkStore')->name('bulkStore');

//dashboard popup
Route::get('update/seen/{id}',                          'LiveAttendanceController@updateSeen')->name('updateSeen');
Route::get("attendance/fetch/form",                    "LiveAttendanceController@form");
Route::get('get/progress',                             'LiveAttendanceController@getProgress');
});
// Two Factor Authentication
Route::group(['middleware'=>['auth']],function() {
    Route::get('2fa',               'TwoFAController@index')->name('2fa.index');
    Route::post('2fa',              'TwoFAController@store')->name('2fa.post');
    Route::get('2fa/reset',         'TwoFAController@resend')->name('2fa.resend');
});

Route::get('/clear-cache',function()
{
    if(in_array(strtolower(auth()->user()->email), User::$developers))
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        echo "cleared";
    }
    else{
        abort(404);
    }
});

Route::get('attendance/store',                         'LiveAttendanceController@storeAttendance');


Route::get('remove/document',                          'EmployeeController@removeDocument');
Route::get('reset/birthday/readon',                    'EmployeeController@resetBirthdayReadOn');
// Route::get('send/reminder',                            'TicketController@sendReminder');





