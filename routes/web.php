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

/*RUTAS PASSWORD RESET*/

use Illuminate\Support\Facades\Route;

Route::get('/', 'InicioController@index')->name('inicio');

Route::get('ejemplo/dashboard', function () { return view('ejemplos.dashboard');  });
Route::get('ejemplo/formularios', function () { return view('ejemplos.formularios');  });
Route::get('ejemplo/tablas', function () { return view('ejemplos.tablas');  });


Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');


Route::get('seguridad/login', 'Seguridad\LoginController@index')->name('login');
Route::post('seguridad/login', 'Seguridad\LoginController@login')->name('login_post');
Route::get('seguridad/logout', 'Seguridad\LoginController@logout')->name('logout');
Route::post('ajax-sesion', 'AjaxController@setSession')->name('ajax')->middleware('auth');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'superadmin']], function () 
{
    Route::get('', 'AdminController@index');
    /*RUTAS DE USUARIO*/
    Route::get('usuario', 'UsuarioController@index')->name('usuario');
    Route::get('usuario/crear', 'UsuarioController@crear')->name('crear_usuario');
    Route::post('usuario', 'UsuarioController@guardar')->name('guardar_usuario');
    Route::get('usuario/{id}/editar', 'UsuarioController@editar')->name('editar_usuario');
    Route::put('usuario/{id}', 'UsuarioController@actualizar')->name('actualizar_usuario');
    Route::delete('usuario/{id}', 'UsuarioController@eliminar')->name('eliminar_usuario');
    /*RUTAS DE PERMISO
    Route::get('permiso', 'PermisoController@index')->name('permiso');
    Route::get('permiso/crear', 'PermisoController@crear')->name('crear_permiso');
    Route::post('permiso', 'PermisoController@guardar')->name('guardar_permiso');
    Route::get('permiso/{id}/editar', 'PermisoController@editar')->name('editar_permiso');
    Route::put('permiso/{id}', 'PermisoController@actualizar')->name('actualizar_permiso');
    Route::delete('permiso/{id}', 'PermisoController@eliminar')->name('eliminar_permiso');*/
    /*RUTAS DEL MENU*/
    Route::get('menu', 'MenuController@index')->name('menu');
    Route::get('menu/crear', 'MenuController@crear')->name('crear_menu');
    Route::post('menu', 'MenuController@guardar')->name('guardar_menu');
    Route::get('menu/{id}/editar', 'MenuController@editar')->name('editar_menu');
    Route::put('menu/{id}', 'MenuController@actualizar')->name('actualizar_menu');
    Route::get('menu/{id}/eliminar', 'MenuController@eliminar')->name('eliminar_menu');
    Route::post('menu/guardar-orden', 'MenuController@guardarOrden')->name('guardar_orden');
    /*RUTAS ROL
    Route::get('rol', 'RolController@index')->name('rol');
    Route::get('rol/crear', 'RolController@crear')->name('crear_rol');
    Route::post('rol', 'RolController@guardar')->name('guardar_rol');
    Route::get('rol/{id}/editar', 'RolController@editar')->name('editar_rol');
    Route::put('rol/{id}', 'RolController@actualizar')->name('actualizar_rol');
    Route::delete('rol/{id}', 'RolController@eliminar')->name('eliminar_rol');*/
    /*RUTAS MENU_ROL*/
    Route::get('menu-rol', 'MenuRolController@index')->name('menu_rol');
    Route::post('menu-rol', 'MenuRolController@guardar')->name('guardar_menu_rol');
    /*RUTAS PERMISO_ROL*/
    Route::get('permiso-rol', 'PermisoRolController@index')->name('permiso_rol');
    Route::post('permiso-rol', 'PermisoRolController@guardar')->name('guardar_permiso_rol');
});
/**
 * Rutas PartGroup
 */
Route::get('part_group', 'PartGroupController@index')->name('part_group')->middleware('auth');
Route::get('part_group/crear', 'PartGroupController@crear')->name('crear_part_group')->middleware('auth');
Route::post('part_group', 'PartGroupController@guardar')->name('guardar_part_group')->middleware('auth');
Route::get('part_group/{id}/editar', 'PartGroupController@editar')->name('editar_part_group')->middleware('auth');
Route::put('part_group/{id}', 'PartGroupController@actualizar')->name('actualizar_part_group')->middleware('auth');
Route::delete('part_group/{id}', 'PartGroupController@eliminar')->name('eliminar_part_group')->middleware('auth');


/**
 * Rutas PartTitle
 */
Route::get('part_title', 'PartTitleController@index')->name('part_title')->middleware('auth');
Route::get('part_title/crear', 'PartTitleController@crear')->name('crear_part_title')->middleware('auth');
Route::post('part_title', 'PartTitleController@guardar')->name('guardar_part_title')->middleware('auth');
Route::get('part_title/{id}/editar', 'PartTitleController@editar')->name('editar_part_title')->middleware('auth');
Route::put('part_title/{id}', 'PartTitleController@actualizar')->name('actualizar_part_title')->middleware('auth');
Route::delete('part_title/{id}', 'PartTitleController@eliminar')->name('eliminar_part_title')->middleware('auth');

/**
 * Rutas PackingTypeManufacture
 */
Route::get('packing_type_manufacture', 'PackingTypeManufactureController@index')->name('packing_type_manufacture')->middleware('auth');
Route::get('packing_type_manufacture/crear', 'PackingTypeManufactureController@crear')->name('crear_packing_type_manufacture')->middleware('auth');
Route::post('packing_type_manufacture', 'PackingTypeManufactureController@guardar')->name('guardar_packing_type_manufacture')->middleware('auth');
Route::get('packing_type_manufacture/{id}/editar', 'PackingTypeManufactureController@editar')->name('editar_packing_type_manufacture')->middleware('auth');
Route::put('packing_type_manufacture/{id}', 'PackingTypeManufactureController@actualizar')->name('actualizar_packing_type_manufacture')->middleware('auth');
Route::delete('packing_type_manufacture/{id}', 'PackingTypeManufactureController@eliminar')->name('eliminar_packing_type_manufacture')->middleware('auth');

/**
 * Rutas PackingTypeManufacture
 */
Route::get('packing_type', 'PackingTypeController@index')->name('packing_type')->middleware('auth');
Route::get('packing_type/crear', 'PackingTypeController@crear')->name('crear_packing_type')->middleware('auth');
Route::post('packing_type', 'PackingTypeController@guardar')->name('guardar_packing_type')->middleware('auth');
Route::get('packing_type/{id}/editar', 'PackingTypeController@editar')->name('editar_packing_type')->middleware('auth');
Route::put('packing_type/{id}', 'PackingTypeController@actualizar')->name('actualizar_packing_type')->middleware('auth');
Route::delete('packing_type/{id}', 'PackingTypeController@eliminar')->name('eliminar_packing_type')->middleware('auth');


/**
 * Rutas PartNumber
 */
Route::get('part_number', 'PartNumberController@index')->name('part_number')->middleware('auth');
Route::get('part_number/crear', 'PartNumberController@crear')->name('crear_part_number')->middleware('auth');
Route::post('part_number', 'PartNumberController@guardar')->name('guardar_part_number')->middleware('auth');
Route::get('part_number/{id}/editar', 'PartNumberController@editar')->name('editar_part_number')->middleware('auth');
Route::put('part_number/{id}', 'PartNumberController@actualizar')->name('actualizar_part_number')->middleware('auth');
Route::delete('part_number/{id}', 'PartNumberController@eliminar')->name('eliminar_part_number')->middleware('auth');

/**
 * Rutas Machine
 */
Route::get('machine', 'MachineController@index')->name('machine')->middleware('auth');
Route::get('machine/crear', 'MachineController@crear')->name('crear_machine')->middleware('auth');
Route::post('machine', 'MachineController@guardar')->name('guardar_machine')->middleware('auth');
Route::get('machine/{id}/editar', 'MachineController@editar')->name('editar_machine')->middleware('auth');
Route::put('machine/{id}', 'MachineController@actualizar')->name('actualizar_machine')->middleware('auth');
Route::delete('machine/{id}', 'MachineController@eliminar')->name('eliminar_machine')->middleware('auth');

/**
 * Rutas MedPlates
 */
Route::get('med_plate', 'MedPlateController@index')->name('med_plate')->middleware('auth');
Route::get('med_plate/crear', 'MedPlateController@crear')->name('crear_med_plate')->middleware('auth');
Route::post('med_plate', 'MedPlateController@guardar')->name('guardar_med_plate')->middleware('auth');
Route::get('med_plate/{id}/editar', 'MedPlateController@editar')->name('editar_med_plate')->middleware('auth');
Route::put('med_plate/{id}', 'MedPlateController@actualizar')->name('actualizar_med_plate')->middleware('auth');
Route::delete('med_plate/{id}', 'MedPlateController@eliminar')->name('eliminar_med_plate')->middleware('auth');
Route::delete('med_plate_det/{id}', 'MedPlateController@eliminarDet')->name('eliminar_med_plate_det')->middleware('auth');
Route::post('med_plate_det', 'MedPlateController@guardarDet')->name('guardar_med_plate_det')->middleware('auth');

/**
 * Rutas Adapter
 */
Route::get('adapter', 'AdapterController@index')->name('adapter')->middleware('auth');
Route::get('adapter/crear', 'AdapterController@crear')->name('crear_adapter')->middleware('auth');
Route::post('adapter', 'AdapterController@guardar')->name('guardar_adapter')->middleware('auth');
Route::get('adapter/{id}/editar', 'AdapterController@editar')->name('editar_adapter')->middleware('auth');
Route::put('adapter/{id}', 'AdapterController@actualizar')->name('actualizar_adapter')->middleware('auth');
Route::delete('adapter/{id}', 'AdapterController@eliminar')->name('eliminar_adapter')->middleware('auth');


/**
 * Rutas Paddle
 */
Route::get('paddle', 'PaddleController@index')->name('paddle')->middleware('auth');
Route::get('paddle/crear', 'PaddleController@crear')->name('crear_paddle')->middleware('auth');
Route::post('paddle', 'PaddleController@guardar')->name('guardar_paddle')->middleware('auth');
Route::get('paddle/{id}/editar', 'PaddleController@editar')->name('editar_paddle')->middleware('auth');
Route::put('paddle/{id}', 'PaddleController@actualizar')->name('actualizar_paddle')->middleware('auth');
Route::delete('paddle/{id}', 'PaddleController@eliminar')->name('eliminar_paddle')->middleware('auth');

Route::post('paddle_det/delete', 'PaddleController@eliminarDet')->name('eliminar_padlle_det')->middleware('auth');
Route::post('padtlle_det', 'PaddleController@guardarDet')->name('guardar_padlle_det')->middleware('auth');
Route::post('padlle_det_get', 'PaddleController@getDet')->name('get_padlle_det')->middleware('auth');


/**
 * Rutas Manufacture
 */
Route::get('manufacture', 'ManufactureController@index')->name('manufacture')->middleware('auth');
Route::get('manufacture/crear', 'ManufactureController@crear')->name('crear_manufacture')->middleware('auth');
Route::post('manufacture', 'ManufactureController@guardar')->name('guardar_manufacture')->middleware('auth');
Route::get('manufacture/{id}/editar', 'ManufactureController@editar')->name('editar_manufacture')->middleware('auth');
Route::put('manufacture/{id}', 'ManufactureController@actualizar')->name('actualizar_manufacture')->middleware('auth');
Route::delete('manufacture/{id}', 'ManufactureController@eliminar')->name('eliminar_manufacture')->middleware('auth');

Route::post('manufacture_det/delete', 'ManufactureController@eliminarDet')->name('eliminar_manufacture_det')->middleware('auth');
Route::post('manufacture_det', 'ManufactureController@guardarDet')->name('guardar_manufacture_det')->middleware('auth');
Route::post('manufacture_det_get', 'ManufactureController@getDet')->name('get_manufacture_det')->middleware('auth');

/**
 * Rutas Manufacture Code
 */
Route::get('manufacture_code', 'ManufactureCodeController@index')->name('manufacture_code')->middleware('auth');
Route::get('manufacture_coder/crear', 'ManufactureCodeController@crear')->name('crear_manufacture_code')->middleware('auth');
Route::post('manufacture_code', 'ManufactureCodeController@guardar')->name('guardar_manufacture_code')->middleware('auth');
Route::get('manufacture_code/{id}/editar', 'ManufactureCodeController@editar')->name('editar_manufacture_code')->middleware('auth');
Route::put('manufacture_code/{id}', 'ManufactureCodeController@actualizar')->name('actualizar_manufacture_code')->middleware('auth');
Route::delete('manufacture_code/{id}', 'ManufactureCodeController@eliminar')->name('eliminar_manufacture_code')->middleware('auth');



/**
 * Rutas Customer Canisters
 */
Route::get('customer_canister', 'CustomerCanistersController@index')->name('customer_canister')->middleware('auth');

/**
 * Rutas Shape
 */
Route::get('shape', 'ShapeController@index')->name('shape')->middleware('auth');
Route::get('shape/crear', 'ShapeController@crear')->name('crear_shape')->middleware('auth');
Route::post('shape', 'ShapeController@guardar')->name('guardar_shape')->middleware('auth');
Route::get('shape/{id}/editar', 'ShapeController@editar')->name('editar_shape')->middleware('auth');
Route::put('shape/{id}', 'ShapeController@actualizar')->name('actualizar_shape')->middleware('auth');
Route::delete('shape/{id}', 'ShapeController@eliminar')->name('eliminar_shape')->middleware('auth');


/**
 * Rutas Medication
 */
Route::get('medication', 'MedicationController@index')->name('medication')->middleware('auth');
Route::get('medication/crear', 'MedicationController@crear')->name('crear_medication')->middleware('auth');
Route::post('medication', 'MedicationController@guardar')->name('guardar_medication')->middleware('auth');
Route::get('medication/{id}/editar', 'MedicationController@editar')->name('editar_medication')->middleware('auth');
Route::put('medication/{id}', 'MedicationController@actualizar')->name('actualizar_medication')->middleware('auth');
Route::delete('medication/{id}', 'MedicationController@eliminar')->name('eliminar_medication')->middleware('auth');

/**
 * Rutas Medication Removal
 */
Route::get('medication_removal', 'MedicationRemovalController@index')->name('medication_removal')->middleware('auth');

/**
 * Rutas Analize Dispense Reports
 */
Route::get('analize_dispense_reports', 'AnalizeDispenseReportsController@index')->name('analize_dispense_reports')->middleware('auth');

/**
* Rutas Leads Generation
*/
Route::get('lead_generation', 'CustomerLeadGenerationController@index')->name('lead_generation')->middleware('auth');


/**
 * Rutas Service Records
 */
Route::get('service_records', 'ServiceRecordsController@index')->name('service_records')->middleware('auth');
Route::get('service_records/create', 'ServiceRecordsController@create')->name('create_service_records')->middleware('auth');


/**
 * Rutas Create Orders
 */
Route::get('create_orders', 'CreateOrdersController@index')->name('create_orders')->middleware('auth');




/**
 * Rutas Sipping Canister Validation
 */
Route::get('sipping_canister_validation', 'SippingCanisterValidationController@index')->name('sipping_canister_validation')->middleware('auth');
Route::get('sipping_canister_validation/validation', 'SippingCanisterValidationController@validation')->name('validar_sipping_canister_validation')->middleware('auth');


/**
 * Rutas Canister Program Download
 */
Route::get('canister_program_download', 'CanisterProgramDownloadController@index')->name('canister_program_download')->middleware('auth');


/**
 * Rutas System Reports */
Route::get('system_reports', 'SystemReportController@index')->name('system_reports')->middleware('auth');

/**
 * Rutas Audit Logs
 */
Route::get('audit_logs', 'AuditLogsController@index')->name('audit_logs')->middleware('auth');


/**
 * Rutas exchangeAmt
 */
Route::get('exchangeAmt', 'ExchangeAmtController@index')->name('exchangeAmt')->middleware('auth');
Route::get('exchangeAmt/crear', 'ExchangeAmtController@crear')->name('crear_exchangeAmt')->middleware('auth');
Route::post('exchangeAmt', 'ExchangeAmtController@guardar')->name('guardar_exchangeAmt')->middleware('auth');
Route::get('exchangeAmt/{id}/editar', 'ExchangeAmtController@editar')->name('editar_exchangeAmt')->middleware('auth');
Route::put('exchangeAmt/{id}', 'ExchangeAmtController@actualizar')->name('actualizar_exchangeAmt')->middleware('auth');
Route::delete('exchangeAmt/{id}', 'ExchangeAmtController@eliminar')->name('eliminar_exchangeAmt')->middleware('auth');

/**
 * Rutas StateController
 */
Route::get('state', 'StateController@index')->name('state')->middleware('auth');
Route::get('state/crear', 'StateController@crear')->name('crear_state')->middleware('auth');
Route::post('state', 'StateController@guardar')->name('guardar_state')->middleware('auth');
Route::get('state/{id}/editar', 'StateController@editar')->name('editar_state')->middleware('auth');
Route::put('state/{id}', 'StateController@actualizar')->name('actualizar_state')->middleware('auth');
Route::delete('state/{id}', 'StateController@eliminar')->name('eliminar_state')->middleware('auth');

/**
 * Rutas DistributorController
 */
Route::get('distributor', 'DistributorController@index')->name('distributor')->middleware('auth');
Route::get('distributor/crear', 'DistributorController@crear')->name('crear_distributor')->middleware('auth');
Route::post('distributor', 'DistributorController@guardar')->name('guardar_distributor')->middleware('auth');
Route::get('distributor/{id}/editar', 'DistributorController@editar')->name('editar_distributor')->middleware('auth');
Route::put('distributor/{id}', 'DistributorController@actualizar')->name('actualizar_distributor')->middleware('auth');
Route::delete('distributor/{id}', 'DistributorController@eliminar')->name('eliminar_distributor')->middleware('auth');


/**
 * Rutas CustomerController
 */
Route::get('customer', 'CustomerController@index')->name('customer')->middleware('auth');
Route::get('customer/crear', 'CustomerController@crear')->name('crear_customer')->middleware('auth');
Route::post('customer', 'CustomerController@guardar')->name('guardar_customer')->middleware('auth');
Route::get('customer/{id}/editar', 'CustomerController@editar')->name('editar_customer')->middleware('auth');
Route::put('customer/{id}', 'CustomerController@actualizar')->name('actualizar_customer')->middleware('auth');
Route::delete('customer/{id}', 'CustomerController@eliminar')->name('eliminar_customer')->middleware('auth');



Route::post('customer/serial/crear',  'CustomerController@guardarSerial')->name('guardar_customer_serial')->middleware('auth');
Route::post('customer/serial/getseriales', 'CustomerController@getSeriales')->name('get_customer_seriales')->middleware('auth');
Route::post('customer/serial/getserial', 'CustomerController@getSerial')->name('get_customer_serial')->middleware('auth');
Route::post('customer/serial/delete', 'CustomerController@eliminarSerial')->name('eliminar_customer_serial')->middleware('auth');
Route::get('customer/get_by_distributor/{distributor_id}', 'CustomerController@get_by_distributor')->name('customer_get_by_distributor')->middleware('auth');

Route::post('customer/contacto/crear',  'CustomerController@guardarContacto')->name('guardar_customer_contacto')->middleware('auth');
Route::post('customer/contacto/getcontactos', 'CustomerController@getContactos')->name('get_customer_contactos')->middleware('auth');
Route::post('customer/contacto/getcontacto', 'CustomerController@getContacto')->name('get_customer_contacto')->middleware('auth');
Route::post('customer/contacto/delete', 'CustomerController@eliminarContacto')->name('eliminar_customer_contacto')->middleware('auth');


/**
 * Rutas CanisterPricing
 */
Route::get('canister_pricing', 'CanisterPricingController@index')->name('canister_pricing')->middleware('auth');
Route::post('canister_pricing', 'CanisterPricingController@guardar')->name('guardar_canister_pricing')->middleware('auth');


/*
Route::get('part_group/crear', 'PartGroupController@crear')->name('crear_part_group')->middleware('auth');

Route::get('part_group/{id}/editar', 'PartGroupController@editar')->name('editar_part_group')->middleware('auth');
Route::put('part_group/{id}', 'PartGroupController@actualizar')->name('actualizar_part_group')->middleware('auth');
Route::delete('part_group/{id}', 'PartGroupController@eliminar')->name('eliminar_part_group')->middleware('auth');
*/


/**
 * Rutas OrderController
 */
Route::get('order', 'ManagerOrdersController@index')->name('order')->middleware('auth');
Route::get('order/crear', 'ManagerOrdersController@crear')->name('crear_order')->middleware('auth');
Route::post('order', 'ManagerOrdersController@guardar')->name('guardar_order')->middleware('auth');
Route::get('order/{id}/editar', 'ManagerOrdersController@editar')->name('editar_order')->middleware('auth');
Route::put('order/{id}', 'ManagerOrdersController@actualizar')->name('actualizar_order')->middleware('auth');
Route::delete('order/{id}', 'ManagerOrdersController@eliminar')->name('eliminar_order')->middleware('auth');

Route::post('order/medication/getmedicationslist',  'ManagerOrdersController@getMedications')->name('order_get_medications_list')->middleware('auth');
Route::post('order/getmedications',  'ManagerOrdersController@getOrderMedications')->name('order_get_medications')->middleware('auth');
Route::post('order/medication/addMedications',  'ManagerOrdersController@addMedications')->name('order_add_medications_list')->middleware('auth');
Route::post('order/medication/delete', 'ManagerOrdersController@eliminarMedication')->name('eliminar_order_medication')->middleware('auth');


/*
Route::post('customer/serial/getseriales', 'CustomerController@getSeriales')->name('get_customer_seriales')->middleware('auth');
Route::post('customer/serial/getserial', 'CustomerController@getSerial')->name('get_customer_serial')->middleware('auth');


Route::post('customer/contacto/crear',  'CustomerController@guardarContacto')->name('guardar_customer_contacto')->middleware('auth');
Route::post('customer/contacto/getcontactos', 'CustomerController@getContactos')->name('get_customer_contactos')->middleware('auth');
Route::post('customer/contacto/getcontacto', 'CustomerController@getContacto')->name('get_customer_contacto')->middleware('auth');
Route::post('customer/contacto/delete', 'CustomerController@eliminarContacto')->name('eliminar_customer_contacto')->middleware('auth');
*/