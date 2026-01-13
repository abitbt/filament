<x-filament::page>
    {{-- Modal Actions Section --}}
    <x-filament::section>
        <x-slot name="heading">
            Modal Variations
        </x-slot>
        <x-slot name="description">
            Different types of modal dialogs and overlays
        </x-slot>

        <div class="flex flex-wrap gap-3">
            {{ $this->simpleModalAction }}
            {{ $this->confirmationAction }}
            {{ $this->formModalAction }}
            {{ $this->slideOverAction }}
            {{ $this->wizardModalAction }}
        </div>
    </x-filament::section>

    {{-- Notifications Section --}}
    <x-filament::section>
        <x-slot name="heading">
            Notifications
        </x-slot>
        <x-slot name="description">
            Toast notifications with different styles and behaviors
        </x-slot>

        <div class="flex flex-wrap gap-3">
            {{ $this->notificationSuccessAction }}
            {{ $this->notificationWarningAction }}
            {{ $this->notificationDangerAction }}
            {{ $this->notificationInfoAction }}
        </div>
    </x-filament::section>

    {{-- Button Styles Section --}}
    <x-filament::section>
        <x-slot name="heading">
            Button Styles
        </x-slot>
        <x-slot name="description">
            Various button colors, sizes, and variants
        </x-slot>

        <div class="space-y-6">
            {{-- Colors --}}
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Colors</h4>
                <div class="flex flex-wrap gap-3">
                    <x-filament::button color="primary">Primary</x-filament::button>
                    <x-filament::button color="success">Success</x-filament::button>
                    <x-filament::button color="warning">Warning</x-filament::button>
                    <x-filament::button color="danger">Danger</x-filament::button>
                    <x-filament::button color="info">Info</x-filament::button>
                    <x-filament::button color="gray">Gray</x-filament::button>
                </div>
            </div>

            {{-- Sizes --}}
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Sizes</h4>
                <div class="flex flex-wrap items-center gap-3">
                    <x-filament::button size="xs">Extra Small</x-filament::button>
                    <x-filament::button size="sm">Small</x-filament::button>
                    <x-filament::button size="md">Medium</x-filament::button>
                    <x-filament::button size="lg">Large</x-filament::button>
                    <x-filament::button size="xl">Extra Large</x-filament::button>
                </div>
            </div>

            {{-- Outlined --}}
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Outlined</h4>
                <div class="flex flex-wrap gap-3">
                    <x-filament::button color="primary" outlined>Primary</x-filament::button>
                    <x-filament::button color="success" outlined>Success</x-filament::button>
                    <x-filament::button color="warning" outlined>Warning</x-filament::button>
                    <x-filament::button color="danger" outlined>Danger</x-filament::button>
                </div>
            </div>

            {{-- With Icons --}}
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">With Icons</h4>
                <div class="flex flex-wrap gap-3">
                    <x-filament::button icon="heroicon-o-plus">Create</x-filament::button>
                    <x-filament::button icon="heroicon-o-pencil" color="warning">Edit</x-filament::button>
                    <x-filament::button icon="heroicon-o-trash" color="danger">Delete</x-filament::button>
                    <x-filament::button icon="heroicon-o-arrow-down-tray"
                        icon-position="after">Download</x-filament::button>
                </div>
            </div>

            {{-- Icon Only --}}
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Icon Only</h4>
                <div class="flex flex-wrap gap-3">
                    <x-filament::button icon="heroicon-o-plus" color="primary" label="Add" />
                    <x-filament::button icon="heroicon-o-pencil" color="warning" label="Edit" />
                    <x-filament::button icon="heroicon-o-trash" color="danger" label="Delete" />
                    <x-filament::button icon="heroicon-o-cog-6-tooth" color="gray" label="Settings" />
                </div>
            </div>

            {{-- Button Groups --}}
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Button Groups</h4>
                <x-filament::button.group>
                    <x-filament::button icon="heroicon-o-chevron-left" color="gray" />
                    <x-filament::button color="gray">1</x-filament::button>
                    <x-filament::button color="gray">2</x-filament::button>
                    <x-filament::button color="gray">3</x-filament::button>
                    <x-filament::button icon="heroicon-o-chevron-right" color="gray" />
                </x-filament::button.group>
            </div>

            {{-- States --}}
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">States</h4>
                <div class="flex flex-wrap gap-3">
                    <x-filament::button>Normal</x-filament::button>
                    <x-filament::button disabled>Disabled</x-filament::button>
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Links Section --}}
    <x-filament::section>
        <x-slot name="heading">
            Links
        </x-slot>
        <x-slot name="description">
            Link components with icons
        </x-slot>

        <div class="flex flex-wrap gap-6">
            <x-filament::link href="#">Default Link</x-filament::link>
            <x-filament::link href="#" color="success">Success Link</x-filament::link>
            <x-filament::link href="#" color="danger">Danger Link</x-filament::link>
            <x-filament::link href="#" icon="heroicon-o-arrow-top-right-on-square">External
                Link</x-filament::link>
        </div>
    </x-filament::section>

    {{-- Badges Section --}}
    <x-filament::section>
        <x-slot name="heading">
            Badges
        </x-slot>
        <x-slot name="description">
            Status indicators and labels
        </x-slot>

        <div class="space-y-4">
            <div class="flex flex-wrap gap-3">
                <x-filament::badge>Default</x-filament::badge>
                <x-filament::badge color="success">Success</x-filament::badge>
                <x-filament::badge color="warning">Warning</x-filament::badge>
                <x-filament::badge color="danger">Danger</x-filament::badge>
                <x-filament::badge color="info">Info</x-filament::badge>
                <x-filament::badge color="gray">Gray</x-filament::badge>
            </div>

            <div class="flex flex-wrap gap-3">
                <x-filament::badge icon="heroicon-o-check-circle" color="success">Approved</x-filament::badge>
                <x-filament::badge icon="heroicon-o-clock" color="warning">Pending</x-filament::badge>
                <x-filament::badge icon="heroicon-o-x-circle" color="danger">Rejected</x-filament::badge>
            </div>
        </div>
    </x-filament::section>
</x-filament::page>
