<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validasi input
            $email = $request->input('email');
            $password = $request->input('password');

            // Log attempt
            Log::info('Login attempt for email: ' . $email);

            // Cari user berdasarkan email
            $user = Users::where('email', $email)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                Log::warning('Failed login attempt for email: ' . $email);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            // Check if user is active
            if (!$user->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is inactive. Please contact administrator.'
                ], 401);
            }

            // Generate token
            $token = Str::random(60);
            $user->remember_token = $token;
            $user->save();

            Log::info('Successful login for user: ' . $email . ' with role: ' . $user->role);

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'userId' => $user->id,
                    'userEmail' => $user->email,
                    'userName' => $user->name,
                    'userAvatar' => $user->avatar ?? '/public/26954e5e29b5c77937e5098d3e817bb5.png',
                    'role' => $user->role ?? 'user'
                ],
                'message' => 'Login successful'
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Ambil token dari header
            $token = $request->header('Authorization');

            if (!$token) {
                Log::warning('Logout attempt without token');
                return response()->json([
                    'success' => false,
                    'message' => 'No authorization token provided'
                ], 401);
            }

            // Bersihkan prefix "Bearer " jika ada
            $token = str_replace('Bearer ', '', $token);

            // Cari user dengan token tersebut
            $user = Users::where('remember_token', $token)->first();

            if ($user) {
                // Log before logout
                Log::info('Logging out user: ' . $user->email);

                // Hapus token
                $user->remember_token = null;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Logged out successfully'
                ]);
            }

            Log::warning('Logout attempt with invalid token');
            return response()->json([
                'success' => false,
                'message' => 'Invalid token'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
            ], 500);
        }
    }

    public function checkLoginStatus(Request $request)
    {
        try {
            // Ambil token dari header atau query parameter
            $token = $request->header('Authorization') ?? $request->query('token');
            
            if (!$token) {
                // Coba ambil dari localStorage melalui request
                $token = $request->input('token') ?? $request->bearerToken();
            }

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No token provided'
                ], 401);
            }

            // Bersihkan prefix "Bearer " jika ada
            $token = str_replace('Bearer ', '', $token);

            // Cari user dengan token tersebut
            $user = Users::where('remember_token', $token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            // Check if user is still active
            if (!$user->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is inactive'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'userId' => $user->id,
                    'userEmail' => $user->email,
                    'userName' => $user->name,
                    'userAvatar' => $user->avatar ?? '/public/26954e5e29b5c77937e5098d3e817bb5.png',
                    'role' => $user->role ?? 'user'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Check login status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
            ], 500);
        }
    }

    // Method untuk mendapatkan user permissions berdasarkan role
    public function getUserPermissions(Request $request)
    {
        try {
            $token = $request->header('Authorization') ?? $request->bearerToken();
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No token provided'
                ], 401);
            }

            $token = str_replace('Bearer ', '', $token);
            $user = Users::where('remember_token', $token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            // Define role-based permissions
            $rolePermissions = [
                'admin' => [
                    'components' => [
                        'cmpHalDashboard', 'cmpHalProfile', 'cmpUsers', 'cmpMasterCabang', 
                        'cmpMasterUnit', 'cmpMasterJabatan', 'cmpMasterOrganisasi', 'cmpMasterBank',
                        'cmpMasterSallary', 'cmpMasterBpjs', 'cmpMasterNpwp', 'cmpMasterKodeLokasi',
                        'cmpMasterProfile', 'cmpMasterProfileDetail', 'cmpMasterKontrak', 'cmpRekKar',
                        'cmpMasterTemplate', 'cmpMasterDocument', 'cmpBobotPkSoba', 'cmpTempPos',
                        'cmpTempPosDetail', 'cmpSignatureMaster', 'MasterKriteria', 'KriteriaDetail',
                        'cmpMasterRapor'
                    ],
                    'menus' => ['DATA', 'MASTERDATA'],
                    'actions' => ['create', 'read', 'update', 'delete']
                ],
                'hr_manager' => [
                    'components' => [
                        'cmpHalDashboard', 'cmpHalProfile', 'cmpMasterProfile', 'cmpMasterProfileDetail',
                        'cmpMasterKontrak', 'cmpMasterRapor', 'MasterKriteria', 'KriteriaDetail',
                        'cmpSignatureMaster', 'cmpTempPos', 'cmpTempPosDetail', 'cmpMasterBpjs',
                        'cmpMasterNpwp', 'cmpRekKar'
                    ],
                    'menus' => ['DATA'],
                    'actions' => ['create', 'read', 'update']
                ],
                'user' => [
                    'components' => [
                        'cmpHalDashboard', 'cmpHalProfile'
                    ],
                    'menus' => [],
                    'actions' => ['read']
                ]
            ];

            $userRole = $user->role ?? 'user';
            $permissions = $rolePermissions[$userRole] ?? $rolePermissions['user'];

            return response()->json([
                'success' => true,
                'role' => $userRole,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            Log::error('Get user permissions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
            ], 500);
        }
    }
}