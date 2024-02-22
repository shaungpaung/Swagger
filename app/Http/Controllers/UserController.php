<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      title="Your API Title",
 *      version="1.0.0",
 *      description="Your API description"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get user details",
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index(Request $request)
    {
        $query = User::orderBy('user_name');
        if ($request->has('query_with')) {
            $query->with($request->query_with);
        }
        return response()->json($query->get());
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     ),
     *     @OA\Response(response="200", description="User successfully created"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required',
            'branch_id' => 'required',
        ]);
        $user = User::create($validated);
        return response()->json(['message' => 'Successfully created user.', 'id' => $user->id]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user by id",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(string $id)
    {
        //
        $user = User::with('branch')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found this id'], 404);
        }
        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update user",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successful"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, string $id)
    {
        //
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User ' . $id . ' Not found'], 404);
        }
        $validated = $request->validate([
            'user_name' => 'required',
            'branch_id' => 'required|exists:App\Models\Branch,id'
        ]);
        $user->update($validated);
        return response()->json(['message' => 'Successfully updated', 'id' => $id]);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/change-password",
     *     summary="Change password",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function changePassword(Request $request, $id)
    {
        $validated = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
        ]);
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found this id.'], 404);
        }
        if (!Hash::check($validated['old_password'], $user->password)) {
            return response()->json(['message' => "Old password is invalid."], 422);
        }
        $user->update([
            'password' => Hash::make($validated["new_password"])
        ]);
        $user->tokens()->delete(); // Revoke all tokens...
        return response()->json([
            'message' => 'Successfully changed password.'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete User",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Successful"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found this id'], 404);
        }
        $user->delete();
        $result = "Successfully deleted";
        return response()->json($result);
    }

    /**
     * @OA\Post(
     *     path="/api/users/login",
     *     summary="Login User",
     *     @OA\Parameter(
     *         name="user_name",
     *         in="query",
     *         description="User's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */

    public function login(Request $request)
    {
        $login = $request->validate([
            'user_name' => 'required',
            'password' => 'required',
        ]);
        $user = User::Where('user_name', $login["user_name"])->first();
        if (!$user) {
            return response()->json(['message' => "This account is not found."], 404);
        }
        if (!$user || !Hash::check($login["password"], $user->password)) {
            return response()->json(["message" => "Password is invalid."], 422);
        }
        $token = $user->createToken($login['user_name'])->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/reset-password",
     *     summary="Reset password",
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function resetPassword(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found this id'], 404);
        }
        $user->update([
            'password' => Hash::make('123456')
        ]);
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Successfully reset password.'
        ]);
    }
}