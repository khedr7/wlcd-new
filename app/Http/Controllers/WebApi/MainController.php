<?php

namespace App\Http\Controllers\WebApi;

use App\Http\Controllers\Controller;
use App\Course;
use App\Helpers\Is_wishlist;
use App\Http\Traits\SendNotification;
use App\Order;
use App\ReviewRating;
use App\User;
use App\Slider;
use App\SliderFacts;
use App\Categories;
use App\SubCategory;
use App\Testimonial;
use App\CategorySlider;
use App\Contact;
use App\Contactreason;
use App\Setting;
use App\Videosetting;
use App\Announcement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    //------------INSTRUCTORS----------------

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

    public function allInstructors(Request $request)
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
            ->paginate(8);


        return response()->json(['instructors' => $instructors], 200);
    }

    public function instructor(Request $request)
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

        $instructor = User::select('id', 'fname', 'lname', 'dob', 'mobile', 'email', 'address', 'user_img', 'role',
                                'detail', 'address', 'city_id', 'state_id', 'country_id', 'gender', 'created_at',
                                'practical_experience', 'basic_skills', 'professional_summary', 'scientific_background', 'courses')
            ->where('status', 1)
            ->where('id', $request->id)
            ->withCount([
                'courses' => function ($query) {
                    $query->where('status', 1);
                },
                'courseOrders' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->first();


        return response()->json(['instructor' => $instructor], 200);
    }

    public function courseInstructor(Request $request)
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

        $course = Course::where('id', $request->course_id)->first(['id', 'user_id']);

        $instructor = User::select('id', 'fname', 'lname', 'dob', 'mobile', 'email', 'address', 'user_img', 'role',
                                'detail', 'address', 'city_id', 'state_id', 'country_id', 'gender', 'created_at',
                                'practical_experience', 'basic_skills', 'professional_summary', 'scientific_background', 'courses')
            ->where('status', 1)
            ->where('id', $course->user_id)
            ->withCount([
                'courses' => function ($query) {
                    $query->where('status', 1);
                },
                'courseOrders' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->first();


        return response()->json(['instructor' => $instructor], 200);
    }
    
    //------------SLIDER----------------

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
    //-----------CATEGORY----------------

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
        App::setlocale($request->lang);

        $category = Categories::where('status', 1)
            ->orderBy('position', 'asc')
            ->get();

        $subcategory = SubCategory::where('status', 1)->get();

        $featured_cate = Categories::where('status', 1)
            ->orderBy('position', 'asc')
            ->where('featured', 1)
            ->get();

        return response()->json(['category' => $category, 'subcategory' => $subcategory,  'featured_cate' => $featured_cate,], 200);
    }

    //----------ALL CATEGORY--------------

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
        
        App::setlocale($request->lang);

        $category = Categories::where('status', 1)
            ->orderBy('position', 'asc')
            ->get();

        $all_categories = [];

        foreach ($category as $cate) {
            $cate_subcategory = SubCategory::where('status', 1)
                ->where('category_id', $cate->id)
                ->get();

            $all_categories[] = [
                'id' => $cate->id,
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $cate->getTranslations('title')),
                'icon'        => $cate->icon,
                'slug'        => $cate->slug,
                'status'      => $cate->status,
                'featured'    => $cate->featured,
                'image'       => $cate->cat_image,
                'imagepath'   => url('images/category/' . $cate->cat_image),
                'position'    => $cate->position,
                'created_at'  => $cate->created_at,
                'updated_at'  => $cate->updated_at,
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

    //------------TESTIMONIAL-----------------

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
        App::setlocale($request->lang);
        
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

    //------------iNTRO VIDEO-----------------

    function videoSetting(Request $request)
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

        $video = Videosetting::first();

        return response()->json(['video' => $video], 200);
    }

    //------------CONTACT US-----------------
    
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

    public function contactDetails(Request $request)
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

        $setting = Setting::first(['default_address', 'wel_email', 'default_phone']);

        return response()->json([
            'data' => $setting,
        ], 200);
    }

}
