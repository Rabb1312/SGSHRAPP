<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\carbon;
use Exception;

use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER USERS';
    }

    protected function getCurrentUser($userid = null)
    {
        try {
            // Get userid from request if not provided
            if (!$userid) {
                $userid = request()->input('userid');
            }

            if (!$userid) {
                return 'USER TEST';
            }

            // Get user by ID
            $user = Users::find($userid);
            return $user ? $user->name : 'USER TEST';
        } catch (\Exception $e) {
            Log::error('Error getting user: ' . $e->getMessage());
            return 'USER TEST';
        }
    }

    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        $offset = $request->offset ?? 0;

        if (!isset($request->search)) {
            $count = (new Users)->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $offset);
            $todos = (new Users())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $offset,
                $request->search
            );
            $todos = (new Users())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }

        // Menambahkan properti nomorBaris yang dimulai dari 1
        $nomorBaris = $offset + 1;

        return response()->json([
            'success' => true,
            'results' => $todos,
            'count' => $count,
            'nomorBaris' => $nomorBaris
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->validate($request, [
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'name' => 'required',
                'role' => 'required',
                'status' => 'required|boolean',
            ]);

            $user_id = $this->getCurrentUser($request->userid);

            $user = new Users();
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->name = $request->name;
            $user->role = $request->role;
            $user->status = $request->status;
            $user->created_by = $user_id;
            $user->updated_by = $user_id;
            $user->save();

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Created Successfully',
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'role' => $user->role,
                    'status' => $user->status
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'code' => 422,
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Failed to create user: ' . $e->getMessage(),
            ], 409);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->validate($request, [
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|min:6',
                'name' => 'required',
                'role' => 'required',
                'status' => 'required|boolean',
            ]);

            $user = Users::findOrFail($id);
            $user_id = $this->getCurrentUser($request->userid);

            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->name = $request->name;
            $user->role = $request->role;
            $user->status = $request->status;
            $user->updated_by = $user_id;
            $user->save();

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Updated Successfully',
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'role' => $user->role,
                    'status' => $user->status
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'code' => 422,
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Failed to update user: ' . $e->getMessage(),
            ], 409);
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();
        $user_id = $this->getCurrentUser();

        try {
            $todo = Users::findOrFail($id);

            Users::where('id', $id)->update(['deleted_by' => $user_id]);
            $todo->delete();

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'deleted succsessfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed delete'
            ], status: 409);
        }
    }

    public function show(int $id)
    {
        try {
            $todos = Users::findOrFail($id);
            $todos->plain_password = '';  // Ini akan diisi di frontend jika user mengubah password

            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $todos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed get data'
            ], status: 404);
        }
    }


    public function getAllData()
    {
        try {
            $getAllData = Users::all();
            return response()->json($getAllData, 200);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }

    // Method tambahan khusus untuk Users
    public function showPassword(int $id)
    {
        try {
            $user = Users::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'current_password' => $this->decryptPassword($user->password)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to get password'
            ], 404);
        }
    }

    private function decryptPassword($hashedPassword)
    {
        try {
            return "secret_password";
        } catch (\Exception $e) {
            return null;
        }
    }
}