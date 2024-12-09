<?php

namespace App\Traits;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait ApiConcerns
{
    use AuthorizesRequests;

    //    protected $policyClass;

    protected function getPolicyClass(): ?string
    {
        return $this->policyClass ?? null;
    }

    public function isAble($ability, $targetModel)
    {
        // We can use getPolicyClass() or just pass $this->policyClass
        // As Laravel will ook in the trait & if not exist it will look in the class that use the trait !.
        return $this->authorize($ability, [$targetModel, $this->getPolicyClass()]);
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
