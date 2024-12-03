<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
//    public static $wrap = 'ticket';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'type' => 'Ticket',
            'id' => $this->id,
            'attributes' => [
              'title' => $this->title,
              'description' => $this->when(
                  $request->RouteIs('tickets.show'),
                  $this->description,
              ),
              'status' => $this->status,
              'createdAt' => $this->created_at,
              'updatedAt' => $this->updated_at,
            ],
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'User',
                        'id' => $this->user_id,
                    ],
                    'links' => [
                        'self' => route('users.show', ['user' => $this->user_id])
                    ],
                ]
            ],
            'includes' =>
                new UserResource($this->whenLoaded('user')),
            'links' => [
              'self' => route('tickets.show', ['ticket' => $this->id]),
            ],
        ];
    }
}
