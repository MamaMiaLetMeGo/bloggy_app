<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAuthorProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;

class AuthorController extends Controller
{
    protected function middleware(): array
    {
        return [
            'auth' => ['except' => ['show', 'index']]
        ];
    }

    public function index(): View
    {
        $authors = User::whereHas('publishedPosts')
            ->withCount('publishedPosts')
            ->orderByDesc('published_posts_count')
            ->paginate(12);

        return view('authors.index', compact('authors'));
    }

    public function show(User $user): View
    {
        $posts = $user->publishedPosts()
            ->with(['categories'])
            ->orderByDesc('published_date')
            ->paginate(10);

        $stats = $user->getPostsStatistics();

        return view('authors.show', compact('user', 'posts', 'stats'));
    }

    public function edit(): View
    {
        return view('authors.edit', [
            'author' => auth()->user(),
        ]);
    }

    public function update(UpdateAuthorProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::delete($user->profile_image);
            }

            $data['profile_image'] = $request->file('profile_image')
                ->store('profile-images', 'public');
        }

        $user->update($data);

        return redirect()
            ->route('author.edit')
            ->with('success', 'Profile updated successfully!');
    }

    public function dashboard(): View
    {
        $user = auth()->user();
        $stats = $user->getPostsStatistics();
        
        $recentPosts = $user->posts()
            ->with('categories')
            ->latest()
            ->take(5)
            ->get();

        $draftPosts = $user->draftPosts()
            ->with('categories')
            ->latest()
            ->take(5)
            ->get();

        return view('authors.dashboard', compact(
            'stats',
            'recentPosts',
            'draftPosts'
        ));
    }
}