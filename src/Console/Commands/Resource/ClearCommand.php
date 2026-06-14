<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Signature('resource:clear')]
#[Description('Clear mapped models and currently cached resources.')]
class ClearCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Resource::wipe();
    }
}
