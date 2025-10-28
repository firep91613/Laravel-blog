<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AuthenticateLoginRequest;
use App\Composers\ResponseComposer;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\Factory as ViewFactory;

final class LoginController extends Controller
{
    protected const ACTION_LOGIN = 'admin.login';
    protected const ACTION_DASHBOARD = 'admin.dashboard';
    protected const VIEW_LOGIN = 'admin.login';
    protected const INVALID_CREDENTIALS_KEY = 'messages.invalid.credentials';
    protected const ERROR_KEY = 'error';

    public function login(ViewFactory $view): View
    {
        return $view->make(self::VIEW_LOGIN);
    }

    public function logout(Request $request, AuthFactory $auth, Redirector $redirect): RedirectResponse
    {
        $auth->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $redirect->route(self::ACTION_LOGIN);
    }

    public function authenticate(AuthenticateLoginRequest $request, AuthFactory $auth, ResponseComposer $composer): RedirectResponse
    {
        if ($auth->guard()->attempt($request->validated())) {
            $request->session()->regenerate();

            return $composer->redirect->route(self::ACTION_DASHBOARD);
        }

        return $composer->redirect->back()
            ->with(self::ERROR_KEY, $composer->translator->get(self::INVALID_CREDENTIALS_KEY));
    }
}
