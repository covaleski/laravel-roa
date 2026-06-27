<?php

namespace Covaleski\Laravel\Catalog\Console\Commands;

use Covaleski\Laravel\Catalog\Facades\Catalog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('resource:clear')]
#[Description('Clear mapped models and currently cached resources.')]
class CatalogClearCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Catalog::wipe();
    }
}
