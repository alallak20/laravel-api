<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\V1\TicketPolicy;
use App\Traits\ApiConcerns;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    use ApiConcerns;
    use ApiResponses;

    protected string $policyClass = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(
            Ticket::filter($filters)
                ->orderBy('created_at', 'asc')
                ->paginate()
        );
    }

    public function show($ticket_id): TicketResource|JsonResponse
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            return new TicketResource($ticket);
        } catch (ModelNotFoundException) {
            return $this->error('Ticket not found', 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): TicketResource|JsonResponse
    {
        try {
            $user = User::findOrFail($request->input('data.relationships.author.data.id'));
        } catch (ModelNotFoundException) {
            return $this->ok('User not found', [
                'error' => 'The provided user ID does not exist.',
            ]);
        }

        $this->isAble('create', null);

        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $ticket_id): TicketResource|JsonResponse
    {
        // Patch.
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            // Policy.
            $this->isAble('update', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException) {
            return $this->ok('Ticket not found', [
                'error' => 'The provided ticket ID does not exist.',
            ]);
        } catch (AuthorizationException) {
            return $this->error("You don't have permission to update this ticket.", 403);
        }
    }

    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {
        // Put.
        try {
            $ticket = Ticket::findOrFail($ticket_id);
        } catch (ModelNotFoundException) {
            return $this->ok('Ticket not found', [
                'error' => 'The provided ticket ID does not exist.',
            ]);
        }

        $user_id = $request->input('data.relationships.author.data.id');

        $this->isAble('replace', $ticket);

        try {
            $user = User::findOrFail($user_id);
        } catch (ModelNotFoundException) {
            return $this->error('User not found', 404);
        }

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id): JsonResponse
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

        } catch (ModelNotFoundException) {
            return $this->error('Ticket not found', 404);
        }

        $this->isAble('delete', $ticket);

        $ticket->delete();

        return $this->ok('Ticket deleted successfully');
    }
}
