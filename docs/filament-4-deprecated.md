# Filament 4 Deprecated Items

Complete list of deprecated classes, methods, and properties in Filament 4, extracted from vendor source code.

---

## Deprecated Classes

### Widget Classes
| Deprecated Class | Replacement |
|------------------|-------------|
| `LineChartWidget` | Extend `ChartWidget` and define `getType()` method |
| `BarChartWidget` | Extend `ChartWidget` and define `getType()` method |
| `PieChartWidget` | Extend `ChartWidget` and define `getType()` method |
| `DoughnutChartWidget` | Extend `ChartWidget` and define `getType()` method |
| `PolarAreaChartWidget` | Extend `ChartWidget` and define `getType()` method |
| `RadarChartWidget` | Extend `ChartWidget` and define `getType()` method |
| `ScatterChartWidget` | Extend `ChartWidget` and define `getType()` method |
| `BubbleChartWidget` | Extend `ChartWidget` and define `getType()` method |

### Action Classes
| Deprecated Class | Replacement |
|------------------|-------------|
| `ButtonAction` | `Action::make()->button()` |
| `IconButtonAction` | `Action::make()->iconButton()` |

### Column Classes
| Deprecated Class | Replacement |
|------------------|-------------|
| `BadgeColumn` | `TextColumn::make()->badge()` |
| `BooleanColumn` | `IconColumn::make()->boolean()` |
| `TagsColumn` | `TextColumn::make()` |

### Form Classes
| Deprecated Class | Replacement |
|------------------|-------------|
| `RelationshipRepeater` | `Repeater::make()->relationship()` |
| `MultiSelect` | `Select::make()->multiple()` |

### Other Classes
| Deprecated Class | Replacement |
|------------------|-------------|
| `Filament\Navigation\MenuItem` | `Filament\Actions\Action` |

---

## Deprecated Methods

### StatsOverviewWidget
| Deprecated | Replacement |
|------------|-------------|
| `getCards()` | `getStats()` |

### Actions - Event Dispatching
| Deprecated | Replacement |
|------------|-------------|
| `dispatchEvent()` | `dispatch()` |
| `emitSelf()` | `dispatchSelf()` |
| `emitTo()` | `dispatchTo()` |

### Actions - Modal
| Deprecated | Replacement |
|------------|-------------|
| `modalSubheading()` | `modalDescription()` |
| `centerModal()` | `modalAlignment(Alignment::Center)` |
| `modalActions()` | `modalFooterActions()` |
| `extraModalActions()` | `extraModalFooterActions()` |
| `modalButton()` | `modalSubmitActionLabel()` |
| `modalFooter()` | `modalContentFooter()` |
| `makeExtraModalAction()` | `makeModalSubmitAction()` |

### Actions - Data
| Deprecated | Replacement |
|------------|-------------|
| `mutateFormDataUsing()` | `mutateDataUsing()` |
| `fillForm()` | `data()` |
| `resetFormData()` | `resetData()` |
| `getFormData()` | `getData()` |
| `getRawFormData()` | `getRawData()` |

### Actions - Schema/Form
| Deprecated | Replacement |
|------------|-------------|
| `infolist()` | `schema()` |
| `disabledForm()` | `disabledSchema()` |
| `hasDisabledForm()` | `disabledSchema()` |
| `form()` | `schema()` |
| `getForm()` | `getSchema()` |
| `isFormDisabled()` | `isSchemaDisabled()` |
| `mountedActionHasForm()` | `mountedActionHasSchema()` |
| `getMountedActionForm()` | `getMountedActionSchema()` |

### Actions - Notifications
| Deprecated | Replacement |
|------------|-------------|
| `failureNotification()` | `failureNotificationTitle()` |
| `successNotification()` | `successNotificationTitle()` |

### Actions - Other
| Deprecated | Replacement |
|------------|-------------|
| `cancel()` | `halt()` |
| `disableLabel()` | `hiddenLabel()` |
| `extraAttributes()` (on Action) | `extraAttributes()` |
| `getAlpineClickHandler()` | `getJsClickHandler()` |
| `afterReplicatedSaved()` | `after()` |
| `disableCreateAnother()` | `createAnother()` |
| `disableAssociateAnother()` | `associateAnother()` |
| `disableAttachAnother()` | `attachAnother()` |
| `assertHeld()` | `assertActionHalted()` |

### Forms - InteractsWithForms Trait
| Deprecated | Replacement |
|------------|-------------|
| `cacheForm()` | `cacheSchema()` |
| `getForms()` | Define a method of the form's name and return the form |
| `getFormStatePath()` | Override `form()` method |
| `hasCachedForm()` | `hasCachedSchema()` |
| `getForm()` | `getSchema()` |
| `getCachedForms()` | `getCachedSchemas()` |
| `getFormModel()` | Override `form()` method |
| `getFormModelClass()` | Override `form()` method |
| `getFormRecord()` | Override `form()` method |
| `getFormRecordTitle()` | Override `form()` method |
| `isCachingForms()` | `isCachingSchemas()` |
| `getActiveFormLocale()` | `getActiveSchemaLocale()` |
| `getOldFormState()` | `getOldSchemaState()` |
| `callMountedFormAction()` | `callMountedAction()` |
| `mountFormAction()` | `mountAction()` |
| `mountedFormActionShouldOpenModal()` | `mountedActionShouldOpenModal()` |
| `mountedFormActionHasForm()` | `mountedActionHasForm()` |
| `getMountedFormAction()` | `getMountedAction()` |
| `unmountFormAction()` | `unmountAction()` |

### Infolists - InteractsWithInfolists Trait
| Deprecated | Replacement |
|------------|-------------|
| `getInfolist()` | `getSchema()` |
| `cacheInfolist()` | `cacheSchema()` |
| `getCachedInfolists()` | `getCachedSchemas()` |
| `hasCachedInfolist()` | `hasCachedSchema()` |
| `callMountedInfolistAction()` | `callMountedAction()` |
| `mountInfolistAction()` | `mountAction()` |
| `mountedInfolistActionShouldOpenModal()` | `mountedActionShouldOpenModal()` |
| `mountedInfolistActionHasForm()` | `mountedActionHasForm()` |
| `getMountedInfolistAction()` | `getMountedAction()` |
| `unmountInfolistAction()` | `getMountedActionComponent()` |

### Tables - Action Methods
| Deprecated | Replacement |
|------------|-------------|
| `callMountedTableAction()` | `callMountedAction()` |
| `mountTableAction()` | `mountAction()` |
| `replaceMountedTableAction()` | `mountAction()` |
| `mountedTableActionShouldOpenModal()` | `mountedActionShouldOpenModal()` |
| `mountedTableActionHasForm()` | `mountedActionHasSchema()` |
| `getMountedTableAction()` | `getMountedAction()` |
| `getMountedTableActionForm()` | `getMountedActionSchema()` |
| `getMountedTableActionRecord()` | `getMountedAction()?->getRecord()` |
| `unmountTableAction()` | `unmountAction()` |

### Tables - Bulk Action Methods
| Deprecated | Replacement |
|------------|-------------|
| `callMountedTableBulkAction()` | `callMountedAction()` |
| `mountTableBulkAction()` | `mountAction()` |
| `replaceMountedTableBulkAction()` | `replaceMountedAction()` |
| `mountedTableBulkActionShouldOpenModal()` | `mountedActionShouldOpenModal()` |
| `mountedTableBulkActionHasForm()` | `mountedActionHasSchema()` |
| `getMountedTableBulkAction()` | `getMountedAction()` |
| `getMountedTableBulkActionForm()` | `getMountedActionSchema()` |

### Tables - Configuration (Override `table()` method instead)
| Deprecated Method |
|-------------------|
| `getTableHeading()` |
| `getTableDescription()` |
| `getTableQuery()` |
| `getTableQueryStringIdentifier()` |
| `getTableRecordTitle()` |
| `getTableRecordTitleAttribute()` |
| `getTableModelLabel()` |
| `getTablePluralModelLabel()` |
| `isTableStriped()` |
| `getTablePollingInterval()` |
| `isTableLoadingDeferred()` |
| `getDefaultTableRecordsPerPageSelectOption()` |
| `getTableRecordsPerPageSelectOptions()` |
| `getTableFilters()` |
| `getTableFiltersLayout()` |
| `isTableFilterable()` |
| `getTableFiltersFormColumns()` |
| `getColumnManagerFormWidth()` |
| `getColumnManagerFormMaxHeight()` |
| `isToggleColumnManagerTriggerActionHidden()` |
| `getTableContentGrid()` |
| `getTableContentFooter()` |
| `getTableContent()` |
| `getDefaultTableSortColumn()` |
| `getDefaultTableSortDirection()` |
| `getDefaultTableSortSelectColumnOption()` |
| `getTableEmptyState()` |
| `getTableEmptyStateActions()` |
| `getTableEmptyStateDescription()` |
| `getTableEmptyStateHeading()` |
| `getTableEmptyStateIcon()` |
| `getTableColumns()` |
| `getTableRecordAction()` |
| `getTableActions()` |
| `getTableActionsPosition()` |
| `getTableBulkActions()` |
| `isTableSearchable()` |
| `getTableSearchSessionKey()` |
| `isTableReorderable()` |
| `getTableReorderColumn()` |
| `getTableHeaderActions()` |
| `getTableHeader()` |
| `getTableHeaderActionsPosition()` |
| `hasTableHeading()` |

### Tables - Column Manager
| Deprecated | Replacement |
|------------|-------------|
| `toggleTableColumn()` | `applyTableColumnManager()` |
| `getTableColumnToggleFormStateSessionKey()` | `getTableColumnManagerSessionKey()` |

### KeyValue Component
| Deprecated | Replacement |
|------------|-------------|
| `addButtonLabel()` | `addActionLabel()` |
| `deleteButtonLabel()` | `deleteActionLabel()` |
| `reorderButtonLabel()` | `reorderActionLabel()` |
| `disableAddingRows()` | `addable()` |
| `disableDeletingRows()` | `deletable()` |
| `disableEditingKeys()` | `editableKeys()` |
| `disableEditingValues()` | `editableValues()` |

### Repeater/Builder Components
| Deprecated | Replacement |
|------------|-------------|
| `label()` (for add button) | `addActionLabel()` |
| `disableItemCreation()` | `addable(false)` |
| `disableItemDeletion()` | `deletable(false)` |
| `disableItemMovement()` | `reorderable(false)` |
| `enableCollapseAll()` | `collapseAllActionLabel()` |
| `removable()` | `deletable()` |
| `sortable()` | `reorderable()` |

### ImageColumn
| Deprecated | Replacement |
|------------|-------------|
| `height()` | `imageHeight()` |
| `size()` | `imageSize()` |
| `rounded()` | `circular()` |
| `getHeight()` | `getImageHeight()` |
| `isRounded()` | `isCircular()` |

### ImageEntry
| Deprecated | Replacement |
|------------|-------------|
| `height()` | `imageHeight()` |
| `size()` | `imageSize()` |
| `rounded()` | `circular()` |
| `getHeight()` | `getImageHeight()` |
| `isRounded()` | `isCircular()` |

### IconColumn
| Deprecated | Replacement |
|------------|-------------|
| `options()` | `icons()` |

### TagsColumn
| Deprecated | Replacement |
|------------|-------------|
| `limit()` | `limitList()` |

### TextColumn/TextEntry
| Deprecated | Replacement |
|------------|-------------|
| `disableClick()` | `disabledClick()` |

### Select Component
| Deprecated | Replacement |
|------------|-------------|
| `disablePlaceholderSelection()` | `selectablePlaceholder()` |

### TextInput Component
| Deprecated | Replacement |
|------------|-------------|
| `disableAutocomplete()` | `autocomplete()` |
| `disableAutocapitalize()` | `autocapitalize()` |

### DateTimePicker Component
| Deprecated | Replacement |
|------------|-------------|
| `withoutDate()` | `date(false)` |
| `withoutTime()` | `time(false)` |
| `withoutSeconds()` | `seconds(false)` |
| `minDate()` | `minValue()` |
| `maxDate()` | `maxValue()` |
| `getMinDate()` | `getMinValue()` |
| `getMaxDate()` | `getMaxValue()` |

### RichEditor/MarkdownEditor Component
| Deprecated | Replacement |
|------------|-------------|
| `attachmentDirectory()` | `fileAttachmentsDirectory()` |
| `attachmentVisibility()` | `fileAttachmentsVisibility()` |
| `getUploadedAttachmentUrlUsing()` | `getFileAttachmentUrlUsing()` |
| `saveUploadedFileAttachmentsUsing()` | `saveUploadedFileAttachmentUsing()` |

### FileUpload Component
| Deprecated | Replacement |
|------------|-------------|
| `enableDownload()` | `downloadable()` |
| `enableOpen()` | `openable()` |
| `enableReordering()` | `reorderable()` |
| `removeUploadedFileUsing()` | `deleteUploadedFileUsing()` |
| `removeUploadedFileButtonPosition()` | `deleteUploadedFileButtonPosition()` |

> **Note:** `imageCropAspectRatio()`, `imageResizeTargetHeight()`, `imageResizeTargetWidth()` are NOT deprecated. They are for Filepond's automatic image transformation before upload (without user interaction). `imageEditorAspectRatios()` and `imageEditorViewport*()` are separate methods for the interactive image editor.

### KeyValueEntry
| Deprecated | Replacement |
|------------|-------------|
| `emptyLabel()` | `placeholder()` |

### Support Concerns
| Deprecated | Replacement |
|------------|-------------|
| `isRelationship()` | `hasRelationship()` |
| `hasPlaceholder()` | `getPlaceholder() !== null` |

### ImportColumn
| Deprecated | Replacement |
|------------|-------------|
| `getExample()` | `getExamples()` |

### Testing
| Deprecated | Replacement |
|------------|-------------|
| `assertFormSet()` (on actions) | `assertSchemaStateSet()` |
| `assertHasFormErrors()` | `assertHasSchemaErrors()` |
| `assertHasNoFormErrors()` | `assertHasNoSchemaErrors()` |

---

## Deprecated Properties

### Resource Properties
| Deprecated | Replacement |
|------------|-------------|
| `$label` | `$modelLabel` |
| `$pluralLabel` | `$pluralModelLabel` |
| `getLabel()` | `getModelLabel()` |
| `getPluralLabel()` | `getPluralModelLabel()` |

### RelationManager Properties (Override `table()` method instead)
| Deprecated Property |
|--------------------|
| `$inverseRelationship` |
| `$canAssociate` |
| `$canAttach` |
| `$canCreate` |
| `$canDissociate` |
| `$canDissociateAny` |

### TableWidget Properties (Override `table()` method instead)
| Deprecated Property |
|--------------------|
| `$heading` |

---

## Deprecated FilamentManager Methods

| Deprecated | Replacement |
|------------|-------------|
| `renderHook()` | `FilamentView::renderHook()` |
| `registerNavigationGroups()` | `navigationGroups()` on panel configuration |
| `registerNavigationItems()` | `navigationItems()` on panel configuration |
| `registerPages()` | `pages()` on panel configuration |
| `registerRenderHook()` | `renderHook()` on panel configuration |
| `registerResources()` | `resources()` on panel configuration |
| `registerScripts()` | `FilamentAsset` facade |
| `registerScriptData()` | `FilamentAsset` facade |
| `registerStyles()` | `FilamentAsset` facade |
| `registerTheme()` | `theme()` on panel configuration |
| `registerViteTheme()` | `viteTheme()` on panel configuration |
| `registerUserMenuItems()` | `userMenuItems()` on panel configuration |
| `registerWidgets()` | `widgets()` on panel configuration |

---

## Deprecated Page Methods

| Deprecated | Replacement |
|------------|-------------|
| `getHeaderWidgetsColumns()` | `getWidgetsSchemaComponents($this->getHeaderWidgets())` |
| `getFooterWidgetsColumns()` | `getWidgetsSchemaComponents($this->getFooterWidgets())` |
| `getVisibleHeaderWidgets()` | `getWidgetsSchemaComponents()` |
| `getVisibleFooterWidgets()` | `getWidgetsSchemaComponents()` |
| `getVisibleWidgets()` (Dashboard) | `getWidgetsSchemaComponents($this->getWidgets())` |
| `getLabel()` (Resource Page) | Override resource's `getModelLabel()` |
| `formActionsAreAlignedLeft()` | `alignFormActionsStart()` |
| `formActionsAreAlignedRight()` | `alignFormActionsEnd()` |

### EditRecord Page
| Deprecated | Replacement |
|------------|-------------|
| `getSavedNotification()` | `getSavedNotificationTitle()` |

### CreateRecord Page
| Deprecated | Replacement |
|------------|-------------|
| `getCreatedNotification()` | `getCreatedNotificationTitle()` |

### EditProfile Page
| Deprecated | Replacement |
|------------|-------------|
| `getCancelAction()` | `getCancelFormAction()` |

---

## Deprecated Blade Components

| Deprecated | Replacement |
|------------|-------------|
| `<x-filament-widgets::widgets>` | Use a schema to render widgets |
| `PanelsIconAlias::SIDEBAR_EXPAND_BUTTON` (RTL) | `PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL` |
| `PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON` (RTL) | `PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON_RTL` |

---

## Deprecated Traits

| Deprecated | Replacement |
|------------|-------------|
| `Filament\Tables\Columns\Concerns\CanBeCopied` | `Filament\Support\Concerns\CanBeCopied` |
| `HasContent` trait | Override `table()` method |
| `CanBeStriped` trait | Override `table()` method |
| `CanPollRecords` trait | Override `table()` method |
| `HasEmptyState` trait | Override `table()` method |
| `HasHeader` trait | Override `table()` method |
| `HasRecordAction` trait | Override `table()` method |

---

## JavaScript Notifications Deprecations

| Deprecated | Replacement |
|------------|-------------|
| `dispatchEvent()` | `dispatch()` |
| `emitSelf()` | `dispatchSelf()` |
| `emitTo()` | `dispatchTo()` |

---

## Notes

1. **Table Configuration**: Most table-related deprecations follow the same pattern - override the `table()` method to configure the table instead of using separate methods.

2. **Forms → Schemas**: Filament 4 renamed "Forms" to "Schemas" internally, hence many `*Form*` methods are deprecated in favor of `*Schema*` equivalents.

3. **Actions Unification**: Table actions, form actions, and infolist actions have been unified under a single action system.

4. **Chart Widgets**: All specific chart widget classes are deprecated. Use `ChartWidget` with the `getType()` method returning the chart type string (`'line'`, `'bar'`, `'pie'`, etc.).

---

*Generated from Filament 4.x vendor source code*
