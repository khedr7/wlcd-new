<?php

namespace App\Http\Controllers\Api;

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
use App\RelatedCourse;
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
use Illuminate\Support\Facades\App;

class MainController extends Controller
{
    use SendNotification;

    function homeSetting(Request $request)
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

        $settings = Setting::first();

        $currency2 = Currency::where('default', '1')->first();

        $currency = [
            'id' => $currency2->id,
            'icon' => $currency2->symbol,
            'currency' => $currency2->code,
            'default' => $currency2->default,
            'created_at' => $currency2->created_at,
            'updated_at' => $currency2->updated_at,
            'name' => $currency2->name,
            'format' => $currency2->format,
            'exchange_rate' => $currency2->default == 1 ? 1 : $currency2->exchange_rate,
        ];

        return response()->json(['settings' => $settings, 'currency' => $currency], 200);
    }
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

        $slider = Slider::where('status', '1')
            ->orderBy('position', 'ASC')
            ->get();
        $sliderfacts = SliderFacts::get();

        return response()->json(['slider' => $slider, 'sliderfacts' => $sliderfacts], 200);
    }
    function homeTestimonials(Request $request)
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

        $testimonials = Testimonial::where('status', 1)->get();

        $testimonial_result = [];

        foreach ($testimonials as $testimonial) {
            $testimonial_result[] = [
                'id' => $testimonial->id,
                'client_name' => $testimonial->client_name,
                'details' => strip_tags($testimonial->details),
                'status' => $testimonial->status,
                'image' => $testimonial->image,
                'imagepath' => url('images/testimonial/' . $testimonial->image),
                'created_at' => $testimonial->created_at,
                'updated_at' => $testimonial->created_at,
            ];
        }

        return response()->json(['testimonial' => $testimonial_result], 200);
    }
    function homeCategories(Request $request)
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

        $category = Categories::where('status', 1)
            ->orderBy('position', 'asc')
            ->get();

        $subcategory = SubCategory::where('status', 1)->get();
        $childcategory = ChildCategory::where('status', 1)->get();

        $featured_cate = Categories::where('status', 1)
            ->orderBy('position', 'asc')
            ->where('featured', 1)
            ->get();

        return response()->json(['category' => $category, 'subcategory' => $subcategory, 'childcategory' => $childcategory, 'featured_cate' => $featured_cate,], 200);
    }
    function homeAllCategories(Request $request)
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

        $category = Categories::where('status', 1)
            ->orderBy('position', 'asc')
            ->get();

        $all_categories = [];

        foreach ($category as $cate) {
            $cate_subcategory = SubCategory::where('status', 1)
                ->where('category_id', $cate->id)
                ->with('childcategory')
                ->get();

            $all_categories[] = [
                'id' => $cate->id,
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $cate->getTranslations('title')),
                'icon' => $cate->icon,
                'slug' => $cate->slug,
                'status' => $cate->status,
                'featured' => $cate->featured,
                'image' => $cate->cat_image,
                'imagepath' => url('images/category/' . $cate->cat_image),
                'position' => $cate->position,
                'created_at' => $cate->created_at,
                'updated_at' => $cate->updated_at,
                'subcategory' => $cate_subcategory,
            ];
        }

        $category_slider = CategorySlider::first();

        $category_slider1 = [];

        if (isset($category_slider)) {
            foreach ($category_slider->category_id as $cats) {
                $catee = Categories::find($cats);

                if (isset($catee)) {
                    $category_slider1[] = [
                        'id' => $catee->id,
                        'title' => array_map(function ($lang) {
                            return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                        }, $catee->getTranslations('title')),
                    ];
                }
            }

            //Display only first category course

            // find first category from the @array $category_slider

            $firstcat = Categories::whereHas('courses', function ($q) {
                return $q->where('status', '=', '1');
            })
                ->whereHas('courses.user')
                ->with(['courses', 'courses.user'])
                ->find($category_slider->category_id[0]);

            if (isset($firstcat)) {
                foreach ($firstcat->courses as $course) {
                    $category_slider_courses[] = [
                        'id' => $course->id,

                        'title' => array_map(function ($lang) {
                            return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                        }, $course->getTranslations('title')),
                        'level_tags' => $course->level_tags,
                        'short_detail' => array_map(function ($lang) {
                            return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                        }, $course->getTranslations('short_detail')),
                        'price' => $course->price,
                        'discount_price' => $course->discount_price,
                        'featured' => $course->featured,
                        'status' => $course->status,
                        'preview_image' => $course->preview_image,
                        'imagepath' => url('images/course/' . $course->preview_image),
                        'total_rating_percent' => course_rating($course->id)->getData()->total_rating_percent,
                        'total_rating' => course_rating($course->id)->getData()->total_rating,
                        'in_wishlist' => Is_wishlist::in_wishlist($course->id),
                        'instructor' => [
                            'id' => $course->user->id,
                            'name' => $course->user->fname . ' ' . $course->user->lname,
                            'image' => url('/images/user_img/' . $course->user->user_img),
                        ],
                    ];
                }

                $category_slider1[0]['course'] = $category_slider_courses;
            }
        }

        return response()->json(['allcategory' => $all_categories, 'category_slider' => $category_slider1], 200);
    }

    public function home(Request $request)
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

        $settings = Setting::first();

        $adsense = Adsense::first();
        $currency2 = Currency::where('default', '1')->first();

        $currency = [
            'id' => $currency2->id,
            'icon' => $currency2->symbol,
            'currency' => $currency2->code,
            'default' => $currency2->default,
            'created_at' => $currency2->created_at,
            'updated_at' => $currency2->updated_at,
            'name' => $currency2->name,
            'format' => $currency2->format,
            'exchange_rate' => $currency2->default == 1 ? 1 : $currency2->exchange_rate,
        ];
        $slider = Slider::where('status', '1')
            ->orderBy('position', 'ASC')
            ->get();
        $sliderfacts = SliderFacts::get();
        $trusted = Trusted::where('status', 1)->get();

        $testimonials = Testimonial::where('status', 1)->get();

        $testimonial_result = [];

        foreach ($testimonials as $testimonial) {
            $testimonial_result[] = [
                'id' => $testimonial->id,
                'client_name' => $testimonial->client_name,
                'details' => strip_tags($testimonial->details),
                'status' => $testimonial->status,
                'image' => $testimonial->image,
                'imagepath' => url('images/testimonial/' . $testimonial->image),
                'created_at' => $testimonial->created_at,
                'updated_at' => $testimonial->created_at,
            ];
        }

        $category = Categories::where('status', 1)
            ->orderBy('position', 'asc')
            ->get();

        $subcategory = SubCategory::where('status', 1)->get();
        $childcategory = ChildCategory::where('status', 1)->get();

        $featured_cate = Categories::where('status', 1)
            ->orderBy('position', 'asc')
            ->where('featured', 1)
            ->get();

        $all_categories = [];

        foreach ($category as $cate) {
            $cate_subcategory = SubCategory::where('status', 1)
                ->where('category_id', $cate->id)
                ->with('childcategory')
                ->get();

            $all_categories[] = [
                'id' => $cate->id,
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $cate->getTranslations('title')),
                'icon' => $cate->icon,
                'slug' => $cate->slug,
                'status' => $cate->status,
                'featured' => $cate->featured,
                'image' => $cate->cat_image,
                'imagepath' => url('images/category/' . $cate->cat_image),
                'position' => $cate->position,
                'created_at' => $cate->created_at,
                'updated_at' => $cate->updated_at,
                'subcategory' => $cate_subcategory,
            ];
        }

        $meeting = Meeting::get();

        $getstarted = GetStarted::first();

        $category_slider = CategorySlider::first();

        $category_slider1 = [];

        if (isset($category_slider)) {
            foreach ($category_slider->category_id as $cats) {
                $catee = Categories::find($cats);

                if (isset($catee)) {
                    $category_slider1[] = [
                        'id' => $catee->id,
                        'title' => array_map(function ($lang) {
                            return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                        }, $catee->getTranslations('title')),
                    ];
                }
            }

            //Display only first category course

            // find first category from the @array $category_slider

            $firstcat = Categories::whereHas('courses', function ($q) {
                return $q->where('status', '=', '1');
            })
                ->whereHas('courses.user')
                ->with(['courses', 'courses.user'])
                ->find($category_slider->category_id[0]);

            if (isset($firstcat)) {
                foreach ($firstcat->courses as $course) {
                    $category_slider_courses[] = [
                        'id' => $course->id,

                        'title' => array_map(function ($lang) {
                            return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                        }, $course->getTranslations('title')),
                        'level_tags' => $course->level_tags,
                        'short_detail' => array_map(function ($lang) {
                            return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                        }, $course->getTranslations('short_detail')),
                        'price' => $course->price,
                        'discount_price' => $course->discount_price,
                        'featured' => $course->featured,
                        'status' => $course->status,
                        'preview_image' => $course->preview_image,
                        'imagepath' => url('images/course/' . $course->preview_image),
                        'total_rating_percent' => course_rating($course->id)->getData()->total_rating_percent,
                        'total_rating' => course_rating($course->id)->getData()->total_rating,
                        'in_wishlist' => Is_wishlist::in_wishlist($course->id),
                        'instructor' => [
                            'id' => $course->user->id,
                            'name' => $course->user->fname . ' ' . $course->user->lname,
                            'image' => url('/images/user_img/' . $course->user->user_img),
                        ],
                    ];
                }

                $category_slider1[0]['course'] = $category_slider_courses;
            }
        }

        return response()->json(['settings' => $settings, 'adsense' => $adsense, 'currency' => $currency, 'slider' => $slider, 'sliderfacts' => $sliderfacts, 'trusted' => $trusted, 'testimonial' => $testimonial_result, 'category' => $category, 'subcategory' => $subcategory, 'childcategory' => $childcategory, 'featured_cate' => $featured_cate, 'meeting' => $meeting, 'getstarted' => $getstarted, 'allcategory' => $all_categories, 'category_slider' => $category_slider1], 200);
    }

    public function main()
    {
        return response()->json(['ok'], 200);
    }

    public function course(Request $request)
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

        $course = Course::where('status', 1)
            ->with('include')
            ->with('whatlearns')
            ->with('review')
            ->get();

        $course = $course->map(function ($c) use ($course) {
            $c['in_wishlist'] = Is_wishlist::in_wishlist($c->id);
            return $c;
        });

        return response()->json(['course' => $course], 200);
    }

    public function courseLessons(Request $request)
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


        $course = Course::where('status', 1)->where('id', $request->course_id)->first();

        $chapters = CourseChapter::where('status', 1)->where('course_id', $course->id)
            ->with([
                'courseclass' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->get();

        return response()->json(['chapters' => $chapters], 200);
    }

    public function relatedcourse(Request $request)
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

        $related = RelatedCourse::where('main_course_id', $request->course_id)->get();
        $related_id = [];
        foreach ($related as $related_course) {
            array_push($related_id, $related_course->course_id);
        }

        $course = Course::where('status', 1)->whereIn('id', $related_id)
            ->orderBy('id', 'DESC')
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with('user')
            ->get();


        foreach ($course as $result) {

            if (isset($result->review)) {
                $ratings_var11 = 0;
                $review_like = 0;
                $review_dislike = 0;

                foreach ($result->review as $key => $review) {
                    $user_count = count([$review]);
                    $user_sub_total = 0;
                    $user_learn_t = $review->learn * 5;
                    $user_price_t = $review->price * 5;
                    $user_value_t = $review->value * 5;
                    $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                    $user_count = $user_count * 3 * 5;
                    $rat1 = $user_sub_total / $user_count;
                    $ratings_var11 = ($rat1 * 100) / 5;

                    $review_like = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $request->course_id)
                        ->where('review_like', 1)
                        ->count();

                    $review_dislike = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $request->course_id)
                        ->where('review_dislike', 1)
                        ->count();

                    $reviewszz[] = [
                        'id' => $review->id,
                        'user_id' => $review->user_id,
                        'fname' => $review->user->fname,
                        'lname' => $review->user->lname,
                        'userimage' => $review->user->user_img,
                        'imagepath' => url('images/user_img/'),
                        'learn' => $review->learn,
                        'price' => $review->price,
                        'value' => $review->value,
                        'reviews' => $review->review,
                        'created_by' => $review->created_at,
                        'updated_by' => $review->updated_at,
                        'total_rating' => $ratings_var11,
                        'like_count' => $review_like,
                        'dislike_count' => $review_dislike,
                    ];
                }
            }

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $instructors_student = Order::where('instructor_id', $result->user->id)->count();
            $result->user->instructors_student = isset($instructors_student) ? $instructors_student : null;
            $result->user->course_count = Course::where('user_id', $result->user->id)->count();
        }




        $course = $course->map(function ($c) use ($course) {
            $reviews = ReviewRating::where('course_id', $c->id)
                ->where('status', '1')
                ->get();
            $count = ReviewRating::where('course_id', $c->id)->count();
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

            $c['in_wishlist'] = Is_wishlist::in_wishlist($c->id);
            $c['total_rating_percent'] = round($course_total_rating, 2);
            $c['total_rating'] = $total_rating;
            return $c;
        });

        return response()->json(['course' => $course], 200);
    }

    public function recentcourse(Request $request)
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

        $course = Course::where('status', 1)
            ->orderBy('id', 'DESC')
            ->take(5)
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                }, 'language' => function ($query) {
                    $query->where('status', 1);
                },
                'review' => function ($query) {
                    $query->with('user:id,fname,lname,user_img');
                },
                'user',
            ])
            ->get();


        foreach ($course as $result) {

            if (isset($result->review)) {
                $ratings_var11 = 0;
                $review_like = 0;
                $review_dislike = 0;

                foreach ($result->review as $key => $review) {
                    $user_count = count([$review]);
                    $user_sub_total = 0;
                    $user_learn_t = $review->learn * 5;
                    $user_price_t = $review->price * 5;
                    $user_value_t = $review->value * 5;
                    $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                    $user_count = $user_count * 3 * 5;
                    $rat1 = $user_sub_total / $user_count;
                    $ratings_var11 = ($rat1 * 100) / 5;

                    $review_like = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_like', 1)
                        ->count();

                    $review_dislike = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_dislike', 1)
                        ->count();

                    $review->review_like = $review_like;
                    $review->review_dislike = $review_dislike;
                }
            }

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            $progress = CourseProgress::where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($progress)) {
                $result->mark_chapter_id = $progress->mark_chapter_id;
                $result->all_chapter_id  = $progress->all_chapter_id;
            } else {
                $result->mark_chapter_id = null;
                $result->all_chapter_id  = null;
            }
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }

            $instructors_student = Order::where('instructor_id', $result->user->id)->count();
            $result->user->instructors_student = isset($instructors_student) ? $instructors_student : null;
            $result->user->course_count = Course::where('user_id', $result->user->id)->count();


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
        $course->makeHidden('chapter');
        return response()->json(['course' => $course], 200);
    }
    public function allRecentcourse(Request $request)
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

        $course = Course::where('status', 1)
            ->orderBy('id', 'DESC')
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                }, 'language' => function ($query) {
                    $query->where('status', 1);
                },
                'review' => function ($query) {
                    $query->with('user:id,fname,lname,user_img');
                },
                'user'
            ])
            ->paginate(6);


        foreach ($course as $result) {

            if (isset($result->review)) {
                $ratings_var11 = 0;
                $review_like = 0;
                $review_dislike = 0;

                foreach ($result->review as $key => $review) {
                    $user_count = count([$review]);
                    $user_sub_total = 0;
                    $user_learn_t = $review->learn * 5;
                    $user_price_t = $review->price * 5;
                    $user_value_t = $review->value * 5;
                    $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                    $user_count = $user_count * 3 * 5;
                    $rat1 = $user_sub_total / $user_count;
                    $ratings_var11 = ($rat1 * 100) / 5;

                    $review_like = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_like', 1)
                        ->count();

                    $review_dislike = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_dislike', 1)
                        ->count();

                    $review->review_like = $review_like;
                    $review->review_dislike = $review_dislike;
                }
            }

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            $progress = CourseProgress::where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($progress)) {
                $result->mark_chapter_id = $progress->mark_chapter_id;
                $result->all_chapter_id  = $progress->all_chapter_id;
            } else {
                $result->mark_chapter_id = null;
                $result->all_chapter_id  = null;
            }
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }

            $instructors_student = Order::where('instructor_id', $result->user->id)->count();
            $result->user->instructors_student = isset($instructors_student) ? $instructors_student : null;
            $result->user->course_count = Course::where('user_id', $result->user->id)->count();


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
        $course->makeHidden('chapter');
        return response()->json(['course' => $course], 200);
    }

    public function featuredcourse(Request $request)
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

        $course = Course::where('status', 1)
            ->where('featured', 1)
            ->orderBy('id', 'DESC')
            ->take(5)
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                }, 'language' => function ($query) {
                    $query->where('status', 1);
                },
                'review' => function ($query) {
                    $query->with('user:id,fname,lname,user_img');
                },
                'user'
            ])
            ->get();


        foreach ($course as $result) {

            if (isset($result->review)) {
                $ratings_var11 = 0;
                $review_like = 0;
                $review_dislike = 0;

                foreach ($result->review as $key => $review) {
                    $user_count = count([$review]);
                    $user_sub_total = 0;
                    $user_learn_t = $review->learn * 5;
                    $user_price_t = $review->price * 5;
                    $user_value_t = $review->value * 5;
                    $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                    $user_count = $user_count * 3 * 5;
                    $rat1 = $user_sub_total / $user_count;
                    $ratings_var11 = ($rat1 * 100) / 5;

                    $review_like = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_like', 1)
                        ->count();

                    $review_dislike = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_dislike', 1)
                        ->count();

                    $review->review_like = $review_like;
                    $review->review_dislike = $review_dislike;
                }
            }

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            $progress = CourseProgress::where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($progress)) {
                $result->mark_chapter_id = $progress->mark_chapter_id;
                $result->all_chapter_id  = $progress->all_chapter_id;
            } else {
                $result->mark_chapter_id = null;
                $result->all_chapter_id  = null;
            }
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }

            $instructors_student = Order::where('instructor_id', $result->user->id)->count();
            $result->user->instructors_student = isset($instructors_student) ? $instructors_student : null;
            $result->user->course_count = Course::where('user_id', $result->user->id)->count();


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
        $course->makeHidden('chapter');
        return response()->json(['featured' => $course], 200);
    }

    public function allfeaturedcourse(Request $request)
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

        $course = Course::where('status', 1)
            ->where('featured', 1)
            ->orderBy('id', 'DESC')
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                }, 'language' => function ($query) {
                    $query->where('status', 1);
                },
                'review' => function ($query) {
                    $query->with('user:id,fname,lname,user_img');
                },
                'user'
            ])
            ->paginate(6);


        foreach ($course as $result) {

            if (isset($result->review)) {
                $ratings_var11 = 0;
                $review_like = 0;
                $review_dislike = 0;

                foreach ($result->review as $key => $review) {
                    $user_count = count([$review]);
                    $user_sub_total = 0;
                    $user_learn_t = $review->learn * 5;
                    $user_price_t = $review->price * 5;
                    $user_value_t = $review->value * 5;
                    $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                    $user_count = $user_count * 3 * 5;
                    $rat1 = $user_sub_total / $user_count;
                    $ratings_var11 = ($rat1 * 100) / 5;

                    $review_like = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_like', 1)
                        ->count();

                    $review_dislike = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_dislike', 1)
                        ->count();

                    $review->review_like = $review_like;
                    $review->review_dislike = $review_dislike;
                }
            }

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            $progress = CourseProgress::where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($progress)) {
                $result->mark_chapter_id = $progress->mark_chapter_id;
                $result->all_chapter_id  = $progress->all_chapter_id;
            } else {
                $result->mark_chapter_id = null;
                $result->all_chapter_id  = null;
            }
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }

            $instructors_student = Order::where('instructor_id', $result->user->id)->count();
            $result->user->instructors_student = isset($instructors_student) ? $instructors_student : null;
            $result->user->course_count = Course::where('user_id', $result->user->id)->count();


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
        $course->makeHidden('chapter');
        return response()->json(['allfeatured' => $course], 200);
    }

    public function relatedCourses(Request $request)
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

        $data = RelatedCourse::where('main_course_id', $request->course_id)->where('status', 1)->get();

        $related_courses_id = [];
        foreach ($data as $related) {
            array_push($related_courses_id, $related->course_id);
        }



        $course = Course::where('status', 1)
            ->whereIn('id', $related_courses_id)
            ->orderBy('id', 'DESC')
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                }, 'language' => function ($query) {
                    $query->where('status', 1);
                },
                'review' => function ($query) {
                    $query->with('user:id,fname,lname,user_img');
                },
                'user'
            ])
            ->get();


        foreach ($course as $result) {

            if (isset($result->review)) {
                $ratings_var11 = 0;
                $review_like = 0;
                $review_dislike = 0;

                foreach ($result->review as $key => $review) {
                    $user_count = count([$review]);
                    $user_sub_total = 0;
                    $user_learn_t = $review->learn * 5;
                    $user_price_t = $review->price * 5;
                    $user_value_t = $review->value * 5;
                    $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                    $user_count = $user_count * 3 * 5;
                    $rat1 = $user_sub_total / $user_count;
                    $ratings_var11 = ($rat1 * 100) / 5;

                    $review_like = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_like', 1)
                        ->count();

                    $review_dislike = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_dislike', 1)
                        ->count();

                    $review->review_like = $review_like;
                    $review->review_dislike = $review_dislike;
                }
            }

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            $progress = CourseProgress::where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($progress)) {
                $result->mark_chapter_id = $progress->mark_chapter_id;
                $result->all_chapter_id  = $progress->all_chapter_id;
            } else {
                $result->mark_chapter_id = null;
                $result->all_chapter_id  = null;
            }
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }

            $instructors_student = Order::where('instructor_id', $result->user->id)->count();
            $result->user->instructors_student = isset($instructors_student) ? $instructors_student : null;
            $result->user->course_count = Course::where('user_id', $result->user->id)->count();


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
        $course->makeHidden('chapter');
        return response()->json(['course' => $course], 200);
    }

    public function instructorCourses(Request $request)
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

        $course = Course::where('status', 1)
            ->where('user_id', '=', $request->instructor_id)
            ->orderBy('id', 'DESC')
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                }, 'language' => function ($query) {
                    $query->where('status', 1);
                },
                'review' => function ($query) {
                    $query->with('user:id,fname,lname,user_img');
                },
                'user'
            ])
            ->get();


        foreach ($course as $result) {

            if (isset($result->review)) {
                $ratings_var11 = 0;
                $review_like = 0;
                $review_dislike = 0;

                foreach ($result->review as $key => $review) {
                    $user_count = count([$review]);
                    $user_sub_total = 0;
                    $user_learn_t = $review->learn * 5;
                    $user_price_t = $review->price * 5;
                    $user_value_t = $review->value * 5;
                    $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                    $user_count = $user_count * 3 * 5;
                    $rat1 = $user_sub_total / $user_count;
                    $ratings_var11 = ($rat1 * 100) / 5;

                    $review_like = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_like', 1)
                        ->count();

                    $review_dislike = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_dislike', 1)
                        ->count();

                    $review->review_like = $review_like;
                    $review->review_dislike = $review_dislike;
                }
            }

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            $progress = CourseProgress::where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($progress)) {
                $result->mark_chapter_id = $progress->mark_chapter_id;
                $result->all_chapter_id  = $progress->all_chapter_id;
            } else {
                $result->mark_chapter_id = null;
                $result->all_chapter_id  = null;
            }
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }

            $instructors_student = Order::where('instructor_id', $result->user->id)->count();
            $result->user->instructors_student = isset($instructors_student) ? $instructors_student : null;
            $result->user->course_count = Course::where('user_id', $result->user->id)->count();


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
        $course->makeHidden('chapter');
        return response()->json(['course' => $course], 200);
    }


    public function bundle(Request $request)
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

        $bundles = BundleCourse::where('status', 1)->get();

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

    public function studentfaq(Request $request)
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

        $faq = FaqStudent::where('status', 1)->get();
        return response()->json(['faq' => $faq], 200);
    }

    public function instructorfaq(Request $request)
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

        $faq = FaqInstructor::where('status', 1)->get();
        return response()->json(['faq' => $faq], 200);
    }

    public function blog(Request $request)
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

        $blog = Blog::where('status', 1)->get();

        $blog_result = [];

        foreach ($blog as $data) {
            $blog_result[] = [
                'id' => $data->id,
                'user' => $data->user_id,
                'date' => $data->date,
                'image' => $data->image,
                'heading' => preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($data->heading))),
                'detail' => preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($data->detail))),
                'text' => $data->text,
                'approved' => $data->approved,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];
        }

        return response()->json(['blog' => $blog_result], 200);
    }

    public function blogdetail(Request $request)
    {
        $this->validate($request, [
            'blog_id' => 'required',
        ]);

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

        $blog = Blog::where('id', $request->blog_id)
            ->where('status', 1)
            ->with('user')
            ->get();

        return response()->json(['blog' => $blog], 200);
    }

    public function recentblog(Request $request)
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

        $blog = Blog::where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(['blog' => $blog], 200);
    }

    public function showwishlist(Request $request)
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

        $user = Auth::guard('api')->user();

        $wishlist = Wishlist::where('user_id', $user->id)->orderBy('id', 'desc')->get();

        $myWishlistCourses_id = [];
        foreach ($wishlist as $wish) {
            array_push($myWishlistCourses_id, $wish->course_id);
        }

        $course = Course::where('status', 1)
            ->whereIn('id', $myWishlistCourses_id)
            ->orderByRaw("FIELD(id, " . implode(',', $myWishlistCourses_id) . ")")
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                },
                'language' => function ($query) {
                    $query->where('status', 1);
                },
                'review' => function ($query) {
                    $query->with('user:id,fname,lname,user_img');
                },
                'user'
            ])
            ->paginate(6);


        foreach ($course as $result) {

            if (isset($result->review)) {
                $ratings_var11 = 0;
                $review_like = 0;
                $review_dislike = 0;

                foreach ($result->review as $key => $review) {
                    $user_count = count([$review]);
                    $user_sub_total = 0;
                    $user_learn_t = $review->learn * 5;
                    $user_price_t = $review->price * 5;
                    $user_value_t = $review->value * 5;
                    $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                    $user_count = $user_count * 3 * 5;
                    $rat1 = $user_sub_total / $user_count;
                    $ratings_var11 = ($rat1 * 100) / 5;

                    $review_like = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_like', 1)
                        ->count();

                    $review_dislike = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_dislike', 1)
                        ->count();

                    $review->review_like = $review_like;
                    $review->review_dislike = $review_dislike;
                }
            }

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            $progress = CourseProgress::where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($progress)) {
                $result->mark_chapter_id = $progress->mark_chapter_id;
                $result->all_chapter_id  = $progress->all_chapter_id;
            } else {
                $result->mark_chapter_id = null;
                $result->all_chapter_id  = null;
            }
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }

            $instructors_student = Order::where('instructor_id', $result->user->id)->count();
            $result->user->instructors_student = isset($instructors_student) ? $instructors_student : null;
            $result->user->course_count = Course::where('user_id', $result->user->id)->count();


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
        $course->makeHidden('chapter');
        return response()->json(['course' => $course], 200);
    }
    // public function showwishlist(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'secret' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['Secret Key is required']);
    //     }

    //     $key = DB::table('api_keys')
    //         ->where('secret_key', '=', $request->secret)
    //         ->first();

    //     if (!$key) {
    //         return response()->json(['Invalid Secret Key !']);
    //     }

    //     $user = Auth::guard('api')->user();

    //     $wishlist = Wishlist::where('user_id', $user->id)

    //         ->with([
    //             'courses' => function ($query) {
    //                 $query->with('user');
    //             },
    //         ])
    //         ->get();

    //     return response()->json(['wishlist' => $wishlist], 200);
    // }

    public function addtowishlist(Request $request)
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

        $auth = Auth::guard('api')->user();

        $orders = Order::where('user_id', $auth->id)
            ->where('course_id', $request->course_id)
            ->first();

        $wishlist = Wishlist::where('course_id', $request->course_id)
            ->where('user_id', $auth->id)
            ->first();

        if (isset($orders)) {
            return response()->json('You Already purchased this course !', 401);
        } else {
            if (!empty($wishlist)) {
                return response()->json('Course is already in wishlist !', 401);
            } else {
                $wishlist = Wishlist::create([
                    'course_id' => $request->course_id,
                    'user_id' => $auth->id,
                ]);

                return response()->json('Course is added to your wishlist !', 200);
            }
        }
    }

    public function removewishlist(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required',
        ]);

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

        $auth = Auth::guard('api')->user();

        $wishlist = Wishlist::where('course_id', $request->course_id)
            ->where('user_id', $auth->id)
            ->delete();

        if ($wishlist == 1) {
            return response()->json(['1'], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function userprofile(Request $request)
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

        $user = Auth::guard('api')->user();
        $code = $user->token();
        return response()->json(['user' => $user, 'code' => $code->id], 200);
    }

    public function updateprofile(Request $request)
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

        $auth = Auth::guard('api')->user();

        $request->validate([
            'email' => 'required',
            'current_password' => 'required',
        ]);
        $input = $request->all();

        if (Hash::check($request->current_password, $auth->password)) {
            if ($file = $request->file('user_img')) {
                if ($auth->user_img != null) {
                    $image_file = @file_get_contents(public_path() . '/images/user_img/' . $auth->user_img);
                    if ($image_file) {
                        unlink(public_path() . '/images/user_img/' . $auth->user_img);
                    }
                }
                $name = time() . '_' . $file->getClientOriginalName();
                $name = str_replace(" ", "_", $name);
                $file->move('images/user_img', $name);
                $input['user_img'] = $name;
            }
            $auth->update([
                'fname' => isset($input['fname']) ? $input['fname'] : $auth->fname,
                'lname' => isset($input['lname']) ? $input['lname'] : $auth->lname,
                'email' => $input['email'],
                'password' => isset($input['password']) ? bcrypt($input['password']) : $auth->password,
                'mobile' => isset($input['mobile']) ? $input['mobile'] : $auth->mobile,
                'dob' => isset($input['dob']) ? $input['dob'] : $auth->dob,
                'user_img' => isset($input['user_img']) ? $input['user_img'] : $auth->user_img,
                'address' => isset($input['address']) ? $input['address'] : $auth->address,
                'detail' => isset($input['detail']) ? $input['detail'] : $auth->detail,
            ]);

            $auth->save();
            return response()->json(['auth' => $auth], 200);
        } else {
            return response()->json('error: password doesnt match', 400);
        }
    }

    public function addtocart(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required',
        ]);

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

        $auth = Auth::guard('api')->user();

        $courses = Course::where('id', $request->course_id)->first();

        $orders = Order::where('user_id', $auth->id)
            ->where('course_id', $request->course_id)
            ->first();
        $cart = Cart::where('course_id', $request->course_id)
            ->where('user_id', $auth->id)
            ->first();

        if (isset($courses)) {
            if ($courses->type == 1) {
                if (isset($orders)) {
                    return response()->json('You Already purchased this course !', 401);
                } else {
                    if (!empty($cart)) {
                        return response()->json('Course is already in cart !', 401);
                    } else {
                        $cart = Cart::create([
                            'course_id' => $request->course_id,
                            'user_id' => $auth->id,
                            'category_id' => $courses->category_id,
                            'price' => $courses->price,
                            'offer_price' => $courses->discount_price,
                        ]);

                        return response()->json('Course is added to your cart !', 200);
                    }
                }
            } else {
                return response()->json('Course is free', 401);
            }
        } else {
            return response()->json('Invalid Course ID', 401);
        }
    }

    public function removecart(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required',
        ]);

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

        $auth = Auth::guard('api')->user();

        $cart = Cart::where('course_id', $request->course_id)
            ->where('user_id', $auth->id)
            ->delete();

        if ($cart == 1) {
            return response()->json(['1'], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function showcart(Request $request)
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

        $user = Auth::guard('api')->user();

        $carts = Cart::where('user_id', $user->id)
            ->with('courses')

            ->with([
                'courses' => function ($query) {
                    $query->with('user');
                },
            ])

            ->with([
                'bundle' => function ($query) {
                    $query->with('user');
                },
            ])

            ->get();

        $price_total = 0;
        $offer_total = 0;
        $cpn_discount = 0;
        $offer_percent = 0;
        $offer_amount = 0;

        //cart price after offer
        foreach ($carts as $key => $c) {
            if ($c->offer_price != 0) {
                $offer_total = $offer_total + $c->offer_price;
            } else {
                $offer_total = $offer_total + $c->price;
            }
        }

        //for price total
        foreach ($carts as $key => $c) {
            $price_total = $price_total + $c->price;
        }

        //for coupon discount total
        foreach ($carts as $key => $c) {
            $cpn_discount = $cpn_discount + $c->disamount;
        }

        $cart_total = 0;

        foreach ($carts as $key => $c) {
            if ($cpn_discount != 0) {
                $cart_total = $offer_total - $cpn_discount;
            } else {
                $cart_total = $offer_total;
            }
        }

        //for offer percent
        foreach ($carts as $key => $c) {
            if ($cpn_discount != 0) {
                $offer_amount = $price_total - ($offer_total - $cpn_discount);
                $value = $offer_amount / $price_total;
                $offer_percent = $value * 100;
            } else {
                $offer_amount = $price_total - $offer_total;
                $value = $offer_amount / $price_total;
                $offer_percent = $value * 100;
            }
        }

        return response()->json(
            [
                'cart' => $carts,
                'price_total' => $price_total,
                'offer_total' => $price_total - $offer_total,
                'cpn_discount' => $cpn_discount,
                'offer_percent' => round($offer_percent, 2),
                'cart_total' => $cart_total,
            ],
            200,
        );
    }

    public function removeallcart(Request $request)
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

        $auth = Auth::guard('api')->user();

        $cart = Cart::where('user_id', $auth->id)->delete();

        if (isset($cart)) {
            return response()->json(['1'], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function addbundletocart(Request $request)
    {
        $this->validate($request, [
            'bundle_id' => 'required',
        ]);

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

        $auth = Auth::guard('api')->user();

        $bundle_course = BundleCourse::where('id', $request->bundle_id)->first();

        $orders = Order::where('user_id', $auth->id)
            ->where('bundle_id', $request->bundle_id)
            ->first();

        $cart = Cart::where('bundle_id', $request->bundle_id)
            ->where('user_id', $auth->id)
            ->first();

        if (isset($bundle_course)) {
            if ($bundle_course->type == 1) {
                if (isset($orders)) {
                    return response()->json('You Already purchased this course !', 401);
                } else {
                    if (!empty($cart)) {
                        return response()->json('Bundle Course is already in cart !', 401);
                    } else {
                        $cart = Cart::create([
                            'bundle_id' => $request->bundle_id,
                            'user_id' => $auth->id,
                            'type' => '1',
                            'price' => $bundle_course->price,
                            'offer_price' => $bundle_course->discount_price,
                            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        ]);

                        return response()->json('Bundle Course is added to your cart !', 200);
                    }
                }
            } else {
                return response()->json('Bundle course is free !', 401);
            }
        } else {
            return response()->json('Invalid Bundle Course ID !', 401);
        }
    }

    public function removebundlecart(Request $request)
    {
        $this->validate($request, [
            'bundle_id' => 'required',
        ]);

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

        $auth = Auth::guard('api')->user();

        $cart = Cart::where('bundle_id', $request->bundle_id)
            ->where('user_id', $auth->id)
            ->delete();

        if ($cart == 1) {
            return response()->json(['1'], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function detailpage(Request $request)
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
                    $query->where('status', 1);
                },
            ])

            ->with([
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'related' => function ($query) {
                    $query->where('status', 1)->with('courses');
                },
            ])
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1);
                },
            ])

            ->with('user')

            ->with([
                'order' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1)->with('user');
                },
            ])

            ->with([
                'courseclass' => function ($query) {
                    $query->where('status', 1)->with('user');
                },
            ])

            ->with('policy')
            ->first();

        if (!$result) {
            return response()->json('404 | Course not found !');
        }

        if (isset($result->review)) {
            $ratings_var11 = 0;
            $review_like = 0;
            $review_dislike = 0;

            foreach ($result->review as $key => $review) {
                $user_count = count([$review]);
                $user_sub_total = 0;
                $user_learn_t = $review->learn * 5;
                $user_price_t = $review->price * 5;
                $user_value_t = $review->value * 5;
                $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                $user_count = $user_count * 3 * 5;
                $rat1 = $user_sub_total / $user_count;
                $ratings_var11 = ($rat1 * 100) / 5;

                $review_like = ReviewHelpful::where('review_id', $review->id)
                    ->where('course_id', $request->course_id)
                    ->where('review_like', 1)
                    ->count();

                $review_dislike = ReviewHelpful::where('review_id', $review->id)
                    ->where('course_id', $request->course_id)
                    ->where('review_dislike', 1)
                    ->count();

                $reviewszz[] = [
                    'id' => $review->id,
                    'user_id' => $review->user_id,
                    'fname' => $review->user->fname,
                    'lname' => $review->user->lname,
                    'userimage' => $review->user->user_img,
                    'imagepath' => url('images/user_img/'),
                    'learn' => $review->learn,
                    'price' => $review->price,
                    'value' => $review->value,
                    'reviews' => $review->review,
                    'created_by' => $review->created_at,
                    'updated_by' => $review->updated_at,
                    'total_rating' => $ratings_var11,
                    'like_count' => $review_like,
                    'dislike_count' => $review_dislike,
                ];
            }
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

        $student_enrolled = Order::where('course_id', $request->course_id)->count();

        return response()->json([
            'course' => $result->makeHidden(['review']),
            'review' => isset($reviewszz) ? $reviewszz : null,
            'learn' => $total_learn,
            'price' => $total_price,
            'value' => $total_value,
            'total_rating_percent' => $course_total_rating,
            'total_rating' => $total_rating,
            'student_enrolled' => isset($student_enrolled) ? $student_enrolled : null,
        ]);
    }

    public function pages(Request $request)
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
        return response()->json(['pages' => Page::get()], 200);
    }

    public function allnotification(Request $request)
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

        $user = Auth::guard('api')->user();
        $notifications = $user->unreadnotifications;

        if ($notifications) {
            return response()->json(['notifications' => $notifications], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function notificationread(Request $request, $id)
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

        $userunreadnotification = Auth::guard('api')
            ->user()
            ->unreadNotifications->where('id', $id)
            ->first();

        if ($userunreadnotification) {
            $userunreadnotification->markAsRead();
            return response()->json(['1'], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function readallnotification(Request $request)
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

        $notifications = auth()
            ->User()
            ->unreadNotifications()
            ->count();

        if ($notifications > 0) {
            $user = auth()->User();

            foreach ($user->unreadNotifications as $unnotification) {
                $unnotification->markAsRead();
            }

            return response()->json(['1'], 200);
        } else {
            return response()->json(['Notification already marked as read !'], 401);
        }
    }

    public function instructorprofile(Request $request)
    {
        $this->validate($request, [
            'instructor_id' => 'required',
        ]);

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

        $user = User::where('id', $request->instructor_id)->first();
        $course_count = Course::where('user_id', $user->id)->count();
        $enrolled_user = Order::where('instructor_id', $user->id)->count();
        $course = Course::where('user_id', $user->id)->get();

        if ($user) {
            return response()->json(['user' => $user, 'course' => $course, 'course_count' => $course_count, 'enrolled_user' => $enrolled_user], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function review(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required',
        ]);

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

        $review = ReviewRating::where('course_id', $request->course_id)
            ->with('user')
            ->get();

        $review_count = ReviewRating::where('course_id', $request->course_id)->count();

        if ($review) {
            return response()->json(['review' => $review], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function duration(Request $request)
    {
        $this->validate($request, [
            'chapter_id' => 'required',
        ]);

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

        $chapter = CourseChapter::where('course_id', $request->chapter_id)->first();

        if ($chapter) {
            $duration = CourseClass::where('coursechapter_id', $chapter->id)->sum('duration');
        } else {
            return response()->json(['Invalid Chapter ID !'], 401);
        }

        if ($chapter) {
            return response()->json(['duration' => $duration], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function apikeys(Request $request)
    {
        $key = DB::table('api_keys')->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        return response()->json(['key' => $key], 200);
    }

    public function coursedetail(Request $request)
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

        $course = Course::where('status', 1)

            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
            ])

            ->with([
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'related' => function ($query) {
                    $query->where('status', 1);
                },
            ])

            ->with('review')

            ->with([
                'language' => function ($query) {
                    $query->where('status', 1);
                },
            ])

            ->with('user')

            ->with([
                'order' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                },
            ])

            ->with([
                'courseclass' => function ($query) {
                    $query->where('status', 1);
                },
            ])

            ->with('policy')
            ->get();

        return response()->json(['course' => $course], 200);
    }

    public function showcoupon(Request $request)
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

        $coupon = Coupon::get();

        return response()->json(['coupon' => $coupon], 200);
    }

    public function becomeaninstructor(Request $request)
    {
        $this->validate($request, [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required',
            'age' => 'required',
            'mobile' => 'required',
            'gender' => 'required',
            'detail' => 'required',
            'file' => 'required',
            'image' => 'required',
        ]);

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

        $auth = Auth::guard('api')->user();

        $users = Instructor::where('user_id', $auth->id)->get();

        if (!$users->isEmpty()) {
            return response()->json('Already Requested !', 401);
        } else {
            if ($file = $request->file('image')) {
                $name = time() . '_' . $file->getClientOriginalName();
                $name = str_replace(" ", "_", $name);
                $file->move('images/instructor', $name);
                $input['image'] = $name;
            }

            if ($file = $request->file('file')) {
                $name = time() . '_' . $file->getClientOriginalName();
                $name = str_replace(" ", "_", $name);
                $file->move('files/instructor/', $name);
                $input['file'] = $name;
            }

            $input = $request->all();

            $instructor = Instructor::create([
                'user_id' => $auth->id,
                'fname' => isset($input['fname']) ? $input['fname'] : $auth->fname,
                'lname' => isset($input['lname']) ? $input['lname'] : $auth->lname,
                'email' => $input['email'],
                'mobile' => isset($input['mobile']) ? $input['mobile'] : $auth->mobile,
                'age' => isset($input['age']) ? $input['age'] : $auth->age,
                'image' => isset($input['image']) ? $input['image'] : $auth->image,
                'file' => $input['file'],
                'detail' => isset($input['detail']) ? $input['detail'] : $auth->detail,
                'gender' => isset($input['gender']) ? $input['gender'] : $auth->gender,
                'status' => '0',
            ]);

            if ($instructor) {
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $body = 'A new instructor request has been added.';
                    $notification = NewNotification::create(['body' => $body]);
                    $notification->users()->attach(['user_id' => $admin->user_id]);
                }
            }

            return response()->json(['instructor' => $instructor], 200);
        }
    }

    public function aboutus(Request $request)
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

        $about = About::all()->toArray();
        return response()->json(['about' => $about], 200);
    }

    public function contactus(Request $request)
    {
        $this->validate($request, [
            'fname'   => 'required',
            'email'   => 'required',
            'mobile'  => 'required',
            'message' => 'required',
            'reason_id' => 'exists:contactreasons,id',
        ]);

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

        $created_contact = Contact::create([
            'fname' => $request->fname,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'message' => $request->message,
            'reason_id' => $request->reason_id,
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ]);

        return response()->json(['contact' => $created_contact], 200);
    }

    public function courseprogress(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required',
        ]);

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

        $auth = Auth::guard('api')->user();

        $course = Course::where('status', 1)
            ->where('id', $request->course_id)
            ->first();

        $progress = CourseProgress::where('course_id', $course->id)
            ->where('user_id', $auth->id)
            ->first();

        return response()->json(['progress' => $progress], 200);
    }

    public function courseprogressupdate(Request $request)
    {
        $this->validate($request, [
            'checked' => 'required',
            'course_id' => 'required',
        ]);

        $course_return = $request->checked;

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

        $auth = Auth::guard('api')->user();

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

    public function terms(Request $request)
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

        $terms_policy = Terms::get()->toArray();

        return response()->json(['terms_policy' => $terms_policy], 200);
    }

    public function career(Request $request)
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

        $career = Career::get()->toArray();

        return response()->json(['career' => $career], 200);
    }

    public function zoom(Request $request)
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

        $meeting = Meeting::get()->toArray();

        return response()->json(['meeting' => $meeting], 200);
    }

    public function bigblue(Request $request)
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

        $bigblue = BBL::get()->toArray();

        return response()->json(['bigblue' => $bigblue], 200);
    }

    public function coursereport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required',
            'title' => 'required',
            'email' => 'required',
            'detail' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if ($errors->first('course_id')) {
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail']);
            }
            if ($errors->first('detail')) {
                return response()->json(['message' => $errors->first('detail'), 'status' => 'fail']);
            }
        }
        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $auth = Auth::guard('api')->user();
        $course = Course::where('id', $request->course_id)->first();
        $created_report = CourseReport::create([
            'course_id' => $course->id,
            'user_id' => $auth->id,
            'title' => $course->title,
            'email' => $auth->email,
            'detail' => $request->detail,
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ]);
        return response()->json(['message' => 'Course reported!', 'status' => 'success'], 200);
    }

    public function coursecontent(Request $request, $id)
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

        $result = Course::where('id', '=', $id)
            ->where('status', 1)
            ->first();

        if (!$result) {
            return response()->json('404 | Course not found !');
        }

        $order = Order::where('course_id', $result->id)->get();

        $chapters = CourseChapter::where('course_id', $result->id)
            ->where('status', 1)
            ->with('courseclass')
            ->get();

        $classes = CourseClass::where('course_id', $result->id)
            ->where('status', 1)
            ->get();

        $overview[] = [
            'course_title' => $result->title,
            'short_detail' => strip_tags($result->short_detail),
            'detail' => strip_tags($result->detail),
            'instructor' => $result->user->fname,
            'instructor_email' => $result->user->email,
            'instructor_detail' => strip_tags($result->user->detail),
            'user_enrolled' => count($order),
            'classes' => count($classes),
        ];

        $quiz = [];

        if (isset($result->quiztopic)) {
            foreach ($result->quiztopic as $key => $topic) {
                $questions = [];

                if ($topic->type == null) {
                    foreach ($topic->quizquestion as $key => $data) {
                        if ($data->type == null) {
                            if ($data->answer == 'A') {
                                $correct_answer = $data->a;

                                $options = [$data->b, $data->c, $data->d];
                            } elseif ($data->answer == 'B') {
                                $correct_answer = $data->b;

                                $options = [$data->a, $data->c, $data->d];
                            } elseif ($data->answer == 'C') {
                                $correct_answer = $data->c;

                                $options = [$data->a, $data->b, $data->d];
                            } elseif ($data->answer == 'D') {
                                $correct_answer = $data->d;

                                $options = [$data->a, $data->b, $data->c];
                            }
                        }

                        $all_options = [
                            'A' => $data->a,
                            'B' => $data->b,
                            'C' => $data->c,
                            'D' => $data->d,
                        ];

                        $questions[] = [
                            'id' => $data->id,
                            'course' => $result->title,
                            'topic' => $topic->title,
                            'question' => $data->question,
                            'correct' => $correct_answer,
                            'status' => $data->status,
                            'incorrect_answers' => $options,
                            'all_answers' => $all_options,
                            'correct_answer' => $data->answer,
                        ];
                    }
                } elseif ($topic->type == 1) {
                    foreach ($topic->quizquestion as $key => $data) {
                        $questions[] = [
                            'id' => $data->id,
                            'course' => $result->title,
                            'topic' => $topic->title,
                            'question' => $data->question,
                            'status' => $data->status,
                            'correct' => null,
                            'correct' => null,
                            'status' => $data->status,
                            'incorrect_answers' => null,
                            'correct_answer' => null,
                        ];
                    }
                }

                $startDate = '0';

                if (Auth::guard('api')->check()) {
                    $order = Order::where('course_id', $id)
                        ->where('user_id', '=', Auth::guard('api')->user()->id)
                        ->first();

                    $days = $topic->due_days;
                    $orderDate = optional($order)['created_at'];

                    $bundle = Order::where('user_id', Auth::guard('api')->user()->id)
                        ->where('bundle_id', '!=', null)
                        ->get();

                    $course_id = [];

                    foreach ($bundle as $b) {
                        $bundle = BundleCourse::where('id', $b->bundle_id)->first();
                        array_push($course_id, $bundle->course_id);
                    }

                    $course_id = array_values(array_filter($course_id));
                    $course_id = array_flatten($course_id);

                    if ($orderDate != null) {
                        $startDate = date('Y-m-d', strtotime("$orderDate +$days days"));
                    } elseif (isset($course_id) && in_array($id, $course_id)) {
                        $startDate = date('Y-m-d', strtotime("$bundle->created_at +$days days"));
                    } else {
                        $startDate = '0';
                    }
                }

                $mytime = \Carbon\Carbon::now()->toDateString();

                $quiz[] = [
                    'id' => $topic->id,
                    'course_id' => $result->id,
                    'course' => $result->title,
                    'title' => $topic->title,
                    'description' => $topic->description,
                    'per_question_mark' => $topic->per_q_mark,
                    'status' => $topic->status,
                    'quiz_again' => $topic->quiz_again,
                    'due_days' => $topic->due_days,
                    'type' => $topic->type,
                    'timer' => $topic->timer,
                    'created_by' => $topic->created_at,
                    'updated_by' => $topic->updated_at,
                    'quiz_live_days' => $startDate,
                    'today_date' => $mytime,
                    'questions' => $questions,
                ];
            }
        }

        $announcement = Announcement::where('course_id', $id)
            ->where('status', 1)
            ->get();

        $announcements = [];

        foreach ($announcement as $announc) {
            $announcements[] = [
                'id' => $announc->id,
                'user' => $announc->user->fname,
                'course_id' => $announc->courses->title,
                'detail' => strip_tags($announc->announsment),
                'status' => $announc->status,
                'created_at' => $announc->created_at,
                'updated_at' => $announc->updated_at,
            ];
        }

        $assign = [];

        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();

            $assignments = Assignment::where('course_id', $id)
                ->where('user_id', Auth::guard('api')->user()->id)
                ->get();

            foreach ($assignments as $assignment) {
                $assign[] = [
                    'id' => $assignment->id,
                    'user' => $assignment->user->fname,
                    'course_id' => $assignment->courses->title,
                    'instructor' => $assignment->instructor->fname,
                    'chapter_id' => $assignment->chapter['chapter_name'],
                    'title' => $assignment->title,
                    'assignment' => $assignment->assignment,
                    'assignment_path' => url('files/assignment/' . $assignment->assignment),
                    'type' => $assignment->type,
                    'detail' => strip_tags($assignment->detail),
                    'rating' => $assignment->rating,
                    'created_at' => $assignment->created_at,
                    'updated_at' => $assignment->updated_at,
                ];
            }
        }

        $appointments = Appointment::where('course_id', $id)->get();

        $appointment = [];

        foreach ($appointments as $appoint) {
            $appointment[] = [
                'id' => $appoint->id,
                'user' => $appoint->user->fname,
                'course_id' => $appoint->courses->title,
                'instructor' => $appoint->instructor->fname,
                'title' => $appoint->title,
                'detail' => strip_tags($appoint->detail),
                'accept' => $appoint->accept,
                'reply' => $appoint->reply,
                'status' => $appoint->status,
                'created_at' => $appoint->created_at,
                'updated_at' => $appoint->updated_at,
            ];
        }

        $questions = Question::where('course_id', $id)->get();

        $question = [];

        foreach ($questions as $ques) {
            $answer = [];
            foreach ($ques->answers as $key => $data) {
                $answer[] = [
                    'course' => $data->courses->title,
                    'user' => $data->user->fname,
                    'instructor' => $data->instructor->fname,
                    'image' => $ques->instructor->user_img,
                    'imagepath' => url('images/user_img/' . $ques->user->user_img),
                    'question' => $data->question->question,
                    'answer' => strip_tags($data->answer),
                    'status' => $data->status,
                ];
            }

            $question[] = [
                'id' => $ques->id,
                'user' => $ques->user->fname,
                'instructor' => $ques->instructor->fname,
                'image' => $ques->instructor->user_img,
                'imagepath' => url('images/user_img/' . $ques->user->user_img),
                'course' => $ques->courses->title,
                'title' => strip_tags($ques->question),
                'status' => $ques->status,
                'created_at' => $ques->created_at,
                'updated_at' => $ques->updated_at,
                'answer' => $answer,
            ];
        }

        $zoom_meeting = Meeting::where('course_id', '=', $id)->get();
        $bigblue_meeting = BBL::where('course_id', '=', $id)->get();
        $google_meet = Googlemeet::where('course_id', '=', $id)->get();
        $jitsi_meeting = JitsiMeeting::where('course_id', '=', $id)->get();

        $previouspapers = PreviousPaper::where('course_id', '=', $id)->get();

        $papers = [];

        foreach ($previouspapers as $data) {
            $papers[] = [
                'id' => $data->id,
                'course' => $data->courses->title,
                'title' => $data->title,
                'file' => $data->file,
                'filepath' => url('files/papers/' . $data->file),
                'detail' => strip_tags($data->detail),
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];
        }

        return response()->json(['overview' => $overview, 'quiz' => $quiz, 'announcement' => $announcements, 'assignment' => $assign, 'questions' => $question, 'appointment' => $appointment, 'chapter' => $chapters, 'zoom_meeting' => $zoom_meeting, 'bigblue_meeting' => $bigblue_meeting, 'jitsi_meeting' => $jitsi_meeting, 'google_meet' => $google_meet, 'papers' => $papers], 200);
    }

    public function assignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required',
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if ($errors->first('course_id')) {
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail']);
            }
            if ($errors->first('chapter_id')) {
                return response()->json(['message' => $errors->first('chapter_id'), 'status' => 'fail']);
            }
            if ($errors->first('title')) {
                return response()->json(['message' => $errors->first('title'), 'status' => 'fail']);
            }
            if ($errors->first('file')) {
                return response()->json(['message' => $errors->first('file'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $auth = Auth::guard('api')->user();
        $course = Course::where('id', $request->course_id)->first();
        if ($file = $request->file('file')) {
            $name = time() . '_' . $file->getClientOriginalName();
            $name = str_replace(" ", "_", $name);
            $file->move('files/assignment', $name);
            $input['assignment'] = $name;
        }
        $assignment = Assignment::create([
            'user_id' => $auth->id,
            'instructor_id' => $course->user_id,
            'course_id' => $course->id,
            'chapter_id' => $request->chapter_id,
            'title' => $request->title,
            'assignment' => $name,
            'type' => 0,
        ]);

        if (isset($assignment) && isset($course->user_id)) {
            $body = 'A new assignment has been added to course: ' . $course->title;
            $notification = NewNotification::create(['body' => $body]);
            $notification->users()->attach(['user_id' => $course->user_id]);
            $user = User::where('id', $course->user_id)->first();
            if (isset($user->device_token)) {
                $this->send_notification($user->device_token, 'New Assignment', $body);
            }
        }

        return response()->json(['message' => 'Assignment submitted successfully', 'status' => 'success'], 200);
    }

    public function appointment(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required',
            'title' => 'required',
        ]);

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

        $auth = Auth::guard('api')->user();

        $course = Course::where('id', $request->course_id)->first();

        $appointment = Appointment::create([
            'user_id' => $auth->id,
            'instructor_id' => $course->user_id,
            'course_id' => $course->id,
            'title' => $request->title,
            'detail' => $request->detail,
            'accept' => '0',
            'start_time' => \Carbon\Carbon::now()->toDateTimeString(),
        ]);

        $users = User::where('id', $course->user_id)->first();

        if ($appointment) {
            if (env('MAIL_USERNAME') != null) {
                try {
                    /*sending email*/
                    $x = 'You get Appointment Request';
                    $request = $appointment;
                    Mail::to($users->email)->send(new UserAppointment($x, $request));
                } catch (\Swift_TransportException $e) {
                    return back()->with('success', trans('flash.RequestMailError'));
                }
            }
        }

        return response()->json(['appointment' => $appointment], 200);
    }

    public function question(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required',
            'question' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if ($errors->first('course_id')) {
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail']);
            }
            if ($errors->first('question')) {
                return response()->json(['message' => $errors->first('question'), 'status' => 'fail']);
            }
        }

        if ($validator->fails()) {
            return response()->json(['Secret Key is required']);
        }

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $auth = Auth::guard('api')->user();

        $course = Course::where('id', $request->course_id)->first();

        $question = Question::create([
            'user_id' => $auth->id,
            'instructor_id' => $course->user_id,
            'course_id' => $course->id,
            'status' => 1,
            'question' => $request->question,
        ]);

        if (isset($question) && isset($course->user_id)) {
            if ($course->user_id != $auth->id) {
                $user = User::where('id', $course->user_id)->first();
                $body = 'A new question has been added to course: ' . $course->title;
                $notification = NewNotification::create(['body' => $body]);
                $notification->users()->attach(['user_id' => $course->user_id]);
                if (isset($user->device_token)) {
                    $this->send_notification($user->device_token, 'New Question', $body);
                }
            }
        }

        return response()->json(['question' => $question], 200);
    }

    public function answer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required',
            'question_id' => 'required',
            'answer' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if ($errors->first('course_id')) {
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail']);
            }
            if ($errors->first('question_id')) {
                return response()->json(['message' => $errors->first('question_id'), 'status' => 'fail']);
            }
            if ($errors->first('answer')) {
                return response()->json(['message' => $errors->first('answer'), 'status' => 'fail']);
            }
        }
        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $auth = Auth::guard('api')->user();
        $course = Course::where('id', $request->course_id)->first();
        $question = Question::where('id', $request->question_id)->first();

        $answer = Answer::create([
            'ans_user_id' => $auth->id,
            'ques_user_id' => $question->user_id,
            'instructor_id' => $course->user_id,
            'course_id' => $course->id,
            'question_id' => $question->id,
            'status' => 1,
            'answer' => $request->answer,
        ]);

        if (isset($answer) && isset($course->user_id)) {
            if ($course->user_id != $auth->id) {
                $user = User::where('id', $question->user_id)->first();
                $body = 'A new answer has been added to your question on course: ' . $course->title;
                $notification = NewNotification::create(['body' => $body]);
                $notification->users()->attach(['user_id' => $user->id]);
                if (isset($user->device_token)) {
                    $this->send_notification($user->device_token, 'New Answer', $body);
                }
            }
        }
        return response()->json(['message' => 'Answer Submitted', 'status' => 'success'], 200);
    }

    public function appointmentdelete(Request $request, $id)
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

        Appointment::where('id', $id)->delete();

        return response()->json('Deleted Successfully !', 200);
    }

    public function quizsubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required',
            'question_id' => 'required',
            'topic_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail'], 400);
            }
            if ($errors->first('course_id')) {
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail'], 400);
            }
            if ($errors->first('question_id')) {
                return response()->json(['message' => $errors->first('question_id'), 'status' => 'fail'], 400);
            }
            if ($errors->first('topic_id')) {
                return response()->json(['message' => $errors->first('topic_id'), 'status' => 'fail'], 400);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $auth = Auth::guard('api')->user();
        $course = Course::where('id', $request->course_id)->first();
        $topics = QuizTopic::where('id', $request->topic_id)->first();
        $unique_question = array_unique($request->question_id);
        $quiz_already = QuizAnswer::where('user_id', $auth->id)->where('topic_id', $topics->id)->first();
        if ($quiz_already != null && $topics->quiz_again == 1) {
            QuizAnswer::where('user_id', $auth->id)->where('topic_id', $topics->id)->delete();
        } elseif ($quiz_already != null && $topics->quiz_again == 0) {
            return response()->json(array('message' => 'you did the quiz befor', 'status' => 'error'), 400);
        }
        if ($topics->type == null) {
            for ($i = 1; $i <= count($request->answer); $i++) {
                $already_answer = QuizAnswer::where('question_id', $unique_question[$i])->where('topic_id', $topics->id)->where('user_id', Auth::guard('api')->user()->id)->first();
                if ($already_answer == null) {
                    $question = Quiz::where('id', $unique_question[$i])->first();

                    $answers[] = [
                        'user_id' => Auth::guard('api')->user()->id,
                        'user_answer' => $request->answer[$i],
                        'question_id' => $unique_question[$i],
                        'course_id' => $topics->course_id,
                        'topic_id' => $topics->id,
                        'answer' => $question->answer,
                        // 'answer' => $request->canswer[$i],
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ];
                }
            }
            QuizAnswer::insert($answers);
            return response()->json(array('message' => 'Quiz Submitted', 'status' => 'success'), 200);
        } elseif ($topics->type == 1) {
            for ($i = 1; $i <= count($request->txt_answer); $i++) {

                $already_answer = QuizAnswer::where('question_id', $unique_question[$i])->where('topic_id', $topics->id)->where('user_id', Auth::guard('api')->user()->id)->first();
                if (!isset($already_answer)) {
                    $answers[] = [
                        'user_id' => Auth::guard('api')->user()->id,
                        'question_id' => $unique_question[$i],
                        'course_id' => $topics->course_id,
                        'topic_id' => $topics->id,
                        'txt_answer' => $request->txt_answer[$i],
                        'type' => '1',
                        'txt_approved' => '0',
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ];
                }
            }
            QuizAnswer::insert($answers);
            return response()->json(array('message' => 'Quiz Submitted', 'status' => 'success'), 200);
        }
    }

    public function userreview(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required',
            'learn' => 'required|integer|min:1|max:5|between:1,5',
            'price' => 'required|integer|min:1|max:5|between:1,5',
            'value' => 'required|integer|min:1|max:5|between:1,5',
            'review' => 'required',
        ]);

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

        $auth = Auth::guard('api')->user();

        $course = Course::where('id', $request->course_id)->first();

        $orders = Order::where('user_id', Auth::guard('api')->User()->id)
            ->where('course_id', $course->id)
            ->first();

        $review = ReviewRating::where('user_id', Auth::guard('api')->User()->id)
            ->where('course_id', $course->id)
            ->first();

        if (!empty($orders)) {
            if (!empty($review)) {
                return response()->json('Already Reviewed !', 402);
            } else {
                $input = $request->all();

                $review = ReviewRating::create([
                    'user_id' => $auth->id,
                    'course_id' => $input['course_id'],
                    'learn' => $input['learn'],
                    'price' => $input['price'],
                    'value' => $input['value'],
                    'review' => $input['review'],
                    'approved' => '1',
                    'featured' => '0',
                    'status' => '1',
                ]);

                return response()->json(['review' => $review], 200);
            }
        } else {
            return response()->json('Please Purchase course !', 401);
        }
    }

    public function paginationcourse(Request $request)
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

        $paginator = Course::where('status', 1)
            ->with('include')
            ->with('whatlearns')
            ->with('review')
            ->paginate(5);

        $paginator->getCollection()->transform(function ($c) use ($paginator) {
            $c['in_wishlist'] = Is_wishlist::in_wishlist($c->id);
            return $c;
        });

        return response()->json(['course' => $paginator], 200);
    }

    public function categoryPage(Request $request, $id, $name)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $category = Categories::where('status', '1')
            ->where('id', $id)
            ->first();

        if (!$category) {
            return response()->json(['Invalid Category !']);
        }

        $subcategory = $category
            ->subcategory()
            ->where('status', 1)
            ->get();

        if ($request->type) {
            $course = $category
                ->courses()
                ->where('status', '1')
                ->where('type', '=', $request->type == 'paid' ? '1' : '0')
                ->paginate($request->limit ?? 10);
        } elseif ($request->sortby) {
            if ($request->sortby == 'l-h') {
                $courses = $cats
                    ->courses()
                    ->where('status', '1')
                    ->where('type', '=', '1')
                    ->orderBy('price', 'DESC')
                    ->paginate($request->limit ?? 10);
            }

            if ($request->sortby == 'h-l') {
                $courses = $cats
                    ->courses()
                    ->where('status', '1')
                    ->where('type', '=', '1')
                    ->orderBy('price', 'ASC')
                    ->paginate($request->limit ?? 10);
            }

            if ($request->sortby == 'a-z') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->orderBy('title', 'ASC')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->orderBy('title', 'ASC')
                        ->paginate($request->limit ?? 10);
                }
            }

            if ($request->sortby == 'z-a') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->orderBy('title', 'DESC')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->orderBy('title', 'DESC')
                        ->paginate($request->limit ?? 10);
                }
            }

            if ($request->sortby == 'newest') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->orderBy('created_at', 'DESC')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->orderBy('created_at', 'DESC')
                        ->paginate($request->limit ?? 10);
                }
            }

            if ($request->sortby == 'featured') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->where('featured', '=', '1')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('featured', '=', '1')
                        ->paginate($request->limit ?? 10);
                }
            } elseif ($request->limit) {
                $courses = $cats
                    ->courses()
                    ->where('status', '1')
                    ->paginate($request->limit ?? 10);
            }
        } else {
            $course = Course::where('status', 1)
                ->where('category_id', $category->id)
                ->paginate($request->limit ?? 10);
        }

        $result = [
            'id' => $category->id,
            'title' => array_map(function ($lang) {
                return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
            }, $category->getTranslations('title')),
            'icon' => $category->icon,
            'slug' => $category->slug,
            'status' => $category->status,
            'featured' => $category->featured,
            'image' => $category->cat_image,
            'imagepath' => url('images/category/' . $category->cat_image),
            'position' => $category->position,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
            'subcategory' => $subcategory,
            'course' => $course,
        ];
        return response()->json($result, 200);
    }

    public function subcategoryPage(Request $request, $id, $name)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }
        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $category = SubCategory::where('id', $id)->first();
        if (!$category) {
            return response()->json(['Invalid Category !']);
        }
        $subcategory = ChildCategory::where('status', 1)
            ->where('subcategory_id', $category->id)
            ->get();
        if ($request->type) {
            $course = $category
                ->courses()
                ->where('status', '1')
                ->where('type', '=', $request->type == 'paid' ? '1' : '0')
                ->paginate($request->limit ?? 10);
        } elseif ($request->sortby) {
            if ($request->sortby == 'l-h') {
                $courses = $cats
                    ->courses()
                    ->where('status', '1')
                    ->where('type', '=', '1')
                    ->orderBy('price', 'DESC')
                    ->paginate($request->limit ?? 10);
            }
            if ($request->sortby == 'h-l') {
                $courses = $cats
                    ->courses()
                    ->where('status', '1')
                    ->where('type', '=', '1')
                    ->orderBy('price', 'ASC')
                    ->paginate($request->limit ?? 10);
            }
            if ($request->sortby == 'a-z') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->orderBy('title', 'ASC')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->orderBy('title', 'ASC')
                        ->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'z-a') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->orderBy('title', 'DESC')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->orderBy('title', 'DESC')
                        ->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'newest') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->orderBy('created_at', 'DESC')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->orderBy('created_at', 'DESC')
                        ->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'featured') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->where('featured', '=', '1')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('featured', '=', '1')
                        ->paginate($request->limit ?? 10);
                }
            } elseif ($request->limit) {
                $courses = $cats
                    ->courses()
                    ->where('status', '1')
                    ->paginate($request->limit ?? 10);
            }
        } else {
            $course = Course::where('status', 1)
                ->where('category_id', $category->id)
                ->paginate($request->limit ?? 10);
        }
        $result = [
            'id' => $category->id,
            'title' => array_map(function ($lang) {
                return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
            }, $category->getTranslations('title')),
            'icon' => $category->icon,
            'slug' => $category->slug,
            'status' => $category->status,
            'image' => Avatar::create($category->title),
            'position' => $category->position,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
            'childcategory' => $subcategory,
            'course' => $course,
        ];
        return response()->json($result, 200);
    }
    public function childcategoryPage(Request $request, $id, $name)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }
        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $category = ChildCategory::where('id', $id)->first();
        if (!$category) {
            return response()->json(['Invalid Category !']);
        }
        if ($request->type) {
            $course = $category
                ->courses()
                ->where('status', '1')
                ->where('type', '=', $request->type == 'paid' ? '1' : '0')
                ->paginate($request->limit ?? 10);
        } elseif ($request->sortby) {
            if ($request->sortby == 'l-h') {
                $courses = $cats
                    ->courses()
                    ->where('status', '1')
                    ->where('type', '=', '1')
                    ->orderBy('price', 'DESC')
                    ->paginate($request->limit ?? 10);
            }
            if ($request->sortby == 'h-l') {
                $courses = $cats
                    ->courses()
                    ->where('status', '1')
                    ->where('type', '=', '1')
                    ->orderBy('price', 'ASC')
                    ->paginate($request->limit ?? 10);
            }
            if ($request->sortby == 'a-z') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->orderBy('title', 'ASC')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->orderBy('title', 'ASC')
                        ->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'z-a') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->orderBy('title', 'DESC')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->orderBy('title', 'DESC')
                        ->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'newest') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->orderBy('created_at', 'DESC')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->orderBy('created_at', 'DESC')
                        ->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'featured') {
                if ($request->type) {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('type', '=', $request->type == 'paid' ? 1 : 0)
                        ->where('featured', '=', '1')
                        ->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats
                        ->courses()
                        ->where('status', '1')
                        ->where('featured', '=', '1')
                        ->paginate($request->limit ?? 10);
                }
            } elseif ($request->limit) {
                $courses = $cats
                    ->courses()
                    ->where('status', '1')
                    ->paginate($request->limit ?? 10);
            }
        } else {
            $course = Course::where('status', 1)
                ->where('category_id', $category->id)
                ->paginate($request->limit ?? 10);
        }
        $result = [
            'id' => $category->id,
            'title' => array_map(function ($lang) {
                return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
            }, $category->getTranslations('title')),
            'icon' => $category->icon,
            'slug' => $category->slug,
            'status' => $category->status,
            'featured' => $category->featured,
            'image' => Avatar::create($category->title),
            'position' => $category->position,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
            'course' => $course,
        ];
        return response()->json($result, 200);
    }

    public function deleteAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'assignment_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }

            if ($errors->first('assignment_id')) {
                return response()->json(['message' => $errors->first('assignment_id'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = Auth::guard('api')->user();

        Assignment::where('id', $request->assignment_id)
            ->where('user_id', $user->id)
            ->delete();

        return response()->json(['watchlist' => $watch], 200);
    }

    public function requestCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $user = Auth::guard('api')->user();

        $alreadyRequest = Instructor::where('user_id', Auth::guard('api')->user()->id)->first();

        if ($alreadyRequest != null) {
            return response()->json([
                'message' => 'Already Requested',
            ]);
        }

        return response()->json([
            'message' => 'Please Request to became an instructor',
        ]);
    }

    public function cancelRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')
            ->where('secret_key', '=', $request->secret)
            ->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $user = Auth::guard('api')->user();

        if (Instructor::where('user_id', $user->id)->exists()) {
            $instructor = Instructor::where('user_id', $user->id)->first();
            $instructor->delete();

            return response()->json([
                'message' => 'records deleted',
            ]);
        } else {
            return response()->json(
                [
                    'message' => 'Instructor not found',
                ],
                404,
            );
        }
    }

    public function watchcourse($id)
    {
        if (Auth::guard('api')->check()) {
            $order = Order::where('status', '1')
                ->where('user_id', Auth::guard('api')->User()->id)
                ->where('course_id', $id)
                ->first();

            $courses = Course::where('id', $id)->first();

            $bundle = Order::where('user_id', Auth::guard('api')->User()->id)
                ->where('bundle_id', '!=', null)
                ->get();

            $gsetting = Setting::first();

            //attandance start
            if (!empty($order)) {
                if ($gsetting->attandance_enable == 1) {
                    $date = Carbon::now();
                    //Get date
                    $date->toDateString();

                    $courseAttandance = Attandance::where('course_id', '=', $id)
                        ->where('user_id', Auth::guard('api')->User()->id)
                        ->where('date', '=', $date->toDateString())
                        ->first();

                    if (!$courseAttandance) {
                        $attanded = Attandance::create([
                            'user_id' => Auth::guard('api')->user()->id,
                            'course_id' => $id,
                            'instructor_id' => $courses->user_id,
                            'date' => $date->toDateString(),
                            'order_id' => $id,
                            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        ]);
                    }
                }
            } //attandance end

            $course = Course::findOrFail($id);

            $course_id = [];

            foreach ($bundle as $b) {
                $bundle = BundleCourse::where('id', $b->bundle_id)->first();
                array_push($course_id, $bundle->course_id);
            }

            $course_id = array_values(array_filter($course_id));

            $course_id = array_flatten($course_id);

            if (Auth::guard('api')->User()->role == 'admin') {
                return view('watch', compact('courses'));
            } elseif (Auth::guard('api')->User()->id == $course->user_id) {
                return view('watch', compact('courses'));
            } else {
                if (!empty($order)) {
                    $coursewatch = WatchCourse::where('course_id', '=', $id)
                        ->where('user_id', Auth::guard('api')->User()->id)
                        ->first();

                    if ($gsetting->device_control == 1) {
                        if (!$coursewatch) {
                            $watching = WatchCourse::create([
                                'user_id' => Auth::guard('api')->user()->id,
                                'course_id' => $id,
                                'start_time' => \Carbon\Carbon::now()->toDateTimeString(),
                                'active' => '1',
                                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                            ]);

                            return view('watch', compact('courses'));
                        } else {
                            if ($coursewatch->active == 0) {
                                $coursewatch->active = 1;
                                $coursewatch->save();
                                return view('watch', compact('courses'));
                            } else {
                                return response()->json(['message' => 'User Already Watching Course !!', 'status' => 'fail'], 402);
                            }
                        }
                    } else {
                        return view('watch', compact('courses'));
                    }
                } elseif (isset($course_id) && in_array($id, $course_id)) {
                    return view('watch', compact('courses'));
                } else {
                    return response()->json(['message' => 'Unauthorization Action', 'status' => 'fail'], 402);
                }
            }
        }
        return response()->json(['message' => 'Please Login to Continue', 'status' => 'fail'], 401);
    }

    public function reviewlike(Request $request, $id)
    {
        $user = Auth::user();

        $help = ReviewHelpful::where('review_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($request->review_like == '1') {
            if (isset($help)) {
                ReviewHelpful::where('id', $help->id)->update([
                    'review_like' => '1',
                    'review_dislike' => '0',
                ]);
            } else {
                $created_review = ReviewHelpful::create([
                    'course_id' => $request->course_id,
                    'user_id' => $user->id,
                    'review_id' => $id,
                    'helpful' => 'yes',
                    'review_like' => '1',
                ]);

                ReviewHelpful::where('id', $created_review->id)->update([
                    'review_dislike' => '0',
                ]);
            }
        } elseif ($request->review_dislike == '1') {
            if (isset($help)) {
                ReviewHelpful::where('id', $help->id)->update([
                    'review_dislike' => '1',
                    'review_like' => '0',
                ]);
            } else {
                $created_review = ReviewHelpful::create([
                    'course_id' => $request->course_id,
                    'user_id' => $user->id,
                    'review_id' => $id,
                    'helpful' => 'yes',
                    'review_dislike' => '1',
                ]);

                ReviewHelpful::where('id', $created_review->id)->update([
                    'review_like' => '0',
                ]);
            }
        } elseif ($help->review_like == '1') {
            ReviewHelpful::where('id', $help->id)->update([
                'review_like' => '0',
            ]);
        } elseif ($help->review_dislike == '1') {
            ReviewHelpful::where('id', $help->id)->update([
                'review_dislike' => '0',
            ]);
        }

        return response()->json(['message' => 'Updated Successfully', 'status' => 'success'], 200);
    }

    public function getcategoryCourse($catid)
    {
        $cat = Categories::whereHas('courses')
            ->whereHas('courses.user')
            ->where('status', '1')
            ->with(['courses.instructor'])
            ->find($catid);

        if (isset($cat)) {
            foreach ($cat->courses as $course) {
                $category_slider_courses[] = [
                    'id' => $course->id,

                    'title' => array_map(function ($lang) {
                        return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                    }, $course->getTranslations('title')),
                    'level_tags' => $course->level_tags,
                    'short_detail' => array_map(function ($lang) {
                        return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                    }, $course->getTranslations('short_detail')),
                    'price' => $course->price,
                    'discount_price' => $course->discount_price,
                    'featured' => $course->featured,
                    'status' => $course->status,
                    'preview_image' => $course->preview_image,
                    'total_rating_percent' => course_rating($course->id)->getData()->total_rating_percent,
                    'total_rating' => course_rating($course->id)->getData()->total_rating,
                    'imagepath' => url('images/course/' . $course->preview_image),
                    'in_wishlist' => Is_wishlist::in_wishlist($course->id),
                    'instructor' => [
                        'id' => $course->user->id,
                        'name' => $course->user->fname . ' ' . $course->user->lname,
                        'image' => url('/images/user_img/' . $course->user->user_img),
                    ],
                ];
            }

            $category_slider1['course'] = $category_slider_courses;

            return response()->json([
                'course' => $category_slider_courses,
            ]);
        } else {
            return response()->json([
                'course' => null,
                'msg' => 'No courses or category found !',
            ]);
        }
    }
    // public function mycourses(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'secret' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['Secret Key is required']);
    //     }

    //     $key = DB::table('api_keys')
    //         ->where('secret_key', '=', $request->secret)
    //         ->first();

    //     if (!$key) {
    //         return response()->json(['Invalid Secret Key !']);
    //     }

    //     $user = Auth::guard('api')->user();

    //     $enroll = Order::where('user_id', $user->id)
    //         ->where('status', 1)
    //         ->get()
    //         ->transform(function ($order) {
    //             $order['bundle_course_id'] = isset($order->bundle) ? $order->bundle->course_id : null;
    //             return $order;
    //         });

    //     $enroll_details = [];
    //     $title = null;
    //     if (isset($enroll)) {
    //         foreach ($enroll as $enrol) {
    //             $course = Course::where('status', 1)
    //                 ->where('id', $enrol->course_id)
    //                 ->with([
    //                     'include' => function ($query) {
    //                         $query->where('status', 1);
    //                     },
    //                 ])
    //                 ->with(['user'])
    //                 ->with([
    //                     'whatlearns' => function ($query) {
    //                         $query->where('status', 1);
    //                     },
    //                 ])
    //                 ->with([
    //                     'progress' => function ($query) {
    //                         $query->where('user_id', Auth::guard('api')->user()->id);
    //                     },
    //                 ])
    //                 ->first();

    //             if (!isset($course)) {
    //                 $bundle = BundleCourse::where('id', $enrol->bundle_id)
    //                     ->with('user')
    //                     ->first();
    //             }

    //             if (isset($bundle)) {
    //                 $title = $bundle->title;
    //             }

    //             if (isset($bundle) || isset($course)) {
    //                 $total_rating_percent = 0;
    //                 $course_total_rating = 0;
    //                 $total_rating = 0;

    //                 if (isset($course)) {
    //                     $reviews = ReviewRating::where('course_id', $course->id)
    //                         ->where('status', '1')
    //                         ->get();
    //                     $count = ReviewRating::where('course_id', $course->id)->count();
    //                     $learn = 0;
    //                     $price = 0;
    //                     $value = 0;
    //                     $sub_total = 0;
    //                     $sub_total = 0;
    //                     $title = $course->title;

    //                     if ($count > 0) {
    //                         foreach ($reviews as $review) {
    //                             $learn = $review->learn * 5;
    //                             $price = $review->price * 5;
    //                             $value = $review->value * 5;
    //                             $sub_total = $sub_total + $learn + $price + $value;
    //                         }
    //                         $count = $count * 3 * 5;
    //                         $rat = $sub_total / $count;
    //                         $ratings_var0 = ($rat * 100) / 5;
    //                         $course_total_rating = $ratings_var0;
    //                     }

    //                     $count = $count * 3 * 5;

    //                     if ($count != 0) {
    //                         $rat = $sub_total / $count;
    //                         $ratings_var = ($rat * 100) / 5;
    //                         $overallrating = $ratings_var0 / 2 / 10;
    //                         $total_rating = round($overallrating, 1);
    //                     }
    //                     $total_rating_percent = round($course_total_rating, 2);
    //                     $total_rating = $total_rating;
    //                 }

    //                 $enroll_details[] = [
    //                     'title' => $title,
    //                     'enroll' => $enrol,
    //                     'course' => $course ?? null,
    //                     'bundle' => $bundle ?? null,
    //                     'total_rating_percent' => $total_rating_percent,
    //                     'total_rating' => $total_rating,
    //                 ];
    //             }
    //         }
    //     }
    //     return response()->json(['enroll_details' => $enroll_details], 200);
    // }

    public function mycourses(Request $request)
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


        $my_orders = Order::where('status', '=', 1)->where('user_id', '=', Auth::guard('api')->id())->get(['id', 'course_id']);
        $mycourses_id = [];
        foreach ($my_orders as $myorder) {
            if ($myorder->course_id != null) {
                array_push($mycourses_id, $myorder->course_id);
            }
            if ($myorder->bundle_id != null) {
                $bundle = BundleCourse::where('id', $myorder->bundle_id)->first();
                foreach ($bundle->course_id as $bCourse_id) {
                    array_push($mycourses_id, $bCourse_id);
                }
            }
        }

        $course = Course::where('status', 1)
            ->whereIn('id', $mycourses_id)
            ->orderBy('id', 'DESC')
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                },
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                }, 'language' => function ($query) {
                    $query->where('status', 1);
                },
                'review' => function ($query) {
                    $query->with('user:id,fname,lname,user_img');
                },
                'user'
            ])
            ->paginate(6);


        foreach ($course as $result) {

            if (isset($result->review)) {
                $ratings_var11 = 0;
                $review_like = 0;
                $review_dislike = 0;

                foreach ($result->review as $key => $review) {
                    $user_count = count([$review]);
                    $user_sub_total = 0;
                    $user_learn_t = $review->learn * 5;
                    $user_price_t = $review->price * 5;
                    $user_value_t = $review->value * 5;
                    $user_sub_total = $user_sub_total + $user_learn_t + $user_price_t + $user_value_t;

                    $user_count = $user_count * 3 * 5;
                    $rat1 = $user_sub_total / $user_count;
                    $ratings_var11 = ($rat1 * 100) / 5;

                    $review_like = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_like', 1)
                        ->count();

                    $review_dislike = ReviewHelpful::where('review_id', $review->id)
                        ->where('course_id', $result->id)
                        ->where('review_dislike', 1)
                        ->count();

                    $review->review_like = $review_like;
                    $review->review_dislike = $review_dislike;
                }
            }

            $student_enrolled = Order::where('course_id', $result->course_id)->count();
            $result->student_enrolled = isset($student_enrolled) ? $student_enrolled : null;
            $result->lecture_count = isset($result->chapter) ? count($result->chapter) : 0;

            $enrolled_status = Order::where('status', '=', 1)->where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            $progress = CourseProgress::where('course_id', $result->id)->where('user_id', Auth::guard('api')->id())->first();
            if (isset($progress)) {
                $result->mark_chapter_id = $progress->mark_chapter_id;
                $result->all_chapter_id  = $progress->all_chapter_id;
            } else {
                $result->mark_chapter_id = null;
                $result->all_chapter_id  = null;
            }
            if (isset($enrolled_status)) {
                $result->enrolled_status = true;
            } else {
                $result->enrolled_status = false;
            }

            $instructors_student = Order::where('instructor_id', $result->user->id)->count();
            $result->user->instructors_student = isset($instructors_student) ? $instructors_student : null;
            $result->user->course_count = Course::where('user_id', $result->user->id)->count();


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
        $course->makeHidden('chapter');
        return response()->json(['course' => $course], 200);
    }

    public function quiz_reports(Request $request, $id)
    {
        $auth = Auth::user();
        $topic = QuizTopic::where('id', $id)->get();
        $questions = Quiz::where('topic_id', $id)->get();
        $count_questions = $questions->count();
        $topics = QuizTopic::where('id', $id)->first();
        $ans = QuizAnswer::where('user_id', $auth->id)
            ->where('topic_id', $id)
            ->get();

        $mark = 0;

        if ($topics->type == null) {
            foreach ($ans as $answer) {
                if ($answer->answer == $answer->user_answer) {
                    $mark++;
                }
            }
        } else {
            foreach ($ans as $answer) {
                if ($answer->txt_approved == 1) {
                    $mark++;
                }
            }
        }

        $per_question_mark = $topics->per_q_mark;
        $correct = $mark * $topics->per_q_mark;

        return response()->json([
            'question_count' => $count_questions,
            'correct_count' => $mark,
            'correct_answer' => $mark,
            'per_question_mark' => $per_question_mark,
            'total_marks' => $correct,
        ], 200);
    }

    public function getFavCategories(Request $request)
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

        $categories = Categories::get();

        $result = [];

        foreach ($categories as $category) {
            $favCat = FavCategory::where('user_id', Auth::id())
                ->where('category_id', $category->id)
                ->first();
            $checked = 0;
            if (isset($favCat)) {
                $checked = 1;
            }

            $result[] = [
                'id' => $category->id,
                //   'title' => array_map(function ($lang) {
                //     return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                //}, $category->getTranslations('title')),
                'title' => $category->title,
                'icon' => $category->icon,
                'slug' => $category->slug,
                'status' => $category->status,
                'featured' => $category->featured,
                'image' => $category->cat_image,
                'imagepath' => url('images/category/' . $category->cat_image),
                'position' => $category->position,
                'checked' => $checked,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            ];
        }

        return response()->json(['category' => $result], 200);
    }

    public function addToFavCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'favCategories' => 'array',
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

        $auth = Auth::guard('api')->user();

        FavCategory::where('user_id', Auth::id())->delete();

        foreach ($request->favCategories as $key => $favCategory) {
            $favCat = FavCategory::create([
                'user_id' => $auth->id,
                'category_id' => $favCategory,
            ]);
        }

        $subCategories = SubCategory::whereIn('category_id', $request->favCategories)->get();

        $result = [];

        foreach ($subCategories as $category) {
            $favSub = FavSubcategory::where('user_id', Auth::id())
                ->where('subcategory_id', $category->id)
                ->first();
            $checked = 0;
            if (isset($favSub)) {
                $checked = 1;
            }

            $result[] = [
                'id' => $category->id,
                'category_id' => $category->category_id,
                //'title' => array_map(function ($lang) {
                //   return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                //}, $category->getTranslations('title')),
                'title' => $category->title,
                'icon' => $category->icon,
                'slug' => $category->slug,
                'status' => $category->status,
                'featured' => $category->featured,
                'checked' => $checked,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            ];
        }

        return response()->json(['subCategories' => $result], 200);
    }

    public function addToFavSubcategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'favSubcategories' => 'array',
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

        $auth = Auth::guard('api')->user();

        FavSubcategory::where('user_id', Auth::id())->delete();

        foreach ($request->favSubcategories as $key => $favSubcategory) {
            $favSubcat = FavSubcategory::create([
                'user_id' => $auth->id,
                'subcategory_id' => $favSubcategory,
            ]);
        }

        $courses = Course::whereIn('subcategory_id', $request->favSubcategories)->get();

        return response()->json(['$courses' => $courses], 200);
    }

    public function courseVrFilter(Request $request)
    {
        $baseURL = env('APP_URL');

        if (isset($request->vr_hole)) {
            $courses = Course::where('vr_hole', '=', $request->vr_hole)
                ->where('status', 1)

                // ->with([
                //     'chapter' => function ($query) {
                //         $query->where('status', 1)->select('id', 'course_id', 'chapter_name');
                //     },
                // ])

                ->with([
                    'courseclass' => function ($query) {
                        $query
                            ->where('status', 1)
                            ->where('type', 'video')
                            ->select('id', 'course_id', 'title', 'url', 'video');
                    },
                ])
                ->get(['id', 'title', 'vr_code', 'vr_hole']);

            if (count($courses) == 0) {
                return response()->json(['message' => 'there is no courses with this VR hole number'], 400);
            }
        } else {

            $courses = Course::where('status', 1)
                ->where('vr_hole', '!=', null)

                ->with([
                    'courseclass' => function ($query) {
                        $query
                            ->where('status', 1)
                            ->where('type', 'video')
                            ->select('id', 'course_id', 'title', 'url', 'video');
                    },
                ])

                ->get(['id', 'title', 'vr_code', 'vr_hole']);
        }


        $vr_courses = [];


        foreach ($courses as $key => $course) {
            // dd($course);
            // $course->makeHidden('requirement')->toArray();

            // foreach ($course->related as $related) {
            //     // foreach ($related->courses as $rCourse) {

            //         if ($related->preview_image != null && $related->preview_image != '' && !starts_with($related->preview_image, 'http')) {
            //             $related->preview_image = $baseURL . 'images/course/' . $related->preview_image;
            //         }
            //         if ($related->video != null && $related->video != '' && !starts_with($related->video, 'http')) {
            //             $related->video = $baseURL . 'video/preview/' . $related->video;
            //         }
            //     // }
            // }

            // if ($course->preview_image != null && $course->preview_image != '' && !starts_with($course->preview_image, 'http')) {
            //     $course->preview_image = $baseURL . 'images/course/' . $course->preview_image;
            // }
            // if ($course->user->user_img != null && $course->user->user_img != '' && !starts_with($course->user->user_img, 'http')) {
            //     $course->user->user_img = $baseURL . 'images/user_img/' . $course->user->user_img;
            // }
            // if ($course->category->cat_image != null && $course->category->cat_image != '' && !starts_with($course->category->cat_image, 'http')) {
            //     $course->category->cat_image = $baseURL . 'images/category/' . $course->category->cat_image;
            // }

            // if ($course->video != null && $course->video != '' && !starts_with($course->video, 'http')) {
            //     $course->video = $baseURL . 'video/preview/' . $course->video;
            // }
            // foreach ($course->chapter as $chapter) {
            //     if ($chapter->file != null && $chapter->file != '' && !starts_with($chapter->file, 'http')) {
            //         $chapter->file = $baseURL . 'files/material/' . $chapter->file;
            //     }
            //     if ($chapter->user->user_img != null && $chapter->user->user_img != '' && !starts_with($chapter->user->user_img, 'http')) {
            //         $chapter->user->user_img = $baseURL . 'images/user_img/' . $chapter->user->user_img;
            //     }
            // }

            // foreach ($course->order as $key => $order) {
            //     if ($order->proof != null && $order->proof != '' && !starts_with($order->proof, 'http')) {
            //         $order->proof = $baseURL . 'images/order/' . $order->proof;
            //     }
            // }

            $vr_courses[$key]['vr_hole'] = $course->vr_hole;
            $vr_courses[$key]['vr_code'] = $course->vr_code;


            foreach ($course->courseclass as $index => $class) {

                if ($class->video != null && $class->video != '' && !starts_with($class->video, 'http')) {
                    $class->video = $baseURL . 'video/class/' . $class->video;
                }
                if ($class->url != null && $class->url != '') {
                    $class->video = $class->url;
                }
                // if ($class->audio != null && $class->audio != '' && !starts_with($class->audio, 'http')) {
                //     $class->audio = $baseURL . 'files/audio/' . $class->audio;
                // }
                // if ($class->pdf != null && $class->pdf != '' && !starts_with($class->pdf, 'http')) {
                //     $class->pdf = $baseURL . 'files/pdf/' . $class->pdf;
                // }
                // if ($class->image != null && $class->image != '' && !starts_with($class->image, 'http')) {
                //     $class->image = $baseURL . 'images/class/' . $class->image;
                // }
                // if ($class->zip != null && $class->zip != '' && !starts_with($class->zip, 'http')) {
                //     $class->zip = $baseURL . 'files/zip/' . $class->zip;
                // }
                // if ($class->file != null && $class->file != '' && !starts_with($class->file, 'http')) {
                //     $class->file = $baseURL . 'files/class/material/' . $class->file;
                // }
                // if ($class->preview_video != null && $class->preview_video != '' && !starts_with($class->preview_video, 'http')) {
                //     $class->preview_video = $baseURL . 'video/class/preview/' . $class->preview_video;
                // }
                // if ($class->user->user_img != null && $class->user->user_img != '' && !starts_with($class->user->user_img, 'http')) {
                //     $class->user->user_img = $baseURL . 'images/user_img/' . $class->user->user_img;
                // }



                $vr_courses[$key]['classes'][$index]['class_title'] = $class->title;
                $vr_courses[$key]['classes'][$index]['video'] = $class->video;
            }
        }


        // $vr_courses = response()->json(['vr_courses' => $vr_courses], 200);
        $vr_courses = str_replace(array('['), '{', htmlspecialchars(json_encode(['vr_courses' => $vr_courses]), ENT_NOQUOTES));
        $vr_courses = str_replace(array(']'), '}', $vr_courses);

        // $data['dara'] = $vr_courses;
        return $vr_courses;

        return response()->json(['data' => $data], 200);
    }

    public function contactReasons(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail'], 400);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $data =  Contactreason::where('status', '1')->get(['id', 'reason']);

        return response()->json([
            'reasons' => $data,
        ], 200);
    }

    public function userNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail'], 400);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = User::where('id', Auth::id())->first();

        $notifications = $user->newNotifications()->orderBy('created_at', 'desc')->get();
        foreach ($notifications as $notification) {
            $notification->status = $notification->pivot->status;
        }
        $notifications->makeHidden(['pivot']);
        return response()->json([
            'notifications' => $notifications,
        ], 200);
    }

    public function unreadNotificationsCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail'], 400);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = User::where('id', Auth::id())->first();

        $notifications = $user->newNotifications()->where('status', 0)->orderBy('created_at', 'desc')->count();

        return response()->json([
            'notifications_count' => $notifications,
        ], 200);
    }

    public function editNotificationsStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail'], 400);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        DB::table('notification_user')->where('user_id', '=',  Auth::id())->update(['status' => 1]);


        return response()->json([
            'message' => 'All notifications have been read.',
        ], 200);
    }
    public function deleteNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'id' => 'required|exists:new_notifications,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if ($errors->first('id')) {
                return response()->json(['message' => $errors->first('id'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $notification = NewNotification::where('id', $request->id)->first();

        // $notification->users()->detach(Auth::id());
        DB::table('notification_user')->where('notification_id', '=',  $request->id)->where('user_id', '=',  Auth::id())->delete();


        return response()->json([
            'message' => 'Notification has been deleted successfully.',
        ], 200);
    }

    public function bulkDeleteNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail'], 400);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = User::where('id', Auth::id())->first();

        $notifications = $user->newNotifications;
        foreach ($notifications as $notification) {
            DB::table('notification_user')->where('notification_id', '=',  $notification->id)->where('user_id', '=',  Auth::id())->delete();
        }

        return response()->json([
            'message' => 'Notifications have been deleted successfully.',
        ], 200);
    }

    public function courseAnnouncements(Request $request)
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


        $announcements = Announcement::where('status', 1)
            ->where('course_id', $request->course_id)
            ->get();

        return response()->json(['announcements' => $announcements], 200);
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

    public function coursePrevPapers(Request $request)
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

        // App::setlocale($request->lang);

        $PreviousPapers = PreviousPaper::where('status', 1)
            ->where('course_id', $request->course_id)
            ->get();

        return response()->json(['PreviousPapers' => $PreviousPapers], 200);
    }

    public function courseQuizzes(Request $request)
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

        // App::setlocale($request->lang);

        $quiz = QuizTopic::where('course_id', $request->course_id)->where('status', 1)
            ->with(['quizquestion'])
            ->withCount(['quizquestion'])->get();

        return response()->json(['quiz' => $quiz], 200);
    }
    public function courseQuestions(Request $request)
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

        // App::setlocale($request->lang);

        $questions = Question::where('course_id', $request->course_id)->where('status', 1)
        ->with([
            'answers' => function ($query) {
                $query->where('status', 1);
            },
        ])
        ->withCount([
            'answers' => function ($query) {
                $query->where('status', 1);
            },
        ])
        ->get();
        return response()->json(['questions' => $questions], 200);
    }
}
