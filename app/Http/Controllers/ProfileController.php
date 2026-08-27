<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\ImageManagerStatic as Image;
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
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'] ?? null;

        $r2PublicUrl = rtrim(config('filesystems.disks.r2.url') ?? env('R2_PUBLIC_URL', 'https://media.glodaxia.com'), '/');

        // Handle Avatar Removal from Cloudflare R2
        if ($request->boolean('remove_avatar') && $user->getRawOriginal('avatar_url')) {
            $raw = $user->getRawOriginal('avatar_url');
            $cleanPath = ltrim(str_replace([$r2PublicUrl, 'https://media.glodaxia.com', 'storage/'], '', $raw), '/');
            Storage::disk('r2')->delete($cleanPath);
            $user->avatar_url = null;
        }

        // Handle Avatar Upload directly to Cloudflare R2 (Converted to WebP 256x256)
        if ($request->hasFile('avatar')) {
            if ($user->getRawOriginal('avatar_url')) {
                $raw = $user->getRawOriginal('avatar_url');
                $cleanPath = ltrim(str_replace([$r2PublicUrl, 'https://media.glodaxia.com', 'storage/'], '', $raw), '/');
                Storage::disk('r2')->delete($cleanPath);
            }

            $file = $request->file('avatar');
            $image = Image::read($file->getRealPath());
            $webpData = (string) $image->fit(256, 256)->encode('webp', 90);

            $filename = 'avatars/user_' . $user->id . '_' . bin2hex(random_bytes(8)) . '.webp';
            Storage::disk('r2')->put($filename, $webpData, [
                'visibility' => 'public',
                'ContentType' => 'image/webp',
            ]);

            $user->avatar_url = "{$r2PublicUrl}/{$filename}";
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