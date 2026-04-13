<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

#[Signature('resource:list')]
#[Description('List all currently mapped resources.')]
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
            ],
            Arr::map(Resource::all(), fn ($resource) => [
                $resource->name,
                $resource->model,
            ]),
        );
    }
}
