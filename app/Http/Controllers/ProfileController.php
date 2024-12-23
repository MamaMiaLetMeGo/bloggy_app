<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{

    /**
     * Display the user's profile form.
     */
    public function show(Request $request, ?User $user = null): View
    {
        // If no user is provided, show the authenticated user's profile
        $user = $user ?? $request->user();
        
        // Load the user's posts
        $user->load(['posts' => function ($query) {
            $query->published()
                  ->latest('published_date')
                  ->take(5);
        }]);

        return view('profile.show', [
            'user' => $user,
        ]);
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's two-factor authentication settings.
     */
    public function showTwoFactor(Request $request): View
    {
        $user = $request->user();
        $enabled = $user->two_factor_secret !== null;
        $qrCode = '';
        $secret = '';

        if (!$enabled) {
            $secret = $user->createTwoFactorSecret();
            $qrCode = $user->getTwoFactorQrCodeSvg();
        }

        return view('profile.2fa', [
            'enabled' => $enabled,
            'qrCode' => $qrCode,
            'secret' => $secret,
            'user' => $user,
        ]);
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function enableTwoFactor(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->verifyTwoFactorCode($request->code)) {
            $user->enableTwoFactor();
            return redirect()->route('profile.2fa.show')
                ->with('status', 'Two-factor authentication has been enabled.');
        }

        return back()->withErrors(['code' => 'The provided code is invalid.']);
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disableTwoFactor(Request $request)
    {
        $request->user()->disableTwoFactor();
        
        return redirect()->route('profile.2fa.show')
            ->with('status', 'Two-factor authentication has been disabled.');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'profile_image' => ['nullable', 'image', 'max:1024'], // max 1MB
        ]);

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::delete($user->profile_image);
            }
            
            // Store new image
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $validated['profile_image'] = $path;
        }

        // Handle image removal
        if ($request->input('remove_image') === '1' && $user->profile_image) {
            Storage::delete($user->profile_image);
            $validated['profile_image'] = null;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
