<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Auth\ResetPasswordEmailStore;
use App\Composers\ResponseComposer;
use App\Http\Requests\Public\Auth\ResetPasswordStore;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;

class ResetPasswordController extends Controller
{
    protected const ACTION_SHOW_FORM = 'public.auth.showForm';
    protected const VIEW_FORGOT_PASSWORD = 'public.auth.forgot-password';
    protected const VIEW_RESET_PASSWORD = 'public.auth.reset-password';
    protected const SUCCESS_KEY = 'success';
    protected const EMAIL_ERROR_KEY = 'email';

    public function showEmailForm(ViewFactory $view): View
    {
        return $view->make(self::VIEW_FORGOT_PASSWORD);
    }

    public function emailStore(ResetPasswordEmailStore $request, PasswordBroker $broker, ResponseComposer $composer): RedirectResponse
    {
        $status = $broker->sendResetLink($request->only('email'));

        if ($status === PasswordBroker::RESET_LINK_SENT) {
            return $composer->redirect->back()
                ->with(self::SUCCESS_KEY, $composer->translator->get($status));
        } else {
            return $composer->redirect->back()
                ->withErrors(self::EMAIL_ERROR_KEY, $composer->translator->get($status));
        }
    }

    public function showResetForm(string $token, ViewFactory $view): View
    {
        return $view->make(self::VIEW_RESET_PASSWORD, ['token' => $token]);
    }

    public function resetStore(ResetPasswordStore $request, PasswordBroker $broker, Dispatcher $dispatcher, ResponseComposer $composer): RedirectResponse
    {
        $status = $broker->reset($request->validated(), function (User $user, string $password) use($dispatcher) {
            $user->forceFill(['password' => $password])->setRememberToken(Str::random(60));
            $user->save();
            $dispatcher->dispatch(new PasswordReset($user));
        });

        if ($status === PasswordBroker::PASSWORD_RESET) {
            return $composer->redirect->route(self::ACTION_SHOW_FORM)
                ->with(self::SUCCESS_KEY, $composer->translator->get($status));
        } else {
            return $composer->redirect->back()
                ->withErrors(self::EMAIL_ERROR_KEY, $composer->translator->get($status));
        }
    }
}
