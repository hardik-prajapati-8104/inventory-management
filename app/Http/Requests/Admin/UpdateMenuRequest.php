<?php

namespace App\Http\Requests\Admin;

use App\Models\Menu;
use Illuminate\Support\Facades\Route;

class UpdateMenuRequest extends StoreMenuRequest
{
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->route_name && ! Route::has($this->route_name)) {
                $validator->errors()->add('route_name', 'The selected route name does not exist.');
            }

            $menu = $this->route('menu');

            if ($menu && $this->filled('parent_id')) {
                $invalidIds = array_merge([$menu->id], Menu::descendantIds($menu->id));

                if (in_array((int) $this->parent_id, $invalidIds, true)) {
                    $validator->errors()->add('parent_id', 'A menu item cannot be a parent of itself or its own descendant.');
                }
            }
        });
    }
}
