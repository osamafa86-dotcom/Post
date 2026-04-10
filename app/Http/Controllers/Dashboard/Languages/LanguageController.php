<?php

namespace App\Http\Controllers\Dashboard\Languages;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request, $lang = 'en')
    {
        $enabledLanguages = config('app.enabled_languages', ['ar', 'en']);

        if (!in_array($lang, $enabledLanguages)) {
            abort(404);
        }

        session(['locale' => $lang]);
        App::setLocale($lang);

        if (auth()->check()) {
            auth()->user()->update(['default_dashboard_language' => $lang]);
        }

        // FiMED: smart redirect - swap language prefix in URL (frontend only)
        if (config('app.launch') === 'fimed') {
            $previousUrl = url()->previous();
            $parsed = parse_url($previousUrl);
            $path = $parsed['path'] ?? '/';

            // Dashboard pages: just redirect back normally
            if (str_contains($path, '/dashboard')) {
                return redirect()->back();
            }

            // Check if previous URL was a post page
            if (str_contains($path, '/post')) {
                $postId = $request->query('post_id') ?? null;

                // Try to extract post ID from previous URL query params
                if (!$postId && isset($parsed['query'])) {
                    parse_str($parsed['query'], $queryParams);
                    $postId = $queryParams['id'] ?? null;
                }

                // Find translated post via translation_group
                if ($postId) {
                    $currentPost = Post::withoutGlobalScopes()->find($postId);
                    if ($currentPost && $currentPost->translation_group) {
                        $translatedPost = Post::withoutGlobalScopes()
                            ->where('translation_group', $currentPost->translation_group)
                            ->where('lang', $lang)
                            ->where('id', '!=', $currentPost->id)
                            ->first();

                        if ($translatedPost) {
                            return redirect()->route('main.fimed.show_post', [
                                'lang' => $lang,
                                'id' => $translatedPost->id,
                                'slug' => $translatedPost->slug,
                            ]);
                        }
                    }
                    // No translation found - go to homepage
                    return redirect()->route('main.fimed.index', ['lang' => $lang]);
                }
            }

            // For all other pages: replace lang prefix in URL path
            $segments = explode('/', trim($path, '/'));
            if (!empty($segments) && in_array($segments[0], $enabledLanguages)) {
                $segments[0] = $lang;
            } else {
                array_unshift($segments, $lang);
            }

            $newPath = '/' . implode('/', $segments);
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            return redirect($newPath . $query);
        }

        return redirect()->back();
    }
}
