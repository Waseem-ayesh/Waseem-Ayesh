<?php

require __DIR__ . '/auth.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\FeasibilityStudyController;
use App\Http\Controllers\FeasibilityRequestController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\PlantDiseaseController;
use App\Http\Controllers\DiseaseTreatmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\FieldVisitController;
use App\Http\Controllers\PlatformSettingController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\RoleController;


/*
|--------------------------------------------------------------------------
| Authenticated User
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


/*
|--------------------------------------------------------------------------
| Public Routes
| لا تتطلب تسجيل دخول
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register'])
    ->name('api.auth.register');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login');


/*
|--------------------------------------------------------------------------
| Free Consultation
| متاح للزائر والمستخدم المسجل
|--------------------------------------------------------------------------
*/

Route::post('/consultations', [ConsultationController::class, 'store'])
    ->name('api.consultations.store');


/*
|--------------------------------------------------------------------------
| Public Feasibility Studies
| عرض دراسات الجدوى متاح للزوار بدون تسجيل دخول
|--------------------------------------------------------------------------
*/

Route::get('/feasibility_studies', [FeasibilityStudyController::class, 'index'])
    ->name('api.feasibility-studies.index');

Route::get('/feasibility_studies/{id}', [FeasibilityStudyController::class, 'show'])
    ->name('api.feasibility-studies.show');


/*
|--------------------------------------------------------------------------
| Public Categories
|--------------------------------------------------------------------------
*/

Route::get('/categories', [CategoryController::class, 'index'])
    ->name('api.categories.index');

Route::get('/categories/{id}', [CategoryController::class, 'show'])
    ->name('api.categories.show');


/*
|--------------------------------------------------------------------------
| Public Regions
|--------------------------------------------------------------------------
*/

Route::get('/regions', [RegionController::class, 'index'])
    ->name('api.regions.index');

Route::get('/regions/{id}', [RegionController::class, 'show'])
    ->name('api.regions.show');


/*
|--------------------------------------------------------------------------
| Public Specializations
|--------------------------------------------------------------------------
*/

Route::get('/specializations', [SpecializationController::class, 'index'])
    ->name('api.specializations.index');

Route::get('/specializations/{id}', [SpecializationController::class, 'show'])
    ->name('api.specializations.show');


/*
|--------------------------------------------------------------------------
| Public Plants
|--------------------------------------------------------------------------
*/

Route::get('/plants', [PlantController::class, 'index'])
    ->name('api.plants.index');

Route::get('/plants/{id}', [PlantController::class, 'show'])
    ->name('api.plants.show');


/*
|--------------------------------------------------------------------------
| Public Knowledge Base
|--------------------------------------------------------------------------
*/

Route::get('/knowledge_base_item', [KnowledgeBaseController::class, 'index'])
    ->name('api.knowledge-base.index');

Route::get('/knowledge_base_item/{id}', [KnowledgeBaseController::class, 'show'])
    ->name('api.knowledge-base.show');


/*
|--------------------------------------------------------------------------
| Public Platform Settings
|--------------------------------------------------------------------------
*/

Route::get('/platform_settings', [PlatformSettingController::class, 'index'])
    ->name('api.platform-settings.index');


/*
|--------------------------------------------------------------------------
| Protected Routes
| تتطلب تسجيل دخول باستخدام Sanctum
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.auth.logout');

    Route::get('/auth/me', [AuthController::class, 'me'])
        ->name('api.auth.me');


    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:Admin'])->group(function () {

        Route::apiResource('users', UserController::class);

        Route::apiResource('roles', RoleController::class);

        Route::put('/platform_settings', [PlatformSettingController::class, 'update'])
            ->name('api.platform-settings.update');
    });


    /*
    |--------------------------------------------------------------------------
    | Consultation Management
    | الإدارة تحتاج تسجيل دخول + صلاحية
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'permission:manage consultations|answer consultations'
    ])->group(function () {

        Route::get('/consultations', [ConsultationController::class, 'index']);

        Route::get('/consultations/{id}', [ConsultationController::class, 'show']);

        Route::put('/consultations/{id}', [ConsultationController::class, 'update']);

        Route::patch('/consultations/{id}', [ConsultationController::class, 'update']);

        Route::delete('/consultations/{id}', [ConsultationController::class, 'destroy']);
    });


    /*
    |--------------------------------------------------------------------------
    | Feasibility Studies Management
    | إنشاء وتعديل وحذف الدراسات يحتاج تسجيل دخول وصلاحية
    |--------------------------------------------------------------------------
    */

    Route::middleware(['permission:manage feasibility studies'])->group(function () {

        Route::post(
            '/feasibility_studies',
            [FeasibilityStudyController::class, 'store']
        );

        Route::put(
            '/feasibility_studies/{id}',
            [FeasibilityStudyController::class, 'update']
        );

        Route::patch(
            '/feasibility_studies/{id}',
            [FeasibilityStudyController::class, 'update']
        );

        Route::delete(
            '/feasibility_studies/{id}',
            [FeasibilityStudyController::class, 'destroy']
        );
    });


    /*
    |--------------------------------------------------------------------------
    | Feasibility Requests
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'permission:manage feasibility requests|create feasibility request|view own feasibility request'
    ])->group(function () {

        Route::apiResource(
            'feasibility_requests',
            FeasibilityRequestController::class
        );
    });


    /*
    |--------------------------------------------------------------------------
    | Plant Diseases
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'plant_diseases',
        PlantDiseaseController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Disease Treatments
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'disease_treatments',
        DiseaseTreatmentController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Field Visits
    | تتطلب تسجيل الدخول
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'field_visits',
        FieldVisitController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Knowledge Base Management
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:Admin|Agricultural Expert'])->group(function () {

        Route::post(
            '/knowledge_base_item',
            [KnowledgeBaseController::class, 'store']
        )->name('api.knowledge-base.store');

        Route::put(
            '/knowledge_base_item/{id}',
            [KnowledgeBaseController::class, 'update']
        )->name('api.knowledge-base.update');

        Route::delete(
            '/knowledge_base_item/{id}',
            [KnowledgeBaseController::class, 'destroy']
        )->name('api.knowledge-base.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Attachments
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/attachments',
        [AttachmentController::class, 'store']
    )->name('api.attachments.store');

    Route::get(
        '/attachments/{id}',
        [AttachmentController::class, 'show']
    )->name('api.attachments.show');

    Route::delete(
        '/attachments/{id}',
        [AttachmentController::class, 'destroy']
    )->name('api.attachments.destroy');

});