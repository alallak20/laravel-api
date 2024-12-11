<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use App\Traits\ApiConcerns;
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    use ApiConcerns;
    use ApiResponses;

    protected string $policyClass = UserPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(User::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        if ($this->isAble('store', User::class)) {
            return new UserResource(User::create($request->mappedAttributes()));
        }

        return $this->error("You don't have permission to create this User.", 403);
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
    public function update(UpdateUserRequest $request, $user_id)
    {
        // Patch.
        try {
            $user = User::findOrFail($user_id);

            // Policy.
            if ($this->isAble('update', $user)) {
                $user->update($request->mappedAttributes());

                return new UserResource($user);
            }

            return $this->error("You don't have permission to update this user.", 403);
        } catch (ModelNotFoundException) {
            return $this->ok('User not found', [
                'Error' => 'The provided user ID does not exist.',
            ]);
        }
    }

    public function replace(ReplaceUserRequest $request, $user_id)
    {
        // Put.
        try {
            $user = User::findOrFail($user_id);

            if ($this->isAble('replace', $user)) {
                $user->update($request->mappedAttributes());

                return new UserResource($user);
            }

            return $this->error("You don't have permission to update this user.", 403);
        } catch (ModelNotFoundException) {
            return $this->ok('User not found', [
                'Error' => 'The provided user ID does not exist.',
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            if ($this->isAble('delete', $user)) {
                $user->delete();

                return $this->ok('User deleted successfully');
            }

            return $this->error("You don't have permission to delete this user.", 403);
        } catch (ModelNotFoundException) {
            return $this->error('User not found', 404);
        }
    }
}
