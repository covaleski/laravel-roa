<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('resource:clear')]
#[Description('Clear all cached resources.')]
class ClearCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $disk = Storage::build(config('roa.cache'));
        $disk->delete($disk->files());
    }
}
