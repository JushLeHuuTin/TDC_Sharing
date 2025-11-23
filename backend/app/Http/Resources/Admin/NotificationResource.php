<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'recipient_name' => $this->whenLoaded('user', $this->user->full_name ?? 'Người nhận không xác định'),
            'content'        => Str::limit($this->content, 80, '...'), // Cắt ngắn nội dung
            'type'           => $this->type,
            'is_read'        => (bool) $this->is_read,
            'created_at'     => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '--',
        ];
    }
}