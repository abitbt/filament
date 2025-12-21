<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\RoleResource\Schemas\RoleForm;
use App\Models\Role;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->getAttribute('name');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Role $record */
        $record = $this->record;

        // Get current permission IDs
        $permissionIds = $record->permissions()->pluck('id')->all();

        // Convert to access levels and merge into form data
        $accessLevels = RoleForm::permissionsToAccessLevels($permissionIds);

        return array_merge($data, $accessLevels);
    }

    protected function afterSave(): void
    {
        // Sync permissions based on access levels
        $permissionIds = RoleForm::accessLevelsToPermissionIds($this->data ?? []);

        /** @var Role $record */
        $record = $this->record;
        $record->permissions()->sync($permissionIds);
    }
}
