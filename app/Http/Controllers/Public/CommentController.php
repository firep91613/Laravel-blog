<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Comment\StoreRequest;
use App\Services\Public\CommentService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CommentController extends Controller
{
    public function __construct(
        protected CommentService $service,
        protected ResponseFactory $response
    ) {}

    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $comment = $this->service->save($validated);

        return $this->response->json($comment, 201);
    }

    public function update(StoreRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $comment = $this->service->update($validated, $id);

        return $this->response->json($comment, 201);
    }

    public function destroy(Request $request, int $id): ?JsonResponse
    {
        if ($request->expectsJson()) {
            $canDelete = (bool) $request->input('canDelete');

            return $this->response->json($this->service->delete($id, $canDelete));
        }

        return null;
    }
}
