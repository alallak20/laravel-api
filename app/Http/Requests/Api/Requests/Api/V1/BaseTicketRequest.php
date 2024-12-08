<?php

namespace App\Http\Requests\Api\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{
    public function mappedAttributes(?int $user_id = null): array
    {
        $attributeMap = [
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'user_id',
        ];

        $attributeToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributeToUpdate[$attribute] = $this->input($key);
            }
        }

        if (isset($user_id)) {
            $attributeToUpdate['user_id'] = $user_id;
        }

        return $attributeToUpdate;
    }

    public function messages(): array
    {
        return [
            'data.attributes.status.in' => 'The status must be one of A, C, H, X',
        ];
    }
}
