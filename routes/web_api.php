<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\NewPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebApi\Auth\LoginController;
use App\Http\Controllers\WebApi\Auth\RegisterController;
use App\Http\Controllers\WebApi\CourseController;
use App\Http\Controllers\WebApi\MainController;

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

Route::middleware(['ip_block'])->group(function () {
    Route::post('/login',         [LoginController::class, 'login']);
    Route::post('register',       [RegisterController::class, 'register']);
    Route::post('forgotpassword', [LoginController::class, 'forgotApi']); // 1
    Route::post('verifycode',     [LoginController::class, 'verifyApi']); // 2
    Route::post('resetpassword',  [LoginController::class, 'resetApi']); // 3
    Route::post('logout',         [LoginController::class, 'logoutApi']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('join_us',      [RegisterController::class, 'instructor']);
        Route::get('my-courses',    [CourseController::class, 'myCourses']);
        Route::get('my-allcourses', [CourseController::class, 'myAllCourses']);
    });

    //general api
    Route::get('sliders',      [MainController::class, 'homeSliders']);
    Route::get('testimonials', [MainController::class, 'homeTestimonials']);

    //category
    Route::get('categories',     [MainController::class, 'homeCategories']);
	Route::get('all-categories', [MainController::class, 'homeAllCategories']);
    Route::get('subcategory',    [MainController::class, 'subcategoryPage']);
    

    //Get Courses.
    Route::get('recent/course', [CourseController::class, 'recentCourses']);
    Route::get('all-recent/course', [CourseController::class, 'allRecentCourses']);
    Route::get('featured/course', [CourseController::class, 'featuredCourses']);
    Route::get('all-featured/course', [CourseController::class, 'allFeaturedCourses']);
    Route::get('best-selling/course', [CourseController::class, 'bestSellingCourses']);
    Route::get('all-best-selling/course', [CourseController::class, 'allBestSellingCourses']);
    Route::get('free/course', [CourseController::class, 'freeCourses']);
    Route::get('all-free/course', [CourseController::class, 'allFreeCourses']);
    Route::get('top-discounted/course', [CourseController::class, 'topDiscountedCourses']);
    Route::get('all-top-discounted/course', [CourseController::class, 'allTopDiscountedCourses']);
    Route::get('bundle/course', [CourseController::class, 'bundleCourses']);
    Route::get('all-bundle/course', [CourseController::class, 'allBundleCourses']);
    
    //Get instructors
    Route::get('instructors/home', [MainController::class, 'homeInstructors']);


});