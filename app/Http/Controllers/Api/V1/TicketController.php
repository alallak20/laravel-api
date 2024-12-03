<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Traits\ApiConcerns;

class TicketController extends Controller
{
    use ApiConcerns;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->include('author')){
            return TicketResource::collection(Ticket::with('user')->paginate());
        }

        return TicketResource::collection(Ticket::paginate());
//        return Ticket::all();
    }

    public function show(Ticket $ticket)
    {
        if( $this->include('author') ){
            return new TicketResource($ticket->load('user'));
        }

        return new TicketResource($ticket);
//        return Ticket::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}