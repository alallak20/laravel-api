<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorTicketsController extends Controller
{
    use ApiResponses;

    public function index($author_id, TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)->filter($filters)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($author_id, StoreTicketRequest $request): TicketResource
    {
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $author_id,
        ];

        return new TicketResource(Ticket::create($model));
    }

    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id): TicketResource|JsonResponse
    {
        // Put.
        try {
            $ticket = Ticket::findOrFail($ticket_id);
        } catch (ModelNotFoundException) {
            return $this->ok('Ticket not found', [
                'error' => 'The provided ticket ID does not exist.',
            ]);
        }

        try {
            $user = User::findOrFail($author_id);
        } catch (ModelNotFoundException) {
            return $this->error('User not found', 404);
        }

        if ($ticket->user_id == $author_id) {
            $model = [
                'title' => $request->input('data.attributes.title'),
                'description' => $request->input('data.attributes.description'),
                'status' => $request->input('data.attributes.status'),
                'user_id' => $request->input('data.relationships.author.data.id'),
            ];

            $ticket->update($model);

            return new TicketResource($ticket);
        }

        return $this->error();
    }

    public function destroy($author_id, $ticket_id): JsonResponse
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if ($ticket->user_id == $author_id) {
                $ticket->delete();

                return $this->ok('Ticket deleted successfully');
            }

            return $this->error('Ticket not found', 404);
        } catch (ModelNotFoundException) {
            return $this->error('Ticket not found', 404);
        }
    }
}
