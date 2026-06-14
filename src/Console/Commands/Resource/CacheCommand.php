<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('resource:cache')]
#[Description('Update mapped models and cache all resources.')]
class CacheCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('resource:clear');
        Resource::each(fn ($resource) => $resource->cache());
    }
}
