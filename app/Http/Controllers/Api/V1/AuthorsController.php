<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Traits\ApiConcerns;

class AuthorsController extends Controller
{
    use ApiConcerns;

    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filters)
    {
        // Only authors thanks to join & distinct .
        return UserResource::collection(
            User::select('users.*')
                ->join('tickets', 'users.id', '=', 'tickets.user_id')
                ->filter($filters)
                ->distinct()
                ->paginate()
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(AuthorFilter $filters, $author_id)
    {
        return new UserResource(
            User::where('id', $author_id)
                ->filter($filters)
                ->firstOrFail());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
    }
}
