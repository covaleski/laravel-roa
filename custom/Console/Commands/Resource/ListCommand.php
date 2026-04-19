<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Covaleski\LaravelRoa\Facades\Resource;
use Covaleski\LaravelRoa\Interfaces\ResourceAttributeInterface;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

use function Covaleski\LaravelRoa\format_filesize;

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
                    ? format_filesize($resource->getSize())
                    : 'Not cached',
            ])->all(),
        );
    }
}
