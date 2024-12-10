<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use App\Traits\ApiConcerns;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorTicketsController extends Controller
{
    use ApiConcerns;
    use ApiResponses;

    protected string $policyClass = TicketPolicy::class;

    public function index(TicketFilter $filters, $author_id): AnonymousResourceCollection
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)->filter($filters)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): TicketResource|JsonResponse
    {
        try {
            $this->isAble('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttributes([
                'author' => 'user_id',
            ])));
        } catch (AuthorizationException) {
            return $this->error("You don't have permission to create this ticket.", 403);
        }
    }

    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id): TicketResource|JsonResponse
    {
        // Put.
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            $this->isAble('replace', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch (ModelNotFoundException) {
            return $this->ok('Ticket not found', [
                'Error' => 'The provided author ticket does not exist.',
            ]);
        } catch (AuthorizationException) {
            return $this->error("You don't have permission to update this ticket.", 403);
        }
    }

    public function update(UpdateTicketRequest $request, $author_id, $ticket_id): TicketResource|JsonResponse
    {
        // Patch.
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            $this->isAble('update', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        } catch (ModelNotFoundException) {
            return $this->ok('Ticket not found', [
                'Error' => 'The provided author ticket does not exist.',
            ]);
        } catch (AuthorizationException) {
            return $this->error("You don't have permission to update this ticket.", 403);
        }
    }

    public function destroy($author_id, $ticket_id): JsonResponse
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            $this->isAble('delete', $ticket);

            $ticket->delete();

            return $this->ok('Ticket deleted successfully.');
        } catch (ModelNotFoundException) {
            return $this->error('Author ticket not found', 404);
        } catch (AuthorizationException) {
            return $this->error("You don't have permission to delete this ticket.", 403);
        }
    }
}
