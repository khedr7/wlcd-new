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
        Route::get('course/progress', [CourseController::class, 'courseProgress']);
        Route::post('course/progress/update',[CourseController::class, 'courseprogressupdate']);
        Route::get('course/googleMeetings', [CourseController::class, 'courseGoogleMeetings']);
    });

    //general api
    Route::get('sliders',      [MainController::class, 'homeSliders']);
    Route::get('testimonials', [MainController::class, 'homeTestimonials']);
    Route::get('videoSetting', [MainController::class, 'videoSetting']);
    Route::post('contactus',   [MainController::class, 'contactus']);
    Route::get('contactus/reasons', [MainController::class, 'contactReasons']);
    Route::get('contact/details',   [MainController::class, 'contactDetails']);
    Route::get('blog',              [MainController::class,'blog']);
    Route::get('home_blog',         [MainController::class,'home_blog']);
    Route::get('blogdetail',        [MainController::class,'blogdetail']);
    Route::get('faq',               [MainController::class,'faq']);
    Route::get('policy',            [MainController::class,'terms']);
    Route::get('deals',             [MainController::class,'deals']);
    Route::post('follow',           [MainController::class,'follow']);
    Route::post('unfollow',         [MainController::class,'unfollow']);
   
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

    Route::get('course/details',  [CourseController::class, 'courseDetails']);
    Route::get('course/chapters', [CourseController::class, 'courseChpaters']);


    //Get instructors
    Route::get('instructors/home',  [MainController::class, 'homeInstructors']);
    Route::get('instructors/all',   [MainController::class, 'allInstructors']);
    Route::get('instructor/get',    [MainController::class, 'instructor']);
    Route::get('course/instructor', [MainController::class, 'courseInstructor']);
});
