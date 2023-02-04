<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class AuthController extends Controller
{
    use ResponseHelper;

    public function register_admin(Request $request)
    {
        return $this->register($request, 'admin');
    }

    public function register_user(Request $request)
    {
        return $this->register($request, 'user');
    }

    private function register(Request $request, $role = 'user')
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (count($validator->errors()) > 0) {
            return $this->onError(400, '', $validator->errors());
        }

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role' => $role
        ]);

        $this->guard()->login($user);

        return $this->onSuccess($user, '', 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (count($validator->errors()) > 0) {
            return $this->onError(400, '', $validator->errors());
        }

        $credentials = [
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ];

        if(!auth()->validate($credentials)):
            return $this->onError(401, trans('auth.failed'));
        endif;

        $user = auth()->getProvider()->retrieveByCredentials($credentials);

        $this->guard()->login($user);

        return $this->onSuccess($user);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->onSuccess([], 'Logged Out', 204);
    }

    public function me()
    {
        $user = auth()->user();
        
        return $this->onSuccess($user);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
