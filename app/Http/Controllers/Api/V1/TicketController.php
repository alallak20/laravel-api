<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\V1\TicketPolicy;
use App\Traits\ApiConcerns;
use App\Traits\ApiResponses;
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

    public function show(Ticket $ticket): TicketResource
    {
        return new TicketResource($ticket);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): TicketResource|JsonResponse
    {
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttributes()));
        }

        return $this->error("You don't have permission to create this ticket.", 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): TicketResource|JsonResponse
    {
        // Patch.
        // Policy.
        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        }

        return $this->error("You don't have permission to update this ticket.", 403);
    }

    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        // Put.
        if ($this->isAble('replace', $ticket)) {
            $user_id = $request->input('data.relationships.author.data.id');

            $user = User::findOrFail($user_id);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        }

        return $this->error("You don't have permission to update this ticket.", 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        if ($this->isAble('delete', $ticket)) {
            $ticket->delete();

            return $this->ok('Ticket deleted successfully');
        }

        return $this->error("You don't have permission to delete this ticket.", 403);

    }
}
