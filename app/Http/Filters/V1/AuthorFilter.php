<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;

class AuthorFilter extends QueryFilter
{
    protected array $sortable = [
        'name',
        'email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ];

    public function include(string $relationship): Builder
    {
        return $this->builder->with($relationship);
    }

    public function id(string $id): Builder
    {
        return $this->builder->whereIn('id', explode(',', $id));
    }

    public function email(string $email): Builder
    {
        $likeStr = str_replace('*', '%', $email);

        return $this->builder->where('email', 'LIKE', $likeStr);
    }

    public function name(string $name): Builder
    {
        $likeStr = str_replace('*', '%', $name);

        return $this->builder->where('name', 'LIKE', $likeStr);
    }

    public function createdAt(string $dates): Builder
    {
        $dateValues = explode(',', $dates);

        if (count($dateValues) > 1) {
            return $this->builder->whereBetween('created_at', $dateValues);
        }

        return $this->builder->whereDate('created_at', $dates);
    }

    public function updatedAt(string $dates): Builder
    {
        $dateValues = explode(',', $dates);

        if (count($dateValues) > 1) {
            return $this->builder->whereBetween('updated_at', $dateValues);
        }

        return $this->builder->whereDate('updated_at', $dates);
    }
}
