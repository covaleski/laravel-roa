<?php

namespace Covaleski\Laravel\Catalog\Console\Commands;

use Covaleski\Laravel\Catalog\Facades\Catalog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('catalog:cache')]
#[Description('Update mapped models and cache all of them.')]
class CatalogCacheCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('catalog:clear');
        Catalog::each(fn ($model) => $model->cache());
    }
}
