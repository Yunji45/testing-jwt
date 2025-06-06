<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\KafkaService;


class UserController extends Controller
{
    // created Register API
    public function register(Request $request)
    {

        // form theke je value gulo pabo, adike sugulo validator diye check kra hobe valid kina
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'email'=>'required|string|email|max:100|unique:users',
            'password'=>'required|string|min:6|confirmed',
        ]);

        // jdi form e kono input e problem thake tahle sei error show krar jnne fails() use hoise
        if ($validator->fails())
        {
            return response()->json(['errors'=>$validator->errors()]);
        }

        // ar jdi sob thik thake, tahle amra akta user register krbo 'create' function er dhara
        // sathe json er dhara msg, user value pathabo.
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);

        return response()->json([
            'msg'=>'User Registerd Successfully',
            'user'=>$user,
        ]);
    }

    // created Login API
    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email'=>'required|string|email|',
    //         'password'=>'required|string|min:6',
    //     ]);

    //     if ($validator->fails())
    //     {
    //         return response()->json($validator->errors());
    //     }
    //     $token = auth()->attempt($validator->validated());
    //     if (!$token)
    //     {
    //         return response()->json([
    //             'success'=>false,
    //             'msg' =>'Username and Password is uncorrect',
    //         ]);

    //     }

    //     // Ambil data pengguna yang terkait dengan token
    //     $user = auth()->user();

    //     // Membuat token dengan klaim tambahan
    //     $tokenWithClaims = auth()->setTTL(60)->claims([
    //         'name' => $user->name, // Menambahkan nama pengguna ke klaim
    //     ])->login($user);

    //     return response()->json([
    //         'success' => true,
    //         'msg'=>'Successfully Login',
    //         'token' => $tokenWithClaims,
    //         'token_type' => 'Bearer',
    //         'expires_in' => auth()->factory()->getTTL()*60
    //     ]);
    // }

    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|string|email',
    //         'password' => 'required|string|min:6',
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }
    
    //     $user = User::where('email', $request->email)->first();
    
    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'success' => false,
    //             'msg' => 'Username and Password are incorrect',
    //         ], 401);
    //     }
    
    //     $token = JWTAuth::fromUser($user);
    
    //     return response()->json([
    //         'success' => true,
    //         'msg' => 'Successfully Login',
    //         'token' => $token,
    //         'token_type' => 'Bearer',
    //         'name' => $user->name,
    //         'user_id' => $user->id,
    //         'expires_in' => auth()->factory()->getTTL() * 60,
    //     ]);
    // }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'msg' => 'Username and Password are incorrect',
            ], 401);
        }
    
        $token = JWTAuth::fromUser($user);
        try {
            Http::post('http://82.25.108.179:50000/api/v1/login', [
                'success'    => true,
                'msg'        => 'Successfully Login',
                'token'      => $token,
                'token_type' => 'Bearer',
                'name'       => $user->name,
                'email'      => $user->email,
                'user_id'    => $user->id,
                // 'no_hp'      => $user->no_hp,
                'wilayah_id'    => $user->wilayah_id,
                'region_id'    => $user->region_id,
                'expires_in' => auth()->factory()->getTTL() * 60,
            ]);
        } catch (\Exception $e) {
        }
        return response()->json([
            'success'    => true,
            'msg'        => 'Successfully Login',
            'token'      => $token,
            'token_type' => 'Bearer',
            'name'       => $user->name,
            'email'      => $user->email,
            'user_id'    => $user->id,
            'no_hp'      => $user->no_hp,
            'wilayah_id'    => $user->wilayah_id,
            'region_id'    => $user->region_id,
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
        
    

    // created Logout API
    public function logout()
    {
        try {
            auth()->logout();
            return response()->json([
                'success' => true,
                'msg' => 'User Log out Successfull',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ]);

        }

    }

    // created profile API
    public function profile()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => auth()->user(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ]);

        }

    }

    // created profile-update API
    public function updateProfile(Request $request)
    {

        if (auth()->user()){
            $validator = Validator::make($request->all(), [
                'id'=>'required',
                'name'=>'required|string',
                'email'=>'required|string|email',
            ]);

            if ($validator->fails())
            {
                return response()->json($validator->errors());
            }

            $user = User::find($request->id);
            $user->name = $request->name;
            if ($user->email != $request->email) {
                $user->is_verified = 0;
            }
            $user->email = $request->email;
            $user->save();
            return response()->json([
                'success' => true,
                'msg' => 'User Updated Successfully',
                'data' => $user,
            ]);



        } else {
            return response()->json([
                'success' => false,
                'msg' => 'User is Not Authenticated',
            ]);
        }
    }

    // send verification mail with verify link
    public function sendVerifyMail($email)
    {
        if (auth()->user()){
            $user = User::where('email', $email)->get();
            if (count($user) > 0) {
                // random url bananor jnne, 40ta random word nilam,domain nilam
                // sese url banalam agula diye
                $random = Str::random(40);
                $domain = URL::to('/');
                $url = $domain.'/verify-mail/'.$random;

                // Mail 'view' page e email er data send krte hobe, tai $data variable e kore
                // url, email,title,body pathiye dilam 'data' array akare.
                $data['url'] = $url;
                $data['email'] = $email;
                $data['title'] = 'Email Verification';
                $data['body'] = 'Please click here to below verify your mail';

                Mail::send('verifyMail', ['data'=>$data], function($message) use ($data){
                    $message->to($data['email'])->subject($data['title']);
                });

                $user = User::find($user[0]['id']);
                $user->remember_token = $random;
                $user->save();
                return response()->json([
                    'success' => true,
                    'msg' => 'Mail Sent Successfully',
                ]);


            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'User not found.',
                ]);
            }

        } else {
            return response()->json([
                'success' => false,
                'msg' => 'User is Not Authenticated',
            ]);
        }
    }

    // token link after verification message
    public function verificationMail($token)
    {
        $user = User::where('remember_token', $token)->get();
        if (count($user) > 0) {
            $datetime = Carbon::now()->format('Y-m-d H:i:s');
            $user = User::find($user[0]['id']);
            $user->remember_token = '';
            $user->is_verified = 1;
            $user->email_verified_at = $datetime;
            $user->save();

            return "<h1>Email Verified Successfully</h1>";


        } else {
            return view('404');
        }

    }

    public function refreshToken()
    {

        if (auth()->user()){

            return response()->json([
                'success' => true,
                'token' => auth()->refresh(),
                'token_type' => 'Bearer',
                'expires_in' => auth()->factory()->getTTL()*60
            ]);



        } else {
            return response()->json([
                'success' => false,
                'msg' => 'User is Not Authenticated',
            ]);
        }
    }



}
