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
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    use ApiResponses;

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

        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $request->input('data.relationships.author.data.id'),
        ];

        return new TicketResource(Ticket::create($model));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        // Patch.
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

        try {
            $user = User::findOrFail($user_id);
        } catch (ModelNotFoundException) {
            return $this->error('User not found', 404);
        }

        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $request->input('data.relationships.author.data.id'),
        ];

        $ticket->update($model);

        return new TicketResource($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id): JsonResponse
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();

            return $this->ok('Ticket deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->error('Ticket not found', 404);
        }
    }
}
