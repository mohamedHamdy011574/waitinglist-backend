<?php

namespace App\Models\Helpers;

use App\Models\Category;

trait CategoryHelpers
{

    public function childs(){
        return Category::where('parent_id', $this->id)->get();
        // return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent(){
        $parent = Category::where('id', $this->id)->value('parent_id');
        return Category::find($parent);
        // return $this->hasOne(Category::class, 'parent_id');
    }


    /**
     * @return mixed
     */
    public function specialties()
    {
        return Specialty::whereHas('clinicBranches', function ($query) {
            $query->where('clinic_id', $this->id);
        })->get();
    }

}
