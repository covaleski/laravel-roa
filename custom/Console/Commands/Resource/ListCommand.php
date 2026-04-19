<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Covaleski\LaravelRoa\format_size_units;

#[Signature('resource:list')]
#[Description('List all currently mapped models.')]
class ListCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->table(
            [
                'Name',
                'Model',
                'Cache',
            ],
            collect(Resource::all())->map(fn ($resource) => [
                $resource->name,
                $resource->model,
                $resource->isCached()
                    ? format_size_units($resource->getSize())
                    : 'Not cached',
            ])->all(),
        );
    }
}
