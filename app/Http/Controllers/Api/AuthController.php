<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Registro
     * 
     * Este metodo registra al usuario en el API
     * 
     * @unauthenticated
     * 
     * @bodyParam name string required User's name. Example: user1
     * @bodyParam email string required User's email. Example: test@test.com
     * @bodyParam password string required User's password. Example: 12345
     * @bodyParam password_confirmation string required User's password for confirmation. Example: 12345
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        
        return response($user, Response::HTTP_CREATED);
    }
    
    /**
     * Login
     * 
     * Este metodo devuelve el token del usuario
     * 
     * @unauthenticated
     * 
     * @bodyParam email string required User's email. Example: test@test.com
     * @bodyParam password string required User's password. Example: 12345
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('cookie_token', $token, 60 * 24);
            return response(['token' => $token], Response::HTTP_OK)->withoutCookie($cookie);
        }
        
        return response(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
    }
}
