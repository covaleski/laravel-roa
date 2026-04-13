<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('resource:cache')]
#[Description('Map models and cache resources now.')]
class CacheCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('resource:clear');
        Resource::all();
    }
}
