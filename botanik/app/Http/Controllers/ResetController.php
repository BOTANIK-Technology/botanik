<?php

namespace App\Http\Controllers;

use App\Jobs\SendMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResetController extends Controller
{
    private const PASSWORD_LENGTH_MIN = 999999;
    private const PASSWORD_LENGTH_MAX = 99999999;

    public function reset(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (empty($user)) {

            return redirect(
                route('custom.reset.confirm', ['email' => $request->email, 'business' => $request->business])
            );
        }

        try {
            $newPassword = random_int(self::PASSWORD_LENGTH_MIN, self::PASSWORD_LENGTH_MAX);
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                $e->getMessage(),
            ]);
        }

        $user->update([
            'password' => bcrypt($newPassword)
        ]);

        SendMail::dispatch(
            $request->business,
            $user->email,
            $newPassword,
            $user->name,
            $request->business_name
        )->delay(now()->addMinutes(2));

        return redirect(
            route('custom.reset.confirm', ['email' => $user->email])
        );
    }

    public function confirm(Request $request)
    {
        return view('auth.login', ['email' => $request->email]);
    }
}
