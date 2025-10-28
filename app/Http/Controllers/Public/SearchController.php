<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;

final class SearchController extends Controller
{
    protected const VIEW_INDEX = 'public.search.index';
    protected const PER_PAGE = 5;

    public function __invoke(Request $request, Post $post, ViewFactory $view): View
    {
        $posts = $post->search($request->get('query'))
            ->query(function ($builder) {
                $builder->join('categories', 'posts.category_id', '=', 'categories.id')
                    ->join('users', 'posts.user_id', '=', 'users.id');
            })->paginate(self::PER_PAGE);

        return $view->make(self::VIEW_INDEX, [
            'posts' => $posts
        ]);
    }
}
