<?php

namespace App\Filament\Clusters\Demo\Pages;

use App\Filament\Clusters\Demo\DemoCluster;
use App\Filament\Clusters\Demo\Enums\OrderStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class ActionsDemo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static ?string $navigationLabel = 'Actions & Modals';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = DemoCluster::class;

    protected string $view = 'filament.clusters.demo.pages.actions-demo';

    public OrderStatus $orderStatus = OrderStatus::New;

    /**
     * @var list<string>
     */
    public array $orderTags = [];

    /**
     * @return array<Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('primary_action')
                ->label('Primary')
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->action(fn () => $this->sendNotification('Primary action clicked', 'primary')),

            Action::make('success_action')
                ->label('Success')
                ->color('success')
                ->icon('heroicon-o-check')
                ->action(fn () => $this->sendNotification('Success action clicked', 'success')),

            ActionGroup::make([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->action(fn () => $this->sendNotification('Edit clicked', 'info')),
                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(fn () => $this->sendNotification('Duplicate clicked', 'info')),
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn () => $this->sendNotification('Delete clicked', 'danger')),
            ])
                ->label('More Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->color('gray')
                ->button(),
        ];
    }

    public function simpleModalAction(): Action
    {
        return Action::make('simpleModal')
            ->label('Simple Modal')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->modalHeading('Simple Modal')
            ->modalDescription('This is a simple modal with just a message and action buttons.')
            ->modalSubmitActionLabel('Confirm')
            ->action(fn () => $this->sendNotification('Simple modal confirmed', 'success'));
    }

    public function confirmationAction(): Action
    {
        return Action::make('confirmation')
            ->label('Confirmation Dialog')
            ->icon('heroicon-o-exclamation-triangle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Delete Item?')
            ->modalDescription('This action cannot be undone. Are you sure you want to proceed?')
            ->modalSubmitActionLabel('Yes, delete it')
            ->action(fn () => $this->sendNotification('Item deleted', 'danger'));
    }

    public function formModalAction(): Action
    {
        return Action::make('formModal')
            ->label('Form Modal')
            ->icon('heroicon-o-document-plus')
            ->color('primary')
            ->modalHeading('Create New Item')
            ->modalDescription('Fill in the details below to create a new item.')
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Select::make('category')
                    ->label('Category')
                    ->options([
                        'tech' => 'Technology',
                        'design' => 'Design',
                        'marketing' => 'Marketing',
                    ])
                    ->required()
                    ->native(false),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3),
            ])
            ->action(function (array $data): void {
                $this->sendNotification(
                    'Item created: '.$data['name'],
                    'success'
                );
            });
    }

    public function slideOverAction(): Action
    {
        return Action::make('slideOver')
            ->label('Slide Over')
            ->icon('heroicon-o-arrows-right-left')
            ->color('gray')
            ->slideOver()
            ->modalHeading('Slide Over Panel')
            ->modalDescription('This modal slides in from the side instead of appearing in the center.')
            ->schema([
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Textarea::make('content')
                    ->label('Content')
                    ->rows(5),
                Toggle::make('published')
                    ->label('Published'),
            ])
            ->action(function (array $data): void {
                $this->sendNotification(
                    'Slide over submitted: '.$data['title'],
                    'success'
                );
            });
    }

    public function wizardModalAction(): Action
    {
        return Action::make('wizardModal')
            ->label('Wizard Modal')
            ->icon('heroicon-o-rectangle-stack')
            ->color('warning')
            ->modalHeading('Multi-Step Wizard')
            ->steps([
                Step::make('Account')
                    ->description('Set up your account')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->required(),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(),
                    ]),
                Step::make('Profile')
                    ->description('Complete your profile')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Full Name')
                            ->required(),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel(),
                    ]),
                Step::make('Review')
                    ->description('Review and confirm')
                    ->schema([
                        Section::make('Summary')
                            ->description('Please review your information before submitting.')
                            ->schema([]),
                    ]),
            ])
            ->action(function (array $data): void {
                $this->sendNotification(
                    'Wizard completed for: '.($data['username'] ?? 'unknown'),
                    'success'
                );
            });
    }

    public function notificationSuccessAction(): Action
    {
        return Action::make('notifySuccess')
            ->label('Success')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->action(fn () => Notification::make()
                ->title('Success!')
                ->body('Your changes have been saved successfully.')
                ->success()
                ->send());
    }

    public function notificationWarningAction(): Action
    {
        return Action::make('notifyWarning')
            ->label('Warning')
            ->color('warning')
            ->icon('heroicon-o-exclamation-triangle')
            ->action(fn () => Notification::make()
                ->title('Warning')
                ->body('Please review the form before submitting.')
                ->warning()
                ->duration(5000)
                ->send());
    }

    public function notificationDangerAction(): Action
    {
        return Action::make('notifyDanger')
            ->label('Danger')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->action(fn () => Notification::make()
                ->title('Error!')
                ->body('Something went wrong. Please try again.')
                ->danger()
                ->persistent()
                ->send());
    }

    public function notificationInfoAction(): Action
    {
        return Action::make('notifyInfo')
            ->label('Info')
            ->color('info')
            ->icon('heroicon-o-information-circle')
            ->action(fn () => Notification::make()
                ->title('Did you know?')
                ->body('You can customize notifications with icons, colors, and actions.')
                ->info()
                ->actions([
                    Action::make('learn')
                        ->label('Learn More')
                        ->url('https://filamentphp.com/docs', shouldOpenInNewTab: true),
                    Action::make('dismiss')
                        ->label('Dismiss')
                        ->close(),
                ])
                ->send());
    }

    public function buttonSizesAction(): Action
    {
        return Action::make('sizes')
            ->label('Button Sizes Demo')
            ->icon('heroicon-o-arrows-pointing-out')
            ->modalHeading('Button Size Variations')
            ->modalContent(view('filament.clusters.demo.pages.partials.button-sizes'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function workflowActionGroup(): ActionGroup
    {
        return ActionGroup::make([
            Action::make('process')
                ->label('Process')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn (): bool => $this->orderStatus === OrderStatus::New)
                ->action(function (): void {
                    $this->orderStatus = OrderStatus::Processing;
                    $this->sendNotification('Order is now processing', 'success');
                }),

            Action::make('ship')
                ->label('Ship')
                ->icon('heroicon-o-truck')
                ->color('success')
                ->visible(fn (): bool => $this->orderStatus === OrderStatus::Processing)
                ->slideOver()
                ->modalSubmitActionLabel('Ship')
                ->schema([
                    Textarea::make('notes')
                        ->label('Shipping notes')
                        ->rows(3),
                ])
                ->extraModalFooterActions([
                    Action::make('ship_and_notify')
                        ->label('Ship & notify customer')
                        ->color('info')
                        ->action(function (): void {
                            $this->orderStatus = OrderStatus::Shipped;
                            $this->sendNotification('Order shipped & customer notified', 'success');
                        }),
                ])
                ->action(function (): void {
                    $this->orderStatus = OrderStatus::Shipped;
                    $this->sendNotification('Order shipped', 'success');
                }),

            Action::make('deliver')
                ->label('Deliver')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn (): bool => $this->orderStatus === OrderStatus::Shipped)
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->orderStatus = OrderStatus::Delivered;
                    $this->sendNotification('Order marked as delivered', 'success');
                }),

            Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => ! in_array($this->orderStatus, [OrderStatus::Delivered, OrderStatus::Cancelled], true))
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->orderStatus = OrderStatus::Cancelled;
                    $this->sendNotification('Order cancelled', 'danger');
                }),

            Action::make('reset')
                ->label('Reset to new')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->visible(fn (): bool => $this->orderStatus !== OrderStatus::New)
                ->action(function (): void {
                    $this->orderStatus = OrderStatus::New;
                    $this->sendNotification('Workflow reset', 'info');
                }),
        ])
            ->label('Workflow')
            ->icon('heroicon-o-rocket-launch')
            ->color('primary')
            ->button();
    }

    public function createOptionFormAction(): Action
    {
        return Action::make('addTag')
            ->label('Add tag (with inline create)')
            ->icon('heroicon-o-tag')
            ->color('info')
            ->schema([
                Select::make('tag')
                    ->label('Tag')
                    ->options(fn (): array => array_combine($this->orderTags, $this->orderTags))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Tag name')
                            ->required()
                            ->maxLength(40),
                    ])
                    ->createOptionUsing(function (array $data): string {
                        $tag = $data['name'];

                        if (! in_array($tag, $this->orderTags, true)) {
                            $this->orderTags[] = $tag;
                        }

                        return $tag;
                    }),
            ])
            ->action(fn (array $data) => $this->sendNotification('Applied tag: '.$data['tag'], 'success'));
    }

    protected function sendNotification(string $message, string $type): void
    {
        $notification = Notification::make()->title($message);

        match ($type) {
            'success' => $notification->success(),
            'danger' => $notification->danger(),
            'warning' => $notification->warning(),
            'info' => $notification->info(),
            default => $notification->info(),
        };

        $notification->send();
    }

    /**
     * @return array<string, Schema>
     */
    protected function getForms(): array
    {
        return [];
    }
}
