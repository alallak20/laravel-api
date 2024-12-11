<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait ApiConcerns
{
    use AuthorizesRequests;

    //    protected $policyClass;

    protected function getPolicyClass(): ?string
    {
        return $this->policyClass ?? null;
    }

    // Todo::
    // In new laravel versions (>10) why should use (authorize) in request classes.
    // (https://laracasts.com/series/laravel-api-master-class/episodes/21) comments.
    public function isAble($ability, $targetModel): bool
    {
        // We can use getPolicyClass() or just pass $this->policyClass
        // As Laravel will ook in the trait & if not exist it will look in the class that use the trait !.
        try {
            $this->authorize($ability, [$targetModel, $this->getPolicyClass()]);

            return true;
        } catch (AuthorizationException) {
            return false;
        }
    }

    public function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (! isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $includeValues);
    }
}
