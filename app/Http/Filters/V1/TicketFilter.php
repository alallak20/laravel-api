<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;

class TicketFilter extends QueryFilter {
    public function include(string $relationship) : Builder
    {
        return $this->builder->with($relationship);
    }

    public function status(string $status) : Builder
    {
        return $this->builder->whereIn('status', explode(',', $status));
    }

    public function title(string $title) : Builder
    {
        $likeStr = str_replace('*', '%', $title);
        return $this->builder->where('title', 'LIKE', $likeStr);
    }

    public function createdAt(string $dates) : Builder
    {
        $dateValues = explode(',', $dates);

        if (count($dateValues) > 1){
            return $this->builder->whereBetween('created_at', $dateValues);
        }
        return $this->builder->whereDate('created_at', $dates);
    }

    public function updatedAt(string $dates) : Builder
    {
        $dateValues = explode(',', $dates);

        if (count($dateValues) > 1){
            return $this->builder->whereBetween('updated_at', $dateValues);
        }

        return $this->builder->whereDate('updated_at', $dates);
    }
}
