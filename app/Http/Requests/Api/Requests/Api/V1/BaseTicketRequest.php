<?php

namespace App\Http\Requests\Api\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = array_merge([
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'user_id',
        ], $otherAttributes);

        $attributeToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributeToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributeToUpdate;
    }

    public function messages(): array
    {
        return [
            'data.attributes.status.in' => 'The status must be one of A, C, H, X',
            'data.relationships.author.data.id.prohibited' => 'The author ID can NOT be updated (Prohibited)',
            //            'data.relationships.author.data.id.size' => 'The user ID and the signedIn user MUST match',
        ];
    }
}
