<?php

namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization()
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                // If user is Root, they can see EVERYTHING (no scope applied) unless they manually want to filter?
                // User requirement: "Root ... puede ver el contenido de todos...". 
                // "Root en la sección de zonas podrá ver las zonas de todas las organizaciones, por lo que deberemos implementar un filtro".
                // So default behavior for Root: NO SCOPE. For others: SCOPE.

                if (!in_array($user->role, ['root', 'admin_ti']) && $user->organization_id) {
                    $builder->where('organization_id', $user->organization_id);
                }
            }
        });

        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (!$model->organization_id && $user->organization_id) {
                    $model->organization_id = $user->organization_id;
                }
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
