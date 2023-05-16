<?php

use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OtherApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebApi\Auth\LoginController;
use App\Http\Controllers\WebApi\Auth\RegisterController;
use App\Http\Controllers\WebApi\CourseController;
use App\Http\Controllers\WebApi\MainController;
use App\Http\Controllers\WebApi\PaymentController;

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

        Route::get('course/progress', [CourseController::class, 'courseProgress']);
        Route::post('course/progress/update',[CourseController::class, 'courseprogressupdate']);
        Route::get('course/googleMeetings', [CourseController::class, 'courseGoogleMeetings']);
    });

    //general api
    Route::get('home/setting', [MainController::class, 'homeSetting']);
    Route::get('sliders',      [MainController::class, 'homeSliders']);
    Route::get('testimonials', [MainController::class, 'homeTestimonials']);
    Route::get('videoSetting', [MainController::class, 'videoSetting']);
    Route::post('contactus',   [MainController::class, 'contactus']);
    Route::get('contactus/reasons', [MainController::class, 'contactReasons']);
    Route::get('contact/details',   [MainController::class, 'contactDetails']);


    //category
    Route::get('categories',     [MainController::class, 'homeCategories']);
    Route::get('all-categories', [MainController::class, 'homeAllCategories']);
    Route::get('subcategory',    [MainController::class, 'subcategoryPage']);


    //Get Courses.
    Route::get('recent/course',       [CourseController::class, 'recentCourses']);
    Route::get('featured/course',     [CourseController::class, 'featuredCourses']);
    Route::get('best-selling/course', [CourseController::class, 'bestSellingCourses']);
    Route::get('free/course',           [CourseController::class, 'freeCourses']);
    Route::get('top-discounted/course', [CourseController::class, 'topDiscountedCourses']);
    Route::get('bundle/course',         [CourseController::class, 'bundleCourses']);
    Route::get('all-recent/course',       [CourseController::class, 'allRecentCourses']);
    Route::get('all-featured/course',     [CourseController::class, 'allFeaturedCourses']);
    Route::get('all-best-selling/course', [CourseController::class, 'allBestSellingCourses']);
    Route::get('all-free/course',         [CourseController::class, 'allFreeCourses']);
    Route::get('all-top-discounted/course', [CourseController::class, 'allTopDiscountedCourses']);
    Route::get('all-bundle/course',         [CourseController::class, 'allBundleCourses']);

    Route::get('course/details',  [CourseController::class, 'courseDetails']);
    Route::get('course/chapters', [CourseController::class, 'courseChpaters']);


    //Get instructors
    Route::get('instructors/home',  [MainController::class, 'homeInstructors']);
    Route::get('instructors/all',   [MainController::class, 'allInstructors']);
    Route::get('instructor/get',    [MainController::class, 'instructor']);
    Route::get('course/instructor', [MainController::class, 'courseInstructor']);
});
