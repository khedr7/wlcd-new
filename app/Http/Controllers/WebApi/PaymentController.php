<?php

namespace App\Http\Controllers\WebApi;

use App\Course;
use App\Http\Controllers\Controller;
use App\Http\Traits\SendNotification;
use App\NewNotification;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use SendNotification;

    public function enroll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if ($errors->first('course_id')) {
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $auth = Auth::user();

        $course = Course::where('id', $request->course_id)->first();

        $order = Order::create([
            'user_id' => $auth->id,
            'instructor_id' => $course->user_id,
            'course_id' => $course->id,
            'total_amount' => 'Free',
            'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ]);
        if ($order) {
            $admins = User::where('role', 'admin')->where('status', 1)->get();
            $instructor = User::where('id', $course->user_id)->where('status', 1)->first();
            $unique_instructor = 1;
            foreach ($admins as $admin) {
                if (isset($instructor)) {
                    if ($instructor->id == $admin->id) {
                        $unique_instructor = 0;
                    }
                }
                $body = 'A new enrollment request has been added to course: ' . $course->title;
                $notification = NewNotification::create(['body' => $body]);
                $notification->users()->attach(['user_id' => $admin->id]);
                if (isset($admin->device_token)) {
                    $this->send_notification($admin->device_token, 'Course Enrollment', $body);
                }
            }
            if ($unique_instructor == 1 && isset($instructor)) {
                $body = 'A new enrollment request has been added to course: ' . $course->title;
                $notification = NewNotification::create(['body' => $body]);
                $notification->users()->attach(['user_id' => $instructor->id]);
                if (isset($admin->device_token)) {
                    $this->send_notification($admin->device_token, 'Course Enrollment', $body);
                }
            }
        }

        return response()->json(array('message' => 'User Enrolled', 'status' => 'success'), 200);
    }

    public function purchaseHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required']);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = Auth::user();

        $enroll = Order::where('user_id', $user->id)->where('status', 1)
        ->with([
            'courses' => function ($query) {
                $query->where('status', 1)->select( 'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
                                        'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags')
                    ->where('status', 1)
                    ->with([
                        'language' => function ($query) {
                            $query->where('status', 1)->select('id', 'name');
                        },
                        'user' => function ($query) {
                            $query->where('status', 1)->select('id', 'fname', 'lname', 'user_img');
                        },
                    ])
                    ->withCount([
                        'chapter' => function ($query) {
                            $query->where('status', 1);
                        },
                        'order' => function ($query) {
                            $query->where('status', 1);
                        },
                    ]);
            },
        ])
        ->get();

        return response()->json(array('orderhistory' => $enroll), 200);
    }
}
