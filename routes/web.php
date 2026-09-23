<?php

use App\Http\Controllers\Admin\AdminBusinessController;
use App\Http\Controllers\Admin\AdminBusinessProfileController;
use App\Http\Controllers\Admin\AdminCorrectionController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\Admin\AdminFeeController;
use App\Http\Controllers\Admin\AdminRenewalController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminStaffDocumentController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Business\BusinessFeeController;
use App\Http\Controllers\Business\BusinessProfileController;
use App\Http\Controllers\Business\BusinessRenewalController;
use App\Http\Controllers\Business\BusinessStaffController;
use App\Http\Controllers\Business\BusinessStaffSubmissionController;
use App\Http\Controllers\Business\BusinessSubmissionController;
use App\Http\Controllers\Business\BusinessVerificationController;
use App\Http\Controllers\Certificate\CertificateController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\BusinessOwnerDashboardController;
use App\Http\Controllers\Dashboard\GovernmentOfficialDashboardController;
use App\Http\Controllers\Dashboard\SuperAdminDashboardController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Document\DocumentReplacementController;
use App\Http\Controllers\Document\StaffDocumentController;
use App\Http\Controllers\Document\StaffDocumentReplacementController;
use App\Http\Controllers\Government\GovernmentOfficialController;
use App\Http\Controllers\Government\GovernmentStaffController;
use App\Http\Controllers\Home\BusinessDirectoryController;
use App\Http\Controllers\Home\KnowledgeHubController;
use App\Http\Controllers\Home\PublicController;
use App\Http\Controllers\Home\VerificationController;
use App\Http\Controllers\SuperAdmin\BusinessVerificationController as SuperAdminBusinessVerificationController;
use App\Http\Controllers\SuperAdmin\SuperAdminAgencyController;
use App\Http\Controllers\SuperAdmin\SuperAdminAuditLogController;
use App\Http\Controllers\SuperAdmin\SuperAdminDocumentTypeController;
use App\Http\Controllers\SuperAdmin\SuperAdminPostController;
use App\Http\Controllers\SuperAdmin\SuperAdminPublicationController;
use App\Http\Controllers\SuperAdmin\SuperAdminReportController;
use App\Http\Controllers\SuperAdmin\SuperAdminSectorController;
use App\Http\Controllers\SuperAdmin\SuperAdminSettingController;
use App\Http\Controllers\SuperAdmin\SuperAdminStaffDocumentTypeController;
use App\Http\Controllers\SuperAdmin\SuperAdminUserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::view('/about', 'public.about')->name('public.about');
Route::view('/n-bidea', 'public.n-bidea')->name('public.n-bidea');
Route::view('/priority-sectors', 'public.priority-sectors')->name('public.priority-sectors');
Route::view('/afcfta-ecowas', 'public.afcfta-ecowas')->name('public.afcfta-ecowas');
Route::view('/registry', 'public.registry')->name('public.registry');
Route::get('/directory', [BusinessDirectoryController::class, 'index'])->name('public.directory.index');
Route::get('/directory/{businessProfile}', [BusinessDirectoryController::class, 'show'])->name('public.directory.show');
Route::get('/directory/{businessProfile}/logo', [BusinessDirectoryController::class, 'logo'])->name('public.directory.logo');
Route::get('/verify', [VerificationController::class, 'index'])->name('public.verification.index');
Route::post('/verify', [VerificationController::class, 'search'])->name('public.verification.search');
Route::get('/verify/{verificationCode}', [VerificationController::class, 'show'])->name('public.verification.code');
Route::view('/investment-opportunities', 'public.investment-opportunities')->name('public.investment-opportunities');
Route::get('/resources', [KnowledgeHubController::class, 'index'])->name('public.resources');
Route::get('/blog/{post}', [KnowledgeHubController::class, 'showPost'])->name('public.blog.show');
Route::get('/reports/{publication}', [KnowledgeHubController::class, 'showPublication'])->name('public.publications.show');
Route::get('/reports/{publication}/download', [KnowledgeHubController::class, 'downloadPublication'])->name('public.publications.download');
Route::view('/events', 'public.events')->name('public.events');
Route::view('/contact', 'public.contact')->name('public.contact');

// Auth routes (guest middleware)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:auth-register');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:auth-login');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:auth-password')->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware('throttle:auth-password')->name('password.update');
});

// Authenticated routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/directory/{businessProfile}/inquiries', [BusinessDirectoryController::class, 'storeInquiry'])->middleware('throttle:inquiries')->name('public.directory.inquiries.store');

    Route::get('/dashboard', function () {
        return redirect()->route(auth()->user()->dashboardRoute());
    })->name('dashboard');

    // Business Owner routes
    Route::prefix('business-owner')->name('business-owner.')->middleware('role:business_owner')->group(function () {
        Route::get('/dashboard', [BusinessOwnerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('businesses', BusinessController::class);
        Route::get('/businesses/{business}/profile', [BusinessProfileController::class, 'show'])->name('businesses.profile.show');
        Route::get('/businesses/{business}/profile/edit', [BusinessProfileController::class, 'edit'])->name('businesses.profile.edit');
        Route::get('/businesses/{business}/profile/logo', [BusinessProfileController::class, 'logo'])->name('businesses.profile.logo');
        Route::post('/businesses/{business}/profile', [BusinessProfileController::class, 'store'])->name('businesses.profile.store');
        Route::post('/businesses/{business}/submit', [BusinessSubmissionController::class, 'submit'])->name('businesses.submit');
        Route::post('/businesses/{business}/correction-response', [BusinessSubmissionController::class, 'correctionResponse'])->name('businesses.correction-response');
        Route::get('/businesses/{business}/verification', [BusinessVerificationController::class, 'show'])->name('businesses.verification');
        Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
        Route::get('/renewals', [BusinessRenewalController::class, 'index'])->name('renewals.index');
        Route::post('/renewals', [BusinessRenewalController::class, 'store'])->name('renewals.store');
        Route::get('/fees', [BusinessFeeController::class, 'index'])->name('fees.index');
        Route::post('/fees/{fee}/upload-proof', [BusinessFeeController::class, 'uploadProof'])->name('fees.upload-proof');
        Route::get('/businesses/{business}/documents', [DocumentController::class, 'index'])->name('businesses.documents.index');
        Route::post('/businesses/{business}/documents', [DocumentController::class, 'store'])->name('businesses.documents.store');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::post('/documents/{document}/replace', [DocumentReplacementController::class, 'replace'])->name('documents.replace');
        // Staff
        Route::resource('businesses.staff', BusinessStaffController::class)->parameters(['staff' => 'staffMember'])->shallow();
        Route::post('/staff/{staffMember}/submit', [BusinessStaffSubmissionController::class, 'submit'])->name('staff.submit');
        Route::post('/staff/{staffMember}/correction-response', [BusinessStaffSubmissionController::class, 'correctionResponse'])->name('staff.correction-response');
        Route::post('/staff/{staffMember}/documents', [StaffDocumentController::class, 'store'])->name('staff.documents.store');
        Route::delete('/staff-documents/{staffDocument}', [StaffDocumentController::class, 'destroy'])->name('staff-documents.destroy');
        Route::get('/staff-documents/{staffDocument}/download', [StaffDocumentController::class, 'download'])->name('staff-documents.download');
        Route::post('/staff-documents/{staffDocument}/replace', [StaffDocumentReplacementController::class, 'replace'])->name('staff-documents.replace');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin,super_admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/businesses', [AdminBusinessController::class, 'index'])->name('businesses.index');
        Route::get('/businesses/{business}', [AdminBusinessController::class, 'show'])->name('businesses.show');
        Route::post('/businesses/{business}/review', [AdminBusinessController::class, 'review'])->name('businesses.review');
        Route::get('/business-profiles', [AdminBusinessProfileController::class, 'index'])->name('business-profiles.index');
        Route::get('/business-profiles/{businessProfile}/logo', [AdminBusinessProfileController::class, 'logo'])->name('business-profiles.logo');
        Route::get('/business-profiles/{businessProfile}', [AdminBusinessProfileController::class, 'show'])->name('business-profiles.show');
        Route::patch('/business-profiles/{businessProfile}', [AdminBusinessProfileController::class, 'update'])->name('business-profiles.update');
        Route::get('/businesses/{business}/documents', [AdminDocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/{document}', [AdminDocumentController::class, 'show'])->name('documents.show');
        Route::post('/documents/{document}/review', [AdminDocumentController::class, 'review'])->name('documents.review');
        Route::get('/documents/{document}/download', [AdminDocumentController::class, 'download'])->name('documents.download');
        Route::get('/businesses/{business}/correction', [AdminCorrectionController::class, 'show'])->name('correction.show');
        Route::get('/renewals', [AdminRenewalController::class, 'index'])->name('renewals.index');
        Route::get('/renewals/{renewalRequest}', [AdminRenewalController::class, 'show'])->name('renewals.show');
        Route::post('/renewals/{renewalRequest}/approve', [AdminRenewalController::class, 'approve'])->name('renewals.approve');
        Route::post('/renewals/{renewalRequest}/reject', [AdminRenewalController::class, 'reject'])->name('renewals.reject');
        Route::get('/fees', [AdminFeeController::class, 'index'])->name('fees.index');
        Route::post('/fees/{fee}/confirm', [AdminFeeController::class, 'confirm'])->name('fees.confirm');
        Route::get('/staff', [AdminStaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/{staffMember}', [AdminStaffController::class, 'show'])->name('staff.show');
        Route::post('/staff/{staffMember}/review', [AdminStaffController::class, 'review'])->name('staff.review');
        Route::post('/staff-documents/{staffDocument}/review', [AdminStaffDocumentController::class, 'review'])->name('staff-documents.review');
        Route::get('/staff-documents/{staffDocument}/download', [AdminStaffDocumentController::class, 'download'])->name('staff-documents.download');
    });

    // Super Admin routes
    Route::prefix('super-admin')->name('super-admin.')->middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/businesses/{business}/verification', [SuperAdminBusinessVerificationController::class, 'store'])->name('businesses.verification.store');
        // Users
        Route::resource('users', SuperAdminUserController::class);
        Route::post('/users/{user}/suspend', [SuperAdminUserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/activate', [SuperAdminUserController::class, 'activate'])->name('users.activate');
        // Sectors
        Route::resource('sectors', SuperAdminSectorController::class);
        // Knowledge Hub
        Route::resource('posts', SuperAdminPostController::class);
        Route::resource('publications', SuperAdminPublicationController::class);
        // Document Types
        Route::resource('document-types', SuperAdminDocumentTypeController::class);
        Route::resource('staff-document-types', SuperAdminStaffDocumentTypeController::class)->except('show');
        // Agencies
        Route::resource('agencies', SuperAdminAgencyController::class);
        // Settings
        Route::get('/settings', [SuperAdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SuperAdminSettingController::class, 'update'])->name('settings.update');
        // Audit Logs
        Route::get('/audit-logs', [SuperAdminAuditLogController::class, 'index'])->name('audit-logs.index');
        // Reports
        Route::get('/reports', [SuperAdminReportController::class, 'index'])->name('reports.index');
        Route::post('/reports', [SuperAdminReportController::class, 'generate'])->name('reports.generate');
    });

    // Government Official routes
    Route::prefix('government')->name('government.')->middleware('role:government_official')->group(function () {
        Route::get('/dashboard', [GovernmentOfficialDashboardController::class, 'index'])->name('dashboard');
        Route::get('/search', [GovernmentOfficialController::class, 'search'])->name('search');
        Route::post('/search', [GovernmentOfficialController::class, 'search'])->name('search.execute');
        Route::get('/businesses/{business}', [GovernmentOfficialController::class, 'show'])->name('businesses.show');
        Route::post('/businesses/{business}/record-check', [GovernmentOfficialController::class, 'recordCheck'])->name('businesses.record-check');
        Route::get('/staff/{staffMember}', [GovernmentStaffController::class, 'show'])->name('staff.show');
    });
});
