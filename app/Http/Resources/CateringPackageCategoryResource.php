<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CateringPackageCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'package_count' => count($this->catering_packages),
            'packages' => CateringPackageResource::collection($this->catering_packages),
        ];
    }
}
