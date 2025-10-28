<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use App\Policies\UserPolicy;
use App\View\Composers\AdminLogoComposer;
use App\View\Composers\DefaultUsersAvatarComposer;
use App\View\Composers\FrontSubTitleComposer;
use App\View\Composers\FrontTitleComposer;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(Gate $gate, ViewFactory $view): void
    {
        $view->composer('layouts.admin', AdminLogoComposer::class);
        $view->composer('layouts.public', FrontTitleComposer::class);
        $view->composer('layouts.public', FrontSubTitleComposer::class);
        $view->composer([
            'layouts.public',
            'layouts.admin',
            'public.posts.show',
            'public.profile.show',
            'public.profile.edit'], DefaultUsersAvatarComposer::class);

        $gate->policy(Post::class, PostPolicy::class);
        $gate->policy(User::class, UserPolicy::class);
    }
}
