<?php

namespace Covaleski\Laravel\Catalog\Console\Commands;

use Covaleski\Laravel\Catalog\Facades\Catalog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Covaleski\Laravel\Catalog\format_size_units;

#[Signature('catalog:list')]
#[Description('List all currently mapped models.')]
class CatalogListCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Output map file information
        $size = format_size_units(Catalog::getSize());
        $timestamp = date('Y-m-d H:i:s', Catalog::getTimestamp());
        $this->info('Map file:');
        $this->newLine();
        $this->line(Catalog::getFilename());
        $this->line("Size: {$size}");
        $this->line("Last modified: {$timestamp}");
        $this->newLine();
        // Output listed directories
        $this->info('Directories:');
        $this->newLine();
        Catalog::getDirectories()->each(fn ($dir) => $this->line($dir));
        $this->newLine();
        // Output model list
        $this->info('Models:');
        $this->newLine();
        $this->table(
            [
                'Model',
                'Cache Size',
                'Last Cached',
            ],
            collect(Catalog::all())->map(fn ($model) => [
                $model->model,
                $model->isCached()
                    ? format_size_units($model->getSize())
                    : 'Not cached',
                $model->isCached()
                    ? date('Y-m-d H:i:s', $model->getTimestamp())
                    : 'Never',
            ])->all(),
        );
    }
}
