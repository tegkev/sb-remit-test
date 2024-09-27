<?php

namespace App\Http\Resources;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Blog
 */
class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'thumbnail_url' => $this->thumbnail_url,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
