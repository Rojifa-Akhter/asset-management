<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyOTP;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function profile(){
        $user=Auth::user();
        return $user;
    }
    public function signup(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|unique:users,email',
        //     'address' => 'required|string|max:255',
        //     'phone' => 'nullable|string|max:15',
        //     'password' => 'required|string|min:6',
        //     'role' => 'nullable|string|in:Super Admin,Organization,Location Employee,Support Agent,Third Party,Technician,User',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        // }

        $path = null;
        if ($request->has('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $new_name=time().'.'.$extension;
            $path = $image->move(public_path('uploads/profile_images'),$new_name);
        }

        $otp = rand(100000, 999999);
        $otp_expires_at = now()->addMinutes(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Dynamically set the role from the input
            'image' => $new_name,
            'otp' => $otp,
            'otp_expires_at' => $otp_expires_at,
            'status' => 'inactive',
        ]);

        try {
            Mail::to($user->email)->send(new VerifyOTP($otp));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        $message = match ($user->role) {
            'Super Admin' => 'Welcome Super Admin! Please verify your email.',
            'Organization' => 'Welcome Organization! Please verify your email.',
            'Third Party' => 'Welcome Third Party! Please verify your email.',
            'Location Employee' => 'Welcome Location Employee! Please verify your email.',
            'Support Agent' => 'Welcome Support Agent! Please verify your email.',
            'Technician' => 'Welcome Technician! Please verify your email.',
            default => 'Welcome User! Please verify your email.',
        };

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 200);

    }
    // verify email
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 400);
        }
        $user = User::where('otp', $request->otp)->first();

        if ($user) {
            $user->otp = null;
            $user->email_verified_at = now();
            $user->status = 'active';
            $user->save();

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'error' => 'Invalid OTP.'], 400);
    }
    //login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Email not found.'], 404);
        }

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid password.'], 401);
        }

        $imageUrl = $user->image ?? asset('uploads/profile_images/' . $user->image);


        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'user_information' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
                'image' => $imageUrl,
            ],
        ], 200);
    }

    public function guard()
    {
        return Auth::guard('api');
    }
    // update profile
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:16',
            'password' => 'nullable|string|min:6|confirmed',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        // Update user data
        $user->name = $validatedData['name'] ?? $user->name;
        $user->address = $validatedData['address'] ?? $user->address;
        $user->phone = $validatedData['phone'] ?? $user->phone;

        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        if ($request->hasFile('image')) {
            if (!empty($user->image)) {
                $oldImagePath = str_replace(asset('storage/'), '', $user->image);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            $path = $request->file('image')->store('profile_images', 'public');
            $user->image = $path;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'address' => $user->address,
                'contact' => $user->phone,
                'image' => $user->image ? asset('storage/' . $user->image) : null,
                'role' => $user->role,
            ],
        ], 200);

    }

    //change password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect.'], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password changed successfully']);
    }
    // forgate password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email not registered.'], 404);
        }
        $otp = rand(100000, 999999);

        DB::table('users')->updateOrInsert(
            ['email' => $request->email],
            ['otp' => $otp, 'created_at' => now()]
        );

        try {
            Mail::to($request->email)->send(new VerifyOTP($otp));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to send OTP.'], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent to your email.'], 200);
    }

    // reset password
    public function resetPassword(Request $request)
    {
        // return $request;
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successful.'], 200);
    }
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email not registered.'], 404);
        }

        $otp = rand(100000, 999999);

        DB::table('users')->updateOrInsert(
            ['email' => $request->email],
            ['otp' => $otp, 'created_at' => now()]
        );

        try {
            Mail::to($request->email)->send(new VerifyOTP($otp));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to resend OTP.'], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP resent to your email.'], 200);
    }
    public function logout()
    {
        if (!auth('api')->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not authenticated.',
            ], 401);
        }

        auth('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out.',
        ]);
    }

}
