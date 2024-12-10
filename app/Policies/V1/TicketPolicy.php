<?php

namespace App\Policies\V1;

use App\Models\Ticket;
use App\Models\User;
use App\Permission\V1\Abilities;

class TicketPolicy
{
    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateUser)
            || $user->tokenCan(Abilities::CreateOwnTicket);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::UpdateOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return $user->tokenCan(Abilities::UpdateTicket);
    }

    public function replace(User $user): bool
    {
        return $user->tokenCan(Abilities::ReplaceTicket);
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::DeleteOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return $user->tokenCan(Abilities::DeleteTicket);
    }
}
