<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/branches",
     *     summary="Get branch details",
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        //
        $branch = Branch::all();
        return response()->json($branch);
    }

    /**
     * @OA\Post(
     *     path="/api/branches",
     *     summary="Create branch",
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
            'township_id' => 'required'
        ]);
        $branch = Branch::create($validated);
        return response()->json($branch);
    }

    /**
     * @OA\Get(
     *     path="/api/branches/{id}",
     *     summary="Get branch by id",
     *@OA\Parameter(
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
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['Message' => 'Not found this id'], 404);
        }
        return response()->json($branch);
    }

    /**
     * @OA\Put(
     *     path="/api/branches/{id}",
     *     summary="Update branch",
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
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['Message' => 'Not found this id'], 404);
        }
        $validated = $request->validate([
            'name' => ['required', Rule::unique('branches', 'name')->ignore($id, 'id')],
            'township_id' => 'required'
        ]);
        $branch->update($validated);
        return response()->json($branch);
    }

    /**
     * @OA\Delete(
     *     path="/api/branches/{id}",
     *     summary="Delete branch",
     *     description="Delete branch",
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
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['message' => 'Not found this id'], 404);
        }
        $branch->delete();
        return response()->json(['message' => 'Successfully delete']);
    }
}