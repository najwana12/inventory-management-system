<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Role;
use App\Models\GoodsIn;
use App\Models\GoodsOut;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $roles = Role::all();
        return view('admin.settings.employee', compact('roles'));
    }

    public function list(Request $request): JsonResponse
    {
        $users = User::with('role')->latest()->get();

        if ($request->ajax()) {
            return DataTables::of($users)
                ->addColumn('role_name', function ($data) {
                    return $data->role ? $data->role->name : '-';
                })
                ->addColumn('tindakan', function ($data) {
                    $btn  = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'>";
                    $btn .= "<i class='fas fa-pen m-1'></i>" . __("edit") . "</button>";
                    $btn .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'>";
                    $btn .= "<i class='fas fa-trash m-1'></i>" . __("delete") . "</button>";
                    return $btn;
                })
                ->rawColumns(['tindakan'])
                ->make(true);
        }

        return response()->json([])->setStatusCode(400);
    }

    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:4',
            'role_id'  => 'required|exists:roles,id',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
        ]);

        return response()->json([
            'message' => __("saved successfully")
        ])->setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'message' => __("data not found")
            ])->setStatusCode(404);
        }

        return response()->json([
            'data' => $user
        ])->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'message' => __("data not found")
            ])->setStatusCode(404);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user->name     = $request->input('name');
        $user->username = $request->input('username');
        $user->role_id  = $request->input('role_id');   // <- UPDATE ROLE DI SINI

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return response()->json([
            'message' => __("data changed successfully")
        ])->setStatusCode(200);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __("data not found")
                ])->setStatusCode(404);
            }

            $hasGoodsIn  = GoodsIn::where('user_id', $id)->exists();
            $hasGoodsOut = GoodsOut::where('user_id', $id)->exists();

            if ($hasGoodsIn || $hasGoodsOut) {
                return response()->json([
                    'message' => __("user cannot be deleted because it is used in transactions")
                ])->setStatusCode(400);
            }

            $user->delete();

            return response()->json([
                'message' => __("data deleted successfully")
            ])->setStatusCode(200);

        } catch (QueryException $e) {
            return response()->json([
                'message' => __("user cannot be deleted because it is used in transactions")
            ])->setStatusCode(400);
        }
    }
}
