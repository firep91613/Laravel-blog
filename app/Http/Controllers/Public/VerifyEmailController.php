<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Composers\ResponseComposer;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;

class VerifyEmailController extends Controller
{
    protected const VIEW_VERIFY_EMAIL = 'public.email.verify-email';
    protected const ALREADY_CONFIRMED_EMAIL_KEY = 'messages.common.email_already_confirmed';
    protected const VERIFICATION_LINK_SENT_KEY = 'messages.common.verification_link_sent';
    protected const ERROR_KEY = 'error';
    protected const STATUS_KEY = 'status';

    public function showVerificationNotice(Request $request, ViewFactory $view, ResponseComposer $composer): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $composer->redirect->to('/')
                ->with(self::ERROR_KEY, $composer->translator->get(self::ALREADY_CONFIRMED_EMAIL_KEY));
        }

        return $view->make(self::VIEW_VERIFY_EMAIL);
    }

    public function verify(EmailVerificationRequest $request, Redirector $redirect): RedirectResponse
    {
        $request->fulfill();

        return $redirect->to('/');
    }

    public function resendVerificationEmail(Request $request, ResponseComposer $composer): RedirectResponse {
        $request->user()->sendEmailVerificationNotification();

        return $composer->redirect->back()
            ->with(self::STATUS_KEY, $composer->translator->get(self::VERIFICATION_LINK_SENT_KEY));
    }
}
