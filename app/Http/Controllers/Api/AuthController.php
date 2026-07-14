<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponses;

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
            );
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return $this->successResponse(
            new UserResource($user),
            "User registered successfully",
            201, $token
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse(
                null, "Invaild login credentials", 401,
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            new UserResource($user),
            "authentication succss",
            200, $token
        );
    }

    public function logout(Request $request): JsonResponse
    {
        // Revoke all tokens...
        // $request->user()->tokens()->delete();

        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            null, "Logout success"
        );
    }
}
