<?php declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Redirector;
use Illuminate\Log\Logger;

class DbException extends \Exception
{
    protected ResponseFactory $response;
    protected Redirector $redirect;

    public function __construct(protected string $userMessage)
    {
        parent::__construct("Хьюстон, у нас проблемы с базой!");
    }

    public function report(Logger $logger, ResponseFactory $response, Redirector $redirect): void
    {
        $this->response = $response;
        $this->redirect = $redirect;

        $logger->error($this->userMessage);
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return $this->response->json([
                'errors' => true,
                'db_exception' => true,
                'message' => $this->message
            ], 400);
        }

        return $this->redirect->back()->with('error', $this->message);
    }
}
