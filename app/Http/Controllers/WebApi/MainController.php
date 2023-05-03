<?php

namespace App\Http\Controllers\WebApi;

use App\About;
use App\Adsense;
use App\Announcement;
use App\Answer;
use App\Appointment;
use App\Assignment;
use App\Attandance;
use App\BBL;
use App\Blog;
use App\BundleCourse;
use App\Career;
use App\Cart;
use App\Categories;
use App\CategorySlider;
use App\ChildCategory;
use App\Contact;
use App\Contactreason;
use App\Coupon;
use App\Course;
use App\CourseChapter;
use App\CourseClass;
use App\CourseProgress;
use App\CourseReport;
use App\Currency;
use App\FaqInstructor;
use App\FaqStudent;
use App\FavCategory;
use App\FavSubcategory;
use App\GetStarted;
use App\Googlemeet;
use App\Http\Controllers\Controller;
use App\Instructor;
use App\JitsiMeeting;
use App\Mail\UserAppointment;
use App\Meeting;
use App\Order;
use App\Page;
use App\PreviousPaper;
use App\PrivateCourse;
use App\Question;
use App\QuizAnswer;
use App\QuizTopic;
use App\ReviewHelpful;
use App\ReviewRating;
use App\Setting;
use App\Slider;
use App\SliderFacts;
use App\SubCategory;
use App\Terms;
use App\Testimonial;
use App\Trusted;
use App\User;
use App\WatchCourse;
use App\Wishlist;
use Avatar;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Is_wishlist;
use App\Quiz;
use Mail;
use Validator;
use App\Http\Traits\SendNotification;
use App\NewNotification;

class MainController extends Controller
{
  
    function homeSliders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        App::setlocale($request->lang);

        $slider = Slider::where('status', '1')
            ->orderBy('position', 'ASC')
            ->get();
        $sliderfacts = SliderFacts::get();

        return response()->json(['slider' => $slider, 'sliderfacts' => $sliderfacts], 200);
    }
}
