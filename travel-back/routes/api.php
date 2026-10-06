<?php

use App\Http\Controllers\Api\Admin\TopBannerController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\HeroSlideController;
use App\Http\Controllers\Api\HowItWorksController;
use App\Http\Controllers\Api\LogoController;
use App\Http\Controllers\Api\NavItemController;
use App\Http\Controllers\Api\OfferShowController;
use App\Http\Controllers\Api\TourPackageController;
use App\Http\Controllers\Api\TourPackageDayActivityController;
use App\Http\Controllers\Api\TourPackageHighlightController;
use App\Http\Controllers\Api\TourPackageInformationController;
use App\Http\Controllers\Api\TourPackageItineraryController;
use App\Http\Controllers\Api\TourPackageOfferController;
use App\Http\Controllers\Api\TourPackageOfferHotelController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Models\OfferShow;
use Illuminate\Support\Facades\Route;



// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated (any role)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Admin-only
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::patch('/users/{id}/restore', [UserController::class, 'restore']);
});

//google or facebook login route
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);


// Top banner routes
Route::get('top-banners', [TopBannerController::class, 'index']);
Route::get('top-banners/{topBanner}', [TopBannerController::class, 'show']);
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    Route::get('top-banners', [TopBannerController::class, 'adminIndex']);
    Route::get('top-banners/{topBanner}', [TopBannerController::class, 'show']);
    Route::post('top-banners', [TopBannerController::class, 'store']);
    Route::put('top-banners/{topBanner}', [TopBannerController::class, 'update']);
    Route::delete('top-banners/{topBanner}', [TopBannerController::class, 'destroy']);
});


// Public: fetch the current logo
Route::get('logo', [LogoController::class, 'index']);

// Admin: create/update/delete the logo
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    Route::post('logo', [LogoController::class, 'store']);
    Route::put('logo/{logo}', [LogoController::class, 'update']);
    Route::delete('logo/{logo}', [LogoController::class, 'destroy']);
});


//Nav Item Route
Route::get('nav-items', [NavItemController::class, 'index']);
 
// Admin — THIS GROUP WAS MISSING, which is why /admin/nav-items 404'd
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    Route::get('/nav-items', [NavItemController::class, 'adminIndex']);
    Route::post('nav-items', [NavItemController::class, 'store']);
    Route::get('nav-items/{nav_item}', [NavItemController::class, 'show']);
    Route::put('nav-items/{nav_item}', [NavItemController::class, 'update']);
    Route::delete('nav-items/{nav_item}', [NavItemController::class, 'destroy']);
    Route::post('nav-items/reorder', [NavItemController::class, 'reorder']);
});


Route::get('/hero-slides', [HeroSlideController::class, 'index']);

// Admin (auth:sanctum + role middleware)
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])
    ->prefix('admin')
    ->name('admin.') // <-- required
    ->group(function () {
        Route::get('/hero-slides', [HeroSlideController::class, 'adminIndex'])->name('hero-slides.index');
        Route::get('/hero-slides/{heroSlide:id}', [HeroSlideController::class, 'show'])->name('hero-slides.show');
        Route::post('/hero-slides', [HeroSlideController::class, 'store'])->name('hero-slides.store');
        Route::match(['put', 'patch'], '/hero-slides/{heroSlide:id}', [HeroSlideController::class, 'update'])->name('hero-slides.update');
        Route::delete('/hero-slides/{heroSlide:id}', [HeroSlideController::class, 'destroy'])->name('hero-slides.destroy');
    });


// Public route for frontend/mobile app display
Route::get('/how-it-works', [HowItWorksController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/how-it-works', [HowItWorksController::class, 'adminIndex']);
    // Standard CRUD routes for admin
    Route::post('/how-it-works', [HowItWorksController::class, 'store']);
    Route::get('/how-it-works/{howItWorksStep}', [HowItWorksController::class, 'show']);
    Route::put('/how-it-works/{howItWorksStep}', [HowItWorksController::class, 'update']);
    Route::patch('/how-it-works/{howItWorksStep}', [HowItWorksController::class, 'update']);
    Route::delete('/how-it-works/{howItWorksStep}', [HowItWorksController::class, 'destroy']);
    
});    


// Category Routes
Route::get('category', [CategoryController::class, 'index']);
Route::get('category/{category}', [CategoryController::class, 'show']);

// Admin Routes
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Add this GET route for admin index:
    Route::get('category', [CategoryController::class, 'index']); 
    Route::post('category', [CategoryController::class, 'store']);
    Route::match(['put', 'patch', 'post'], 'category/{category:id}', [CategoryController::class, 'update']);
    Route::delete('category/{category:id}', [CategoryController::class, 'destroy']);
});

Route::get('category/{category}/tour-package', [TourPackageController::class, 'byCategory']);

// Tour Package Routes
Route::get('tour-package', [TourPackageController::class, 'index']);
Route::get('tour-package/{tourPackage:slug}', [TourPackageController::class, 'show']);

// Admin Routes
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Add this GET route for admin index:
    Route::get('tour-package', [TourPackageController::class, 'index']); 
    Route::post('tour-package', [TourPackageController::class, 'store']);
    Route::match(['put', 'patch', 'post'], 'tour-package/{tourPackage}', [TourPackageController::class, 'update']);
    Route::delete('tour-package/{tourPackage}', [TourPackageController::class, 'destroy']);
});

// Public
Route::get('tour-highlight', [TourPackageHighlightController::class, 'index']);
Route::get('tour-highlight/{tourPackageHighlight}', [TourPackageHighlightController::class, 'show']);

// Admin
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('tour-highlight', [TourPackageHighlightController::class, 'store']);
    Route::match(['put', 'patch', 'post'], 'tour-highlight/{tourPackageHighlight}', [TourPackageHighlightController::class, 'update']);
    Route::delete('tour-highlight/{tourPackageHighlight}', [TourPackageHighlightController::class, 'destroy']);
});

// Public
Route::get('tour-itinerary', [TourPackageItineraryController::class, 'index']);
Route::get('tour-itinerary/{tourPackageItinerary}', [TourPackageItineraryController::class, 'show']);

// Admin
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('tour-itinerary', [TourPackageItineraryController::class, 'index'])->name('tour-itinerary.index');
    Route::get('tour-itinerary/{tourPackageItinerary}', [TourPackageItineraryController::class, 'show'])->name('tour-itinerary.show');
    Route::post('tour-itinerary', [TourPackageItineraryController::class, 'store']);
    Route::match(['put', 'patch', 'post'], 'tour-itinerary/{tourPackageItinerary}', [TourPackageItineraryController::class, 'update']);
    Route::delete('tour-itinerary/{tourPackageItinerary}', [TourPackageItineraryController::class, 'destroy']);
});


// Public
Route::get('tour-activity', [TourPackageDayActivityController::class, 'index']);
Route::get('tour-activity/{tourPackageDayActivity}', [TourPackageDayActivityController::class, 'show']);

// Admin (inside your existing admin group)
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('tour-activity', [TourPackageDayActivityController::class, 'index'])->name('tour-activity.index');
    Route::get('tour-activity/{tourPackageDayActivity}', [TourPackageDayActivityController::class, 'show'])->name('tour-activity.show');
    Route::post('tour-activity', [TourPackageDayActivityController::class, 'store']);
    Route::match(['put', 'patch', 'post'], 'tour-activity/{tourPackageDayActivity}', [TourPackageDayActivityController::class, 'update']);
    Route::delete('tour-activity/{tourPackageDayActivity}', [TourPackageDayActivityController::class, 'destroy']);
});


// Public Tour Information
Route::get('tour-information', [TourPackageInformationController::class, 'index']);
Route::get('tour-information/{tourPackageInformation}', [TourPackageInformationController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin')->group(function () {
    Route::get('tour-information', [TourPackageInformationController::class, 'index'])->name('tour-active.index');
    Route::get('tour-information/{tourPackageInformation}', [TourPackageInformationController::class, 'show'])->name('tour-active.show');
    Route::post('tour-information', [TourPackageInformationController::class, 'store']);
    Route::match(['put', 'patch', 'post'],'tour-information/{tourPackageInformation}', [TourPackageInformationController::class, 'update']);
    Route::delete('tour-information/{tourPackageInformation}', [TourPackageInformationController::class, 'destroy']);
});


// Public Tour Offer
Route::get('tour-price-offer', [TourPackageOfferController::class, 'index']);
Route::get('tour-price-offer/{tourPackageOffer}', [TourPackageOfferController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin')->group(function () {
    Route::get('tour-price-offer', [TourPackageOfferController::class, 'index'])->name('tour-active.index');
    Route::get('tour-price-offer/{tourPackageOffer}', [TourPackageOfferController::class, 'show'])->name('tour-active.show');
    Route::post('tour-price-offer', [TourPackageOfferController::class, 'store']);
    Route::match(['put', 'patch', 'post'],'tour-price-offer/{tourPackageOffer}', [TourPackageOfferController::class, 'update']);
    Route::delete('tour-price-offer/{tourPackageOffer}', [TourPackageOfferController::class, 'destroy']);
});


// Public Tour Offer Hotel
Route::get('tour-hotel', [TourPackageOfferHotelController::class, 'index']);
Route::get('tour-hotel/{tourPackageOfferHotel}', [TourPackageOfferHotelController::class, 'show']);
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin')->group(function () {
    Route::get('tour-hotel', [TourPackageOfferHotelController::class, 'index'])->name('tour-active.index');
    Route::get('tour-hotel/{tourPackageOfferHotel}', [TourPackageOfferHotelController::class, 'show'])->name('tour-active.show');
    Route::post('tour-hotel', [TourPackageOfferHotelController::class, 'store']);
    Route::match(['put', 'patch', 'post'],'tour-hotel/{tourPackageOfferHotel}', [TourPackageOfferHotelController::class, 'update']);
    Route::delete('tour-hotel/{tourPackageOfferHotel}', [TourPackageOfferHotelController::class, 'destroy']);
});


// Public Offer Show
Route::get('offer-show', [OfferShowController::class, 'index']);
Route::get('offer-show/{offerShow}', [OfferShowController::class, 'show']);
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin')->group(function () {
    Route::get('offer-show', [OfferShowController::class, 'index'])->name('offer-active.index');
    Route::get('offer-show/{offerShow}', [OfferShowController::class, 'show'])->name('offer-active.show');
    Route::post('offer-show', [OfferShowController::class, 'store']);
    Route::match(['put', 'patch', 'post'],'offer-show/{offerShow}', [OfferShowController::class, 'update']);
    Route::delete('offer-show/{offerShow}', [OfferShowController::class, 'destroy']);
});

// Public
Route::get('destinations', [DestinationController::class, 'index']);
Route::get('destinations/{destination}', [DestinationController::class, 'show']);

// Admin
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('destinations', [DestinationController::class, 'store']);
    Route::match(['put', 'patch', 'post'], 'destinations/{destination}', [DestinationController::class, 'update']);
    Route::delete('destinations/{destination}', [DestinationController::class, 'destroy']);
});

