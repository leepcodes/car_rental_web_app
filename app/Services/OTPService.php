<?php

namespace App\Services;

use App\Models\OTP;
use App\Models\User;
use App\Mail\OTPVerificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OTPService
{
    public static function isUserVerified(User $user): bool
    {
        if ($user->is_verified == 1 || $user->is_verified === true) {
            return true;
        }

        if ($user->email_verified_at) {
            try {
                $date = Carbon::parse($user->email_verified_at);
                if ($date->year > 1000) {
                    return true;
                }
            } catch (\Exception $e) {
                // Invalid date format
            }
        }

        return false;
    }

    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function expireActiveOTPs(int $userId): int
    {
        return OTP::where('user_id', $userId)
            ->where('status', 'active')
            ->update(['status' => 'expired']);
    }

    public static function createOTP(User $user): OTP
    {
        Log::info('Starting OTP generation', ['user_id' => $user->id]);

        self::expireActiveOTPs($user->id);

        $code = self::generateCode();
        
        $otp = OTP::create([
            'user_id' => $user->id,
            'code' => $code,
            'status' => 'active',
        ]);

        Log::info('OTP created', ['otp_id' => $otp->id]);

        return $otp;
    }

    public static function sendOTPEmail(User $user, string $code): bool
    {
        try {
            Mail::to($user->email)->send(new OTPVerificationMail($user, $code));
            Log::info('OTP email sent', ['email' => $user->email]);
            return true;
        } catch (\Exception $e) {
            Log::error('OTP email failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public static function findActiveOTP(int $userId, string $code): ?OTP
    {
        return OTP::where('user_id', $userId)
            ->where('code', $code)
            ->where('status', 'active')
            ->first();
    }

    public static function isOTPExpired(OTP $otp, int $minutes = 10): bool
    {
        return $otp->created_at->addMinutes($minutes)->isPast();
    }

    public static function verifyUser(User $user): void
    {
        DB::beginTransaction();
        
        try {
            $now = Carbon::now();
            
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'is_verified' => 1,
                    'email_verified_at' => $now,
                    'updated_at' => $now
                ]);

            Log::info('User verified', ['user_id' => $user->id]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User verification failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public static function hasRecentOTP(int $userId): ?OTP
    {
        return OTP::where('user_id', $userId)
            ->where('created_at', '>', Carbon::now()->subMinute())
            ->first();
    }

    public static function cancelActiveOTPs(int $userId): int
    {
        return OTP::where('user_id', $userId)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);
    }

    public static function getRedirectUrl(?int $vehicleId = null): string
    {
        $intendedUrl = session('url.intended');
        
        if ($intendedUrl) {
            session()->forget('url.intended');
            return $intendedUrl;
        }

        if ($vehicleId) {
            return route('client.booking.show', ['id' => $vehicleId]);
        }

        return route('client.booking');
    }
}