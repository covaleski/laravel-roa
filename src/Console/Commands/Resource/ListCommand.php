<?php

namespace Covaleski\Laravel\Catalog\Console\Commands\Resource;

use Covaleski\Laravel\Catalog\Facades\Resource;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Covaleski\Laravel\Catalog\format_size_units;

#[Signature('resource:list')]
#[Description('List all currently mapped models.')]
class ListCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Output map file information
        $size = format_size_units(Resource::getSize());
        $timestamp = date('Y-m-d H:i:s', Resource::getTimestamp());
        $this->info('Map file:');
        $this->newLine();
        $this->line(Resource::getFilename());
        $this->line("Size: {$size}");
        $this->line("Last modified: {$timestamp}");
        $this->newLine();
        // Output listed directories
        $this->info('Directories:');
        $this->newLine();
        Resource::getDirectories()->each(fn ($dir) => $this->line($dir));
        $this->newLine();
        // Output resource list
        $this->info('Resources:');
        $this->newLine();
        $this->table(
            [
                'Name',
                'Model',
                'Cache Size',
                'Last Cached',
            ],
            collect(Resource::all())->map(fn ($resource) => [
                $resource->name,
                $resource->model,
                $resource->isCached()
                    ? format_size_units($resource->getSize())
                    : 'Not cached',
                $resource->isCached()
                    ? date('Y-m-d H:i:s', $resource->getTimestamp())
                    : 'Never',
            ])->all(),
        );
    }
}
