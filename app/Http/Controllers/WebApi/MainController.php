<?php

namespace App\Http\Controllers\WebApi;

use App\Http\Controllers\Controller;
use App\Course;
use App\Helpers\Is_wishlist;
use App\Http\Traits\SendNotification;
use App\Order;
use App\ReviewRating;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    public function homeInstructors(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required']);
        }

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        App::setlocale($request->lang);

        $instructors = User::select('id', 'fname', 'lname', 'mobile', 'email', 'user_img', 'role', 'detail')
            ->where('status', 1)
            ->whereIn('role', ['instructor', 'admin'])
            ->withCount([
                'courses' => function ($query) {
                    $query->where('status', 1);
                },
                'courseOrders' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->orderBy('courses_count', 'desc')
            ->take(8)
            ->get();


        return response()->json(['instructors' => $instructors], 200);
    }
}
