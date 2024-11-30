<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    public function toggleAdmin(User $user)
    {
        try {
            // Prevent self-demotion for the last admin
            if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove the last admin user'
                ], 422);
            }

            $user->update(['is_admin' => !$user->is_admin]);
            
            Log::info('User admin status changed', [
                'user_id' => $user->id,
                'is_admin' => $user->is_admin,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => $user->is_admin ? 'User is now an admin' : 'Admin privileges removed',
                'is_admin' => $user->is_admin
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to toggle admin status', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin status'
            ], 500);
        }
    }
} 