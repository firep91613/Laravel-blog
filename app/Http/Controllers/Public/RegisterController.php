<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Register\StoreRequest;
use App\Services\Public\RegisterService;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;

final class RegisterController extends Controller
{
    protected const ACTION_VERIFY = 'verification.notice';
    protected const VIEW_CREATE = 'public.register.showForm';
    protected const REGISTRATION_SUCCESS_KEY = 'messages.common.registration_success';
    protected const SUCCESS_KEY = 'success';

    public function showForm(ViewFactory $view): View
    {
        return $view->make(self::VIEW_CREATE);
    }

    public function register(StoreRequest $request, AuthFactory $auth, RegisterService $service, ResponseComposer $composer): RedirectResponse
    {
        $validated = $request->validated();
        $user = $service->create($validated);
        $auth->guard()->login($user);

        return $composer->redirect->route(self::ACTION_VERIFY)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::REGISTRATION_SUCCESS_KEY));
    }
}
