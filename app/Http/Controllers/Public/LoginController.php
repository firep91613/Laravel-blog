<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Auth\AuthenticateLoginRequest;
use App\Composers\ResponseComposer;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\Factory as ViewFactory;

final class LoginController extends Controller
{
    protected const ACTION_LOGIN = 'public.auth.showForm';
    protected const VIEW_LOGIN = 'public.auth.showForm';
    protected const INVALID_CREDENTIALS_KEY = 'messages.invalid.credentials';
    protected const ERROR_KEY = 'error';

    public function showForm(ViewFactory $view): View
    {
        return $view->make(self::VIEW_LOGIN);
    }

    public function authenticate(AuthenticateLoginRequest $request, AuthFactory $auth, ResponseComposer $composer): RedirectResponse
    {
        $credentials = $request->validated();

        if ($auth->guard()->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $redirectTo = $request->input('redirect_to');

            return $redirectTo ? $composer->redirect->to($redirectTo) : $composer->redirect->intended();
        }

        return $composer->redirect->back()
            ->with(self::ERROR_KEY, $composer->translator->get(self::INVALID_CREDENTIALS_KEY));
    }

    public function logout(Request $request, AuthFactory $auth, Redirector $redirect): RedirectResponse
    {
        $auth->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $redirect->route(self::ACTION_LOGIN);
    }
}
