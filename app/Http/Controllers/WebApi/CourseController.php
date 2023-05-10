<?php

namespace App\Http\Controllers\WebApi;

use App\Helpers\Is_wishlist;
use App\Http\Controllers\Controller;
use App\Http\Traits\SendNotification;
use Illuminate\Http\Request;
use App\{
    BundleCourse,
    Course,
    CourseChapter,
    CourseProgress,
    Googlemeet,
    Order,
    PrivateCourse,
    ReviewRating,
    
};
use Illuminate\Support\Facades\{
    App,
    Auth,
    DB,
    Validator,
    
};

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

        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
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
            ])
            ->orderByDesc('id')
            ->take(8)
            ->get();

        foreach ($course as $result) {

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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

        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
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
            ])
            ->orderByDesc('id')
            ->paginate(8);



        foreach ($course as $result) {
            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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

        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->where('status', 1)
            ->where('featured', 1)
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
            ])
            ->orderByDesc('id')
            ->take(8)
            ->get();

        foreach ($course as $result) {

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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


        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->where('status', 1)
            ->where('featured', 1)
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
            ])
            ->orderByDesc('id')
            ->paginate();

        foreach ($course as $result) {

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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

        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->where('status', 1)
            ->where('type', 1)
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
            ])
            ->orderBy('order_count', 'desc')
            ->take(8)
            ->get();

        foreach ($course as $result) {

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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

        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->where('status', 1)
            ->where('type', 1)
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
            ])
            ->orderBy('order_count', 'desc')
            ->paginate(8);

        foreach ($course as $result) {

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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

        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->whereIn('id', $mycourses_id)
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
            ])
            ->orderByDesc('id')
            ->take(8)
            ->get();

        foreach ($course as $result) {

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            // dd($enrolled_status);
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


        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->whereIn('id', $mycourses_id)
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
            ])
            ->orderByDesc('id')
            ->paginate(8);

        foreach ($course as $result) {

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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

        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->where('status', 1)
            ->where('type', '!=', 1)
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
            ])
            ->orderByDesc('id')
            ->take(8)
            ->get();


        foreach ($course as $result) {

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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


        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->where('status', 1)
            ->where('type', '!=', 1)
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
            ])
            ->orderByDesc('id')
            ->paginate(8);

        foreach ($course as $result) {

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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

        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->where('status', 1)
            ->where('type', '1')
            ->where('discount_price', '!=', null)
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
            ])
            ->get();

        foreach ($course as $result) {

            if ($result->price != 0) {
                $result->discount_percentage = (($result->price - $result->discount_price) / $result->price) * 100;
            }

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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

        $course = Course::select([
            'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title',
            'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
        ])
            ->where('status', 1)
            ->where('type', '1')
            ->where('discount_price', '!=', null)
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
            ])
            ->paginate(8);

        foreach ($course as $result) {

            if ($result->price != 0) {
                $result->discount_percentage = (($result->price - $result->discount_price) / $result->price) * 100;
            }

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
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

    public function courseDetails(Request $request)
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

        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();

            $private_courses = PrivateCourse::where('status', 1)
                ->where('course_id', '=', $request->course_id)
                ->first();

            if (isset($private_courses)) {
                $user_id = [];
                array_push($user_id, $private_courses->user_id);
                $user_id = array_values(array_filter($user_id));
                $user_id = array_flatten($user_id);

                $user_id;

                if (in_array($user->id, $user_id)) {
                    return response()->json(['Unauthorized Action'], 401);
                }
            }
        }

        $result = Course::where('id', '=', $request->course_id)
            ->where('status', 1)
            ->with('category')
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1)->select('id', 'course_id', 'icon', 'detail', 'status');
                },
                'whatlearns' => function ($query) {
                    $query->where('status', 1)->select('id', 'course_id', 'detail', 'status');
                },
                'language' => function ($query) {
                    $query->where('status', 1)->select('id', 'name');
                },

                'policy',
            ])
            ->withCount([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
                'order' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->first();

        if (!$result) {
            return response()->json('404 | Course not found !');
        }

        $reviews = ReviewRating::where('course_id', $request->course_id)
            ->where('status', '1')
            ->get();
        $count = ReviewRating::where('course_id', $request->course_id)->count();
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

        //learn
        $learn = 0;
        $total = 0;
        $total_learn = 0;

        if ($count > 0) {
            $count = ReviewRating::where('course_id', $request->course_id)->count();

            foreach ($reviews as $review) {
                $learn = $review->learn * 5;
                $total = $total + $learn;
            }

            $count = $count * 1 * 5;
            $rat = $total / $count;
            $ratings_var1 = ($rat * 100) / 5;

            $total_learn = $ratings_var1;
        }

        //price
        $price = 0;
        $total = 0;
        $total_price = 0;

        if ($count > 0) {
            $count = ReviewRating::where('course_id', $request->course_id)->count();

            foreach ($reviews as $review) {
                $price = $review->price * 5;
                $total = $total + $price;
            }

            $count = $count * 1 * 5;
            $rat = $total / $count;
            $ratings_var2 = ($rat * 100) / 5;

            $total_price = $ratings_var2;
        }

        //value
        $value = 0;
        $total = 0;
        $total_value = 0;

        if ($count > 0) {
            $count = ReviewRating::where('course_id', $request->course_id)->count();

            foreach ($reviews as $review) {
                $value = $review->value * 5;
                $total = $total + $value;
            }

            $count = $count * 1 * 5;
            $rat = $total / $count;
            $ratings_var3 = ($rat * 100) / 5;

            $total_value = $ratings_var3;
        }

        $result->makeHidden(['review', 'instructor_revenue', 'new_course_mail', 'reject_txt', 'involvement_request']);

        $enrollment_status = Order::where('course_id', $request->course_id)->where('user_id', Auth::guard('api')->id())->first();

        return response()->json([
            'course' => $result,
            'learn' => $total_learn,
            'price' => $total_price,
            'value' => $total_value,
            'total_rating_percent' => $course_total_rating,
            'total_rating' => $total_rating,
            'enrollment_status' => isset($enrollment_status) ? 1 : 0,
        ]);
    }

    public function courseChpaters(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required|exists:courses,id',

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

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        App::setlocale($request->lang);

        $chapters = CourseChapter::where('status', 1)
            ->where('course_id', $request->course_id)
            ->with([
                'courseclass' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->withCount([
                'courseclass' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->get();

        return response()->json(['chapters' => $chapters], 200);
    }

    public function courseProgress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required|exists:courses,id',

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

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $auth = Auth::guard('api')->user();

        $order = Order::where('user_id', '=', $auth->id)->where('course_id', '=', $request->course_id)->where('status', '=', 1)->first();
        if (!isset($order)) {
            return response()->json(['message' => 'Buy the course first'], 403);
        }

        $progress = CourseProgress::where('course_id', $request->course_id)
            ->where('user_id', $auth->id)
            ->first();

        return response()->json(['progress' => $progress], 200);
    }

    public function courseprogressupdate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'checked' => 'required',
            'course_id' => 'required|exists:courses,id',
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

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $auth = Auth::guard('api')->user();

        $order = Order::where('user_id', '=', $auth->id)->where('course_id', '=', $request->course_id)->where('status', '=', 1)->first();
        if (!isset($order)) {
            return response()->json(['message' => 'Buy the course first'], 403);
        }

        $course_return = $request->checked;
        $course = Course::where('id', $request->course_id)->first();

        $progress = CourseProgress::where('course_id', $course->id)
            ->where('user_id', $auth->id)
            ->first();

        if (isset($progress)) {
            $chapter = CourseChapter::where('status', 1)
                ->where('course_id', $course->id)
                ->get();

            $chapter_id = [];

            foreach ($chapter as $c) {
                array_push($chapter_id, "$c->id");
            }

            $updated_progress = CourseProgress::where('course_id', $course->id)
                ->where('user_id', '=', $auth->id)
                ->update([
                    'mark_chapter_id' => $course_return,
                    'all_chapter_id' => $chapter_id,
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                ]);

            return response()->json(['created_progress' => $updated_progress], 200);
        } else {
            $chapter = CourseChapter::where('status', 1)
                ->where('course_id', $course->id)
                ->get();

            $chapter_id = [];

            foreach ($chapter as $c) {
                array_push($chapter_id, "$c->id");
            }

            $created_progress = CourseProgress::create([
                'course_id' => $course->id,
                'user_id' => $auth->id,
                'mark_chapter_id' => json_decode($course_return, true),
                'all_chapter_id' => $chapter_id,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]);

            return response()->json(['created_progress' => $created_progress], 200);
        }
    }

    public function courseGoogleMeetings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required|exists:courses,id',

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

        $auth = Auth::guard('api')->user();

        $order = Order::where('user_id', '=', $auth->id)->where('course_id', '=', $request->course_id)->where('status', '=', 1)->first();
        if (!isset($order)) {
            return response()->json(['message' => 'Buy the course first'], 403);
        }

        $google_meet = Googlemeet::where('course_id', '=', $request->course_id)->get();


        return response()->json(array('google_meet' => $google_meet), 200);
    }
}
