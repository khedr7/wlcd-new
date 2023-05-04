<?php

namespace App\Http\Controllers\WebApi;

use App\BundleCourse;
use App\Course;
use App\Helpers\Is_wishlist;
use App\Http\Controllers\Controller;
use App\Http\Traits\SendNotification;
use App\Order;
use App\ReviewRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    use SendNotification;

    public function recentCourses(Request $request)
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

        $course = Course::where('status', 1)
            ->orderBy('id', 'DESC')
            ->take(8)
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->get();

        foreach ($course as $result) {


            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        return response()->json(['recentcourses' => $course], 200);
    }

    public function allRecentCourses(Request $request)
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

        $course = Course::where('status', 1)
            ->orderBy('id', 'DESC')
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->paginate(8);

        foreach ($course as $result) {

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        return response()->json(['recentcourses' => $course], 200);
    }

    public function featuredCourses(Request $request)
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

        $course = Course::where('status', 1)
            ->where('featured', 1)
            ->orderBy('id', 'DESC')
            ->take(8)
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->get();

        foreach ($course as $result) {

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        return response()->json(['featuredcourses' => $course], 200);
    }

    public function allFeaturedCourses(Request $request)
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

        $course = Course::where('status', 1)
            ->where('featured', 1)
            ->orderBy('id', 'DESC')
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->paginate(8);

        foreach ($course as $result) {

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        return response()->json(['featuredcourses' => $course], 200);
    }

    public function bestSellingCourses(Request $request)
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

        $course = Course::where('status', 1)
            ->where('type', 1)
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->withcount([
                'order' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->orderBy('order_count', 'desc')
            ->take(8)
            ->get();

        foreach ($course as $result) {
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter', 'order');
        return response()->json(['bestselling' => $course], 200);
    }

    public function allBestSellingCourses(Request $request)
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

        $course = Course::where('status', 1)
            ->where('type', 1)
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->withcount([
                'order' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->orderBy('order_count', 'desc')
            ->paginate(8);

        foreach ($course as $result) {
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter', 'order');
        return response()->json(['allbestselling' => $course], 200);
    }

    public function myCourses(Request $request)
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

        $my_orders = Order::where('status', '=', 1)->where('user_id', '=', Auth::guard('api')->id())->get(['id', 'course_id']);
        $mycourses_id = [];
        foreach ($my_orders as $myorder) {
            array_push($mycourses_id, $myorder->course_id);
        }
        $course = Course::where('status', 1)->whereIn('id', $mycourses_id)
            ->orderBy('id', 'DESC')
            ->take(8)
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->get();

        foreach ($course as $result) {

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        return response()->json(['mycourses' => $course], 200);
    }

    public function myAllCourses(Request $request)
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

        $my_orders = Order::where('status', '=', 1)->where('user_id', '=', Auth::guard('api')->id())->get(['id', 'course_id']);
        $mycourses_id = [];
        foreach ($my_orders as $myorder) {
            array_push($mycourses_id, $myorder->course_id);
        }

        $course = Course::where('status', 1)->whereIn('id', $mycourses_id)
            ->orderBy('id', 'DESC')
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->paginate(8);

        foreach ($course as $result) {

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        return response()->json(['myallcourses' => $course], 200);
    }

    public function freeCourses(Request $request)
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

        $course = Course::where('status', 1)->where('type', '!=', 1)
            ->orderBy('id', 'DESC')
            ->take(8)
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->get();

        foreach ($course as $result) {

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        return response()->json(['freecourses' => $course], 200);
    }

    public function allFreeCourses(Request $request)
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

        $course = Course::where('status', 1)->where('type', '!=', 1)
            ->orderBy('id', 'DESC')
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->paginate(8);

        foreach ($course as $result) {

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        return response()->json(['allfreecourses' => $course], 200);
    }

    public function topDiscountedCourses(Request $request)
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

        $course = Course::where('status', 1)
            ->where('type', '1')
            ->where('discount_price', '!=', null)
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->get();

        foreach ($course as $result) {

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            if ($result->price != 0) {
                $result->discount_percentage = (($result->price - $result->discount_price) / $result->price) * 100;
            }

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        $course = $course->sortByDesc('discount_percentage')->take(8);
        $course = $course->values()->all();
        return response()->json(['topDiscountedcourses' => $course], 200);
    }

    public function allTopDiscountedCourses(Request $request)
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

        $course = Course::where('status', 1)
            ->where('type', '1')
            ->where('discount_price', '!=', null)
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'name');
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->where('status', 1)
                        ->select('id', 'fname', 'lname', 'user_img');
                },
            ])
            ->paginate(8);

        foreach ($course as $result) {

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            if ($result->price != 0) {
                $result->discount_percentage = (($result->price - $result->discount_price) / $result->price) * 100;
            }

            $enrolled_status = Order::where('course_id', $result->course_id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }


            $reviews = ReviewRating::where('course_id', $result->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $result->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {
                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = $count * 3 * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = $count * 3 * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = $ratings_var0 / 2 / 10;

                $total_rating = round($overallrating, 1);
            }

            $result->in_wishlist = Is_wishlist::in_wishlist($result->id);
            $result->total_rating_percent = round($course_total_rating, 2);
            $result->total_rating = $total_rating;
        }

        $course->makehidden('chapter');
        $course = $course->setCollection($course->sortByDesc('discount_percentage')->values());
        return response()->json(['topDiscountedcourses' => $course], 200);
    }

    public function bundleCourses(Request $request)
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

        $bundles = BundleCourse::where('status', 1)->take(8)->get();

        $result = [];

        foreach ($bundles as $bundle) {
            $courses_in_bundle = [];

            foreach ($bundle->course_id as $bundles) {
                $course = Course::where('id', $bundles)->first();

                $courses_in_bundle[] = [
                    'id' => $course->id,
                    'user' => $course->user->fname,
                    'title' => $course->title,
                    'short_detail' => $course->short_detail,
                    'image' => $course->preview_image,
                    'img_path' => url('images/course/' . $course->preview_image),
                    'type' => $course->type,
                    'price' => $course->price,
                    'discount_price' => $course->discount_price,
                ];
            }

            $result[] = [
                'id' => $bundle->id,
                'user' => $bundle->user->fname . ' ' . $bundle->user->lname,
                'user_image' => $bundle->user->user_img,
                'user_image_path' => url('images/user_img/' . $bundle->user->user_img),
                'course_id' => $bundle->course_id,
                'title' => $bundle->title,
                'detail' => strip_tags($bundle->detail),
                'price' => $bundle->price,
                'discount_price' => $bundle->discount_price,
                'type' => $bundle->type,
                'slug' => $bundle->slug,
                'status' => $bundle->status,
                'featured' => $bundle->featured,
                'preview_image' => $bundle->preview_image,
                'imagepath' => url('images/bundle/' . $bundle->preview_image),
                'created_at' => $bundle->created_at,
                'updated_at' => $bundle->updated_at,
                'course' => $courses_in_bundle,
            ];
        }

        if (empty($result)) {
            return response()->json(['bundle' => $result], 200);
        }

        return response()->json(['bundle' => $result], 200);
    }

    public function allBundleCourses(Request $request)
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

        $bundles = BundleCourse::where('status', 1)->paginate(8);

        $result = [];

        foreach ($bundles as $bundle) {
            $courses_in_bundle = [];

            foreach ($bundle->course_id as $bundles) {
                $course = Course::where('id', $bundles)->first();

                $courses_in_bundle[] = [
                    'id' => $course->id,
                    'user' => $course->user->fname,
                    'title' => $course->title,
                    'short_detail' => $course->short_detail,
                    'image' => $course->preview_image,
                    'img_path' => url('images/course/' . $course->preview_image),
                    'type' => $course->type,
                    'price' => $course->price,
                    'discount_price' => $course->discount_price,
                ];
            }

            $result[] = [
                'id' => $bundle->id,
                'user' => $bundle->user->fname . ' ' . $bundle->user->lname,
                'user_image' => $bundle->user->user_img,
                'user_image_path' => url('images/user_img/' . $bundle->user->user_img),
                'course_id' => $bundle->course_id,
                'title' => $bundle->title,
                'detail' => strip_tags($bundle->detail),
                'price' => $bundle->price,
                'discount_price' => $bundle->discount_price,
                'type' => $bundle->type,
                'slug' => $bundle->slug,
                'status' => $bundle->status,
                'featured' => $bundle->featured,
                'preview_image' => $bundle->preview_image,
                'imagepath' => url('images/bundle/' . $bundle->preview_image),
                'created_at' => $bundle->created_at,
                'updated_at' => $bundle->updated_at,
                'course' => $courses_in_bundle,
            ];
        }

        if (empty($result)) {
            return response()->json(['bundle' => $result], 200);
        }

        return response()->json(['bundle' => $result], 200);
    }
}
