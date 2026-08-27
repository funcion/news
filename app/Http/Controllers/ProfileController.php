<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\Tag;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $comments = $user->comments()->with('article')->latest()->paginate(10);
        $trendingTags = Tag::withMinimumArticles(1)->popular(10)->get();

        return view('pages.profile', compact('user', 'comments', 'trendingTags'));
    }

    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'] ?? null;

        // Handle Avatar Removal
        if ($request->boolean('remove_avatar') && $user->avatar_url) {
            $oldPath = str_replace('storage/', '', $user->avatar_url);
            Storage::disk('public')->delete($oldPath);
            $user->avatar_url = null;
        }

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar_url) {
                $oldPath = str_replace('storage/', '', $user->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = 'storage/' . $path;
        }

        $user->save();

        return back()->with('success', __('ui.profile_updated_success'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', __('ui.password_updated_success'));
    }
}