<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\RoleResource\Schemas\RoleForm;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        // Sync permissions based on access levels
        $permissionIds = RoleForm::accessLevelsToPermissionIds($this->data ?? []);

        /** @var \App\Models\Role $record */
        $record = $this->record;
        $record->permissions()->sync($permissionIds);
    }
}
