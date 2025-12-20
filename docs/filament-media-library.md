# Filament Spatie Laravel Media Library Plugin

This documentation covers the Filament integration with [Spatie's Laravel Media Library](https://spatie.be/docs/laravel-medialibrary) for managing file uploads, images, and media collections.

## Installation

The package is already installed. If you need to reinstall:

```bash
composer require filament/spatie-laravel-media-library-plugin
```

### Publish Migrations

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan migrate
```

## Preparing Your Model

Your Eloquent model must implement `HasMedia` and use the `InteractsWithMedia` trait:

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
}
```

### Defining Media Collections

Optionally define collections to organize media:

```php
public function registerMediaCollections(): void
{
    $this->addMediaCollection('images');

    $this->addMediaCollection('avatar')
        ->singleFile(); // Only one file allowed
}
```

### Defining Conversions

Create image variants automatically:

```php
use Spatie\MediaLibrary\MediaCollections\Models\Media;

public function registerMediaConversions(?Media $media = null): void
{
    $this->addMediaConversion('thumb')
        ->width(150)
        ->height(150)
        ->sharpen(10);

    $this->addMediaConversion('preview')
        ->width(800)
        ->height(600);
}
```

---

## Form Component: SpatieMediaLibraryFileUpload

### Basic Usage

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('avatar')
```

The component automatically loads and saves uploads to your model when used in Filament resources.

### Collections

Group files into categories:

```php
SpatieMediaLibraryFileUpload::make('images')
    ->collection('product-images')
```

### Multiple Files

```php
SpatieMediaLibraryFileUpload::make('attachments')
    ->multiple()
    ->collection('documents')
```

### Reordering Files

Enable drag-and-drop reordering for multiple uploads:

```php
SpatieMediaLibraryFileUpload::make('gallery')
    ->multiple()
    ->reorderable()
```

### Image-Only Uploads

```php
SpatieMediaLibraryFileUpload::make('photos')
    ->image()
    ->multiple()
```

### File Type Restrictions

```php
SpatieMediaLibraryFileUpload::make('document')
    ->acceptedFileTypes(['application/pdf', 'application/msword'])
```

### File Size Limits

```php
SpatieMediaLibraryFileUpload::make('attachment')
    ->minSize(512)    // KB
    ->maxSize(10240)  // KB (10MB)
```

### Using Conversions

Load a specific conversion for preview:

```php
SpatieMediaLibraryFileUpload::make('avatar')
    ->collection('avatars')
    ->conversion('thumb')
```

### Responsive Images

Generate responsive image variants on upload:

```php
SpatieMediaLibraryFileUpload::make('hero_image')
    ->responsiveImages()
```

### Custom Properties

Attach metadata to uploaded files:

```php
SpatieMediaLibraryFileUpload::make('documents')
    ->multiple()
    ->customProperties(['source' => 'admin-upload'])
```

Dynamic properties based on file:

```php
SpatieMediaLibraryFileUpload::make('files')
    ->customProperties(fn (TemporaryUploadedFile $file) => [
        'original_filename' => $file->getClientOriginalName(),
    ])
```

### Custom Headers

Set headers for uploaded files (e.g., cache control):

```php
SpatieMediaLibraryFileUpload::make('assets')
    ->customHeaders([
        'Cache-Control' => 'max-age=31536000',
    ])
```

### Image Manipulations

Apply transformations during upload:

```php
SpatieMediaLibraryFileUpload::make('photo')
    ->manipulations([
        'thumb' => ['orientation' => '90'],
    ])
```

### Conversions Disk

Store conversions on a separate disk:

```php
SpatieMediaLibraryFileUpload::make('image')
    ->conversionsDisk('s3')
```

### Filtering Media

Filter which media items to display:

```php
SpatieMediaLibraryFileUpload::make('featured_images')
    ->collection('images')
    ->filterMediaUsing(
        fn (Collection $media): Collection => $media->where('custom_properties.featured', true)
    )
```

### Storage Configuration

```php
SpatieMediaLibraryFileUpload::make('files')
    ->disk('s3')
    ->visibility('private')
```

> **Note (Filament 4):** File visibility defaults to `private` for non-local disks like S3. Use `->visibility('public')` if you need public access.

### All FileUpload Options

The component supports all standard FileUpload options:

```php
SpatieMediaLibraryFileUpload::make('images')
    ->multiple()
    ->maxFiles(5)
    ->minFiles(1)
    ->imageEditor()
    ->imagePreviewHeight('250')
    ->loadingIndicatorPosition('left')
    ->panelAspectRatio('2:1')
    ->panelLayout('integrated')
    ->removeUploadedFileButtonPosition('right')
    ->uploadButtonPosition('left')
    ->uploadProgressIndicatorPosition('left')
    ->openable()
    ->downloadable()
```

---

## Table Column: SpatieMediaLibraryImageColumn

### Basic Usage

```php
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

SpatieMediaLibraryImageColumn::make('avatar')
```

### With Collection

```php
SpatieMediaLibraryImageColumn::make('product_image')
    ->collection('images')
```

### Using Conversions

Display a specific conversion:

```php
SpatieMediaLibraryImageColumn::make('avatar')
    ->collection('avatars')
    ->conversion('thumb')
```

### Filtering Media

```php
SpatieMediaLibraryImageColumn::make('featured_image')
    ->collection('images')
    ->filterMediaUsing(
        fn (Collection $media): Collection => $media->where('custom_properties.featured', true)
    )
```

### All ImageColumn Options

Supports all standard ImageColumn customization:

```php
SpatieMediaLibraryImageColumn::make('avatar')
    ->circular()
    ->size(40)
    ->stacked()
    ->limit(3)
    ->limitedRemainingText()
```

---

## Infolist Entry: SpatieMediaLibraryImageEntry

### Basic Usage

```php
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

SpatieMediaLibraryImageEntry::make('avatar')
```

### With Collection and Conversion

```php
SpatieMediaLibraryImageEntry::make('profile_photo')
    ->collection('avatars')
    ->conversion('preview')
```

### All ImageEntry Options

```php
SpatieMediaLibraryImageEntry::make('gallery')
    ->collection('images')
    ->circular()
    ->stacked()
    ->limit(5)
    ->limitedRemainingText()
```

---

## Rich Editor Integration

Use Media Library for rich editor file attachments:

```php
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileAttachmentProvider;

RichEditor::make('content')
    ->fileAttachmentProvider(
        SpatieMediaLibraryFileAttachmentProvider::make()
            ->collection('editor-attachments')
            ->preserveFilenames()
    )
```

> **Important:** The rich content attribute must be defined as `nullable` in the database when using this provider.

---

## Complete Resource Example

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->columnSpanFull(),

                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('featured_image')
                            ->collection('featured')
                            ->image()
                            ->responsiveImages()
                            ->conversion('thumb'),

                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->collection('gallery')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->maxFiles(10),

                        SpatieMediaLibraryFileUpload::make('documents')
                            ->collection('documents')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf']),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->collection('featured')
                    ->conversion('thumb')
                    ->circular(),

                TextColumn::make('name')
                    ->searchable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
```

---

## Filament 4 Breaking Changes

### File Visibility Default

In Filament 4, file visibility for non-local disks (like S3) defaults to `private`. To restore public behavior:

```php
// In AppServiceProvider boot()
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Columns\ImageColumn;

FileUpload::configureUsing(fn (FileUpload $fileUpload) => $fileUpload
    ->visibility('public'));

ImageColumn::configureUsing(fn (ImageColumn $imageColumn) => $imageColumn
    ->visibility('public'));

ImageEntry::configureUsing(fn (ImageEntry $imageEntry) => $imageEntry
    ->visibility('public'));
```

---

## Useful Links

- [Spatie Media Library Documentation](https://spatie.be/docs/laravel-medialibrary)
- [Filament Plugin Documentation](https://filamentphp.com/plugins/filament-spatie-media-library)
