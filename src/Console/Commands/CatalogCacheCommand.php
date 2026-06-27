<?php

namespace Covaleski\Laravel\Catalog\Console\Commands;

use Covaleski\Laravel\Catalog\Facades\Catalog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('resource:cache')]
#[Description('Update mapped models and cache all resources.')]
class CatalogCacheCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('resource:clear');
        Catalog::each(fn ($resource) => $resource->cache());
    }
}
