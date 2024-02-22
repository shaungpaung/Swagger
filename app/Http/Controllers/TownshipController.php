<?php

namespace App\Http\Controllers;

use App\Models\Township;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TownshipController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/townships",
     *     summary="Get township details",
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        //
        $township = Township::all();
        return response()->json($township);
    }

    /**
     * @OA\Post(
     *     path="/api/townships",
     *     summary="Create township",
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
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => ['required', Rule::unique('branches', 'name')],
        ]);
        $township = Township::create($validated);
        return response()->json($township);
    }

    /**
     * @OA\Get(
     *     path="/api/townships/{id}",
     *     summary="Get township by id",
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
        $township = Township::find($id);
        if (!$township) {
            return response()->json(['Message' => 'Not found this id'], 404);
        }
        return response()->json($township);
    }

    /**
     * @OA\Put(
     *     path="/api/townships/{id}",
     *     summary="Update township",
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
    public function update(Request $request, string $id)
    {
        //
        $township = Township::find($id);
        if (!$township) {
            return response()->json(['Message' => 'Not found this id'], 404);
        }
        $validated = $request->validate([
            'name' => ['required', Rule::unique('branches', 'name')->ignore($id, 'id')],
            'township_id' => 'required'
        ]);
        $township->update($validated);
        return response()->json($township);
    }

    /**
     * @OA\Delete(
     *     path="/api/townships/{id}",
     *     summary="Delete township",
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
    public function destroy(string $id)
    {
        //
        $township = Township::find($id);
        if (!$township) {
            return response()->json(['message' => 'Not found this id'], 404);
        }
        $township->delete();
        return response()->json(['message' => 'Successfully delete']);
    }
}