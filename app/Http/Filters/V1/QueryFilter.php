<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Void_;

abstract class QueryFilter {
    protected Builder $builder;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    // Called by apply method when filters needed.
    protected function filter(Array $filtersNeeded): Builder
    {
        foreach($filtersNeeded as $key => $value){
            if(method_exists($this, $key)){
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach($this->request->all() as $key => $value){
            if(method_exists($this, $key)){
                $this->$key($value);
            }
        }

        return $builder;
    }
}
