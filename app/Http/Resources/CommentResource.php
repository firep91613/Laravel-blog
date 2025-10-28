<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'author' => $this->user->name,
            'avatar' => $this->user->avatar,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
