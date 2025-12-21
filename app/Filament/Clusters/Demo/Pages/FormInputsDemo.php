<?php

namespace App\Filament\Clusters\Demo\Pages;

use App\Filament\Clusters\Demo\DemoCluster;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormInputsDemo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'Form Inputs';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = DemoCluster::class;

    protected string $view = 'filament.clusters.demo.pages.form-inputs-demo';

    /**
     * @var array<string, mixed>
     */
    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'text_input' => 'Hello World',
            'email' => 'demo@example.com',
            'password' => 'secret123',
            'numeric' => 42,
            'phone' => '+1 (555) 123-4567',
            'url' => 'https://filamentphp.com',
            'textarea' => "This is a multi-line\ntext area demo.",
            'select' => 'option_2',
            'multi_select' => ['option_1', 'option_3'],
            'searchable_select' => 'laravel',
            'checkbox' => true,
            'toggle' => true,
            'toggle_buttons' => 'draft',
            'radio' => 'option_b',
            'checkbox_list' => ['feature_1', 'feature_3'],
            'date' => now()->format('Y-m-d'),
            'datetime' => now()->format('Y-m-d H:i:s'),
            'time' => '14:30',
            'color' => '#6366f1',
            'tags' => ['Laravel', 'Filament', 'PHP'],
            'key_value' => [
                'name' => 'John Doe',
                'role' => 'Developer',
                'team' => 'Backend',
            ],
            'rich_editor' => '<p>This is <strong>rich text</strong> with <em>formatting</em>.</p>',
            'markdown' => "# Heading\n\nThis is **bold** and *italic* text.",
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Text Inputs')
                    ->description('Various text input variations')
                    ->columns(2)
                    ->schema([
                        TextInput::make('text_input')
                            ->label('Text Input')
                            ->placeholder('Enter text...')
                            ->helperText('A basic text input field'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->prefixIcon('heroicon-o-envelope'),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable(),

                        TextInput::make('numeric')
                            ->label('Numeric')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(1)
                            ->suffix('units'),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->prefixIcon('heroicon-o-phone'),

                        TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->suffixIcon('heroicon-o-globe-alt'),

                        Textarea::make('textarea')
                            ->label('Textarea')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Selection Inputs')
                    ->description('Dropdowns, checkboxes, and toggles')
                    ->columns(2)
                    ->schema([
                        Select::make('select')
                            ->label('Select')
                            ->options([
                                'option_1' => 'Option 1',
                                'option_2' => 'Option 2',
                                'option_3' => 'Option 3',
                            ])
                            ->native(false),

                        Select::make('multi_select')
                            ->label('Multi Select')
                            ->multiple()
                            ->options([
                                'option_1' => 'Option 1',
                                'option_2' => 'Option 2',
                                'option_3' => 'Option 3',
                                'option_4' => 'Option 4',
                            ])
                            ->native(false),

                        Select::make('searchable_select')
                            ->label('Searchable Select')
                            ->searchable()
                            ->options([
                                'laravel' => 'Laravel',
                                'filament' => 'Filament',
                                'livewire' => 'Livewire',
                                'alpine' => 'Alpine.js',
                                'tailwind' => 'Tailwind CSS',
                            ])
                            ->native(false),

                        Select::make('grouped_select')
                            ->label('Grouped Select')
                            ->options([
                                'Backend' => [
                                    'php' => 'PHP',
                                    'python' => 'Python',
                                    'ruby' => 'Ruby',
                                ],
                                'Frontend' => [
                                    'javascript' => 'JavaScript',
                                    'typescript' => 'TypeScript',
                                ],
                            ])
                            ->native(false),

                        Checkbox::make('checkbox')
                            ->label('Checkbox')
                            ->helperText('Check this box to agree'),

                        Toggle::make('toggle')
                            ->label('Toggle')
                            ->onColor('success')
                            ->offColor('danger'),

                        ToggleButtons::make('toggle_buttons')
                            ->label('Toggle Buttons')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->icons([
                                'draft' => 'heroicon-o-pencil',
                                'published' => 'heroicon-o-check-circle',
                                'archived' => 'heroicon-o-archive-box',
                            ])
                            ->colors([
                                'draft' => 'warning',
                                'published' => 'success',
                                'archived' => 'danger',
                            ])
                            ->inline(),

                        Radio::make('radio')
                            ->label('Radio Buttons')
                            ->options([
                                'option_a' => 'Option A',
                                'option_b' => 'Option B',
                                'option_c' => 'Option C',
                            ])
                            ->descriptions([
                                'option_a' => 'First option description',
                                'option_b' => 'Second option description',
                                'option_c' => 'Third option description',
                            ]),

                        CheckboxList::make('checkbox_list')
                            ->label('Checkbox List')
                            ->options([
                                'feature_1' => 'Feature 1',
                                'feature_2' => 'Feature 2',
                                'feature_3' => 'Feature 3',
                                'feature_4' => 'Feature 4',
                            ])
                            ->columns(2),
                    ]),

                Section::make('Date & Time')
                    ->description('Date and time pickers')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('date')
                            ->label('Date Picker')
                            ->native(false),

                        DateTimePicker::make('datetime')
                            ->label('DateTime Picker')
                            ->native(false),

                        TimePicker::make('time')
                            ->label('Time Picker')
                            ->native(false),
                    ]),

                Section::make('Specialized Inputs')
                    ->description('Color, tags, key-value pairs, and file uploads')
                    ->columns(2)
                    ->schema([
                        ColorPicker::make('color')
                            ->label('Color Picker'),

                        TagsInput::make('tags')
                            ->label('Tags Input')
                            ->placeholder('Add a tag...')
                            ->suggestions([
                                'PHP',
                                'Laravel',
                                'Filament',
                                'Livewire',
                                'Alpine.js',
                            ]),

                        KeyValue::make('key_value')
                            ->label('Key-Value')
                            ->keyLabel('Property')
                            ->valueLabel('Value')
                            ->columnSpanFull(),

                        FileUpload::make('file')
                            ->label('File Upload')
                            ->image()
                            ->imageEditor()
                            ->directory('demo-uploads'),

                        FileUpload::make('multiple_files')
                            ->label('Multiple Files')
                            ->multiple()
                            ->directory('demo-uploads'),
                    ]),

                Section::make('Rich Content Editors')
                    ->description('Rich text and markdown editors')
                    ->schema([
                        RichEditor::make('rich_editor')
                            ->label('Rich Editor')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'orderedList',
                                'bulletList',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                            ]),

                        MarkdownEditor::make('markdown')
                            ->label('Markdown Editor'),
                    ]),
            ]);
    }
}
