<?php

namespace Covaleski\Laravel\Catalog\Console\Commands;

use Covaleski\Laravel\Catalog\Facades\Catalog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('catalog:clear')]
#[Description('Clear currently cached models.')]
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
