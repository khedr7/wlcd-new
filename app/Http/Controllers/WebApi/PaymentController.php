<?php

namespace App\Http\Controllers\WebApi;

use App\BundleCourse;
use App\Cart;
use App\Course;
use App\Currency;
use App\Http\Controllers\Controller;
use App\Http\Traits\SendNotification;
use App\InstructorSetting;
use App\ManualPayment;
use App\NewNotification;
use App\Order;
use App\PendingPayout;
use App\User;
use App\Wishlist;
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
                    $query->where('status', 1)->select(
                        'id',
                        'user_id',
                        'category_id',
                        'subcategory_id',
                        'childcategory_id',
                        'language_id',
                        'title',
                        'price',
                        'discount_price',
                        'featured',
                        'slug',
                        'status',
                        'preview_image',
                        'type',
                        'level_tags'
                    )
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

    public function getManual(Request $request)
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

        $payments = ManualPayment::get();

        $result = array();

        foreach ($payments as $data) {

            $result[] = array(
                'id' => $data->id,
                'name' => $data->name,
                'detail' => strip_tags($data->detail),
                'image' => $data->image,
                'image_path' => url('images/manualpayment/' . $data->image),
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('manual_payment' => $result), 200);
    }

    //------------CART----------------

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret'    => 'required',
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

    public function removeCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret'    => 'required',
            'course_id' => 'required|exists:courses,id'
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

        $cart = Cart::where('course_id', $request->course_id)
            ->where('user_id', $auth->id)
            ->delete();

        if ($cart == 1) {
            return response()->json(['done'], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function showCart(Request $request)
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
            ->with([
                'courses' => function ($query) {
                    $query->select([
                        'id', 'user_id', 'category_id', 'subcategory_id', 'childcategory_id', 'language_id', 'title', 'short_detail', 'detail',
                        'price', 'discount_price', 'featured', 'slug', 'status', 'preview_image', 'type', 'level_tags'
                    ])
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
                        ])->get();
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

    public function removeAllCart(Request $request)
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
            return response()->json(['done'], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function addBundleToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret'    => 'required',
            'bundle_id' => 'required|exists:bundle_courses,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if ($errors->first('bundle_id')) {
                return response()->json(['message' => $errors->first('bundle_id'), 'status' => 'fail']);
            }
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

    public function removeBundleCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret'    => 'required',
            'bundle_id' => 'required|exists:bundle_courses,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if ($errors->first('bundle_id')) {
                return response()->json(['message' => $errors->first('bundle_id'), 'status' => 'fail']);
            }
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
            return response()->json(['done'], 200);
        } else {
            return response()->json(['error'], 401);
        }
    }

    public function payStore(Request $request)
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

        $auth = Auth::user();

        $currency = Currency::where('default', '=', '1')->first();

        $carts = Cart::where('user_id', $auth->id)->get();

        if ($file = $request->file('proof')) {
            $name = time() . '_' . $file->getClientOriginalName();
            $name = str_replace(" ", "_", $name);
            $file->move('images/order', $name);
            $input['proof'] = $name;
        } else {
            $name = null;
        }

        if ($request->pay_status == 1) {

            foreach ($carts as $cart) {

                if ($cart->offer_price != 0) {
                    $pay_amount =  $cart->offer_price;
                } else {
                    $pay_amount =  $cart->price;
                }

                if ($cart->disamount != 0 || $cart->disamount != NULL) {

                    $cpn_discount =  $cart->disamount;
                } else {
                    $cpn_discount =  '';
                }

                $lastOrder = Order::orderBy('created_at', 'desc')->where('order_id', '!=', null)->first();

                if (!$lastOrder) {
                    // We get here if there is no order at all
                    // If there is no number set it to 0, which will be 1 at the end.
                    $number = 0;
                } else {
                    $number = substr($lastOrder->order_id, 3);
                }

                if ($cart->type == 1) {
                    $bundle_id = $cart->bundle_id;
                    $course_id = NULL;
                    $duration = NULL;
                    $instructor_payout = 0;
                    $instructor_id = $cart->bundle->user_id;

                    if ($cart->bundle->duration_type == "m") {

                        if ($cart->bundle->duration != NULL && $cart->bundle->duration != '') {
                            $days = $cart->bundle->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }
                    } else {

                        if ($cart->bundle->duration != NULL && $cart->bundle->duration != '') {
                            $days = $cart->bundle->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }
                    }
                } else {

                    if ($cart->courses->duration_type == "m") {

                        if ($cart->courses->duration != NULL && $cart->courses->duration != '') {
                            $days = $cart->courses->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }
                    } else {
                        if ($cart->courses->duration != NULL && $cart->courses->duration != '') {
                            $days = $cart->courses->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }
                    }

                    $setting = InstructorSetting::first();

                    if ($cart->courses->instructor_revenue != NULL) {
                        $x_amount = $pay_amount * $cart->courses->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    } else {
                        if (isset($setting)) {
                            if ($cart->courses->user->role == "instructor") {
                                $x_amount = $pay_amount * $setting->instructor_revenue;
                                $instructor_payout = $x_amount / 100;
                            } else {
                                $instructor_payout = 0;
                            }
                        } else {
                            $instructor_payout = 0;
                        }
                    }

                    $bundle_id = NULL;
                    $course_id = $cart->course_id;
                    $duration = $cart->courses->duration;
                    $instructor_id = $cart->courses->user_id;
                }


                if ($request->payment_method == 'paypal') {

                    $saleId = $request->sale_id;
                } else {

                    $saleId = NULL;
                }

                if ($request->payment_method == 'bank_transfer') {

                    $transaction_id = str_random(32);
                    $status =  '0';
                } else {
                    $manual_payment = ManualPayment::where('name', $request->payment_method)->first();
                    if (isset($manual_payment) && $manual_payment != NULL) {
                        $status = '0';
                    } else {
                        $status =  '1';
                    }

                    $transaction_id = $request->transaction_id;
                }

                $created_order = Order::create(
                    [
                        'course_id' => $course_id,
                        'user_id' => $auth->id,
                        'instructor_id' => $instructor_id,
                        'order_id' => '#' . sprintf("%08d", intval($number) + 1),
                        'transaction_id' => $transaction_id,
                        'payment_method' => $request->payment_method,
                        'total_amount' => $pay_amount,
                        'coupon_discount' => $cpn_discount,
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
                        'duration' => $duration,
                        'enroll_start' => $todayDate,
                        'enroll_expire' => $expireDate,
                        'bundle_id' => $bundle_id,
                        'sale_id' => $saleId,
                        'status' => $status,
                        'proof' => $name,
                        'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                    ]
                );


                if ($cart->type == 1) {
                    Cart::where('user_id', $auth->id)->where('bundle_id', $cart->bundle_id)->delete();
                } else {
                    Wishlist::where('user_id', $auth->id)->where('course_id', $cart->course_id)->delete();
                    Cart::where('user_id', $auth->id)->where('course_id', $cart->course_id)->delete();
                }


                if ($instructor_payout != 0) {
                    if ($created_order) {
                        if ($cart->type == 0) {
                            if ($cart->courses->user->role == "instructor") {

                                $created_payout = PendingPayout::create(
                                    [
                                        'user_id' => $cart->courses->user_id,
                                        'course_id' => $cart->course_id,
                                        'order_id' => $created_order->id,
                                        'transaction_id' => $request->transaction_id,
                                        'total_amount' => $pay_amount,
                                        'currency' => $currency->code,
                                        'currency_icon' => $currency->symbol,
                                        'instructor_revenue' => $instructor_payout,
                                        'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                                        'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                                    ]
                                );
                            }
                        }
                    }
                }

                // if ($created_order) {
                //     try {
                //         /*sending email*/
                //         $x = 'You are successfully enrolled in a course';
                //         $order = $created_order;
                //         Mail::to(Auth::User()->email)->send(new SendOrderMail($x, $order));
                //     } catch (\Swift_TransportException $e) {
                //     }
                // }

                // if ($cart->type == 0) {

                //     if ($created_order) {
                //         // Notification when user enroll
                //         $cor = Course::where('id', $cart->course_id)->first();

                //         $course = [
                //             'title' => $cor->title,
                //             'image' => $cor->preview_image,
                //         ];

                //         $enroll = Order::where('course_id', $cart->course_id)->get();

                //         if (!$enroll->isEmpty()) {
                //             foreach ($enroll as $enrol) {
                //                 $user = User::where('id', $enrol->user_id)->get();
                //                 Notification::send($user, new UserEnroll($course));
                //             }
                //         }
                //     }
                // }
            }

            return response()->json('Payment Successfull !', 200);
        } else {

            return response()->json('Payment Failed !', 401);
        }

        return response()->json('Payment Failed !', 401);
    }
}
