<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\ForgotPasswordInitialRequest;

class AuthController extends Controller
{
    public AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request->validated());
    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    public function forgotpassword(ForgotPasswordInitialRequest $request)
    {
        return $this->authService->forgotpassword($request->validated());
    }

    public function logout(Request $request)
    {
        // Invalidate the user session
        $request->session()->invalidate();

        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        // Optionally, you can destroy the session completely
        $request->session()->flush();

        return response()->json(['message' => 'Successfully logged out'], 200);

    }

    public function changepassword(Request $request)
    {
        return view('changepassword', ['key' => $request->key, 'email' => $request->email]);

    }

    public function passwordchange(Request $request)
    {
        if($request->key === env('EQUALPARTNER_API_KEY') ) {
            $user = User::where('email', $request->email)->first();
            if($user) {
                $user->password = Hash::make($request->password);
                $user->save();
                return redirect('successchange');
            } else return response()->json('No email like that.');
        }
        return response()->json('You change the verification key.');

        //return $this->authService->passwordchange($request->validated());
        
    
    }
}
