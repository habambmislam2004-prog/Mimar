<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\Subcategory\SubcategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'icon' => $this->icon,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'subcategories' => SubcategoryResource::collection(
                $this->whenLoaded('subcategories')
            ),
            'created_at' => $this->created_at,
        ];
    }
}
