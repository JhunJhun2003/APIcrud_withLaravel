<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Sanctum\HasApiTokens;

class OtpController extends Controller
{
    // Handle OTP sending (no changes needed here mostly)
    public function loginWithOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        try {
            // Generate OTP
            $token = random_int(1000, 9999);
            
            // Store OTP in database
            Otp::where('email', $request->email)->delete();
            
            $passwordReset = new Otp;
            $passwordReset->email = $request->email;
            $passwordReset->otp = $token;
            $passwordReset->created_at = now();
            $passwordReset->save();
            
            // Send email with OTP
            $emailData = [
                'otp' => $token,
                'email' => $request->email,
                'appName' => config('app.name', 'Flutter Login'),
                'expiryTime' => '60 minutes',
            ];
            
            Mail::send('otpemail', $emailData, function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('OTP from todolist - ' . config('app.name', 'Flutter Login'));
                $message->from(
                    config('mail.from.address', 'noreply@Flutter.app'),
                    config('mail.from.name', config('app.name'))
                );
            });
            
            return response()->json(['message' => 'OTP has been sent to your email! Check Mail box','code' => $token], 200);
            
        } catch (\Exception $e) {
            Log::error('OTP sending error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP. Error: ' . $e->getMessage() ], 500);
        }
    }
    
    // Handle OTP verification and RETURN A BEARER TOKEN
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:4',
        ]);
        
        // Find the OTP record
        $otpRecord = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();
        
        if (!$otpRecord) {
            return response()->json(['error' => 'Invalid OTP.'], 401);
        }
        
        // Get the user
        $user = User::where('email', $request->email)->first();
        
        // IMPORTANT: Delete the OTP record immediately (security)
        $otpRecord->delete();
        
        // CREATE SANCTUM TOKEN FOR MOBILE APP
        // You can name it anything, 'mobile-token' helps identify it
        $token = $user->createToken('mobile-app-token')->plainTextToken;
        
        // Return the token AND user data to the mobile app
        return response()->json([
            'message' => 'Logged in successfully!',
            'token' => $token,  // Bearer token for mobile app
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Add any other user fields you want to return
            ]
        ]);
    }
    
}   