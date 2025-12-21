<?php

namespace App\Filament\Clusters\Demo;

use BackedEnum;
use Filament\Clusters\Cluster;
use UnitEnum;

class DemoCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|UnitEnum|null $navigationGroup = 'Demo';

    protected static ?string $navigationLabel = 'Components';

    protected static ?int $navigationSort = 100;

    protected static ?string $slug = 'demo';

    protected static ?string $clusterBreadcrumb = 'Demo';
}
